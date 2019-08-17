<?php 
        $getCookieId = !empty($getCookieId)?$getCookieId:false;
        $currUser = !empty($currUser)?$currUser:false;
        $role = !empty($role)?$role:false;
        $recordID = !empty($recordID)?$recordID:false;
        $dataCompany = !empty($dataCompany)?$dataCompany:false;

        $is_admin = Configure::read('User.Admin.Rumahku');

        $group_id = $this->Rumahku->filterEmptyField($currUser, 'User', 'group_id');
        $company_group_id = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'group_id');

        $options['information'] = array(
            'title_tab' => __('Informasi'),
            'url_tab' => array(
                'controller' => 'users',
                'action' => 'info',
                // !empty($getCookieId) ? $getCookieId : $recordID,
                $recordID,
                'admin' => true,
            ),
        ); 

        if(in_array($group_id, array(3, 4, 5))){
            $options['division'] = array(
                'title_tab' => __('Divisi'),
                'url_tab' => array(
                    'controller' => 'groups',
                    'action' => 'index',
                    // !empty($getCookieId) ? $getCookieId : $recordID,
                    $recordID,
                    'admin' => true,
                ),
            );
            $options['user'] = array(
                'title_tab' => __('User'),
                'url_tab' => array(
                    'controller' => 'users',
                    'action' => 'user_info',
                    // !empty($getCookieId) ? $getCookieId : $recordID,
                    $recordID,
                    'admin' => true,
                ),
            );
        }

        if(in_array($group_id, array(2, 3))){
            $options['information_client'] = array(
                'title_tab' => __('Klien'),
                'url_tab' => array(
                    'controller' => 'users',
                    'action' => 'client_info',
                    $recordID,
                    'role' => $role,
                    'admin' => true,
                ),
            );
        }
        
        switch ($group_id) {
            case '4':
                $options = array_merge($options, array(
                    'principles' => array(
                        'title_tab' => __('Principal'),
                        'url_tab' => array(
                            'controller' => 'users',
                            'action' => 'info_principles',
                            $recordID,
                            'admin' => true,
                        ),
                    ),
                    // 'agents' => $agents,
                ));
                break;
            // case '3':
            //     $options = array_merge($options, array(
            //         'agents' => $agents,
            //     ));
            //     break;
        }

        $options = array_merge($options, array(
            'properties' => array(
                'title_tab' => __('Properti'),
                'url_tab' => array( 
                    'controller' => 'properties',
                    'action' => 'info',
                    // $recordID,
                    // !empty($getCookieId) ? $getCookieId : $recordID,
                    $recordID,
                    'admin' => true,
                ),
            ),
            'ebrosurs' => array(
                'title_tab' => __('eBrosur'),
                'url_tab' => array(
                    'controller' => 'ebrosurs',
                    'action' => 'info',
                    $recordID,
                    'admin' => true,
                ),
            ),
        ));
        
        if( $company_group_id != 4 ) {
            $options = array_merge($options, array(
                'messages' => array(
                    'title_tab' => __('Pesan (Hot Leads)'),
                    'url_tab' => array(
                        'controller' => 'messages',
                        'action' => 'info',
                        $recordID,
                        'admin' => true,
                    ),
                ),
                'kprs' => array(
                    'title_tab' => __('KPR'),
                    'url_tab' => array(
                        'controller' => 'kpr',
                        'action' => 'info',
                        $recordID,
                        'admin' => true,
                    ),
                ),
            ));
        }

        if($is_admin){
            $options['token'] = array(
                'title_tab' => __('Token'),
                'url_tab' => array(
                    'controller' => 'experts',
                    'action' => 'info',
                    $recordID,
                    'admin' => true,
                ),
            );
        }

        if( !empty($currUser) ) {
            echo $this->element('blocks/users/simple_info', array(
                'fileupload' => false,
                'security' => false,
                'User' => $currUser,
            ));
        }

        echo $this->element('blocks/common/tab_link', array(
            'content' => $options,
        ));
?>