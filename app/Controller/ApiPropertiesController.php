<?php
class ApiPropertiesController extends AppController {
	public $components = array(
		'RmProperty', 'RmApiProperty',
		'Rest.Rest' => array(
			'actions' => array(
	            'index' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data', 'offset',
	                ),
	            ),
	            'medias' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data', 'offset',
	                ),
	            ),
	            'videos' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data', 'offset',
	                ),
	            ),
	            'arebi_properties' => array(
	            	'extract' => array(
	                	'paging', 'data'
	                ),
	            ),
	            'api_data_listing' => array(
	            	'extract' => array(
	                	'data', 'status'
	                ),
	            )
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

   	function _callDataProperty ( $value ) {
   		$value = $this->User->Property->getDataList($value, array(
			'contain' => array(
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyNotification',
				'User',
				'PropertyFacility',
				'PropertyPointPlus',
			),
		));
		
		$id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
		$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
		$value = $this->User->Property->PropertyPrice->getMerge($value, $id, false);

		if(!empty($value['PropertySold'])){
			$period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');
			
			$value['PropertySold'] = $this->User->Property->Period->getMerge($value['PropertySold'], $period_id);
		}

		return $value;
   	}

   	function _callUserByEmail () {
		$params = $this->params->query;
		$email = $this->RmCommon->filterEmptyField($params, 'email', false, false, array(
			'type' => 'trailing_slash',
		));

		if( !empty($email) ) {
			$user = $this->User->getMerge(array(), $email, false, 'User', 'User.email');
			$user_id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
		} else {
			$user_id = false;
		}

		return $user_id;
   	}

	public function index(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$mls_id = $this->RmCommon->filterEmptyField($params, 'mls_id');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));
		$user_id = $this->_callUserByEmail();
		$limit = 20;

		$options = $this->User->Property->_callRefineParams($this->params, array(
			'conditions' => array(
				'Property.modified NOT' => NULL,
				// 'User.group_id' => 2,
			),
			'contain' => array(
				// 'User',
			),
			'limit' => $limit,
			'order' => array(
				'Property.modified' => 'ASC',
				'Property.id' => 'ASC',
			),
		));

		if( !empty($lastupdated) ) {
			$options['conditions'][]['OR'] = array(
				'Property.modified >' => $lastupdated,
				'Property.created >' => $lastupdated,
			);
		}
		if( !empty($user_id) ) {
			$options['conditions']['Property.user_id'] = $user_id;
		}
		if( isset($params['offset']) ) {
			$offset = $this->RmCommon->filterIssetField($params, 'offset', false, false, array(
				'type' => 'trailing_slash',
			));
			$options['offset'] = $offset;
		}
		if( !empty($mls_id) ) {
			$options['conditions']['Property.mls_id'] = $mls_id;
		}

		Configure::write('Rest.token', false);
		$properties = $this->User->Property->getData('all', $options, array(
			'status' => 'all-condition',
			'company' => false,
		));

		if(!empty($properties)){
			foreach ($properties as $key => $value) {
				$value = $this->_callDataProperty($value);

				$properties[$key] = $value;
			}
		}

		if( isset($offset) ) {
			$this->set('offset', $offset+$limit);
		}

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($properties, 'manual');
	}

	public function medias(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$mls_id = $this->RmCommon->filterEmptyField($params, 'mls_id');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));
		$user_id = $this->_callUserByEmail();
		$limit = 20;
		
		$options = array(
			'conditions' => array(
				'PropertyMedias.modified NOT' => NULL,
				'PropertyMedias.property_id <>' => 0,
			),
			'limit' => $limit,
			'order' => array(
				'PropertyMedias.modified' => 'ASC',
				'PropertyMedias.id' => 'ASC',
			),
		);

		if( !empty($lastupdated) ) {
			$options['conditions'][]['OR'] = array(
				'PropertyMedias.modified >' => $lastupdated,
				'PropertyMedias.created >' => $lastupdated,
			);
		}
		if( !empty($mls_id) ) {
			$options['contain'][] = 'Property';
			$options['conditions']['Property.mls_id'] = $mls_id;
		}
		if( !empty($user_id) ) {
			$options['contain'][] = 'Property';
			$options['conditions']['Property.user_id'] = $user_id;
		}
		if( isset($params['offset']) ) {
			$offset = $this->RmCommon->filterIssetField($params, 'offset', false, false, array(
				'type' => 'trailing_slash',
			));
			$options['offset'] = $offset;
		}

		Configure::write('Rest.token', false);
		$values = $this->User->Property->PropertyMedias->getData('all', $options, array(
			'status' => false,
		));

		if(!empty($values)){
			foreach ($values as $key => &$value) {
				$value = Common::_callUnset($value, array(
                    'Property',
                ));

				$id = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'property_id');
				$value = $this->User->Property->getMerge($value, $id);
				$value = $this->User->Property->getMergeList($value, array(
					'contain' => array(
						'User' => array(
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));
			}
		}

		if( isset($offset) ) {
			$this->set('offset', $offset+$limit);
		}

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($values, 'manual');
	}

	public function videos(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$mls_id = $this->RmCommon->filterEmptyField($params, 'mls_id');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));
		$user_id = $this->_callUserByEmail();
		$limit = 20;
		
		$options = array(
			'conditions' => array(
				'PropertyVideos.modified NOT' => NULL,
				'PropertyVideos.property_id <>' => 0,
			),
			'limit' => $limit,
			'order' => array(
				'PropertyVideos.modified' => 'ASC',
				'PropertyVideos.id' => 'ASC',
			),
		);

		if( !empty($lastupdated) ) {
			$options['conditions'][]['OR'] = array(
				'PropertyVideos.modified >' => $lastupdated,
				'PropertyVideos.created >' => $lastupdated,
			);
		}
		if( !empty($mls_id) ) {
			$options['contain'][] = 'Property';
			$options['conditions']['Property.mls_id'] = $mls_id;
		}
		if( !empty($user_id) ) {
			$options['contain'][] = 'Property';
			$options['conditions']['Property.user_id'] = $user_id;
		}
		if( isset($params['offset']) ) {
			$offset = $this->RmCommon->filterIssetField($params, 'offset', false, false, array(
				'type' => 'trailing_slash',
			));
			$options['offset'] = $offset;
		}

		Configure::write('Rest.token', false);
		$values = $this->User->Property->PropertyVideos->getData('all', $options, array(
			'status' => 'all',
		));

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$value = Common::_callUnset($value, array(
                    'Property',
                ));
                
				$id = $this->RmCommon->filterEmptyField($value, 'PropertyVideos', 'property_id');
				$value = $this->User->Property->getMerge($value, $id);
				$value = $this->_callDataProperty($value);
				$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');

				$values[$key] = $value;
			}
		}

		if( isset($offset) ) {
			$this->set('offset', $offset+$limit);
		}

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($values, 'manual');
	}

	function property_leads(){
		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$passkey = $this->RmCommon->filterEmptyField($this->params, 'named', 'passkey');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'rumahku-properti-lead-api',
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
				'device' => 'rumahku-properti-lead-api',
				'passkey' => $passkey,
				'lastupdated' => $lastupdated,
				'is_devices' => 'prime',
			);

			if( is_numeric($offset) ) {
				$pass['offset'] = $offset;
			}
			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'properties',
				'action' => 'property_leads',
				'?' => $pass,
				'ext' => 'json',
				'admin' => false,
				'api' => true,
			));

			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$datas = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$data_offset = $this->RmCommon->filterEmptyField($datas, 'offset');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');

			if(!empty($datas)){
				$datas = $this->RmCommon->dataConverter($datas, array(
					'unset' => array(
						'offset'
					),
				));

				foreach ($datas as $key => $data) {
					$modified = $this->RmCommon->filterEmptyField($data, 'PropertyLead', 'modified');
					$data = $this->RmProperty->BeforeSavePropertyView($data, 'PropertyLead');
					$mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');
					$default_msg = sprintf('menyimpan data property Lead dengan properti ID %s', $mls_id);

					$flag = $this->User->Property->PropertyLead->doSave($data);

					if($flag){
						$msg = __('Berhasil %s', $default_msg);
					}else{
						$msg = __('Gagal %s', $default_msg);
					}

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

		}else{
			echo __('Data tidak tersedia');
		}
		die();
	}

	function property_views(){
		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$passkey = $this->RmCommon->filterEmptyField($this->params, 'named', 'passkey');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'rumahku-properti-view-api',
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
				'device' => 'rumahku-properti-view-api',
				'passkey' => $passkey,
				'lastupdated' => $lastupdated,
				'is_devices' => 'prime',
			);

			if( is_numeric($offset) ) {
				$pass['offset'] = $offset;
			}
			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'properties',
				'action' => 'property_views',
				'?' => $pass,
				'ext' => 'json',
				'admin' => false,
				'api' => true,
			));

			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$datas = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$data_offset = $this->RmCommon->filterEmptyField($datas, 'offset');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');

			if(!empty($datas)){
				$datas = $this->RmCommon->dataConverter($datas, array(
					'unset' => array(
						'offset'
					),
				));

				foreach ($datas as $key => $data) {
					$modified = $this->RmCommon->filterEmptyField($data, 'PropertyView', 'modified');
					$data = $this->RmProperty->BeforeSavePropertyView($data);
					$mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');
					$default_msg = sprintf('menyimpan data property view dengan properti ID %s', $mls_id);

					$flag = $this->User->Property->PropertyView->doSave($data);

					if($flag){
						$msg = __('Berhasil %s', $default_msg);
					}else{
						$msg = __('Gagal %s', $default_msg);
					}

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
		}else{
			echo __('Data tidak tersedia');
		}
		die();
	}

	public function arebi_properties($arebi_id){
		$this->loadModel('Property');

		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$limit = $this->RmCommon->filterEmptyField($params, 'limit', false, 10);
		
		$agent_id = $this->User->getData('list', array(
			'conditions' => array(
				'User.group_id' => 2,
				'UserConfig.arebi_id' => $arebi_id,
			),
			'contain' => array(
				'UserConfig'
			)
		), array(
			'status' => 'active',
			'company' => false
		));

		if($limit > 30){
			$limit = 30;
		}

		$options = $this->User->Property->_callRefineParams($this->params, array(
			'conditions' => array(
				'Property.modified NOT' => NULL,
				'Property.user_id' => $agent_id
			),
			'limit' => $limit,
			'order' => array(
				'Property.modified' => 'DESC',
				'Property.id' => 'DESC',
			),
		));

		Configure::write('Rest.token', false);
		$this->paginate = $this->User->Property->getData('paginate', $options, array(
			'status' => 'active',
			'company' => false,
		));

		$properties = $this->paginate('Property');

		if(!empty($properties)){
			foreach ($properties as $key => $value) {
				$currency_id = $this->RmCommon->filterEmptyField($value, 'Property', 'currency_id');
				$period_id = $this->RmCommon->filterEmptyField($value, 'Property', 'period_id');
				$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
				$property_type_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id');

				$value = $this->_callDataProperty($value);

				$value = $this->Property->getMergeDefault($value);
				
				$value['ParentInfo'] = $this->User->getInfoParent($user_id);

				$properties[$key] = $value;
			}
		}

		$properties = $this->RmProperty->arebiFormat($properties);

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($properties, 'manual');

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	public function api_data_listing(){
		$params		= $this->params->query;
		$extension	= $this->params->ext;

		$limit			= $this->RmCommon->filterIssetField($params, 'limit', false, false);
		$format			= $this->RmCommon->filterIssetField($params, 'format', false, true);
		$lastupdated	= $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));

		$this->loadModel('UserIntegratedSyncProperty');

		$syncProperties = $this->UserIntegratedSyncProperty->getData('all', array(
			'limit'			=> $limit, 
			'conditions'	=> array(
				'UserIntegratedSyncProperty.do_sync'		=> 1, 
				'UserIntegratedSyncProperty.is_generated'	=> 0, 
				'UserIntegratedSyncProperty.is_sent'		=> 0, 
			), 
		));

		$propertyID	= Hash::extract($syncProperties, '{n}.UserIntegratedSyncProperty.property_id');
		$properties	= array();

		if($propertyID){
			$this->loadModel('Property');

			$options = array(
				'limit' => $limit, 
				'type_merge' => 'regular_merge', 
				'order' => array(
					'Property.modified' => 'ASC',
				),
				'conditions' => array(
					'Property.id' => $propertyID, 
				),
			);

			if( !empty($lastupdated) ) {
				$options['conditions'][]['OR'] = array(
					'Property.modified >' => $lastupdated,
					'Property.created >' => $lastupdated,
				);
			}

			$contain = array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyFacility',
				'PropertyAsset',
				'PropertySold',
				'User',
			);

			$element = array(
				'mine' => false, 
				'admin_mine' => false,
				'company' => false,
				'other_contain' => true,
				'contain_data' => $contain,
			);

			$properties = $this->RmProperty->_callBeforeViewProperties($options, $element);

			if(!empty($properties)){
				foreach ($properties as $key => $value) {
					$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
					$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
					$property_type_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id');

					$value = $this->Property->PropertyMedias->getMerge($value, $property_id, 'all');
					$value = $this->Property->PropertyVideos->getMerge($value, $property_id, 'all');

					$value = $this->User->UserProfile->getMerge($value, $user_id, true);
					$value['ParentInfo'] = $this->User->getInfoParent($user_id);

					$properties[$key] = $value;

					if(empty($format)){
						$logPath	= sprintf('/UserIntegratedSyncProperty[property_id=%s]', $property_id);
						$logValue	= Set::extract($logPath, $syncProperties);
						$logValue	= Hash::sort($logValue, '{n}.UserIntegratedSyncProperty.id', 'DESC');
						$logValue	= array_shift($logValue);

						$properties[$key] = array_merge($properties[$key], $logValue);
					}
				}

				if($format){
					$properties = $this->RmApiProperty->formatListing($properties);
				}
			}
		}

		if($extension == 'json'){
			Configure::write('Rest.token', true);
			$this->RmCommon->_callDataForAPI($properties, 'manual');	
		}
		else{
			$this->autoRender = false;
			return $properties;
		}
	}

	public function api_master_data($modelKey = false, $getType = 'list'){
		$result	= array();
		$empty	= Common::hashEmptyField($this->params->query, 'empty');

		if($modelKey){
			$model		= null;
			$getType	= in_array($getType, array('all', 'list')) ? $getType : 'list';

			switch($modelKey){
				case 'certificate' : 
					$model = $this->User->Property->Certificate;
				break;
				case 'lot_unit' : 
					$model = $this->User->Property->PropertyAsset->LotUnit;
				break;
				case 'property_condition' : 
					$model = $this->User->Property->PropertyAsset->PropertyCondition;
				break;
				case 'property_direction' : 
					$model = $this->User->Property->PropertyAsset->PropertyDirection;
				break;
				case 'view_site' : 
					$model = $this->User->Property->PropertyAsset->ViewSite;
				break;
			}

			if($model){
				$result = $model->getData($getType, array(
					'cache' => sprintf('%s.%s', $model->alias, $getType), 
				));

				if($getType == 'list' && $result){
					$temp = array();

					if($empty){
						$empty	= is_numeric($empty) ? 'Pilih' : $empty;
						$temp[]	= array(
							'value'		=> '', 
							'text'		=> $empty, 
						);
					}

					foreach($result as $value => $text){
						$temp[] = array('value' => $value, 'text' => $text);
					}

					$result = $temp;
				}
			}
		}

		$this->autoRender = false;
		$this->autoLayout = false;

		echo(json_encode($result));
		exit;
	}
}
?>