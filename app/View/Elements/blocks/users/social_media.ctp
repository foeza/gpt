<?php 
        $facebook   = !empty($_global_variable['facebook'])?$_global_variable['facebook']:false;
        $twitter    = !empty($_global_variable['twitter'])?$_global_variable['twitter']:false;
        $googleplus = !empty($_global_variable['googleplus'])?$_global_variable['googleplus']:false;
        $instagram  = !empty($_global_variable['instagram'])?$_global_variable['instagram']:false;
        $youtube    = !empty($_global_variable['youtube'])?$_global_variable['youtube']:false;

?>
<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('Facebook'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('facebook', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', sprintf(__('Masukkan URL akun Facebook Anda. Contoh: %s'), $facebook), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('Twitter'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('twitter', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', sprintf(__('Masukkan URL akun Twitter Anda. Contoh: %s'), $twitter), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('Google+'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('google_plus', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', sprintf(__('Masukkan URL akun Google+ Anda. Contoh: %s'), $googleplus), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('LinkedIn'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('linkedin', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', __('Masukkan URL akun LinkedIn Anda. Contoh: https://www.linkedin.com/company/primesystemid/'), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('Pinterest'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('pinterest', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', __('Masukkan URL akun Pinterest Anda. Contoh: http://www.pinterest.com/primesystemid'), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('Instagram'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('instagram', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', sprintf(__('Masukkan URL akun Instagram Anda. Contoh: %s'), $instagram), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <?php
            echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Form->label('', __('Youtube Channel'), array('class' => 'col-xl-2 taright col-sm-1')).
                        $this->Form->input('youtube', array(
                            'class' => 'form-control',
                            'label' => false,
                            'div' => array('class' => 'relative  col-sm-10 col-xl-7')
                        )), 
                        array('class' => 'row')
                    ),
                array('class' => 'col-sm-5')
            );

            echo $this->Html->tag('div', 
                $this->Html->tag('div', sprintf(__('Masukkan URL youtube channel Anda. Contoh: %s'), $youtube), 
                    array('class' => 'element-label')
                ),
                array('class' => 'col-sm-7')
            );
        ?>
    </div>
</div>