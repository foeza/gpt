<?php
		$content = '';
		if(!empty($userList)){
			$content = $this->element('blocks/users/forms/superior');
		}
        echo $this->Html->tag('div', $content, array(
			'id' => 'parent-user',
		));

		$group_id = !empty($group_id) ? $group_id : false;
		$content_commission = '';

		if( $group_id == 2 ) {
			$content_commission = $this->element('blocks/users/forms/commission');
		}

		echo $this->Html->tag('div', $content_commission, array(
			'id' => 'user-group-commission',
		));
?>