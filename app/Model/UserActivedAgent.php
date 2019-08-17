<?php
class UserActivedAgent extends AppModel {
	var $name = 'UserActivedAgent';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'agent_decilne_id',
		),
	);

	var $hasMany = array(
		'UserActivedAgentDetail' => array(
			'className' => 'UserActivedAgentDetail',
			'foreignKey' => 'user_actived_agent_id',
		),
	);

	var $validate = array(
		'agent_decilne_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'user harap dipilih',
			),
		),
		'agent_assign_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Agen harap diisi',
			),
		),
		'agent_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'agent_not_self' => array(
				'rule' => array('validateEmailNotSelf'),
				'message' => 'Email yang anda masukkan sama dengan agen yang dinonaktifkan.',
			),
			'agent_pic_email' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email agen yang Anda masukkan tidak terdaftar.',
			),
			'agent_pic' => array(
				'rule' => array('validateUserEmailNotActived'),
				'message' => 'Email agen yang Anda masukkan sedang tidak aktif.',
			),
			'agent_not_company' => array(
				'rule' => array('validateNotCompany'),
				'message' => 'Email agen yang Anda masukkan berbeda perusahaan.',
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
		'reason' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Alasan non-aktifkan harap diisi'
			),
		),
		'rollback_reason' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Alasan diaktifkan harap diisi'
			),
		),
	);

	function validateNotCompany(){
		$data = $this->data;
		$agent_assign_id = Common::hashEmptyField($data, 'UserActivedAgent.agent_assign_id');
		$parent_id = Common::hashEmptyField($data, 'UserActivedAgent.parent_id');

		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $agent_assign_id,
			),
		), array(
			'status' => 'active',
		));

		$param_parent_id = Common::hashEmptyField($user, 'User.parent_id');

		if($parent_id === $param_parent_id){
			return true;
		}

		return false;
	}

	function validateUserEmailNotActived($data){
		if( !empty($data) ) {
			$email = false;
			$deleted_agent_email = false;

			if( !empty($data['agent_email']) ) {
				$email = $data['agent_email'];
			}

			if( !empty($this->data['UserActivedAgent']['agent_decilne_id']) ) {
				$deleted_agent_email = $this->data['UserActivedAgent']['agent_decilne_id'];
			}

			$optionUser = array(
				'conditions'=> array(
					'User.email' => $email,
					'NOT' => array(
						'User.id' => $deleted_agent_email,
					)
				),
			);


			$user = $this->User->getData('first', $optionUser, array(
				'status' => 'non-active'
			));

			if(!empty($user)){
				return false;
			}
			return true;
		}
	}

	function validateEmailNotSelf(){
		$data = $this->data;

		if(!empty($data)){
			$email = Common::hashEmptyField($data, 'UserActivedAgent.agent_email');
			$agent_decilne_id = Common::hashEmptyField($data, 'UserActivedAgent.agent_decilne_id');

			$agent = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $email,
					'User.group_id' => '2'
				),
			));

			$agent_id = Common::hashEmptyField($agent, 'User.id');

			if($agent_decilne_id === $agent_id){
				return false;
			}
			return true;
		}
	}

	function validateUserEmail($data) {

		if( !empty($data) ) {
			$email = false;
			$deleted_agent_email = false;
			if( !empty($data['agent_email']) ) {
				$email = $data['agent_email'];
			}

			if( !empty($this->data['UserActivedAgent']['agent_decilne_id']) ) {
				$deleted_agent_email = $this->data['UserActivedAgent']['agent_decilne_id'];
			}

			$optionUser = array(
				'conditions'=> array(
					'User.email' => $email,
					'NOT' => array(
						'User.id' => $deleted_agent_email,
					)
				),
			);

			$user = $this->User->find('first', $optionUser);

			if(!empty($user)){
				return true;
			}
		}		
		return false;
	}

	function actionNotActivedUser($data, $id){
		// $value = $this->find('first', array(
		// 	'conditions' => array(
		// 		'UserActivedAgent.id' => $id,
		// 		'UserActivedAgent.document_status' => 'assign'
		// 	),
		// ));

		// $value = $this->getMergeList($value, array(
		// 	'contain' => array(
		// 		'UserActivedAgentDetail',
		// 	),
		// ));

		if(!empty($data)){
			$dateNow = date('Y-m-d H:i:s');
			$agent_decilne_id = Common::hashEmptyField($data, 'UserActivedAgent.agent_decilne_id');
			$agent_assign_id = Common::hashEmptyField($data, 'UserActivedAgent.agent_assign_id');

			// not actived user
			$this->User->updateAll(
				array(
					'User.status' => 0,
					'User.active' => 0,
					'User.not_login_prime' => 1,
					'User.modified' => "'".$dateNow."'",
				),
				array(
					'User.id' => $agent_decilne_id,
				)
			);

			// change data properti, client, client_relations

			if($agent_assign_id){
				$data_arr = Configure::read('__Site.dataAgent');
				
				foreach ($data_arr as $model => $dataModel) {
					$field = Common::hashEmptyField($dataModel, 'field');
					$field_count = Common::hashEmptyField($dataModel, 'field_count');
					$data_count = Common::hashEmptyField($data, sprintf('UserActivedAgent.%s', $field_count));

					$val = Hash::Extract($data, sprintf('UserActivedAgentDetail.{n}.UserActivedAgentDetail[type=%s]', $model));
					$val_list = !empty($val) ? Hash::combine($val, '{n}.document_id', '{n}.document_id') : array();

					$this->User->{$model}->updateAll(
						array(
							sprintf('%s.%s', $model, $field) => $agent_assign_id,
							sprintf('%s.modified', $model) => "'".$dateNow."'",
						),
						array(
							sprintf('%s.%s', $model, $field) => $agent_decilne_id,
							sprintf('%s.id', $model) => $val_list,
						)
					);
				}
			}
		}
	}

	function doSave($data = false, $value = false){
		$result = $notification = false;
		
		if(!empty($data['UserActivedAgent'])){
			$agent_decilne_id = Common::hashEmptyField($data, 'UserActivedAgent.agent_decilne_id');
			$agent_assign_id = Common::hashEmptyField($data, 'UserActivedAgent.agent_assign_id');
			$property_count = Common::hashEmptyField($data, 'UserActivedAgent.property_count');
			$client_count = Common::hashEmptyField($data, 'UserActivedAgent.client_count');

			// group user apabila bukan agent remove validator assign_id
			$group_id = Common::HashemptyField($value, 'User.group_id');
			if($group_id <> 2){
				$this->validator()->remove('agent_assign_id');
			}
			//

			## decilne && assign get data for email
			$decline = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $agent_decilne_id,
				),
			), array(
				'status' => array(
					'non-active',
					'active',
				),
			));

			$decline = $this->User->getMergeList($decline, array(
				'contain' => array(
					'Group',
					'UserCompany' => array(
						'foreignKey' => 'parent_id',
						'primaryKey' => 'user_id',
					),
				),
			));

			$assign = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $agent_assign_id,
				),
			), array(
				'status' => array(
					'non-active',
					'active',
				),
			));

			$name_decline = Common::hashEmptyField($decline, 'User.full_name');
			$email_decline = Common::hashEmptyField($decline, 'User.email');

			$name_assign = Common::hashEmptyField($assign, 'User.full_name');
			$email_assign = Common::hashEmptyField($assign, 'User.email');
			##

			$default_msg =  __('non-aktifkan %s', $name_decline);
			$flag = $this->saveAll($data, array(
				'validate' => 'only',
			));

			if($flag){
				$this->saveAll($data);
				$id = $this->id;

				// get data properti
				$data = $this->UserActivedAgentDetail->getMerge($data, $id, 'UserActivedAgentDetail.user_actived_agent_id', 'all', array(
					'type' => 'Property',
				));

				if(!empty($data['UserActivedAgentDetail'])){
					foreach ($data['UserActivedAgentDetail'] as $key => $detail) {
						$document_id = Common::hashEmptyField($detail, 'UserActivedAgentDetail.document_id');
						$type = Common::hashEmptyField($detail, 'UserActivedAgentDetail.type');

						if($type == 'Property'){
							$is_property = true;
							$property = $this->UserActivedAgentDetail->Property->getData('first', array(
								'conditions' => array(
									'Property.id' => $document_id,
								),
							));

							$property_id = Common::hashEmptyField($property, 'Property.id');
							$property = $this->UserActivedAgentDetail->Property->PropertyAddress->getMerge($property, $property_id);

							$detail = array_merge($detail, $property);

						
						} else if($type == 'UserClient') {
							$client = $this->UserActivedAgentDetail->Property->User->UserClient->getData('first', array(
								'conditions' => array(
									'UserClient.id' => $document_id,
								),
							));
							$user_id = Common::hashEmptyField($client, 'UserClient.user_id');
							$client = $this->UserActivedAgentDetail->Property->User->getMerge($client, $user_id);
							$detail = array_merge($detail, $client);
						}

						$data['UserActivedAgentDetail'][$key] = $detail;
					}
				}
				//

				$data['Decline'] = $decline;
				$data['Assign'] = $assign;

				$this->actionNotActivedUser($data, $id);

				$text = $this->getText($property_count, $client_count, false);
				$sendEmail = array(
					array(
                        'to_name' => $name_decline,
                        'to_email' => $email_decline,
                        'subject' => __('Akun anda telah di non-aktifkan oleh admin'),
                        'template' => 'not_active',
                        'data' => $data,
					),
				);

				if($text && $email_assign){
					$sendEmail[] = array(
						'to_name' => $name_assign,
                        'to_email' => $email_assign,
                        'subject' => __('Anda telah mendapatkan data %s dari agen %s', $text, $name_decline),
                        'template' => 'assign_property',
                        'data' => array_merge($data, array(
                        	'id' => $id,
                        )),
					);

					$notification = array(
						'user_id' => $agent_assign_id,
                        'name' => __('Anda telah mendapatkan data %s dari agen %s', $text, $name_decline),
                        'link' => array(
                            'controller' => 'properties',
                            'action' => 'index',
                            'status' => 'assign',
                            'document_id' => $id,
                            'admin' => true
                        ),
					);
				}

				$msg = sprintf(__('Berhasil %s'), $default_msg);
				$log_msg = sprintf('%s #%s', $msg, $agent_decilne_id);

				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $log_msg,
						'old_data' => $data,
						'document_id' => $agent_decilne_id,
					),
					'id' => $id,
					'SendEmail' => $sendEmail,
					'Notification' => $notification,
				);
			} else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'validationErrors' => $this->validationErrors,
				);		
			}
		} 
		return $result;
	}

	function rollbackUser($data, $value){
		$dateNow = date('Y-m-d H:i:s');
		$data_arr = Configure::Read('__Site.dataAgent');

		// is rollback
		$is_rollback = Common::hashEmptyField($data, 'UserActivedAgent.is_rollback');

		// user assign, and decline
		$agent_decilne_id = Common::hashEmptyField($value, 'UserActivedAgent.agent_decilne_id');
		$agent_assign_id = Common::hashEmptyField($value, 'UserActivedAgent.agent_assign_id');

		$agentDetail = Common::hashEmptyField($value, 'UserActivedAgentDetail');
		
		// roll-back properti
		$detailProperties = Hash::Extract($agentDetail, '{n}.UserActivedAgentDetail[type=Property]');

		if(!empty($is_rollback)){
			if($detailProperties){

				$val_list = !empty($detailProperties) ? Hash::combine($detailProperties, '{n}.document_id', '{n}.document_id') : array();

				$this->User->Property->updateAll(array(
					'Property.user_id' => $agent_decilne_id,
					'Property.modified' => "'".$dateNow."'",
				), array(
					'Property.user_id' => $agent_assign_id,
					'Property.id' => $val_list,
				));
			}
			// 

			// roll-back klien
			$models = array('UserClient', 'UserClientRelation');

			foreach ($models as $key => $model) {
				$value_arr = Hash::Extract($agentDetail, sprintf('{n}.UserActivedAgentDetail[type=%s]', $model));
				$field = Common::hashEmptyField($data_arr, sprintf('%s.field', $model));

				if(!empty($value_arr)){
					$val_list = !empty($value_arr) ? Hash::combine($value_arr, '{n}.document_id', '{n}.document_id') : array();

					$this->User->{$model}->updateAll(array(
						sprintf('%s.%s', $model, $field) => $agent_decilne_id,
						sprintf('%s.modified', $model) => "'".$dateNow."'",
					), array(
						sprintf('%s.%s', $model, $field) => $agent_assign_id,
						sprintf('%s.id', $model) => $val_list,
					));
				}
			}
		}
	}

	function getText($property_count = false, $client_count = false, $addText  =true){
		$text = false;
        if(in_array(true, array($property_count, $client_count))){

        	if($addText){
            	$text = ', berikut data';
        	}

            if($property_count){
                $text .= ' properti';                
            }

            if($client_count){
                if($property_count){
                    $text .= '  &';
                }
                $text .= ' klien'; 
            }
        }
        return $text;
	}

	function rollbackNotif($data, $value){
		$decline_id = Common::hashEmptyField($value, 'Decline.id');
		$name_decline = Common::hashEmptyField($value, 'Decline.full_name');
		$email_decline = Common::hashEmptyField($value, 'Decline.email');
		$assign_id = Common::hashEmptyField($value, 'Assign.id');
		$name_assign = Common::hashEmptyField($value, 'Assign.full_name');
		$email_assign = Common::hashEmptyField($value, 'Assign.email');

		// is roll-back
		$is_rollback = Common::hashEmptyField($data, 'UserActivedAgent.is_rollback');

		$property_count = Common::hashEmptyField($data, 'UserActivedAgent.property_count');
		$client_count = Common::hashEmptyField($data, 'UserActivedAgent.client_count');
		$rollback_reason = Common::hashEmptyField($data, 'UserActivedAgent.rollback_reason');

		$text  = $this->getText($property_count, $client_count);
		$urlrollBack = array(
			'controller' => 'properties',
			'action' => 'index',
			'admin' => true,
		);

		$sendEmail = array(
			array(
                'to_name' => $name_decline,
                'to_email' => $email_decline,
                'subject' => __('Akun Anda telah diaktifkan kembali'),
                'template' => 'active',
                'data' => array_merge($value, array(
                	'text' => $text,
                	'rollback_reason' => $rollback_reason,
                	'is_rollback' => $is_rollback,
                )),
			),
			// array(
   //              'to_name' => $name_assign,
   //              'to_email' => $email_assign,
   //              'subject' => __('%s telah diaktifkan kembali', $name_decline),
   //              'template' => 'rollback_assign',
   //              'data' => $value,
			// ),
		);

		$notification = array(
			array(
				'Notification' => array(
					'user_id' => $decline_id,
		            'name' => __('Akun Anda telah diaktifkan kembali'),
		            'link' => $urlrollBack,
				),
			),
			// array(
			// 	'Notification' => array(
			// 		'user_id' => $assign_id,
		 //            'name' => __('%s telah diaktifkan kembali', $name_decline),
		 //            'link' => $urlrollBack,
			// 	),
			// ),
		);

		if($is_rollback){
			$sendEmail[] = array(
				'to_name' => $name_assign,
                'to_email' => $email_assign,
                'subject' => __('%s telah diaktifkan kembali', $name_decline),
                'template' => 'rollback_assign',
                'data' => $value,
			);

			$notification[] = array(
				'Notification' => array(
					'user_id' => $assign_id,
		            'name' => __('%s telah diaktifkan kembali', $name_decline),
		            'link' => $urlrollBack,
				),
			);

		}

		return array(
			'sendEmail' => $sendEmail,
			'notification' => $notification,
		);
	}

	function doSaveActived($data, $user){
		$dateNow = date('Y-m-d H:i:s');
		$user_id = Common::hashEmptyField($user, 'User.id');

		$value = $this->find('first', array(
			'conditions' => array(
				'UserActivedAgent.agent_decilne_id' => $user_id,
				'UserActivedAgent.document_status' => 'assign',
			),
		));

		$UserActivedAgentId = Common::hashEmptyField($value, 'UserActivedAgent.id');

		$data['UserActivedAgent']['document_status'] = 'role-back';
		$data['UserActivedAgent']['id'] = $UserActivedAgentId;
		
		$flag = $this->User->updateAll(array(
			'User.active' => 1,
			'User.status' => 1,
			'User.not_login_prime' => 0,
			'User.modified' => "'".$dateNow."'",
		), array(
			'User.id' => $user_id,
		));

		$flagAgent = $this->saveAll($data, array(
			'validate' => 'only',
		));

		if($flag && $flagAgent ){
			$sendEmail = $notification = false;

			if($value){
				$value = $this->getMergeList($value, array(
					'contain' => array(
						'UserActivedAgentDetail',
						'Decline' => array(
							'elements' => array(
								'status' => array(
									'active',
									'non-active',
								),
							),
							'uses' => 'User',
							'contain' => array(
								'Group',
								'UserCompany' => array(
									'primaryKey' => 'user_id',
									'foreignKey' => 'parent_id',
								),
							),
						),
						'Assign' => array(
							'elements' => array(
								'status' => array(
									'active',
									'non-active',
								),
							),
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'agent_assign_id',
						),
					),
				));
				$decline_name = Common::hashEmptyField($value, 'Decline.full_name');
				$default_msg = __('aktifkan %s', $decline_name);

				if(!empty($value['UserActivedAgentDetail']) && !empty($value['Assign'])){
					foreach ($value['UserActivedAgentDetail'] as $key => $detail) {
						$document_id = Common::hashEmptyField($detail, 'UserActivedAgentDetail.document_id');
						$type = Common::hashEmptyField($detail, 'UserActivedAgentDetail.type');

						if($type == 'Property'){
							$is_property = true;
							$property = $this->UserActivedAgentDetail->Property->getData('first', array(
								'conditions' => array(
									'Property.id' => $document_id,
								),
							));

							$property_id = Common::hashEmptyField($property, 'Property.id');
							$property = $this->UserActivedAgentDetail->Property->PropertyAddress->getMerge($property, $property_id);

							$detail = array_merge($detail, $property);

						
						} else if($type == 'UserClient') {
							$client = $this->UserActivedAgentDetail->Property->User->UserClient->getData('first', array(
								'conditions' => array(
									'UserClient.id' => $document_id,
								),
							));
							$user_id = Common::hashEmptyField($client, 'UserClient.user_id');
							$client = $this->UserActivedAgentDetail->Property->User->getMerge($client, $user_id);
							$detail = array_merge($detail, $client);
						}

						$value['UserActivedAgentDetail'][$key] = $detail;
					}
				}

				$this->rollbackUser($data, $value);
				// get email & notif
				$notifEmail = $this->rollbackNotif($data, $value);
				$sendEmail = Common::hashEmptyField($notifEmail, 'sendEmail');
				$notification = Common::hashEmptyField($notifEmail, 'notification');

				// set data for save roll-back
				if($this->saveAll($data)){
					$this->UserActivedAgentDetail->updateAll(array(
						'UserActivedAgentDetail.document_status' => "'roll-back'",
						'UserActivedAgentDetail.modified' => "'".$dateNow."'",
					), array(
						'UserActivedAgentDetail.user_actived_agent_id' => $UserActivedAgentId,
					));
				}
				// 
			}

			$msg = sprintf(__('Berhasil %s'), $default_msg);
			$log_msg = sprintf('%s #%s', $msg, $user_id);

			$result = array(
				'msg' => $msg,
				'status' => 'success',
				'Log' => array(
					'activity' => $log_msg,
					'old_data' => $data,
					'document_id' => $user_id,
				),
				'SendEmail' => $sendEmail,
				'Notification' => $notification,
			);

		} else {
			$msg = sprintf(__('Gagal %s'), $default_msg);
			$result = array(
				'msg' => $msg,
				'status' => 'error',
				'validationErrors' => $this->validationErrors,
			);	
		}
		return $result;
	}
}
?>