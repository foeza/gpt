<?php
		echo $this->element('headers/email/header_text');
		echo "\n\n";

		echo $content_for_layout;
		echo "\n\n";

		echo $this->element('footers/email/footer_text');

		if( isset($params['debug']) && $params['debug'] == 'text' ){
			die();
		}
?>
