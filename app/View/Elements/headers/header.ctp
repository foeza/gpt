<?php 
		$User = !empty($User)?$User:false;
		$_site_name = !empty($_site_name)?$_site_name:false;
		$notifications = !empty($notifications) ? $notifications : array();
		$notificationMessages = !empty($notificationMessages) ? $notificationMessages : array();

		$logo_path = Configure::read('__Site.logo_photo_folder');
		$company_name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
		$logo = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');
		$domainZimbra = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'domain_zimbra');

		$userPhoto = $this->Rumahku->filterEmptyField($User, 'photo');
		$userFullName = $this->Rumahku->filterEmptyField($User, 'full_name');
		$userFirstName = $this->Rumahku->filterEmptyField($User, 'first_name');

		$cntMsg = $this->Rumahku->filterEmptyField($notificationMessages, 'cnt');
		$dataMsg = $this->Rumahku->filterEmptyField($notificationMessages, 'data');

		$cntNotif = $this->Rumahku->filterEmptyField($notifications, 'cnt');
		$dataNotif = $this->Rumahku->filterEmptyField($notifications, 'data');

		$lblDay = $this->Rumahku->_callGreetingDate();
		$urlMsg = array(
			'plugin' => false, 
			'controller' => 'messages',
			'action' => 'index',
			'admin' => true,
		);
		$urlNotif = array(
			'plugin' => false, 
			'controller' => 'users',
			'action' => 'notifications',
			'admin' => true,
		);
?>
<header id="main" role="banner">
	<nav class="navbar navbar-default hidden-print">
		<div class="container-fluid">
			<div class="navbar-header">
				<button id="main-menu-device" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu" aria-expanded="false">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<?php 

					if($logo){
						$logo = $this->Rumahku->photo_thumbnail(array(
							'save_path' => $logo_path, 
							'src'=> $logo, 
							'size' => 'xxsm',
							'url' => true,
						));

						echo $this->Html->tag('h1', $this->Html->link(false, '/', array(
							'plugin' => false, 
							'target' => '_blank',
							'class' => 'navbar-brand logo',
							'escape' => false,
							'style' => sprintf('background:url(%s);', $logo),
							'title' => $company_name,
						)), array(
							'class' => 'col-sm-10 m0',
						));
					}

				?>
			</div>
			<div class="floright quick-response">
				<ul>
					<?php 
							if( $this->Rumahku->_isAdmin() ) {
								echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('rv4-list'), array(
									'plugin' => false, 
									'controller' => 'acl_manager',
									'action' => 'acl',
									'admin' => true,
								), array(
									'escape' => false,
								)), array(
									'class' => 'dekstop-only',
								));
							}
					?>

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
							            echo $this->Html->tag('span', sprintf(__('%s, '), $lblDay), array(
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
								            echo $this->Html->tag('p', sprintf(__('Akun ini dikelola oleh %s'), $this->Html->tag('strong', 'GPT')));
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
												'plugin' => false, 
												'controller' => 'users',
												'action' => 'edit',
												'admin' => true,
											), array(
												'escape' => false,
												'class' => 'btn default fs085',
											)), array(
												'class' => 'floleft',
											)));
											echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->link(__('Log out'), array(
												'plugin' => false, 
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