<div id="kpr-options" class="launcher-themes mt30">
    <div class="row mb30">
        <div class="template-download col-sm-6">
            <div class="item">
                <div class="action">
                    <?php 
                            echo $this->Html->link(
                                $this->Html->tag('div', '', array(
                                    'class' => 'preview relative',
                                    'style' => __('background: url(%s) no-repeat scroll center;', $this->Html->assetUrl('/img/kprs/agent.jpg', array(
                                        'fullBase'   => true,
                                    ))),
                                )).
                                $this->Html->tag('label', __('KPR Internal')), array(
                                'controller' => 'kpr',
                                'action' => 'add',
                                'admin' => true,
                            ), array(
                                'escape' => false,
                            ));
                    ?>
                </div>
            </div>
        </div>
        <div class="template-download col-sm-6">
            <div class="item">
                <div class="action">
                    <?php 
                            echo $this->Html->link(
                                $this->Html->tag('div', '', array(
                                    'class' => 'preview relative',
                                    'style' => __('background: url(%s) no-repeat scroll center;', $this->Html->assetUrl('/img/kprs/developer.jpg', array(
                                        'fullBase'   => true,
                                    ))),
                                )).
                                $this->Html->tag('label', __('KPR Eksternal')), array(
                                'controller' => 'kpr',
                                'action' => 'developer',
                                'admin' => true,
                            ), array(
                                'escape' => false,
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>