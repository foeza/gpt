<?php 
        $idx = !empty($idx)?$idx:0;
        $data = $this->request->data;

        $dataPrice = $this->Rumahku->filterEmptyField($data, 'PropertyPrice');
        $currency_id = $this->Rumahku->filterIssetField($dataPrice, 'currency_id', $idx, 1);
        $price = $this->Rumahku->filterIssetField($dataPrice, 'price', $idx);
        $period_id = $this->Rumahku->filterIssetField($dataPrice, 'period_id', $idx);

        $options            = empty($options) ? array() : $options;
        $wrapperClass       = Common::hashEmptyField($options, 'wrapper_class', 'col-sm-8');
        $frameLabelClass    = Common::hashEmptyField($options, 'frame_label_class', 'col-sm-4 col-xl-2 taright');
        $frameInputClass    = Common::hashEmptyField($options, 'frame_input_class', 'col-sm-5 col-xl-4 input-group property-input-price');

        if( empty($idx) ) {
            $addClass = 'field-copy';
        } else {
            $addClass = '';
        }
?>
<li class="<?php echo $addClass; ?>">
    <div class="form-group">
        <div class="row">
            <div class="<?php echo($wrapperClass); ?>">
                <div class="row">
                    <?php 
                            echo $this->Html->tag('div', $this->Form->label('price', __('Harga *'), array(
                                'class' => 'control-label',
                            )), array(
                                'class' => $frameLabelClass,
                            ));
                    ?>
                    <div class="<?php echo($frameInputClass); ?>">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="input-group no-margin">
                                    <?php 
                                            echo $this->Form->input('PropertyPrice.currency_id.', array(
                                                'id' => 'currency',
                                                'class' => 'input-group-addon',
                                                'label' => false,
                                                'div' => false,
                                                'required' => false,
                                                'options' => $currencies,
                                                'value' => $currency_id,
                                            ));
                                            echo $this->Form->input('PropertyPrice.price.', array(
                                                'type' => 'text',
                                                'id' => 'price',
                                                'class' => 'form-control has-side-control at-left input_price',
                                                'label' => false,
                                                'div' => false,
                                                'required' => false,
                                                'value' => $price,
                                            ));
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-4 no-left-pad">
                                <div class="input-group no-margin">
                                    <?php 
                                            echo $this->Form->input('PropertyPrice.period_id.', array(
                                                'empty' => __('Periode'),
                                                'label' => false,
                                                'div' => false,
                                                'required' => false,
                                                'options' => $periods,
                                                'value' => $period_id,
                                            ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                            echo $this->Html->tag('span', $this->Html->link($this->Rumahku->icon('rv4-cross'), '#', array(
                                'escape' => false,
                            )), array(
                                'class' => 'col-sm-1 removed',
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</li>