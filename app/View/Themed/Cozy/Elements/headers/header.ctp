<?php 
		$logo_path = Configure::read('__Site.logo_photo_folder');

		$email = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'email');

		$phone = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'phone', '', true, 'formatNumber');
		$name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
		$email = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'contact_email', $email);
		$logo = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');
		$contactInfo = '';

		$customLogo = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $logo_path, 
			'src'=> $logo, 
			'size' => 'xxsm',
		), array(
            'alt' => sprintf('%s - %s', $name, $_SERVER['HTTP_HOST'])
        ));

		if(!empty($phone)){
			$icon = $this->Rumahku->icon('fa fa-phone');
			$link = $this->Html->link($phone, sprintf('tel:%s', $phone), array(
	        	'escape' =>false,
	        ));
			$contactInfo .= $this->Html->tag('li', sprintf('%s %s', $icon, $link));
	    }

	    if(!empty($email)){
			$icon = $this->Rumahku->icon('fa fa-envelope');
			$link = $this->Html->link($email, sprintf('mailto:%s', $email), array(
	        	'escape' =>false,
	        	'class' => 'topBarText',
	        ));
			$contactInfo .= $this->Html->tag('li', sprintf('%s %s', $icon, $link));
	    }

	    //	link ke dashboard, dimunculkan hanya jika user sudah login
        $contactInfo .= $this->element('blocks/common/direct_backend', array(
        	'class' => 'topBarText',
        	'iconInside' => false,
        ));
		// if(isset($User) && $User){
		// 	$icon = $this->Rumahku->icon('fa fa-home');
		// 	$link = Configure::read('User.dashboard_url');
		// 	$link = $this->Html->link('Halaman Admin', $link, array(
	 //        	'escape' =>false,
	 //        	'class' => 'topBarText',
	 //        ));
		// 	$contactInfo .= $this->Html->tag('li', sprintf('%s %s', $icon, $link));
		// }
?>
<header id="header" class="hidden-print">
	<?php
			echo $this->element('headers/top_header');
	?>
	<div id="nav-section">
		<div class="container">
			<?php
            		if(!empty($customLogo)){
                        echo $this->Html->link($customLogo, '/', array(
                        	'escape' => false,
                        	'class' => 'nav-logo'
                    	));
                    }

                    echo $this->element('headers/menu');
            ?>
		</div>
	</div>
</header>