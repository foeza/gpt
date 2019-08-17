<div class="crm mb20">
    <div class="sorted-tools">
        <div class="row">
            <div class="col-sm-8">
                <ul>
                    <?php
                            echo $this->Html->tag('li', 
                                $this->Html->link(__('Properti Saya'), $this->Html->url(
                                    array(
                                        'controller' => 'properties',
                                        'action' => 'index',
                                        'client' => true,
                                        'admin' => false,
                                    )
                                )
                            ));
                            echo $this->Html->tag('li', 
                                $this->Html->link(__('Dijual / Disewa'), $this->Html->url($tabs_action_type), array(
                                    'class' => 'active'
                                )
                            ));
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
        echo $this->element('blocks/users/tabs/client_own_properties_solds');
?>