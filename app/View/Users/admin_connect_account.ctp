<?php
        $data_integrated = isset($data_integrated) ? $data_integrated : false;
        $options = array(
            'frameClass' => 'col-sm-7',
            'labelClass' => 'col-xl-2 col-sm-3',
            'class' => 'relative col-sm-8 col-xl-7',
        );

        if (!empty($data_integrated)) {
            $is_verified = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedConfig', 'is_verified');
            if ($is_verified) {
                echo $this->element('blocks/common/tab_content', array(
                    '_id' => 'wrapper-outer-connect',
                    'content' => array(
                        'connect_account' => array(
                            'content_tab' => $this->element('blocks/users/forms/connect_account', array(
                                'options' => $options
                            )),
                            'title_tab' => __('Connect Account'),
                        ),
                        'integrated_info' => array(
                            'content_tab' => $this->element('blocks/users/forms/integrated_info', array(
                                'options' => $options
                            )),
                            'title_tab' => __('Info Addon'),
                        ),
                    ),
                    '_type' => 'style2',
                ));
            } else {
                echo $this->Html->tag('div', __('Maaf, data belum tersedia. Masih dalam tahap proses verifikasi.'), array(
                    'class' => 'alert alert-warning'
                ));
            }
        } else {
            $urlRegitration = $this->Html->link(__('Daftar sekarang?'), array(
                'controller' => 'users',
                'action' => 'register_integration',
                'admin' => true,
            ), array(
                'target' => '_blank',
            ));
            echo $this->Html->tag('div', __('Anda belum melakukan integrasi. %s', $urlRegitration), array(
                'class' => 'alert alert-warning'
            ));
        }
?>