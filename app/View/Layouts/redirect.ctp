<?php 
		$url = !empty($url)?$url:false;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<META HTTP-EQUIV="refresh" content="1; url=<?php echo $url; ?>">
	<?php
			echo $this->Html->tag('title', __('Redirecting')).PHP_EOL;
	?>
	<link rel="icon" href="/favicon.ico" type="image/jpg" />
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-21661446-1', 'auto');
		ga('send', 'pageview');
	</script>
</head>
<body onLoad="location.replace('<?php echo $url; ?>')">
	<?php 
			echo $this->fetch('content');
	?>
</body>
</html>