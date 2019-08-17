<?php

/**
 * CakePHP ACL Link Helper
 *
 * Based on Joel Stein AclLinkHelper
 * http://bakery.cakephp.org/articles/joel.stein/2010/06/26/acllinkhelper
 *
 * @author      Shahril Abdullah - shahonseven
 * @link        https://github.com/shahonseven/CakePHP-Acl-Link-Helper
 * @package     Helper
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('FormHelper', 'View/Helper');
App::uses('AclComponent', 'Controller/Component');

class AclLinkHelper extends FormHelper {

    public $userModel = 'User';
    public $primaryKey = 'group_id';

    public function __construct(View $View, $settings = array()) {
        parent::__construct($View, $settings);

        if (is_array($settings) && isset($settings['userModel'])) {
            $this->userModel = $settings['userModel'];
        }

        if (is_array($settings) && isset($settings['primaryKey'])) {
            $this->primaryKey = $settings['primaryKey'];
        }
    }

    public function _aclCheck($url, $appendCurrent = true) {
        if( is_string($url) ) {
            $url = str_replace(FULL_BASE_URL, '', $url);
            $parseURL = Router::parse($url);

            $prefix = Common::hashEmptyField($parseURL, 'prefix');

            if( !empty($prefix) ) {
                $action = Common::hashEmptyField($parseURL, 'action');
                $action = str_replace(array( $prefix.'_' ), '', $action);

                $parseURL['action'] = $action;
            }

            $url = $parseURL;
        }
        
        if ( !empty($appendCurrent) && !empty($url) ) {
            $url = array_merge($this->request->params, $url);
        }

        $plugin = '';
        if (isset($url['plugin'])) {
            $plugin = Inflector::camelize($url['plugin']) . '/';
        }else{
            $plugin = '/';
        }

        $controller = '';
        if (isset($url['controller'])) {
            $controller = Inflector::camelize($url['controller']) . '/';
        }

        $action = 'index';
        if (isset($url['action'])) {
            $action = $url['action'];

            if(!empty($url['admin'])){
                $action = 'admin_'.$action;
            }
            if(!empty($url['backprocess'])){
                $action = str_replace('admin_', '', $action);
                $action = 'backprocess_'.$action;
            }
        }

	//	$collection = new ComponentCollection();
	//	$acl = new AclComponent($collection);
	//	$aro = array(
	//		$this->userModel => array(
	//			$this->primaryKey => AuthComponent::user($this->primaryKey)
	//		)
	//	);

		$aco = 'controllers' . $plugin . $controller . $action;

	//	debug($aro);
	//	debug($aco);

	//	return $acl->check($aro, $aco);

	//	kalo list permissions ga keluar kemungkinan belum login, liat di RmUser->setPermission
	//	untuk url konsumsi user yang ga login jangan lewat sini
		$authGroupID	= Common::config('User.group_id', 0);
		$permissions	= Common::config('Permission.'.$authGroupID, array());
	//	$isAllowed		= $permissions && count(array_intersect(array('controllers', $aco), $permissions)) > 0;
		$isAllowed		= $permissions && in_array($aco, $permissions);

	//	if($action == 'admin_roles'){
	//		debug($aco . ' --- ' . $isAllowed);
	//		debug($permissions);
	//		exit;
	//	}

		return $isAllowed;
    }

    public function link($title, $url = null, $options = array(), $confirmMessage = null) {

        $only_text = Common::hashEmptyField($options, 'only_text', false);
        $allow = Common::hashEmptyField($options, 'allow', false);

        if( !empty($allow) ) {
            return $this->Html->link($title, $url, $options, $confirmMessage);
        } else {
            if( is_string($url) ) {
                $url = str_replace(FULL_BASE_URL, '', $url);
                $parseURL = Router::parse($url);

                $prefix = Common::hashEmptyField($parseURL, 'prefix');

                if( !empty($prefix) ) {
                    $action = Common::hashEmptyField($parseURL, 'action');
                    $action = str_replace(array( $prefix.'_' ), '', $action);

                    $parseURL['action'] = $action;
                }
            } else {
                $parseURL = $url;
            }

            if ((is_array($parseURL) && $this->_aclCheck($parseURL)) || is_string($parseURL)) {
                return $this->Html->link($title, $url, $options, $confirmMessage);
            } elseif ( (is_array($parseURL) && empty($this->_aclCheck($parseURL)) ) && $only_text ) {
                return $title;
            } else {
                return '';
            }
        }
    }

    public function postLink($title, $url = null, $options = array(), $confirmMessage = false) {
        if ($this->_aclCheck($url)) {
            return parent::postLink($title, $url, $options, $confirmMessage);
        }
        return '';
    }

    /*
     * check if you have access by array url
     */

    public function aclCheck($url, $appendCurrent = true) {
        return $this->_aclCheck($url, $appendCurrent);
    }

    public function getCompositionUrl($data_arr){
        if(!empty($data_arr)){
            $temp_arr = array();

            $current_controller = Common::filterEmptyField($this->params, 'controller');
            $current_prefix = Common::filterEmptyField($this->params, 'prefix');
            $current_action = $this->action;

            if(!empty($current_prefix)){
                $current_action = str_replace($current_prefix.'_', '', $current_action);
            }
            // debug($current_action);die();

            foreach ($data_arr as $key_parent => $val) {
                $allow_parent_link = true;
                
                $childs     = Common::hashEmptyField($val, 'childs');
                $parent_url = Common::hashEmptyField($val, 'url');
                $allow = Common::hashEmptyField($val, 'allow');

                if(!empty($parent_url) && is_array($parent_url)){
                    $allow_parent_link = $this->_aclCheck($parent_url);
                }

                if($allow_parent_link == true){
                    unset($val['childs']);

                    $temp_arr[$key_parent] = $val;

                    if(!empty($childs) && is_array($childs)){
                        $allow_from_child = false;
                        foreach ($childs as $key => $val_child) {
                            $child_url = Common::hashEmptyField($val_child, 'url');
                            $child_allow = Common::hashEmptyField($val_child, 'allow');

                            if( empty($child_allow) ) {
                                $child_allow = $this->_aclCheck($child_url);
                            }

                            if ($child_allow) {
                                $temp_arr[$key_parent]['childs'][] = $val_child;

                                $action = Common::hashEmptyField($child_url, 'action');
                                $controller = Common::hashEmptyField($child_url, 'controller');

                                if($current_controller === $controller && $current_action === $action){
                                    $temp_arr[$key_parent]['options']['data-active-list'] = true;
                                }

                                $allow_from_child = true;
                            }
                        }

                        if($allow_from_child == false && $allow == false && isset($temp_arr[$key_parent])){
                            unset($temp_arr[$key_parent]);
                        }
                    }
                }else if($allow == true){
                    $temp_arr[$key_parent] = $val;
                }
            }

            $data_arr = $temp_arr;
        }
// debug($temp_arr);die();
        return $data_arr;
    }

    function _callAllowReportSales () {
        $url_unit = array(
            'controller' => 'reports',
            'action' => 'sales_stock',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );
        $url_booking = array(
            'controller' => 'reports',
            'action' => 'booking',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );
        $url_booking_cancel = array(
            'controller' => 'reports',
            'action' => 'booking_cancel',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );

        $sales_unit_available = $this->link('', $url_unit);
        $sales_booking = $this->link('', $url_booking);
        $sales_booking_cancel = $this->link('', $url_booking_cancel);

        if( !empty($sales_booking) ) {
            return $url_booking;
        } else if( !empty($sales_booking_cancel) ) {
            return $url_booking_cancel;
        } else if( !empty($sales_unit_available) ) {
            return $url_unit;
        } else {
            return false;
        }
    }

    function _callAllowReportActivities () {
        $url_activities = array(
            'controller' => 'reports',
            'action' => 'activities',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );
        $url_login = array(
            'controller' => 'reports',
            'action' => 'login',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );

        $sales_activities = $this->link('', $url_activities);
        $sales_login = $this->link('', $url_login);

        if( !empty($sales_activities) ) {
            return $url_activities;
        } else if( !empty($sales_login) ) {
            return $url_login;
        } else {
            return false;
        }
    }

    function _callAllowReportVisitors () {
        $url_visitors = array(
            'controller' => 'reports',
            'action' => 'visitors',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );
        $url_visitor_uniques = array(
            'controller' => 'reports',
            'action' => 'visitor_uniques',
            'period' => 'monthly',
            'admin' => true,
            'plugin' => false
        );

        $visitors = $this->link('', $url_visitors);
        $visitor_uniques = $this->link('', $url_visitor_uniques);

        if( !empty($visitors) ) {
            return $url_visitors;
        } else if( !empty($visitor_uniques) ) {
            return $url_visitor_uniques;
        } else {
            return false;
        }
    }
}
