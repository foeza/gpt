<!DOCTYPE html>
<html lang="en">
<head>
	<noscript>
		<style type="text/css">
			[data-simplebar]{
				overflow: auto;
			}
			.simplebar {
				overflow-y: scroll;
			}
			.simplebar.horizontal {
				overflow-x: scroll;
				overflow-y: hidden;
			}
		</style>
	</noscript>
	<meta charset="UTF-8">
	<title>Prime Agent | Ebrochure Builder</title>
	<?php

		echo($this->Html->meta('icon'));
		echo($this->Html->css(array(
		//	'jquery-ui', 
			'simplebar', 
			'bootstrap.min', 
			'ebrochure_builder/fontawesome.min', 
			'ebrochure_builder/regular.min', 
			'ebrochure_builder/solid.min', 
			'ebrochure_builder/editor', 
			'ebrochure_builder/editor.custom', 
		)));

	//	echo $this->fetch('meta');
	//	echo $this->fetch('css');
	//	echo $this->fetch('script');

		$ebrochure	= empty($ebrochure) ? array() : $ebrochure;
		$regenerate	= empty($regenerate) ? false : true;
		$bodyClass	= $regenerate ? 'ebrochure-regenerate' : '';

	?>
	<script type="text/javascript">
	//	autogenerate ebrochure when set to true
		window.regenerateEbrochure = <?php echo($regenerate ? 'true' : 'false'); ?>;
	</script>
</head>
<body class="<?php echo($bodyClass); ?>">
	<?php

		if($regenerate){
			$splashscreen = $this->Html->div('splashscreen-loader', '');
			$splashscreen.= $this->Html->tag('p', __('Sedang memproses eBrosur. Mohon tunggu...'), array(
				'class' => 'no-margin', 
			));

			echo($this->Html->div('splashscreen-cover', $this->Html->div('splashscreen-wrapper', $splashscreen)));
		}

	//	echo $this->element('blocks/common/flash');
		echo($this->element('blocks/ebrosurs/ebrochure_builder/panels/panel-placeholder', array(
			'ebrochure' => $ebrochure, 
		)));

	?>
	<div class="preview" data-simplebar data-simplebar-direction="vertical">
		<div class="load-spinner-wrapper">
			<div class="load-spinner"></div>
		</div>
		<?php echo($this->fetch('content')); ?>
		<div id="json-viewer" class="hide">
			<div class="input-group">
				<textarea class="form-control fullwidth" rows="15"></textarea>
			</div>
		</div>
	</div>
	<?php

		$default_js	= array(
			'ebrochure_builder/jquery.min', 
			'simplebar', 
		//	'admin/jquery.library', 
			'jquery-ui.min', 
			'admin/customs.library', 
			'admin/functions',
			'ebrochure_builder/library', 
			'ebrochure_builder/centering_guidelines', 
			'ebrochure_builder/aligning_guidelines', 
			'ebrochure_builder/ebrochure.builder', 
			'ebrochure_builder/notify.min', 
		);

		$js = !empty($js) ? $js : $default_js;

		if(isset($layout_js) && !empty($layout_js)) {
			$js = array_merge($js, $layout_js);
		}

		$js = array_merge($js, array(
		//	'customs',
		//	'functions',
		));

		echo($this->Html->script($js, array(
			'defer' => 'defer',
		)));

	//	echo($this->element('blocks/designs/templates/global-template'));
	//	echo($this->element('blocks/backends/commons/modal'));

		echo($this->element('blocks/ebrosurs/ebrochure_builder/modal', array(
			'ebrochure' => $ebrochure, 
		)));

	?>
</body>
</html>