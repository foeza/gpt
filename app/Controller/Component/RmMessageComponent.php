<?php
class RmMessageComponent extends Component {
	var $components = array('Session', 'Auth', 'RmCommon', 'RmUser'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function saveApiDataMigrate($data){
		$this->User = ClassRegistry::init('User');

		$from_email = $this->RmCommon->filterEmptyField($data, 'User', 'email');
		$to_email 	= $this->RmCommon->filterEmptyField($data, 'ToUser', 'email');
		$mls_id 	= $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');

		if(!empty($data)){
			if(isset($data['Property'])){
				unset($data['Property']);
			}

			if(!empty($from_email)){
				$exist_user_data = $this->User->getData('first', array(
		            'conditions' => array(
		                'User.email' => $from_email
		            )
		        ), array(
		            'status' => 'all'
		        ));

		        $data['Message']['from_id'] = $this->RmCommon->filterEmptyField($exist_user_data, 'User', 'id', null);
			}

			if(!empty($to_email)){
				$exist_user_data = $this->User->getData('first', array(
		            'conditions' => array(
		                'User.email' => $to_email
		            )
		        ), array(
		            'status' => 'all'
		        ));

		        $data['Message']['to_id'] = $this->RmCommon->filterEmptyField($exist_user_data, 'User', 'id', null);
			}

			if(!empty($mls_id)){
				$exist_data = $this->User->Property->getData('first', array(
		            'conditions' => array(
		                'Property.mls_id' => $mls_id
		            )
		        ), array(
		            'status' => 'all',
                    'skip_is_sales' => true,
		        ));

		        $data['Message']['property_id'] = $this->RmCommon->filterEmptyField($exist_data, 'Property', 'id', null);
			}

			$property_id = $this->RmCommon->filterEmptyField($data, 'Message', 'property_id', null);
			$to_id = $this->RmCommon->filterEmptyField($data, 'Message', 'to_id', null);

			$data = $this->RmCommon->_callUnset(array(
				'Message' => array(
					'id'
				),
				'ToUser',
				'User',
				'MessageTrash'
			), $data);

			$data['Message']['security_code'] = true;
			
			$data = $this->RmUser->_callMessageBeforeSave($to_id, $property_id, $data);
			
			$this->User->Message->doSend($data);
		}
	}

    function _callRoleCondition ( $value ) {
        $id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
        $group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
        $options = array();

        switch ($group_id) {
            case '4':
                $principle_id = $this->controller->User->getAgents($id, true, 'list', false, array(
                    'role' => 'principle',
                ));
                if(!empty($principle_id)){
                    $options = array(
                        'conditions' => array(
                        	array(
    	                    	'OR' => array(
    	                        	'User.parent_id' => $principle_id,
    	                        	'ToUser.parent_id' => $principle_id,
                        			'ToUser.id' => $principle_id,
    	                		),
                    		),
                        ),
                        'contain' => array(
                            'User',
                            'ToUser',
                        ),
                    );
                }
                
                $this->controller->set('active_menu', 'director');
                break;
            case '3':
                $options = array(
                    'conditions' => array(
                    	array(
	                    	'OR' => array(
	                        	'User.parent_id' => $id,
	                        	'ToUser.parent_id' => $id,
                    			'ToUser.id' => $id,
	                		),
                   	 	),
                    ),
                    'contain' => array(
                        'User',
                        'ToUser',
                    ),
                );
                
                $this->controller->set('active_menu', 'principal');
                break;
            case '2':
                $options = array(
                    'conditions' => array(
                    	array(
	                    	'OR' => array(
	                        	'Message.from_id' => $id,
	                        	'Message.to_id' => $id,
	                		),
                		),
                    ),
                );
                $this->controller->set('active_menu', 'agent');
                break;
        }

        return $options;
    }

    function formatMessage($send_from, $send_to, $data, $admin_id){
    	App::import('Helper', 'Rumahku'); 
		$Rumahku = new RumahkuHelper(new View(null));

		$admin_rku 		= Configure::read('User.Admin.Rumahku');
        $admin_all 		= Configure::read('User.admin');
        $user_id_login 	= Configure::read('User.id');

        $parent_id = Configure::read('Principle.id');

        $agent_company_id = $this->controller->User->getAgents( $parent_id, true );

    	$agent_id = $Rumahku->_callfindAgentCompanyId($send_from, $send_to, $agent_company_id);
        $admin_id = $Rumahku->_callfindAgentCompanyId($send_from, $send_to, $admin_id);

        if(!empty($data)){
        	foreach ($data as $key => $value) {
        		$from_id = $this->RmCommon->filterEmptyField($value, 'Message', 'from_id');
        	
        		if($admin_rku){
                    $for_validation = ($agent_id == $from_id || $from_id == $admin_id) ? true : false;
                }else if($admin_all){
                    $for_validation = ( $agent_id == $from_id || $from_id == $admin_id || $user_id_login == $from_id) ? true : false;
                }else{
                    $for_validation = ($user_id_login == $from_id) ? true : false;
                }

                $position = 'left';
                if( $for_validation ) {
                    $position = 'right';
                }

                $data[$key]['Position'] = $position;
        	}
        }

        return $data;
    }

    function formatListRest($datas){
        if(!empty($datas) && $this->RmCommon->Rest->isActive()){
            foreach ($datas as $key => $data) {
                $value = $this->RmCommon->filterEmptyField($data, 'LastMessage', false, $data);

                $sender = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');
                $senderPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
                $message = $this->RmCommon->filterEmptyField($value, 'Message', 'message');
                $date = $this->RmCommon->filterEmptyField($value, 'Message', 'created');
                $from_id = $this->RmCommon->filterEmptyField($value, 'Message', 'from_id');
                $to_id = $this->RmCommon->filterEmptyField($value, 'Message', 'to_id');
                $read = $this->RmCommon->filterEmptyField($value, 'Message', 'read');

                $mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');
                $agent = $this->RmCommon->filterEmptyField($value, 'ToUser', 'full_name');

                $format_data = array(
                    'from_id' => $from_id,
                    'to_id' => $to_id,
                    'sender_name' => $sender,
                    'sender_photo' => $senderPhoto,
                    'date' => $date,
                );

                if( $this->RmCommon->_isCompanyAdmin() || $this->RmCommon->_isAdmin() ) {
                    $format_data['agent_name'] = $agent;
                    $format_data['mls_id'] = $mls_id;
                }

                $datas[$key]['data_formated'] = $format_data;
            }
        }

        return $datas;
    }
}
?>