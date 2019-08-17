<?php 
		$pagingURL = Router::url($this->here, TRUE);
		echo $this->Form->hidden('pushstate_url', array(
			'id' => 'hid-pushstate-url',
			'value' => $pagingURL,
		));
?>