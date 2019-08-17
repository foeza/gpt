<?php /*
<div class="floating-download-link">
	<?php

		echo($this->Html->tag('p', __('Download aplikasi Prime for Agent di'), array(
			'class' => 'margin-bottom-1',
		)));

		$downloadURL = 'https://itunes.apple.com/us/app/prime-for-agent/id1297181356?mt=8';
		echo($this->Html->link($this->Html->image('apple-store-badge.png'), $downloadURL, array(
			'class'		=> 'app-download-link', 
			'target'	=> '_blank', 
			'escape'	=> false,
		)));

		$downloadURL = 'https://play.google.com/store/apps/details?id=com.primesystem.id';
		echo($this->Html->link($this->Html->image('google-play-badge.png'), $downloadURL, array(
			'class'		=> 'app-download-link', 
			'target'	=> '_blank', 
			'escape'	=> false,
		)));

	?>
</div>
*/ ?>
<div class="download-wrapper">
	<div class="app-logo">
		<?php

			echo($this->Html->image('small-logo.png', array(
				'width' => 50, 
				'height' => 50, 
			)));

		?>
	</div>
	<div class="app-name">
		<?php

			echo($this->Html->tag('p', __('Download<br>Prime System for Agent')));
			echo($this->Html->tag('span', __('Available on Googleplay & App Store'), array(
				'class' => 'small', 
			)));

			$downloadURL = 'https://itunes.apple.com/us/app/prime-for-agent/id1297181356?mt=8';
			echo($this->Html->link(__('Download'), $downloadURL, array(
				'class'		=> 'download-btn btn-ios hide', 
				'target'	=> '_blank', 
				'escape'	=> false,
			)));

			$downloadURL = 'https://play.google.com/store/apps/details?id=com.primesystem.id';
			echo($this->Html->link(__('Download'), $downloadURL, array(
				'class'		=> 'download-btn btn-android hide', 
				'target'	=> '_blank', 
				'escape'	=> false,
			)));

		?>
	</div>
	<?php

		echo($this->Html->link($this->Rumahku->icon('rv4-cross'), 'javascript:void(0);', array(
			'class'		=> 'download-wrapper-close', 
			'escape'	=> false, 
		)));

	?>
</div>