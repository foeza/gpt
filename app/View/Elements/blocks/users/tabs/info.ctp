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
        ));

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