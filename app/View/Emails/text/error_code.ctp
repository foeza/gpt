<?php
		$message_exception = !empty($params['message_exception']) ? $params['message_exception'] : false;
		$line_error = !empty($params['line_error']) ? $params['line_error'] : false;
		$path_error_file = !empty($params['path_error_file']) ? $params['path_error_file'] : false;
		$sql_dump = !empty($params['sql_dump']) ? $params['sql_dump'] : false;

		echo __('Fatal Error')."\n";
		echo sprintf('Url : %s', FULL_BASE_URL.'/'.$this->params->url)."\n";
		echo sprintf('Error : %s', $message_exception)."\n";
		echo sprintf('File : %s', $path_error_file)."\n";
		
		if(!empty($sql_dump)){
			echo sprintf('SQL Query : %s', $sql_dump)."\n";
		}

		echo sprintf('Line : %s', $line_error)."\n";
?>