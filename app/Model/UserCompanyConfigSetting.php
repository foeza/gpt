<?php
class UserCompanyConfigSetting extends AppModel {
	var $name = 'UserCompanyConfigSetting';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        )
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        return $this->merge_options($default_options, $options, $find);
    }
}
?>