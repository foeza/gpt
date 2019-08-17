<?php 
		echo $this->Html->tag('p', __('Kami telah menerima permintaan mengenai perubahan Password Anda'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		echo $this->element('emails/html/common/contact_info');
?>