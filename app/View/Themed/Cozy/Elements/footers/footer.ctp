<?php 
		$logo_path 			= Configure::read('__Site.logo_photo_folder');
		$_launcher_download = isset($_launcher_download)?$_launcher_download:true;

		$logo 				= $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');
		$description 		= $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'description');

		$facebook 			= $this->Rumahku->filterEmptyField($dataCompany, 'UserConfig', 'facebook');
		$twitter 			= $this->Rumahku->filterEmptyField($dataCompany, 'UserConfig', 'twitter');
		$google_plus 		= $this->Rumahku->filterEmptyField($dataCompany, 'UserConfig', 'google_plus');
		$linkedin 			= $this->Rumahku->filterEmptyField($dataCompany, 'UserConfig', 'linkedin');
		$pinterest 			= $this->Rumahku->filterEmptyField($dataCompany, 'UserConfig', 'pinterest');
		$instagram 			= $this->Rumahku->filterEmptyField($dataCompany, 'UserConfig', 'instagram');

		$copyright			= $this->Rumahku->filterEmptyField($_config, 'UserCompanySetting', 'copyright', Configure::read('__Site.site_name'));
		$googleplayLauncher	= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_launcher');
		$launcherUrl		= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'launcher_url');
		$powered 			= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'hide_powered', FALSE);
		$footer_content 	= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'footer_content', false, false);
		$text_powered = Common::hashEmptyField($_config, 'UserCompanyConfig.text_powered');

		$customLogo			= $this->Rumahku->photo_thumbnail(array('save_path' => $logo_path, 'src'=> $logo, 'size' => 'xxsm',));
		$customCopyright	= sprintf('%s &copy; %s',  date('Y'), $copyright);

		if( !empty($facebook) ) {
			$facebook = $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-facebook'), $facebook, array(
				'escape' => false,
				'target' => '_blank',
			)));
		}
		if( !empty($twitter) ) {
			$twitter = $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-twitter'), $twitter, array(
				'escape' => false,
				'target' => '_blank',
			)));
		}
		if( !empty($google_plus) ) {
			$google_plus = $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-google'), $google_plus, array(
				'escape' => false,
				'target' => '_blank',
			)));
		}
		if( !empty($linkedin) ) {
			$linkedin = $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-linkedin'), $linkedin, array(
				'escape' => false,
				'target' => '_blank',
			)));
		}
		if( !empty($pinterest) ) {
			$pinterest = $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-pinterest'), $pinterest, array(
				'escape' => false,
				'target' => '_blank',
			)));
		}
		if( !empty($instagram) ) {
			$instagram = $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-instagram'), $instagram, array(
				'escape' => false,
				'target' => '_blank',
			)));
		}

?>
<footer id="footer" class="hidden-print">
	<div id="footer-top" class="container">
		<div class="row">
			<div class="block col-xs-12 col-sm-12 col-md-5" id="footer-short-desc">
				<?php

						if(!empty($customLogo)){
							echo $this->Html->link($customLogo, '/', array(
								'escape' => false,
							));
							echo '<br><br>';
						}

						if( !empty($footer_content) ) {
							echo $this->Html->tag('p', $this->Rumahku->_callGetDescription($footer_content));
						} else if(!empty($description)){
							$description = $this->Text->truncate($description, 500, 
								array(
									'ending' => '..',
									'exact' => false
								)
							);
							echo $this->Html->tag('p', $description.$this->Html->link(__('selengkapnya'), array(
								'controller' => 'pages', 
								'action' => 'about',
								'admin' => false,
							)));
						}
				?>
			</div>
			<div class="block col-xs-12 col-sm-6 col-md-4">
				<?php
						echo $this->Html->tag('h3', __('Hubungi Kami'));
						echo $this->element('blocks/common/info');
				?>
			</div>
			<div class="block col-xs-12 col-sm-6 col-md-3">
				<?php
						echo $this->Html->tag('h3', __('Tautan Cepat'));
				?>
				<ul class="footer-links">
					<?php
							echo $this->Html->tag('li', $this->Html->link(__('Tentang GPT'), array(
								'controller' => 'pages',
								'action' => 'about'
							)));
					?>
				</ul>
			</div>
			
		</div>
	</div>
	<div id="copyright">
		<div class="container">
			<?php
					echo $customCopyright;
			?>
			<!-- BEGIN SOCIAL NETWORKS -->
			<ul class="social-networks">
				<?php
						echo $facebook;
						echo $twitter;
						echo $google_plus;
						echo $linkedin;
						echo $pinterest;
						echo $instagram;
				?>
			</ul>
			<!-- END SOCIAL NETWORKS -->
		</div>
	</div>

</footer>
<!-- END FOOTER -->