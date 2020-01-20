<div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <?php 
                        echo $this->Html->tag('div', $this->Form->label('price', __('Harga *'), array(
                            'class' => 'control-label',
                        )), array(
                            'class' => 'col-xl-2 taright col-sm-3',
                        ));
                ?>
                <div class="col-sm-7 col-xl-4">
                    <div class="input-group">
                        <?php 
                                echo $this->Form->input('Property.currency_id', array(
                                    'id' => 'currency',
                                    'class' => 'input-group-addon',
                                    'label' => false,
                                    'div' => false,
                                    'required' => false,
                                ));
                                echo $this->Form->input('Property.price', array(
                                    'type' => 'text',
                                    'id' => 'price',
                                    'class' => 'form-control has-side-control at-left input_price',
                                    'label' => false,
                                    'div' => false,
                                    'required' => false,
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>