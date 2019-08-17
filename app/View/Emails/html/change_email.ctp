<?php 
		$old_email = !empty($params['old_email']) ? $params['old_email']:false;
		$new_email = !empty($params['new_email']) ? $params['new_email']:false;

		echo $this->Html->tag('p', sprintf(__('Kami telah menerima permintaan mengenai perubahan Email Anda dari %s menjadi %s'), $old_email, $new_email), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));	
		echo $this->element('emails/html/common/contact_info');
?>