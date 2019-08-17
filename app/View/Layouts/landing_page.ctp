<!DOCTYPE html>
<html lang="en">
<head>
	<?php
			$title_for_layout = !empty($title_for_layout) ? $title_for_layout : false;
	?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
			echo $this->Html->tag('title', $title_for_layout);

			$minify_css = array(
				'jquery', 
				'landing_page/style',
			);

			if(isset($layout_css) && !empty($layout_css)) {
				$minify_css = array_merge($minify_css, $layout_css);
			}

			echo $this->Html->css($minify_css);
	?>

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-96386166-11', 'auto');
		ga('send', 'pageview');
	</script>
</head>
<body>
	<?php
			echo $this->fetch('content');
	?>
</body>
</html>