<?php
class RmEbroschureComponent extends Component {
	var $components = array('RmCommon'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
}
?>