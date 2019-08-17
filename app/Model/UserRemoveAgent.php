<?php
class UserRemoveAgent extends AppModel {
	var $name = 'UserRemoveAgent';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'agent_id',
		),
	);

	var $validate = array(
		'agent_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'agent_pic_email' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email agen yang Anda masukkan tidak valid.',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'reason_principle' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Alasan menghapus harap diisi'
			),
		),
		'reason_admin_decline' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Alasan menghapus harap diisi'
			),
		),
	);

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->virtualFields = array(
	        'status_description' => "
	            CASE 
	                WHEN active = 0 AND status = 1 THEN 'Pending'
	                WHEN status = 1 AND status = 1 THEN 'Approved'
	                WHEN status = 0 AND status = 0 THEN 'Rejected'
	                ELSE 'N/A'
	            END
	        ",
	    );
	}

	public function validateUserEmail($data = array()){
		if($data){
			$targetUserEmail = array_shift($data);

			if($targetUserEmail){
			//	id agent yang di hapus
				$sourceUserID = Common::hashEmptyField($this->data, 'UserRemoveAgent.id');
				$sourceUserID = Common::hashEmptyField($this->data, 'UserRemoveAgent.agent_id', $sourceUserID);

			//	agent penerima transfer property
				$targetUser = $this->User->getData('first', array(
					'conditions'=> array(
						'User.group_id'	=> Configure::read('__Site.Role.company_agent'),
						'User.email'	=> $targetUserEmail,
					//	'NOT' => array(
					//		'User.id' => $deleted_agent_email,
					//	), 
					),
				), array(
					'status'	=> 'semi-active',
					'company'	=> true,
					'admin'		=> true,
				));

				$targetUserID = Common::hashEmptyField($targetUser, 'User.id');

				if($sourceUserID && $targetUserID){
					$sourceUserID = (array) $sourceUserID;

					if(in_array($targetUserID, $sourceUserID)){
						return __('Data tidak dapat dialihkan ke agen yang sama.');
					}
					else{
						return true;
					}
				}
			}
		}

		return false;
	}
	
	function getData( $find = 'all', $options = array() ){
		$default_options = array(
			'conditions'=> array(
				'UserRemoveAgent.status' => 1, 
			),
			'order'=> array(
				'UserRemoveAgent.created' => 'DESC',
			),
		);

		if(!empty($options)){
			$default_options = array_merge($default_options, $options);
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge( $data, $agent_id, $options = false) {
		$default_options = array(
			'conditions' => array(
				'UserRemoveAgent.agent_id' => $agent_id,
			),
		);

		if(!empty($options)){
			$default_options = array_merge_recursive($default_options, $options);
		}

		if( empty($data['UserRemoveAgent']) ) {
			$userRemoveAgent = $this->getData('first', $default_options);

			if( !empty($userRemoveAgent) ) {
				$data = array_merge($data, $userRemoveAgent);
			}
		}

		return $data;
	}

	public function doSave($principleID = null, $data = array()){
		$principleID	= (int) $principleID;
		$data			= (array) $data;

		$status		= 'error';
		$message	= 'Data yang Anda masukkan tidak valid';
		$result		= array('data' => $data);
		$userID		= array();

		if($data){
		//	"id" ini isinya target user yang mau di delete, kenapa fieldnya numpang di "id"????? tanya yang buat
			$userID = Common::hashEmptyField($data, 'UserRemoveAgent.id', array());
			$userID = (array) $userID;

			if($userID){
				$principleID	= Common::hashEmptyField($data, 'UserRemoveAgent.principle_id', $principleID);
				$reason			= Common::hashEmptyField($data, 'UserRemoveAgent.reason_principle', '');

				$data = Hash::insert($data, 'UserRemoveAgent.principle_id', $principleID);
				$data = Hash::insert($data, 'UserRemoveAgent.reason_principle', $reason);

				$flag = $this->saveAll($data, array(
					'validate' => 'only', 
				));

				if($flag){
				//	karna numpang harus langsung di unset biat ga jadi "edit" (ini data create semua)
					$data = Hash::remove($data, 'UserRemoveAgent.id');

				//	gather all data as one array
					$saveData		= array();
					$sourceUserID	= array();
					$historyData	= array();
					$currentDate	= date('Y-m-d H:i:s');

					foreach($userID as $sourceID){
					//	data agent yang mau di-delete
						$sourceUser = $this->User->getData('first', array(
							'contain'		=> array('UserProfile', 'Group'), 
							'conditions'	=> array(
								'User.id' => $sourceID,
							),
						), array(
							'status' => array('active', 'non-active'),
						));

						$sourceGroupID = Common::hashEmptyField($sourceUser, 'User.group_id', 0);

					//	b:append =====================================================================

						$saveData[]		= Hash::insert($data, 'UserRemoveAgent.agent_id', $sourceID);
						$historyData[]	= array(
							'UserHistory' => array(
								'principle_id'	=> $principleID, 
								'group_id'		=> $sourceGroupID, 
								'user_id'		=> $sourceID, 
								'type'			=> 'resign', 
								'old_data'		=> serialize($sourceUser), 
							), 
						);

						if($sourceGroupID == 2){
						//	kalo groupnya agent, tarik paksa property dan client nya
							$sourceUserID[] = $sourceID;
						}

					//	e:append =====================================================================
					}

				//	actual save
					$flag	= $this->saveAll($saveData);
					$status	= 'success';

				//	b:transfer ===================================================================

					if($sourceUserID){
						$agentEmail	= Hash::get($data, 'UserRemoveAgent.agent_email');
						$targetUser	= $this->User->getData('first', array(
							'conditions' => array(
								'User.email' => $agentEmail,
							),
						), array(
							'status'	=> 'semi-active',
							'company'	=> true,
							'admin'		=> true,
						));

						if($targetUser){
							$targetID = Common::hashEmptyField($targetUser, 'User.id');

							$this->User->Property->updateAll(array(
								'Property.user_id'	=> $targetID,
								'Property.modified'	=> "'".$currentDate."'",
							), array(
								'Property.user_id' => $sourceUserID,
							));

							$this->User->UserClient->updateAll(array(
								'UserClient.agent_id' => $targetID,
								'UserClient.modified' => "'".$currentDate."'",
							), array(
								'UserClient.agent_id' => $sourceUserID,
							));

							$this->User->UserClientRelation->updateAll(array(
								'UserClientRelation.agent_id' => $targetID,
								'UserClientRelation.modified' => "'".$currentDate."'",
							), array(
								'UserClientRelation.agent_id' => $sourceUserID,
							));
						}
					}

				//	e:transfer ===================================================================

				//	b:disable ====================================================================
				//	note : kalo ini ga musti group agent, semua yang diselect kena

					$this->User->updateAll(array(
					//	value asli
					//	'User.not_login_prime'	=> 1,
					//	'User.deleted'			=> 1,
					//	'User.modified'			=> "'".$currentDate."'",

					//	value baru (convert jadi agent independen)
						'User.parent_id'		=> 0, 
						'User.group_id'			=> 1, 
						'User.not_login_prime'	=> 1,
						'User.deleted'			=> 0,
						'User.modified'			=> "'".$currentDate."'",
					), array(
						'User.id' => $userID,
					));

				//	e:disable ====================================================================

				//	b:save history log ===========================================================

					$this->User->UserHistory->doSave($historyData);

				//	e:save history log ===========================================================
				}

				$message = __('%s menghapus data', $flag ? 'Berhasil' : 'Gagal');
			}
		}

		$result = array_merge($result, array(
			'status'	=> $status, 
			'msg'		=> $message, 
			'Log'		=> array(
				'activity'	=> $message,
				'old_data'	=> $data,
				'error'		=> $status == 'error', 
			),
		));

		if($userID){
			$result = Hash::insert($result, 'id', $userID);
		}

		if($this->validationErrors){
			$result = Hash::insert($result, 'validationErrors', $this->validationErrors);
		}

		return $result;
	}

/*
	function doSave( $principle_id, $data ) {
		
		$result = false;
		$default_msg = __('menghapus user');

		if ( isset($data['UserRemoveAgent']['reason_principle']) ) {

			$data['UserRemoveAgent']['reason_principle'] = trim($data['UserRemoveAgent']['reason_principle']);
			$data['UserRemoveAgent']['principle_id'] = $principle_id;

			$this->set($data);

			if( $this->validates() ) {
				$dateNow = date('Y-m-d H:i:s');
				$list_deleted_id = implode(",", $data['UserRemoveAgent']['id']);
				
				$propertyModel = ClassRegistry::init('Property');
				$agent_email = !empty($data['UserRemoveAgent']['agent_email'])?$data['UserRemoveAgent']['agent_email']:false;
				$agent = $this->User->getData('first', array(
					'conditions' => array(
						'User.email' => $agent_email,
					),
				), array(
					'status' => 'semi-active',
					'company' => true,
					'admin' => true,
				));
				$agent_id = !empty($agent['User']['id'])?$agent['User']['id']:false;

				$values = $data['UserRemoveAgent']['id'];
				$data['UserRemoveAgent']['id'] = false;

				$collect_id = array();
				foreach( $values as $value ) {
					$id = $value;

					$user = $this->User->getData('first', array(
						'conditions' => array(
							'User.id' => $id,
						),
					), array(
						'status' => array(
							'active',
							'non-active',
						),
					));
					$group_id = Common::hashEmptyField($user, 'User.group_id');

					$collect_id[] = $data['UserRemoveAgent']['agent_id'] = $id;
					$this->save($data);

					if($group_id == '2'){
						$propertyModel->updateAll(
							array(
								'Property.user_id' => $agent_id,
								'Property.modified' => "'".$dateNow."'",
							), 
							array(
								'Property.user_id' => $id,
							)
						);

						$this->User->UserClient->updateAll(
							array(
								'UserClient.agent_id' => $agent_id,
								'UserClient.modified' => "'".$dateNow."'",
							), 
							array(
								'UserClient.agent_id' => $id,
							)
						);

						$this->User->UserClientRelation->updateAll(
							array(
								'UserClientRelation.agent_id' => $agent_id,
								'UserClientRelation.modified' => "'".$dateNow."'",
							), 
							array(
								'UserClientRelation.agent_id' => $id,
							)
						);
					}

					$this->User->updateAll(
						array(
							'User.deleted' => 1,
							'User.modified' => "'".$dateNow."'",
							'User.not_login_prime' => 1,
						), 
						array(
							'User.id' => $id,
						)
					);

				}
				
				$msg = sprintf(__('Berhasil %s'), $default_msg);
				$log_msg = sprintf('%s #%s', $msg, $list_deleted_id);

				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'id' => $collect_id,
					'Log' => array(
						'activity' => $log_msg,
						'old_data' => $data,
						'document_id' => $list_deleted_id,
					),
				);
			} else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'validationErrors' => $this->validationErrors,
					'Log' => array(
						'activity' => $msg,
						'old_data' => $data,
						'error' => 1,
					),
				);
			}
		}

		return $result;
	}
*/
}
?>