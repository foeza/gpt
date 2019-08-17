<div class="wrap-content" style="text-align:center;">
	<?php
			$msg_empty = !empty($msg_empty)?$msg_empty:'';

			echo $this->Html->tag('h2', $msg_empty, array(
				'style' => 'margin:25px 0 40px 0;'
			));
			echo $this->Html->image('error_page.png', array(
				'width' => 700,
			));
	?>
</div>