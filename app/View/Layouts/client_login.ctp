<?php 
        $general_path = Configure::read('__Site.general_folder');
        $logo_path = Configure::read('__Site.logo_photo_folder');
        
        $prime = Configure::read('__Site.prime_site');
        $_site_name = !empty($_site_name)?$_site_name:false;
        $_greeting = !empty($_greeting)?$_greeting:__('Silakan login dengan akun Perusahaan Anda.');

        $company_name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
        $logo = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');
    	$favicon = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'favicon');
        $powered = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'hide_powered');
        $text_powered = Common::hashEmptyField($_config, 'UserCompanyConfig.text_powered');

        $logoCompany = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $logo_path, 
            'src'=> $logo, 
            'size' => 'xxsm',
        ));
        $customFavicon = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $general_path, 
            'src'=> $favicon, 
            'thumb' => false,
            'user_path' => true,
            'url' => true,
        ));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
            echo $this->Html->tag('title', __('Login')).PHP_EOL;
            echo $this->Html->css(array(
                'admin/jquery',
                'login/style',
            )).PHP_EOL;
            echo $this->Html->meta($company_name, $customFavicon, array(
                'type' => 'icon'
            )) . PHP_EOL;
    ?>
</head>
<body class="login">
	<?php 
            echo $this->element('blocks/common/flash');
	?>
	<div class="login-wrapper">
        <div class="login-box">
    		<div class="company-logo">
    			<?php 
    					if( !empty($logoCompany) ) {
    						echo $logoCompany;
    					} else {
    						echo $this->Html->tag('h1', $this->Html->link($_site_name, '#'));
    					}
    			?>
    		</div>
    		<div class="greetings">
    			<?php 
    					echo $this->Html->tag('h4', __('Selamat Datang, klien'));
                        echo $this->Html->tag('p', $_greeting);
    			?>
    		</div>
            <div class="login-input">
                <?php 

                    echo($this->Html->tag('div', $this->fetch('content'), array(
                        'class' => 'container-fluid', 
                    )));

                ?>
            </div>
        </div>

        <?php
                if ( $powered == false ):
                    if( empty($text_powered) ) {
                        $text_powered = $this->Html->link($this->Html->image('/img/poweredby.png', array('height' => 28)), $prime, array(
                            'target' => '_blank',
                            'escape' => false,
                        ));
                    } else {
                        $text_powered = $this->Html->link($text_powered, '/', array(
                            'target' => '_blank',
                        ));
                    }
        ?>
    		<div class="powered">
    			<?php 
    					echo $this->Html->tag('h1', $this->Html->tag('span', __('powered by:')).$text_powered);
    			?>
    		</div>
        <?php endif ?>
	</div>
    <?php
            echo $this->Html->script(array(
                'jquery.library',
                'login/functions'
            )).PHP_EOL;

    ?>
</body>
</html>