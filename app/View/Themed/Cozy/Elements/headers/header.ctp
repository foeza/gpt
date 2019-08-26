<?php
		$logo_path = Configure::read('__Site.logo_photo_folder');

		$name = Common::hashEmptyField($dataCompany, 'UserCompany.name');
		$logo = Common::hashEmptyField($dataCompany, 'UserCompany.logo');

		$customLogo = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $logo_path, 
			'src'		=> $logo, 
			'size' 		=> 'xxsm',
		), array(
            'alt' 		=> sprintf('%s - %s', $name, $_SERVER['HTTP_HOST'])
        ));
        
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