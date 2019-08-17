<?php
	    $template = $this->Rumahku->filterEmptyField($options, 'scriptTemps', null, 'default');

		echo $this->element(__('plugins/fileupload/scripts/%s', $template), array(
			'options' => $options
		));
?>