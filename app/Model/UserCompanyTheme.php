<?php
class UserCompanyTheme extends AppModel {
	var $name = 'UserCompanyTheme';

	function getlist () {
		return $this->find('list', array(
			'conditions' => array(
				'UserCompanyTheme.status' => 1
			),
			'fields' => array(
				'id', 'theme_name'
			)
		));
	}
}
?>