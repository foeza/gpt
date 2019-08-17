<?php                 
        $_class = !empty($_class)?$_class:false;
        $_classFrom = isset($_classFrom)?$_classFrom:'form-style';
        $_classInput = isset($_classInput)?$_classInput:'col-sm-12';
        $_url = !empty($_url)?$_url:false;

        echo $this->Form->create('Message', array(
            'inputDefaults' => array('div' => false),
            'id' => 'contact-us',
            'class' => 'hidden-print '.$_classFrom,
            'data-type' => 'content',
            'data-wrapper-write' => '#contact-us',
            'data-scroll' => '#contact-us',
            'data-scroll-top' => '-50',
            'data-scroll-time' => '0',
            'url' => $_url,
        ));
?>
<div class="<?php echo $_class; ?>">
    <?php
            echo $this->element('blocks/common/flash');

            if( empty($logged_in) ) {
                echo $this->Rumahku->buildFrontEndInputForm('name', false, array(
                    'placeholder' => __('Nama Anda'),
                    'frameClass' => 'form-group '.$_classInput,
                )); 
                echo $this->Rumahku->buildFrontEndInputForm('email', false, array(
                    'placeholder' => __('Email Anda'),
                    'frameClass' => 'form-group '.$_classInput,
                    'inputClass' => 'form-control required fromEmail',
                )); 
                echo $this->Rumahku->buildFrontEndInputForm('phone', false, array(
                    'placeholder' => __('Handphone'),
                    'frameClass' => 'form-group '.$_classInput,
                    'inputClass' => 'form-control required',
                    'attributes' => array(
                        'title' => __('Nomor Telpon yang bisa dihubungi'),
                    ),
                )); 
            }
            
            echo $this->Rumahku->buildFrontEndInputForm('message', false, array(
                'type' => 'textarea',
                'placeholder' => __('Pesan Anda'),
                'frameClass' => 'form-group '.$_classInput,
                'inputClass' => 'form-control required',
            )); 
    ?>

    <?php /*
    <div class="col-sm-12 form-group">
        <div class="checkbox">
            <label>
                <?php   
                        echo $this->Form->input('security_code',array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'class' => false, 
                            'div' => false,
                            'error' => false,
                            'value' => $captcha_code,
                        ));
                        echo __('Saya bukan robot');
                ?>
            </label>
        </div>
        <?php 
                echo $this->Form->error('security_code');
        ?>
    </div>
    */ ?>

    <?php   
		//	google recaptcha
			echo($this->Html->tag('div', false, array(
				'class'		=> 'recaptcha-holder hide', 
				'data-key'	=> Configure::read('__Site.recaptcha_site_key'), 
			)));

            echo $this->Html->tag('div', $this->Form->button($this->Rumahku->icon('fa fa-envelope').__('Kirim Pesan'),array(
                'type' => 'submit',
                'class' => 'btn btn-default-color', 
                'data-auto-disable' => 'false', 
            )),array(
                'class' => 'center', 
            )); 
    ?>
</div>
<?php
        echo $this->Form->end();
?>