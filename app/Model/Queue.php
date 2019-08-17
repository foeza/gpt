<?php
class Queue extends AppModel {
	var $name = 'Queue';

	var $belongsTo = array(
		'Newsletter' => array(
			'className' => 'Newsletter',
			'foreignKey' => 'newsletter_id'
		),
	);
}
?>