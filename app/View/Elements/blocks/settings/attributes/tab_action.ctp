<?php 
        $step = !empty($step)?$step:false;
        $id = !empty($id)?$id:false;
        $urlStepProperties = !empty($urlStepProperties)?$urlStepProperties:'#';
        $urlStepOptions = !empty($urlStepOptions)?$urlStepOptions:array(
            'controller' => 'settings',
            'action' => 'attribute_options',
            $id,
            'admin' => true,
        );
        $urlBack = !empty($urlBack)?$urlBack:'#';
?>
<div class="action-group top">
    <?php
            if( !empty($urlBack) ) {
    ?>
    <div class="floleft">
        <?php
                echo $this->Html->link(__('Back'), $urlBack, array(
                    'class'=> 'btn default',
                ));
        ?>
    </div>
    <?php 
            }

            if( !empty($step) ) {
    ?>
    <div class="step floright">
        <div class="step floright">
            <ul>
                <?php 
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 1, array(
                            'class' => 'step-number',
                            'id' => 'step-1',
                        )), $this->Html->tag('label', __('Properties'), array(
                            'for' => '#step-1',
                        ))), $urlStepProperties, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, 'properties'),
                        ));
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 2, array(
                            'class' => 'step-number',
                            'id' => 'step-2',
                        )), $this->Html->tag('label', __('Manage Options'), array(
                            'for' => '#step-2',
                        ))), $urlStepOptions, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, 'manage-options'),
                        ));
                ?>
            </ul>
        </div>
    </div>
    <?php 
            }
    ?>
</div>