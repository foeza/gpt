<?php
	    $template = $this->Rumahku->filterEmptyField($options, 'loadTemps', null, 'default');

		echo $this->element(__('plugins/fileupload/templates/%s', $template), array(
			'url' 		=> $url, 
			'options' 	=> $options
		));
?>