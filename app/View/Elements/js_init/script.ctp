<?php
		$_widget_help = isset($_widget_help)?$_widget_help:true;

		$user_fullname = $this->Rumahku->filterEmptyField($User, 'full_name');
		$user_email = $this->Rumahku->filterEmptyField($User, 'email');
		$is_admin = $this->Rumahku->_isAdmin();
		$isCompanyAdmin = $this->Rumahku->_isCompanyAdmin();

		$mobile = Configure::read('Global.Data.MobileDetect.mobile');
		$tablet = Configure::read('Global.Data.MobileDetect.tablet');
?>
<script type="text/javascript">
	<?php
			if( !empty($_widget_help) ) {
	?>
	var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
	Tawk_API.visitor = {
		name  : '<?php echo $user_fullname; ?>',
		email : '<?php echo $user_email; ?>',
	};
	(function(){
		var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
		s1.async=true;
		s1.src='https://embed.tawk.to/589961aef90be509fedad1bf/default';
		s1.charset='UTF-8';
		s1.setAttribute('crossorigin','*');
		s0.parentNode.insertBefore(s1,s0);
	})();
	<?php 
			}
	?>
</script>
<?php
		// if( !empty($tour_guide) ) {
		// 	echo $this->element('js_init/tours/menu');
		// } else
		if( !empty($group_tour_guide) && empty($is_admin) && empty($tablet) && empty($mobile) && !empty($isCompanyAdmin) ) {
			echo $this->element('js_init/tours/group');
		}

	echo($this->Html->tag('script', '', array(
		'src'					=> 'https://apis.google.com/js/api.js', 
		'async'					=> 'async', 
		'defer'					=> 'defer', 
		'onload'				=> 'this.onload=function(){};if(typeof handleClientLoad == \'function\') handleClientLoad();', 
		'onreadystatechange'	=> 'if(this.readyState === \'complete\') this.onload();', 
	)));

?>