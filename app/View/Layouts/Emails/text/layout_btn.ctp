<?php
 	echo $content_for_layout;
	if( isset($params['debug']) && $params['debug'] == 'text' ){
		die();
	}
?>
