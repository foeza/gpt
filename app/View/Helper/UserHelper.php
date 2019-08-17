<?php
class UserHelper extends AppHelper {
	var $helpers = array(
		'Rumahku', 'Html', 'Number',
		'Session', 'AclLink',
	);

	function _callParentUser( $user_type, $options = array() ) {
		$result = false;
        $params = $this->params->params;
        $slug = $this->Rumahku->filterEmptyField($params, 'slug');
        $data = $this->request->data;

        $prefix = Configure::read('App.prefix');
        $admin_rumahku = Configure::read('User.Admin.Rumahku');
        $logged_group = Configure::read('User.group_id');


        $company = Configure::read('Config.Company.data');
        $group_company_group_id = $this->Rumahku->filterEmptyField($company, 'User', 'group_id');

        if( $user_type == 'client' && $prefix == 'admin' ) {
            $result = $this->Rumahku->buildInputForm('client_type_id', array_merge($options, array(
                'label' => __('Tipe Klien *'),
                'empty' => __('- Pilih Tipe Klien -'),
            )));

            if( $logged_group != 2 ) {
                $result .= $this->Rumahku->buildInputForm('agent_pic_email', array_merge($options, array(
                    'type' => 'text',
                    'label' => __('Email Agen *'),
                    'id' => 'autocomplete',
                    'attributes' => array(
                        'autocomplete' => 'off',
                        'data-ajax-url' => $this->Html->url(array(
                            'controller' => 'ajax',
                            'action' => 'list_users',
                            2,
                            'admin' => false,
                        )),
                    ),
                    'fieldError' => array(
                        'UserClient.agent_pic_email',
                    ),
                )));
            }
        } 
        else if( ( !empty($admin_rumahku) || in_array($group_company_group_id, array( 4 )) ) && in_array($user_type, array( 'agent', 'Admin', 'Principle' )) ) {
            if( $user_type == 'Principle' || $slug == 'director' ) {
                if( $slug == 'director' ) {
                    $title = __('Email Direktur *');
                } else {
                    $title = __('Email Direktur');
                }
                $group_id = 4;

                $parent_email = Common::hashEmptyField($data, 'User.parent_email');
                $parent_email = Common::hashEmptyField($params, 'named.director', $parent_email);

                $this->request->data = Hash::insert($this->request->data, 'User.parent_email', $parent_email);
            } else {
                $title = __('Email Principle *');
                $group_id = 3;
            }

            $result .= $this->Rumahku->buildInputForm('parent_email', array_merge($options, array(
	            'type' => 'text',
	            'label' => $title,
	            'id' => 'autocomplete',
	            'attributes' => array(
	                'autocomplete' => 'off',
	                'data-ajax-url' => $this->Html->url(array(
	                    'controller' => 'ajax',
	                    'action' => 'list_users',
	                    $group_id,
	                    'admin' => false,
	                ))
	            ),
	            'fieldError' => array(
	            	'User.parent_id',
	            	'User.parent_email',
            	),
	        )));
        }

        return $result;
	}

    function activeUser($value = false){
        $user_id = Common::hashEmptyField($value, 'User.id');
        $deleted = Common::hashEmptyField($value, 'User.deleted');
        $active = Common::hashEmptyField($value, 'User.active');
        $status = Common::hashEmptyField($value, 'User.status');

        $url = array(
            'controller' => 'users',
            'action' => 'actived_agent',
            $user_id,
            'admin' => true,
        );

        if(in_array(true, array($active, $status)) && empty($deleted)){
            $url = array(
                'controller' => 'users',
                'action' => 'actived_agent',
                $user_id,
                'admin' => true,
            );
            $icon = $this->Rumahku->icon('rv4-check', false, 'i', 'no-margin color-green');
            $text = __('Anda yakin ingin non-aktifkan user ini ?');
            $title = __('Non aktifkan user');
        } else {
            $url = array(
                'controller' => 'users',
                'action' => 'inactived_agent',
                $user_id,
                'admin' => true,
            );
            $icon = $this->Rumahku->icon('rv4-cross', false, 'i', 'no-margin color-red');
            $text = __('Anda yakin ingin aktifkan user ini ?');
            $title = __('Aktifkan user');
        }

        $url = $this->Html->url($url);

        return $this->Rumahku->_callLinkLabel( $this->Html->tag('span', $icon, array(
            'class' => 'status-label-checked',
        )), $url, array(
            'escape' => false,
            'class' => 'ajaxModal',
            'title' => $title,
        ));
    }

    function getAddress( $data, $separator = ', ') {
        $result = false;
        $address = Common::hashEmptyField($data, 'UserProfile.address');
        $region = Common::hashEmptyField($data, 'UserProfile.Region.name');
        $city = Common::hashEmptyField($data, 'UserProfile.City.name');
        $subarea = Common::hashEmptyField($data, 'UserProfile.Subarea.name');
        $zip = Common::hashEmptyField($data, 'UserProfile.zip');

        if($address){
            $result .= $address.' ';
        }

        if($subarea){
            $result .= $subarea;
        }

        if($city){
            $result .= $separator. $city;
        }

        if($region) {
            $result .= $separator . $region . ' ' . $zip;
        }
        return $result;
    }

    function _callActionEdit( $value ) {
        $id = Common::hashEmptyField($value, 'User.id');
        $group_id = Common::hashEmptyField($value, 'User.group_id');
        $parent_id = false;
        $parent_named = 'parent_id';

        switch ($group_id) {
            case 1:
                $action = 'edit_non_companies';
                break;
            case 3:
                $action = 'edit_principle';
                break;
            case 4:
                $action = 'edit_director';
                break;
            default:
                $action = 'edit_user';
                $parent_id = Common::hashEmptyField($value, 'User.parent_id');
                $parent_named = 'user_id';
                break;
        }

        return '&nbsp;'.$this->AclLink->link(__('Edit'), array(
            'controller' => 'users',
            'action' => $action,
            $id,
            $parent_named => $parent_id,
            'admin' => true,
        ), array(
            'class' => 'btn green inline',
        ));
    }
}