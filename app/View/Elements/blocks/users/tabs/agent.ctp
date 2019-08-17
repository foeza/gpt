<?php
        if( !empty($user) ) {
            $group_agent_id = Configure::read('__Site.Global.Variable.Company.agent');
            $recordID = Common::hashEmptyField($user, 'User.id');
            $group_id = Common::hashEmptyField($user, 'User.group_id');

    		$params = $this->params->params;
    		$parent_params = Common::_callCompanyParamParentId($params);

            if( $group_id == $group_agent_id ) {
    			echo $this->element('blocks/common/tab_link', array(
    	            'content' => array(
                        'profile' => array(
                            'title_tab' => __('Profile'),
                            'url_tab' => array_merge(array(
                                'controller' => 'users',
                                'action' => 'edit_user',
                                $recordID,
                                'admin' => true,
                            ), $parent_params),
                        ),
                        'target-activity' => array(
                            'title_tab' => __('Target Aktivitas'),
                            'url_tab' => array_merge(array(
                                'controller' => 'groups',
                                'action' => 'target_edit',
                                $recordID,
                                'admin' => true,
                            ), $parent_params),
                        ),
						'personal-page' => array(
							'title_tab' => __('Personal Website'),
							'url_tab' => array_merge(array(
								'admin' => true,
								'controller' => 'users',
								'action' => 'personal_config',
								$recordID,
							), $parent_params),
						),
                    ),
    	        ));
            }
        }
?>