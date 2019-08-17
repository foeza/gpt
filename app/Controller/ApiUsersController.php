<?php
class ApiUsersController extends AppController {
	public $components = array(
		'RmProperty', 'RmSetting',
		'Rest.Rest' => array(
			'actions' => array(
	            'index' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data', 'offset',
	                ),
	            ),
	            'call_messages' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data', 'offset',
	                ),
	            ),
	            'get_arebi_agents' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data'
	                ),
	            ),
	            'verify_principle_arebi' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data'
	                ),
	            ),
	            'get_data_user' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data'
	                ),
	            ),
			),
            'debug' => 2,
        ),
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();

		$this->layout = 'ajax';	
		$this->autoLayout = false;
		$this->autoRender = true;
   	}

	public function index(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));
		$email = $this->RmCommon->filterEmptyField($params, 'email', false, false, array(
			'type' => 'trailing_slash',
		));
		$limit = 30;
		
		$options = $this->User->_callRefineParams($this->params, array(
			'conditions' => array(
				'User.group_id' => array( 1,2,3,5 ),
				'User.modified NOT' => NULL,
			),
			'limit' => $limit,
			'order' => array(
				'User.modified' => 'ASC',
				'User.id' => 'ASC',
			),
		));

		if( !empty($lastupdated) ) {
			$options['conditions'][]['OR'] = array(
				'User.modified >' => $lastupdated,
				'User.created >' => $lastupdated,
			);
		}
		if( !empty($email) ) {
			$options['conditions']['User.email'] = $email;
		}
		if( isset($params['offset']) ) {
			$offset = $this->RmCommon->filterIssetField($params, 'offset', false, false, array(
				'type' => 'trailing_slash',
			));
			$options['offset'] = $offset;
		}

		Configure::write('Rest.token', false);
		$values = $this->User->getData('all', $options, array(
			'status' => 'all',
			'company' => false,
		));

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

				$value = $this->User->getAllNeed($value, $id);
				$value = $this->User->UserCompany->getMerge($value, $id);

				$parent = $this->User->getMerge(array(), $parent_id, true, 'Parent');
				$parent = $this->User->UserCompany->getMerge($parent, $parent_id);
				$parent = $this->User->UserCompanyConfig->getMerge($parent, $parent_id);
				$value['Parent'] = $parent;

	    		$value = $this->User->UserClientType->getMerge($value, $id);
	    		$value = $this->User->UserPropertyType->getMerge($value, $id);
	    		$value = $this->User->UserSpecialist->getMerge($value, $id);
	    		$value = $this->User->UserLanguage->getMerge($value, $id);
	    		$value = $this->User->UserAgentCertificate->getMerge($value, $id);

				$values[$key] = $value;
			}
		}

		if( isset($offset) ) {
			$this->set('offset', $offset+$limit);
		}

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($values, 'manual');
	}

	public function messages(){
		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$passkey = $this->RmCommon->filterEmptyField($this->params, 'named', 'token');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'rumahku-messages-api',
				'token' => $passkey,
			),
		));

		if( !empty($setting) ) {
			$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$offset = $this->RmCommon->filterEmptyField($setting, 'Setting', 'offset', 0);
			$temp = $this->RmCommon->filterEmptyField($setting, 'Setting', 'temp');
			$lastupdated = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value', false);
			$lastupdated = $this->RmCommon->filterEmptyField($this->params, 'named', 'lastupdated', $lastupdated);

			$pass = array(
				'device' => 'rumahku-messages-api',
				'passkey' => $passkey,
				'lastupdated' => $lastupdated,
				'is_devices' => 'prime',
			);

			if( is_numeric($offset) ) {
				$pass['offset'] = $offset;
			}

			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'api_users',
				'action' => 'messages',
				'?' => $pass,
				'ext' => 'json',
				'admin' => false,
			));
			
			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$datas = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$data_offset = $this->RmCommon->filterEmptyField($datas, 'offset');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');
			
			if(!empty($datas)){
				$datas = Common::_callUnset($datas, array(
					'offset'
				));

				foreach ($datas as $key => $data) {
					$modified = $this->RmCommon->filterEmptyField($data, 'Message', 'modified');
					$data = $this->RmUser->apiBeforeSave($data);
					$result = $this->User->Message->apiSave($data);
					$msg = $this->RmCommon->filterEmptyField($result, 'msg');
					$this->RmCommon->setProcessParams($result, false, array(
						'noRedirect' => true,
						'flash' => false,
					));

					echo $msg."<br>";
				}

				if(!empty($data_offset)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', $data_offset);
					$this->Setting->set('temp', $modified);
					$this->Setting->save();
				}
			}else{
				if(!empty($setting_id) && !empty($temp)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', 0);
					$this->Setting->set('value', $temp);
					$this->Setting->set('temp', '');
					$this->Setting->save();
				}
				echo __('Data tidak tersedia');
			}
		} else {
			echo __('Data tidak tersedia');
		}

		die();
	}

	function call_messages(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));
		$offset = $this->RmCommon->filterEmptyField($params, 'offset', false, 0);
		$limit = 30;

		$options = array(
			'conditions' => array(
				'Message.status' => TRUE,
				'Message.instanace' => 'to_rumahku',
			),
			'order' => array('Message.modified' => 'ASC'),
			'limit' => $limit,
		);

		if( $lastupdated ){
			$options['conditions'][]['OR'] = array(
				'Message.modified >' => $lastupdated,
				'Message.created >' => $lastupdated,
			);
		}

		if( $offset ) {
			$options['offset'] = $offset;
		}

		$values = $this->User->Message->find('all', $options);

		if(!empty($values)){
			$values = $this->User->Message->getMergeList($values, array(
				'contain' => array(
					'ToUser' => array(
						'uses' => 'User',
						'primaryKey' => 'id',
						'foreignKey' => 'to_id',
					),
					'User' => array(
						'contain' => array(
							'UserCompany' => array(
								'primaryKey' => 'user_id',
								'foreignKey' => 'parent_id',
							),
						),
					),
				),
			));

			if( isset($offset) ) {
				$values['offset'] = $offset+$limit;
			}
		}
		$this->RmCommon->_callDataForAPI($values, 'manual');
	}

	function get_arebi_agents(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));
		$email_principle = $this->RmCommon->filterEmptyField($params, 'email', false, false, array(
			'type' => 'trailing_slash',
		));
		$arebi_id = $this->RmCommon->filterEmptyField($params, 'arebi_id');

		/*define variable*/
		$values = array();
		$limit = 30;

		if(!empty($email_principle)){
			$principle_data = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $email_principle,
					'User.group_id' => 3
				)
			), array(
				'status' => 'all',
				'company' => false,
			));

			$parent_id = $this->RmCommon->filterEmptyField($principle_data, 'User', 'id');

			if(!empty($parent_id)){
				$options = $this->User->_callRefineParams($this->params, array(
					'conditions' => array(
						'User.group_id' => 2,
						'User.parent_id' => $parent_id,
						'User.modified NOT' => NULL,
					),
					'limit' => $limit,
					'order' => array(
						'User.modified' => 'ASC',
						'User.id' => 'ASC',
					),
				));

				if( !empty($lastupdated) ) {
					$options['conditions'][]['OR'] = array(
						'User.modified >' => $lastupdated,
						'User.created >' => $lastupdated,
					);
				}

				Configure::write('Rest.token', false);
				$values = $this->User->getData('all', $options, array(
					'status' => 'all',
					'company' => false,
				));

				if(!empty($values)){
					foreach ($values as $key => $value) {
						$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
						$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
						$arebi_id = $this->RmCommon->filterEmptyField($value, 'User', 'arebi_id', 0);

						$value = $this->User->getAllNeed($value, $id);

						if(!empty($arebi_id)){
							$this->User->UserConfig->updateAll(
								array(
									'arebi_id' => $arebi_id
								),
								array(
									'user_id' => $id
								)
							);
						}

						$values[$key] = $value;
					}
				}
			}
		}

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($values, 'manual');
	}

	function verify_principle_arebi(){
		$params = $this->params->query;
		
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		
		$email = $this->RmCommon->filterEmptyField($params, 'email', false, false, array(
			'type' => 'trailing_slash',
		));

		$arebi_id = Common::hashEmptyField($params, 'arebi_id', 0);

		if(!empty($arebi_id)){
			$data_nama['User']['full_name'] = $this->RmCommon->filterEmptyField($params, 'full_name');
			$full_name = $this->RmUser->_getUserFullName($data_nama, 'reverse');

			$url = $this->RmCommon->filterEmptyField($params, 'url');

			$data_arebi['Arebi'] = array(
				'id' => $arebi_id,
				'first_name' => $this->RmCommon->filterEmptyField($full_name, 'first_name'),
				'last_name' => $this->RmCommon->filterEmptyField($full_name, 'last_name'),
				'email' => $this->RmCommon->filterEmptyField($params, 'email_arebi'),
				'logo' => $this->RmCommon->filterEmptyField($params, 'logo'),
				'url' => $url,
				'arebi_name' => $this->RmCommon->filterEmptyField($params, 'arebi_name'),
			);

			$this->User->UserConfig->Arebi->saveAll($data_arebi);
		}

		$check_prime = $this->User->getData('first', array(
			'conditions' => array(
				'User.email' => $email
			)
		), array(
			'status' => 'all',
			'company' => false,
		));

		$user_id = $this->RmCommon->filterEmptyField($check_prime, 'User', 'id');

		$value['is_prime'] = !empty($check_prime) ? true : false;

		if(!empty($value['is_prime']) && !empty($user_id)){
			$result = $this->User->UserConfig->updateAll(
				array(
					'UserConfig.arebi_id' => $arebi_id,
					'UserConfig.modified' => "'".date('Y-m-d H:i:s')."'"
				),
				array(
					'UserConfig.user_id' => $user_id
				)
			);
		}
		
		$this->RmCommon->_callDataForAPI($value, 'manual');
	}

	/* ==========================================================
	========= function to update membership bundling RKU ========
	=============================================================
		- $package get the package from rku
		- $data_company get all list company semi-active
		- update all company with the package from rku
	=========================================================== */
	public function update_membership(){
		$msg_empty = __('Data Tidak Ditemukan.');

		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'update-membership',
			),
		));
		
		if( !empty($setting) ) {

			$setting_id    = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$saved_last_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value', false);

			// get package rku
			$path_link = sprintf('api/memberships/list_package/get_package:1/is_cheapest:1');
			$opsi_link = array(
				'not_set_data'  => true,
				'custom_link' 	=> true,
				'path_link' 	=> $path_link,
			);
			$package = $this->RmSetting->callDataMembershipRKU($opsi_link);

			if (!empty($package)) {
				$default_options =  array(
					'conditions' => array(
						'UserCompanyConfig.id IS NOT NULL',
						'DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') >=' => date('Y-m-d'),
					),
					'order' => array(
						'UserCompanyConfig.user_id' => 'ASC',
					),
					'limit' => Configure::read('__Site.config_new_table_pagination'),
				);

				if (!empty($saved_last_id)) {
					$options = array(
						'conditions' => array(
							'UserCompanyConfig.user_id >' => $saved_last_id,
						),
					);
					$default_options = array_merge_recursive($default_options, $options);
				}

				// list data company to update the membership bundling by cheapest package
				$data_company = $this->User->UserCompanyConfig->getData('all', $default_options);

				if (!empty($data_company)) {
					
					foreach ($data_company as $key => $value) {
						$value = $this->User->UserCompanyConfig->getMergeList($value, array(
							'contain' => array(
								'User' => array(
									'elements' => array(
										'status' => 'all',
									),
									'contain' => array(
										'UserCompany',
									),
								),
							),
						));
						$last_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

						$result  = $this->User->UserCompanyConfig->updateMembershipRKU($value, $package);
						$msg     = $this->RmCommon->filterEmptyField($result, 'msg');

						echo $msg."<br><br>";

					}

					if(!empty($last_id)){
						$this->Setting->id = $setting_id;
						$this->Setting->set('value', $last_id);
						$this->Setting->save();
					}
					die();

				} else {
					$msg_empty = __('Belum ada data untuk diupdate.');
				}

			} else {
				$msg_empty = __('Data tidak ditemukan. Silakan atur/pilih dahulu paket membership RKU termurah.');
			}

		} else {
			$msg_empty = __('please check your table settings.');
		}

		$this->set('msg_empty', $msg_empty);
		$this->render('/Elements/empty_data');

	}

	function get_data_user(){
		$params = $this->params->query;

		$email = $this->RmCommon->filterEmptyField($params, 'email', false, false, array(
			'type' => 'trailing_slash',
		));

		$check_prime = $this->User->getData('first', array(
			'conditions' => array(
				'User.email' => $email
			)
		), array(
			'status' => 'all',
			'company' => false,
		));

		$user_id = $this->RmCommon->filterEmptyField($check_prime, 'User', 'id');

		$merge = $this->User->getMerge(array(), $user_id, true);

		$this->RmCommon->_callDataForAPI($merge, 'manual');
	}
}
?>