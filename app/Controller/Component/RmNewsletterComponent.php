<?php
class RmNewsletterComponent extends Component {
	var $components = array('RmCommon'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function _callBeforeSave($data, $dataCompany, $model = 'MailchimpTemplate'){
        $user_company_config_id = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'id', null);
        $is_user_internal = Common::hashEmptyField($data, 'MailchimpList.is_user_internal');

        if($is_user_internal){
            $internalIds = Common::hashEmptyField($data, 'MailchimpListInternal.id');
            $data = Common::_callUnset($data, array(
                'MailchimpListInternal',
            ));

            if($internalIds){
                $data['MailchimpList']['flag_internal'] = false;
                foreach ($internalIds as $id => $val) {
                    if($val > 0){
                        $data['MailchimpListInternal'][] = array(
                            'MailchimpListInternal' => array(
                                'user_company_config_id' => $user_company_config_id,
                                'group_id' => $id,
                            ),
                        );
                        $data['MailchimpList']['flag_internal'] = true;
                    }
                }
            }
        }

		if(!empty($data)){
			if(!empty($user_company_config_id)){
				$data[$model]['user_company_config_id'] = $user_company_config_id;
			}
		}


		return $data;
	}

	function _callBeforeSaveList($list_id, $data){
        App::import('Vendor', 'excelreader/excel_reader2');

        $dataimport = array();
        if(!empty($data['MailchimpListDetail']['import'])){

            if( !empty($data['MailchimpListDetail']['import']['type']) && $data['MailchimpListDetail']['import']['type'] == 'application/vnd.ms-excel'){
                $dataimport = new Spreadsheet_Excel_Reader($data['MailchimpListDetail']['import']['tmp_name'], false);

                $dataimport = $dataimport->dumptoarray();
            }else{
                $data['MailchimpListDetail']['import'] = '';

                $result = array(
                    'msg' => __('Gagal mengimport data.'),
                    'status' => 'error',
                    'Log' => array(
                        'activity' => __('Gagal mengimport data.'),
                        'error' => 1,
                    ),
                );

                $this->RmCommon->setProcessParams($result, false, array(
                    'redirectError' => true,
                ));
            }
        }
        
		$this->MailchimpList = ClassRegistry::init('MailchimpList'); 
	
		if(!empty($list_id)){
			$list = $this->MailchimpList->getData('first', array(
				'conditions' => array(
					'MailchimpList.id' => $list_id,
					'MailchimpList.status' => 1,
				)
			));

			if(!empty($list)){
                if(empty($dataimport) && !empty($data)){
                    $data['MailchimpListDetail']['mailchimp_list_id'] = $list_id;
                }else if(!empty($dataimport)){
					$list_arr = array();
                    foreach ($dataimport as $key => $value) {
                        if($key > 1){
                            if( !empty($value[1]) ) {
                                $email = trim($value[1]);
                                $email = str_replace(array( ' ', ' _', '_ ', '.com.', '. ', '..', ',', 'app27@gmail', '232gmail.com' ), array( '', '_', '_', '.com', '.', '.', '.', 'app27@gmail.com', '232@gmail.com' ), $email);

                                $list_arr['import_data'][] = array(
                                    'mailchimp_list_id' => $list_id,
                                    'email' => $email,
                                    'name' => $value[2],
                                );
                            }
                        }
                    }

                    $data = $list_arr;
				}
			}else{
				$result = array(
					'msg' => __('Data tidak ditemukan.'),
					'status' => 'error'
				);

				$this->RmCommon->setProcessParams($result, false, array(
					'redirectError' => true,
				));
			}
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan.'),
				'status' => 'error'
			);

			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
			));
		}
        
		return $data;
	}

    function _callBeforeSaveCampaign($data){
        $result = false;
        $date = $this->RmCommon->filterEmptyField($data, 'MailchimpCampaign', 'date_send');
        $time_send = $this->RmCommon->filterEmptyField($data, 'MailchimpCampaign', 'time_send');

        if(!empty($data)){
            $data['MailchimpCampaign']['date_send'] = $this->RmCommon->getDate($date);
            
            $result = $data;
        }

        return $result;
    }

	function _callGetAllSession ( $step, $id = false, $model = 'MailchimpCampaign' ) {
        if(empty($id)){
            $sessionName = '__Site.'.$model.'.SessionName.%s%s';
        }else{
            $sessionName = '__Site.'.$model.'.SessionName.%s.%s';
        }

        switch ($step) {
            case 'all':
                $dataBasic = $this->controller->Session->read(sprintf($sessionName, $this->controller->basicLabel, $id));
                $dataTemplate = $this->controller->Session->read(sprintf($sessionName, $this->controller->templateLabel, $id));
                $dataContent = $this->controller->Session->read(sprintf($sessionName, $this->controller->contentLabel, $id));
                $dataConfirmation = $this->controller->Session->read(sprintf($sessionName, $this->controller->confirmationLabel, $id));

                $data = array();

                if( !empty($dataBasic) ) {
                    $data = array_merge($data, $dataBasic);
                }
                if( !empty($dataTemplate) ) {
                    $data = array_merge($data, $dataTemplate);
                }
                if( !empty($dataContent) ) {
                    $data = array_merge($data, $dataContent);
                }
                if( !empty($dataConfirmation) ) {
                    $data = array_merge($data, $dataConfirmation);
                }
                break;
            
            default:
                $data = $this->controller->Session->read(sprintf($sessionName, $step, $id));
                break;
        }

        return $data;
    }

    function _callDeleteSession ($id = false, $model = 'MailchimpCampaign') {
        if(empty($id)){
            $sessionName = '__Site.'.$model.'.SessionName.%s%s';
        }else{
            $sessionName = '__Site.'.$model.'.SessionName.%s.%s';
        }
        
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->basicLabel, $id));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->templateLabel, $id));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->contentLabel, $id));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->confirmationLabel, $id));
    }

    function _callDataSession ( $step, $id = false, $model = 'MailchimpCampaign' ) {
        $data = $this->_callGetAllSession($step, $id, $model);
        
        if( is_array($data) ) {
            if( !empty($data) && is_array($data) ) {
                foreach ($data as $key => $value) {
                    if( is_array($data[$key]) ) {
                        $data[$key] = array_filter($value, function($var) {
                            return ($var != '');
                        });
                    }
                }
            }
            
            $data = array_filter($data, function($var) {
                return ($var != '');
            });
        }

        return $data;
    }

    function _callBeforeRender($data){
        if(!empty($data['data'])){
            $date = $this->RmCommon->filterEmptyField($data['data'], 'MailchimpCampaign', 'date_send');
            $time_send = $this->RmCommon->filterEmptyField($data['data'], 'MailchimpCampaign', 'time_send');
            
            if(!empty($date)){
                $data['data']['MailchimpCampaign']['date_send'] = $this->RmCommon->getDate($date, true);
            }

            if(!empty($time_send)){
                $data['data']['MailchimpCampaign']['time_send'] = date('G:i', strtotime($time_send));   
            }
        }

        return $data;
    }

    function data_step($data, $model){
        if(!empty($data)){

            $data = $data[$model];

            $basic[$model] = array(
                'title_campaign' => $this->RmCommon->filterEmptyField($data, 'title_campaign'),
                'subject_campaign' => $this->RmCommon->filterEmptyField($data, 'subject_campaign'),
                'mailchimp_list_id' => $this->RmCommon->filterEmptyField($data, 'mailchimp_list_id'),
                'email_from' => $this->RmCommon->filterEmptyField($data, 'email_from'),
                'type_period' => $this->RmCommon->filterEmptyField($data, 'type_period'),
                'date_send' => $this->RmCommon->filterEmptyField($data, 'date_send'),
                'time_send' => $this->RmCommon->filterEmptyField($data, 'time_send'),
            );

            $template[$model] = array(
                'type_template' => $this->RmCommon->filterEmptyField($data, 'type_template'),
                'id_template' => $this->RmCommon->filterEmptyField($data, 'id_template'),
            );

            $content[$model] = array(
                'content_campaign' => $this->RmCommon->filterEmptyField($data, 'content_campaign'),
            );

            $confirmation = false;
            if(!empty($basic) && !empty($template) && !empty($content)){
                $confirmation = true;
            }

            $data = array(
                'basic' => $basic,
                'template' => $template,
                'content' => $content,
                'confirmation' => $confirmation,
            );
        }

        return $data;
    }

    function get_email_campaign($data){
        $mailchimp_list_id  = $this->RmCommon->filterEmptyField($data, 'MailchimpCampaign', 'mailchimp_list_id');
        $is_user_internal = Common::hashEmptyField($data, 'MailchimpList.is_user_internal');

        $to_email = array();

        if($is_user_internal){
            $data = $this->getList($data);
            $params = array(
                'groupClient' => Common::hashEmptyField($data, 'GroupClient'),
                'groupUser' => Common::hashEmptyField($data, 'GroupUser'),
            );
            $company_id = Common::hashEmptyField($data, 'MailchimpCampaign.company_id');
            $to_email = $this->controller->User->getCountUserInternal($params, 'list', $company_id);
        } else {
            $to_email = $this->controller->User->MailchimpCampaign->MailchimpList->MailchimpListDetail->getData('all', array(
                    'conditions' => array(
                        'MailchimpListDetail.mailchimp_list_id' => $mailchimp_list_id,
                        'MailchimpListDetail.status' => 1
                    )
            ));

            $to_email = Set::extract('/MailchimpListDetail/email', $to_email);
        }

        // if($mailchimp_list_id == 'client' && !empty($user) && $group_id != 4){
        //     $user_client = $this->User->UserClient->getData('all', array(
        //         'conditions' => array(
        //             'UserClient.company_id' => $user['User']['parent_id']
        //         ),
        //         'group' => array(
        //             'UserClient.user_id'
        //         )
        //     ));
            
        //     if(!empty($user_client)){
        //         foreach ($user_client as $key => $value) {
        //             $user_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'user_id');
                    
        //             $user_client[$key] = $this->User->getMerge($value, $user_id);
        //         }

        //         $to_email = Set::extract('/User/email', $user_client);
        //     }
        // }else{
        //     if( !is_numeric($mailchimp_list_id) && $group_id == 4 ){
        //         $str_pos_agent = strpos($mailchimp_list_id, 'all-agent');
                
        //         if($mailchimp_list_id == 'all-principle'){
        //             $to_email = $this->User->getData('list', array(
        //                 'conditions' => array(
        //                     'User.parent_id' => $user_id
        //                 ),
        //                 'fields' => array(
        //                     'User.email',
        //                     'User.email'
        //                 )
        //             ), array(
        //                 'status' => 'active',
        //                 'role' => 'principle'
        //             ));
        //         }else if($str_pos_agent !== false){
        //             $arr_list = explode('-', $mailchimp_list_id);
        //             $count_arr = count($arr_list);

        //             if( isset($arr_list[$count_arr-1]) && is_numeric($arr_list[$count_arr-1]) ){
        //                 $to_email = $this->User->getData('list', array(
        //                     'conditions' => array(
        //                         'User.parent_id' => $arr_list[$count_arr-1]
        //                     ),
        //                     'fields' => array(
        //                         'User.email',
        //                         'User.email'
        //                     )
        //                 ), array(
        //                     'status' => 'active',
        //                     'role' => 'agent'
        //                 ));
        //             }else{
        //                 $id_principle = $this->User->getData('list', array(
        //                     'conditions' => array(
        //                         'User.parent_id' => $user_id
        //                     ),
        //                     'fields' => array(
        //                         'User.id',
        //                         'User.id'
        //                     )
        //                 ), array(
        //                     'status' => 'active',
        //                     'role' => 'principle'
        //                 ));

        //                 if(!empty($id_principle)){
        //                     $to_email = $this->User->getData('list', array(
        //                         'conditions' => array(
        //                             'User.parent_id' => $id_principle
        //                         ),
        //                         'fields' => array(
        //                             'User.email',
        //                             'User.email'
        //                         )
        //                     ), array(
        //                         'status' => 'active',
        //                         'role' => 'agent'
        //                     ));
        //                 }
        //             }
        //         }
        //     }else{
        //         $to_email = $this->controller->User->MailchimpCampaign->MailchimpList->MailchimpListDetail->getData('all', array(
        //             'conditions' => array(
        //                 'MailchimpListDetail.mailchimp_list_id' => $mailchimp_list_id,
        //                 'MailchimpListDetail.status' => 1
        //             )
        //         ));

        //         $to_email = Set::extract('/MailchimpListDetail/email', $to_email);
        //     }
        // }
        return $to_email;
    }

    function getDetailList($params, $list_id){
        $this->controller->loadModel('MailchimpListDetail');
        
        $options =  $this->controller->MailchimpListDetail->_callRefineParams($params, array(
            'conditions' => array(
                'MailchimpListDetail.mailchimp_list_id' => $list_id,
                'MailchimpListDetail.status' => 1
            ),
            'order' => array(
                'MailchimpListDetail.created' => 'DESC'
            ),
            'limit' => 10
        ));
        
        $this->RmCommon->_callRefineParams($params);

        $this->controller->paginate = $this->controller->MailchimpListDetail->getData('paginate', $options);

        return $this->controller->paginate('MailchimpListDetail');
    }

    function getDetailClient($params){
        $this->controller->loadModel('UserClient');
        $options = $this->controller->UserClient->_callRefineParams($params, array(
            'conditions' => array(
                'UserClient.company_id' => $this->controller->parent_id,
                'UserClient.status' => 1,
            ),
            'order' => array(
                'UserClient.created' => 'DESC',
            ),
        ));

        $this->RmCommon->_callRefineParams($this->params);
        $user_options = array_merge($options, $this->controller->User->getData('paginate', $options, array(
            'status' => 'all',
        )));
        $user_options['contain'][] = 'User';
        
        $this->controller->paginate = $this->controller->UserClient->getData('paginate', $user_options);
        $values = $this->controller->paginate('UserClient');
        return $this->controller->UserClient->getMergeList($values, array(
            'contain' => array(
                'ClientType'
            ),
        ));
    }

    function getDetailUser($params, $groupIds = 0){
        $this->RmCommon->_callRefineParams($params);
        $options = $this->controller->User->_callRefineParams($params, array(
            'conditions' => array(
                'User.group_id' => $groupIds,
            ),
        ));

        $this->controller->paginate = $this->controller->User->getData('paginate', $options, array(
            'status' => 'semi-active',
            'company' => true,
        ));

        $users = $this->controller->paginate('User');
        return $this->controller->User->getMergeList($users, array(
            'contain' => array(
                'Group',
            ),
        ));
    }

    function getList($list = false){
        if(!empty($list)){
            $this->MailchimpListInternal = ClassRegistry::init('MailchimpListInternal'); 

            $list_id = Common::hashEmptyField($list, 'MailchimpList.id');
            $groupIds = $this->MailchimpListInternal->getData('list', array(
                'conditions' => array(
                    'MailchimpListInternal.mailchimp_list_id' => $list_id,
                ),
                'fields' => array(
                    'group_id', 'group_id'
                ),
            ));

            if(!empty($groupIds)){
                $tabs = false;
                $count = count($groupIds);
                $is_client = in_array('10', $groupIds);
                $action = 'detail_list_users';

                if($count == 1 && $is_client){
                    $action = 'detail_list_clients';
                } else if($count > 0 && $is_client){
                    $tabs = true;
                }

                if(!empty($is_client)){
                    $list['GroupClient'] = array('10' => '10');
                }

                $list['GroupUser'] = array_diff($groupIds, array('10'));

                $list['MailchimpList']['tabs'] = $tabs;
                $list['MailchimpList']['action'] = $action;

            }
        }
        return $list;
    }

    function beforeViewList($lists = false){
        if(!empty($lists)){
            if(!empty($lists[0])){
                foreach ($lists as $key => $list) {
                    $list = $this->getList($list);
                    $lists[$key] = $list;
                }
            } else {
                $lists = $this->getList($lists);
            }
        }
        return $lists;
    }

    function getGroups(){
        $group_id = Configure::read('Config.Company.data.User.group_id');

        $groups = $this->controller->User->Group->getData('list', array(
            'order' => array(
                'Group.id' => 'ASC'
            ),
        ), array(
            'role' => 'internal',
            'group_id' => $group_id,
        ));

        if($group_id == 4 && !empty($groups)){
            $groups[5] = __('Admin Director');
        }

        return $groups;
    }

    function getTemplate($step_template, $id){
        if($step_template == 'basic'){
            $modelName = 'MailchimpTemplateBasic';
            $model = ClassRegistry::init($modelName);
        }else if($step_template == 'saved'){
            $modelName = 'MailchimpTemplate';
            $model = ClassRegistry::init($modelName);
        }

        $template = $model->getData('first', array(
            'conditions' => array(
                sprintf('%s.id', $modelName) => $id,
                sprintf('%s.status', $modelName) => 1,
            ),
        ));

        if(!empty($template)){
            $template_content = Common::hashEmptyField($template, sprintf('%s.template_content', $modelName));
            $template[$modelName]['template_content'] = $this->RmCommon->replaceCode($template_content);
        }

        return array(
            'template' => $template,
            'modelName' => $modelName,
        );
    }
}
?>