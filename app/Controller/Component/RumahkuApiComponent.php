<?php  
App::import('Vendor', 'RKUAPI', array('file' => 'RKUAPI'.DS.'RKUAPI.class.php'));
App::import('Sanitize');

class RumahkuApiComponent extends Component { 

    // Configuration 
    //the Username & Password you use to login to your
    var $__default_username = 'companyweb-app123'; 
    var $__default_password = '4bbbe061f904a92379df04a6d5ded6a2'; 

    function _setAccess ( $_username = false, $_password = false ) {
        if( !empty($_username) ) {
            $__default_username = $_username;
        }
        if( !empty($_password) ) {
            $__default_password = $_password;
        }
    }

    function build_url($action = false, $full_url = false,$controller = 'Api'){
        $url = Configure::read('__Site.site_default');
        
        if(!$action){
            $action = 'output';
        }

        $__default_host = sprintf('%s/'.$controller.'/%s.json', $url, $action);
        if($full_url){
            $__default_host = $full_url;
        }
        
        return $__default_host;
    }
         
    function api_access($add_vars = array(), $action = false, $full_url = false,$controller = 'Api') { 
        $api = $this->_credentials( $this->build_url($action, $full_url,$controller) ); 
        $merge_vars = $add_vars;
        if(empty($merge_vars)) { 
            $merge_vars = array(''); 
        }
        $retval = $api->api_access($merge_vars );
        
        if (!$retval){ 
            $retval = $api->errorMessage; 
        }

        return $retval; 
    }

    //***Auth**/ 
    function _credentials( $host = false ) { 
        $api = new RKUAPI($this->__default_username, $this->__default_password, false, $host); 
        if ($api->errorCode!=''){ 
            $retval = $api->errorMessage; 
            echo $retval; die; 
            exit(); 
        } 
        return $api; 
    }
}
?>