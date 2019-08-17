<?php
		$site_name = Configure::read('__Site.site_name');
		$bubleType = !empty($bubleType) ? $bubleType : false;
		$bodyClass = !empty($bodyClass) ? $bodyClass : false;

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
<html lang="en">
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
				// 'jquery',
				'membershipV2/animate', 
				'membershipV2/style',
				'membershipV2/custom-style',
				'membershipV2/custom',
			);

			if(isset($layout_css) && !empty($layout_css)) {
				$minify_css = array_merge($minify_css, $layout_css);
			}

			echo $this->Html->css($minify_css);
			echo $this->fetch('meta');
			echo $this->fetch('css');
			echo $this->fetch('script');
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

<body class="<?php echo $bodyClass; ?>" data-spy="scroll" data-target=".featureBar">
   	<div class="transition-wrapper">
	    <div class="bgone"></div>
	    <div class="bgtwo"></div>
	    <div class="bgthree"></div>
   	</div>
   	<?php
   			if($bubleType){
   	?>
   	<div class="mainBubble at <?php echo $bubleType; ?>">
  		<?php
  				echo $this->Html->tag('div', $this->element('blocks/membershipV2/bubble'), array(
  					'class' => 'container',
  				));
  		?>
   	</div>
   	<?php
   			}
   	?>
   	<?php
   			echo $this->element('blocks/membershipV2/headers/header');
   			echo $this->Html->tag('div', $this->fetch('content'), array(
   				'id' => 'content',
   			));
   			echo $this->element('blocks/membershipV2/footers/footer');
   			echo $this->element('blocks/membershipV2/extra/modal');
   	?>
   	<?php /*
	<div id="tryContent" class="modal tryMe">
		<div class="modalWrapper">
			<div class="tryModalBox">
				<div class="container">
					<span class="tryClose">&times;</span>
					<div class="row justify-content-lg-center">
						<div class="col-lg-7">
							<div class="row">
								<div class="col-lg-2">
									<div class="headingText">
										<div class="headingSeparator margin-top-3"></div>
									</div>
								</div>
								<div class="col-lg-10">
									<div class="headingText">
										<h2 class="heading">Request <span>Order</span></h2>
										<p class="margin-top-4 margin-bottom-4">Lengkapi form di bawah ini untuk mencoba fitur Prime System Agent, gratis!</p>
									</div>
									<form action="">
										<div class="inputGroup margin-bottom-4">
											<input type="text" placeholder="Nama Lengkap" class="fullwidth display-block">
										</div>
										<div class="inputGroup margin-bottom-4">
											<input type="text" placeholder="Nama Perusahaan" class="fullwidth display-block">
										</div>
										<div class="inputGroup margin-bottom-4">
											<input type="email" placeholder="Email" class="fullwidth display-block">
										</div>
										<div class="inputGroup margin-bottom-4">
											<input type="tel" placeholder="Nomor Telepon" class="fullwidth display-block">
										</div>
										<div class="inputGroup margin-bottom-4">
											<textarea name="" id="" cols="30" rows="5" class="fullwidth display-block">Saya tertarik untuk mencoba gratis Prime System selama satu bulan kedepan, dapatkah Tim Prime System memberikan informasi lebih lanjut dan melakukan presentasi di perusahaan kami?</textarea>
										</div>
										<div class="inputGroup margin-bottom-4">
											<div class="checkbox-wrapper">
												<div class="checkbox-button">
													<input type="checkbox" id="check1" value="check1" name="checkbox">
													<label for="check1"></label>
												</div>
												<label for="check1" class="checkbox-label">Saya Bukan Robot</label>
											</div>
										</div>
										<div class="buttonWrapper">
											 <a href="register.html" class="button primary">Daftar Sekarang</a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	*/ ?>
   	<?php

 			$minify_js = array(
				'membershipV2/jquery-1.10.2.min',
				'membershipV2/function',
				'https://use.fontawesome.com/f5dc06f4cd.js',
				'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.bundle.min.js',
				'membershipV2/wow.min',
				'membershipV2/rellax',
				'membershipV2/parallax',
				'membershipV2/sidebar',
				'membershipV2/affix',
				'memberships/functions',
				'membershipV2/customs',
				'admin/functions',
				'admin/customs.library',
			);

			if(isset($layout_js) && !empty($layout_js)) {
				$minify_js = array_merge($minify_js, $layout_js);
			}

			echo $this->Html->script($minify_js);

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