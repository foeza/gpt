<?php 
		$User = !empty($User)?$User:false;
		$_site_name = !empty($_site_name)?$_site_name:false;
		$active_menu = !empty($active_menu)?$active_menu:false;
		$logo_path = Configure::read('__Site.logo_photo_folder');
		$logo = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');

		$is_brochure = Configure::read('Config.Company.data.UserCompanyConfig.is_brochure');

		$customLogo = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $logo_path, 
			'src'=> $logo, 
			'size' => 'xxsm',
		));

		$userPhoto = $this->Rumahku->filterEmptyField($User, 'photo');
		$userFullName = $this->Rumahku->filterEmptyField($User, 'full_name');
		$userFirstName = $this->Rumahku->filterEmptyField($User, 'first_name');

		$clientType = $this->Rumahku->filterEmptyField($User, 'client_type_id');
		$default_link_property = 'index';
		if( $clientType == 1 ) {
			$default_link_property = 'solds';
		}

		$lblDay = $this->Rumahku->_callGreetingDate();
		$urlMsg = array(
			'controller' => 'messages',
			'action' => 'index',
			'admin' => true,
		);
		$urlNotif = array(
			'controller' => 'users',
			'action' => 'notifications',
			'admin' => true,
		);
?>
<div class="client-header">
	<div class="container">
		<?php
				$icon = $this->Rumahku->icon('rv4-angle-left');
				echo $this->Html->link(sprintf('%skembali ke website Rumahku.com', $icon), Configure::read('__Site.site_default'), array(
					'escape' => false
				));
		?>
	</div>
</div>
<header id="main" class="client-header-box" role="banner">
	<nav class="navbar navbar-default hidden-print">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#on-mobile-nav" aria-expanded="false">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<?php 
						echo $this->Html->tag('h1', $this->Html->link($customLogo, '/', array(
							'target' => '_blank',
							'class' => 'navbar-brand logo',
							'escape' => false,
						)));
				?>
			</div>
			<div class="collapse navbar-collapse" id="on-mobile-nav">
				<ul class="nav navbar-nav">
					<?php 
							echo $this->Rumahku->generateSingleMenuHeder('Profil', array(
								'controller' => 'users',
								'action' => 'edit',
								'client' => true,
								'admin' => false,
							), 'li', 'profil', $active_menu);
							echo $this->Rumahku->generateSingleMenuHeder('Pengaturan', array(
								'controller' => 'users',
								'action' => 'security',
								'client' => true,
								'admin' => false,
							), 'li', 'pengaturan', $active_menu);

							if($is_brochure){
								echo $this->Rumahku->generateSingleMenuHeder('eBrosur', array(
									'controller' => 'ebrosurs',
									'action' => 'index',
									'client' => true,
									'admin' => false
								), 'li', 'ebrosur', $active_menu);
							}

							echo $this->Rumahku->generateSingleMenuHeder('Agen', array(
								'controller' => 'users',
								'action' => 'agents',
								'client' => true,
								'admin' => false
							), 'li', 'agen', $active_menu);
							echo $this->Rumahku->generateSingleMenuHeder('Properti', array(
								'controller' => 'properties',
								'action' => $default_link_property,
								'client' => true,
								'admin' => false
							), 'li', 'properti', $active_menu);
					?>
        		</ul>
			</div>
			<div class="desktop-only quick-response clear">
				<ul class="nav navbar-nav" id="main-menu-client">
        			<?php 
       //  					echo $this->Rumahku->generateSingleMenuHeder('Profil', array(
							// 	'controller' => 'users',
							// 	'action' => 'edit',
							// 	'client' => true,
							// 	'admin' => false,
							// ), 'li', 'profil', $active_menu);
							// echo $this->Rumahku->generateSingleMenuHeder('Pengaturan', array(
							// 	'controller' => 'users',
							// 	'action' => 'security',
							// 	'client' => true,
							// 	'admin' => false,
							// ), 'li', 'pengaturan', $active_menu);
        					if($is_brochure){
								echo $this->Rumahku->generateSingleMenuHeder('eBrosur', array(
									'controller' => 'ebrosurs',
									'action' => 'index',
									'client' => true,
									'admin' => false
								), 'li', 'ebrosur', $active_menu);
							}
							echo $this->Rumahku->generateSingleMenuHeder('Agen', array(
								'controller' => 'users',
								'action' => 'agents',
								'client' => true,
								'admin' => false
							), 'li', 'agen', $active_menu);
							echo $this->Rumahku->generateSingleMenuHeder('Properti', array(
								'controller' => 'properties',
								'action' => $default_link_property,
								'client' => true,
								'admin' => false
							), 'li', 'properti', $active_menu);
					?>
        		</ul>
				<ul class="nav navbar-nav navbar-right">
					<li id="user-action">
						<div class="btn-group">
							<a href="" class="dropdown-toggle" data-toggle="dropdown" aria-hashpopup="true" aria-expanded="false">
								<?php 
										echo $this->Html->tag('span', $this->Rumahku->photo_thumbnail(array(
							                'save_path' => Configure::read('__Site.profile_photo_folder'), 
							                'src'=> $userPhoto, 
							                'size' => 'ps',
							            ), array(
							            	'title' => $userFullName,
							            	'alt' => $userFullName,
							            )), array(
							            	'class' => 'user-photo',
							            ));
							            echo $this->Html->tag('span', sprintf(__('Selamat %s, '), $lblDay), array(
							            	'class' => 'greetings',
							            ));
							            echo $this->Html->tag('span', $userFirstName, array(
							            	'class' => 'user-name',
							            ));
							            echo $this->Rumahku->icon('rv4-angle-down fs06');
								?>
							</a>
							<div class="dropdown-menu wow fadeIn">
								<div class="acc-managed">
									<?php 
								            echo $this->Html->tag('p', sprintf(__('Akun ini dikelola oleh %s'), $this->Html->tag('strong', $_site_name)));
									?>
								</div>
								<?php 
										echo $this->element('blocks/users/profile', array(
					                        // 'fileupload' => !empty($form_fileupload)?false:true,
					                    ));
								?>
								<div class="user-action">
									<?php 
											echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->link(__('Edit Profil'), array(
												'controller' => 'users',
												'action' => 'edit',
												'client' => true,
												'admin' => false,
											), array(
												'escape' => false,
												'class' => 'btn default fs085',
											)), array(
												'class' => 'floleft',
											)));
											echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->link(__('Log out'), array(
												'controller' => 'users',
												'action' => 'logout',
												'admin' => true,
											), array(
												'escape' => false,
												'class' => 'btn default fs085',
											)), array(
												'class' => 'floright',
											)));
									?>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</nav>
</header>