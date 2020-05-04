<?php
		if( isset($_GET['slideshow_interval']) ) {
			$slideshow_interval = $_GET['slideshow_interval'] * 1000;
			$is_autoslideshow = true;
		} else {
			$slideshow_interval = $this->Rumahku->filterEmptyField($_config, 'UserCompanySetting', 'slideshow_interval', 5) * 1000;
			$is_autoslideshow = $this->Rumahku->filterEmptyField($_config, 'UserCompanySetting', 'is_autoslideshow');
		
			if( empty($is_autoslideshow) ) {
				$slideshow_interval = false;
			}
		}
?>
<script>
	var intervalSlide = '<?php echo $slideshow_interval; ?>';
</script>

<?php
		echo $this->element('js_init/google_analytic');
?>
