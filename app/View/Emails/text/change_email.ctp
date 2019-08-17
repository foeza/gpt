<?php 
		$old_email = !empty($params['old_email']) ? $params['old_email']:false;
		$new_email = !empty($params['new_email']) ? $params['new_email']:false;
		
		printf(__('Kami telah menerima permintaan mengenai perubahan Email Anda dari %s menjadi %s'), $old_email, $new_email);
		echo "\n\n";

		echo $this->element('emails/text/common/contact_info');
?>