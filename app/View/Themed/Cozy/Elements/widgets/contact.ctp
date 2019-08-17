<?php 
        $label = !empty($label)?$label:__('HUBUNGI KAMI');
        echo $this->Html->tag('h2', $label, array(
            'class' => 'section-title'
        ));
?>
<div id="contact-us">
    <?php 
            echo $this->Form->create('Message', array(
                'data-type' => 'content',
                'data-wrapper-write' => '#contact-us',
                'data-scroll' => '#contact-us',
                'data-scroll-top' => '-180',
                'data-scroll-time' => '0',
                'url' => $this->Html->url(null, true),
            ));
            echo $this->element('blocks/common/template_flash');

            if( empty($logged_in) ) {
                echo $this->Rumahku->buildFrontEndInputForm('name', false, array(
                    'placeholder' => __('Nama Anda'),
                    'frameClass' => 'form-group',
                ));
                echo $this->Rumahku->buildFrontEndInputForm('email', false, array(
                    'type' => 'text',
                    'placeholder' => __('Email Anda'),
                    'frameClass' => 'form-group',
                ));
                echo $this->Rumahku->buildFrontEndInputForm('phone', false, array(
                    'placeholder' => __('Handphone'),
                    'frameClass' => 'form-group',
                    'attributes' => array(
                        'title' => __('Nomor Telpon yang bisa dihubungi'),
                    ),
                ));
            }
                
            echo $this->Rumahku->buildFrontEndInputForm('message', false, array(
                'type' => 'textarea',
                'placeholder' => __('Pesan Anda'),
                'frameClass' => 'form-group',
            )); 
    ?>
    <?php /*
    <div class="form-group">
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
        //  google recaptcha
            echo($this->Html->tag('div', false, array(
                'class'     => 'recaptcha-holder hide', 
                'data-key'  => Configure::read('__Site.recaptcha_site_key'), 
            )));

            echo $this->Html->tag('p', $this->Form->button($this->Rumahku->icon('fa fa-envelope').__('Kirim Pesan'),array(
                'type' => 'submit',
                'class' => 'btn btn-default', 
            )),array(
                'class' => 'center', 
            )); 
            echo $this->Form->end();
    ?>
</div>