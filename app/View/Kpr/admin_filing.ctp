<?php 
        $urlBack = array(
            'controller' => 'kpr',
            'action' => 'index',
            'admin' => true,
        );

        echo $this->Form->create('Kpr', array(
            'type' => 'file',
            'class' => 'form-group',
            'id' => 'fast-kpr-form'
        ));

        $property_price = Common::hashEmptyField($value, 'Kpr.property_price');
        $value['flag_edit'] = false;
?>
<div class="crm form-border-error" id="kpr-form">
    <div class="row mb20 same-height">
        <div class="col-sm-4">
            <?php
                    echo $this->element('blocks/kpr/client_info');
            ?>
        </div>
        <div class="col-sm-8">
            <div class="detail-project-content wrapper-layer">
                <div class="info-wrapper contract m0">
                    <div id="buyer-info">
                        <?php
                                echo $this->Html->tag('h1', __('Informasi Properti'), array(
                                    'class' => 'info-title',
                                ));
                                echo $this->Html->div('my-properties mb0 p15', $this->Kpr->_callKprProperty($value), array(
                                    'id' => 'list-property',
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
            echo $this->Form->hidden('Kpr.property_price', array(
                'value' => $property_price,
                'class' => 'kpr-property-sold-price sold-price',
            ));
            echo $this->element('blocks/kpr/bank_list_ver2');
            echo $this->element('blocks/common/forms/action_custom', array(
                '_with_submit' => true,
                '_float_class' => 'floleft',
                '_button_text' => __('Lanjut'),
                '_textBack' => __('Batal'),
                '_classBack' => 'btn default floleft',
                '_button_class' => 'floright',
                '_urlBack' => $urlBack,
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>