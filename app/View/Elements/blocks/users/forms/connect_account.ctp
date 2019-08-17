<?php
		$options = isset($options) ? $options : array();
        $data_integrated = isset($data_integrated) ? $data_integrated : NULL;

        $is_connect_r123 = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedConfig', 'is_connect_r123');
        $is_connect_olx = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedConfig', 'is_connect_olx');

        $urlConnectR123 = array(
            'controller' => 'users',
            'action' => 'do_integrated',
            'backprocess' => true,
            '?' => array(
                'type_connect' => 'r123', 
                'flag_option' => 'connect_r123', 
            )
        );

        $urldisConnectR123 = array(
            'controller' => 'users',
            'action' => 'do_integrated',
            'backprocess' => true,
            '?' => array(
                'type_connect' => 'r123', 
                'flag_option' => 'disconnect_r123', 
            )
        );

        $urlConnectOLX = array(
            'controller' => 'users',
            'action' => 'do_integrated',
            'backprocess' => true,
            '?' => array(
                'type_connect' => 'olx', 
                'flag_option' => 'connect_olx', 
            )
        );

        $urldisConnectOLX = array(
            'controller' => 'users',
            'action' => 'do_integrated',
            'backprocess' => true,
            '?' => array(
                'type_connect' => 'olx', 
                'flag_option' => 'disconnect_olx', 
            )
        );
?>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-7">
                    <div class="row">
                        <div class="col-sm-4 col-xl-2">
                            <?php
                                    // $iconConnect = $this->Html->image('/img/icons/partners/favicon-r123.ico', array(
                                    //     'class' => 'img-partner',
                                    // ));
                                    $iconConnect = false;
                                    echo $this->Html->tag('label', $iconConnect.__('Connect OLX'), array(
                                        'class' => 'control-label',
                                    ));
                            ?>
                        </div>
                        <?php if (empty($is_connect_olx)): ?>
                            <div class="relative col-sm-8 col-xl-7 mt5">
                                <?php
                                        $iconConnect = $this->Html->image('/img/icons/icon-disconnected.png', array(
                                            'class' => 'icon-connected'
                                        ));
                                        echo $iconConnect.$this->Html->link(__('connect'), $urlConnectOLX, array(
                                            'class' => 'pd-top7 ajaxModal',
                                        ));
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="relative col-sm-8 col-xl-7">
                                <?php
                                        echo $this->Html->tag('div', $this->Html->tag('label', __('Akun terkoneksi.'), array(
                                            'class' => 'control-label',
                                        )), array(
                                            'class' => 'disinblock',
                                        ));
                                        $iconConnect = $this->Html->image('/img/icons/icon-connected.png', array(
                                            'class' => 'icon-disconnected'
                                        ));
                                        echo $iconConnect.$this->Html->link(__('disconnect'), $urldisConnectOLX, array(
                                            'class' => 'pd-top7',
                                        ));
                                ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>