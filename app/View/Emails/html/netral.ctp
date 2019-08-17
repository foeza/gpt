<?php
		if(!empty($params['content'])){
			echo str_replace('src="/app/webroot', 'src="'.FULL_BASE_URL, $params['content']);
		}else{
			echo '';
		}

		if( isset($params['debug']) && $params['debug'] == 'view' ){
			die();
		}
?>