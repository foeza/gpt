<?php
		$message_exception = !empty($params['message_exception']) ? $params['message_exception'] : false;
		$line_error = !empty($params['line_error']) ? $params['line_error'] : false;
		$path_error_file = !empty($params['path_error_file']) ? $params['path_error_file'] : false;
		$sql_dump = !empty($params['sql_dump']) ? $params['sql_dump'] : false;
?>
<h2><?php echo __d('cake_dev', 'Fatal Error'); ?></h2>
<p class="error">
	<?php
			echo $this->Html->tag('strong', __d('cake_dev', 'Url')).sprintf(' : %s <br>', h(FULL_BASE_URL.'/'.$this->params->url));

			if(!empty($message_exception)){
				echo $this->Html->tag('strong', __d('cake_dev', 'Error')).sprintf(' : %s <br>', h($message_exception));
			}

			if(!empty($path_error_file)){
				echo $this->Html->tag('strong', __d('cake_dev', 'File')).sprintf(' : %s <br>', h($path_error_file));
			}

			if(!empty($line_error)){
				echo $this->Html->tag('strong', __d('cake_dev', 'Line')).sprintf(' : %s <br>', h($line_error));
			}

			if(!empty($sql_dump)){
				echo $this->Html->tag('strong', __d('cake_dev', 'SQL Query')).sprintf(' : %s <br>', h($sql_dump));	
			}
	?>
</p>