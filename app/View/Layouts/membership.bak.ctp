<?php
		$site_name = Configure::read('__Site.site_name');

		if(!isset($title_for_layout)) {
			$title_for_layout = Configure::read('__Site.title_for_layout');	
		}
		if(!isset($description_for_layout)) {
			$description_for_layout = Configure::read('__Site.description_for_layout');	
		}
		if(!isset($keywords_for_layout)) {
			$keywords_for_layout = Configure::read('__Site.keywords_for_layout');	
		}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
			echo $this->Html->charset();
			echo $this->Html->tag('title', $title_for_layout);
			echo $this->Html->meta(NULL, NULL, array(
				'name'		=> 'viewport',
				'content'	=> 'width=device-width, initial-scale=1, minimum-scale=1, user-scalable=yes',
				'inline'	=> FALSE
			));
			echo $this->Html->meta('icon');
			echo $this->Html->meta('description', $description_for_layout);
			echo $this->Html->meta('keywords', $keywords_for_layout);
			echo $this->Html->meta(array('name'=> 'copyright', 'content'=> 'Copyright '.date('Y').' '.$site_name));

			$minify_css = array(
				'jquery', 
				'memberships/style', 
				'memberships/custom',
			);

			if(isset($layout_css) && !empty($layout_css)) {
				$minify_css = array_merge($minify_css, $layout_css);
			}

			echo $this->Html->css($minify_css);
			echo $this->fetch('meta');
			echo $this->fetch('css');
			echo $this->fetch('script');

			$bodyClass = empty($bodyClass) ? 'prime' : $bodyClass;
	?>

	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-96386166-12', 'auto');
	ga('send', 'pageview');
	</script>
</head>
<body class="<?php echo($bodyClass); ?>">
	<?php
			echo $this->element('blocks/memberships/headers/header');
			echo $this->fetch('content');
			echo $this->element('blocks/memberships/footers/footer');
			echo $this->element('blocks/memberships/forms/contact');

			$minify_js = array(
				'jquery.library',
				'admin/customs.library',
				'functions',
				'memberships/functions',
			);

			if(isset($layout_js) && !empty($layout_js)) {
				$minify_js = array_merge($minify_js, $layout_js);
			}

			echo $this->Html->script($minify_js);
			echo $this->element('blocks/common/template_flash');

			if(!empty($_GET['openwindow'])){
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#<?php echo $_GET['openwindow'];?>').modal('show');
		});
	</script>
	<?php	
			}
	?>
</body>
</html>
