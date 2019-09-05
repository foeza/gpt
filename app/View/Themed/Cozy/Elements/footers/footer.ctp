<?php 
		$logo_path 	 = Configure::read('__Site.logo_photo_folder');

		$logo 		 = Common::hashEmptyField($_config, 'UserCompany.logo');
		$description = Common::hashEmptyField($dataCompany, 'UserCompany.description');

		$facebook 	 = Common::hashEmptyField($_config, 'UserConfig.facebook');
		$twitter 	 = Common::hashEmptyField($_config, 'UserConfig.twitter');
		$instagram 	 = Common::hashEmptyField($_config, 'UserConfig.instagram');

		$customLogo = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $logo_path,
			'src'		=> $logo,
			'size' 		=> 'fullsize',
		));

		$cp_right	= sprintf('%s &copy; %s',  date('Y'), Configure::read('__Site.site_name'));

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
							echo '<br>';
						}

						if(!empty($description)){
							$description = $this->Text->truncate($description, 500, 
								array(
									'ending' => '..',
									'exact' => false
								)
							);
							echo $description;
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
					echo $cp_right;
			?>
			<!-- BEGIN SOCIAL NETWORKS -->
			<ul class="social-networks">
				<?php
						echo $facebook;
						echo $twitter;
						echo $instagram;
				?>
			</ul>
			<!-- END SOCIAL NETWORKS -->
		</div>
	</div>

</footer>
<!-- END FOOTER -->