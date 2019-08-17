<?php 
		if( !empty($url_loader) ) {
			$here = $url_loader;
		} else {
			$urlParse = Router::parse($this->here);

			$named = Common::hashEmptyField($urlParse, 'named', array());
			$pass = Common::hashEmptyField($urlParse, 'pass', array());
			$urlParse = Common::_callUnset($urlParse, array(
				'named',
				'pass',
			));

			$urlParse['autoload'] = true;
			$urlParse = array_merge($urlParse, $named);
			$urlParse = array_merge($urlParse, $pass);

			$here = $this->Html->url($urlParse);
		}

		if( !empty($custom_template) ) {
			$template = $custom_template;
		} else {
			$template = 'template-preloader';
		}
?>
<div id="render_tbl" class="table" data-reload="<?php echo $here; ?>" data-template=".<?php echo $template; ?>">
	<div class="progress-section">
		<div class="loader">
			<div class="loader-4"></div>
		</div>
	</div>
</div>
<div class="<?php echo $template; ?>"></div>