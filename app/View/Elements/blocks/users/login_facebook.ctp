<?php 
        $data_type = !empty($data_type)?$data_type:false;

        switch ($data_type) {
            case 'register':
                $title = __('Daftar');
                $titleDivider = __('atau, daftar dengan email Anda');
                break;
            
            default:
                $title = __('Masuk');
                $titleDivider = __('atau, masuk dengan akun rumahku.com');
                break;
        }

        echo $this->Html->tag('div', $this->Html->link(sprintf('<i class="fa fa-facebook"></i> %s', sprintf(__('%s dengan facebook'), $title)), 'javascript:void(0);', array(
            'escape' => false,
            'class'=>'facebook btn btn-default btn-block',
            'data-url' => $this->Html->url(array(
                'controller' => 'users',
                'action' => 'facebook_login',
                'admin' => false,
            )),
        )), array(
            'class'=>'text-center',
        ));

        echo $this->Html->tag('div', $this->Html->tag('span', $titleDivider), array(
            'class' => 'divider text-center',
        ));
?>