<?php
		$data = $this->request->data;

		$label = !empty($label) ? $label : '';
		$key = !empty($key) ? $key : '';
		$month_target = !empty($month_target) ? $month_target : '';

		$field 		= 'TargetProjectSaleDetail.'.$key.'.target_revenue';
		$field_2 	= 'TargetProjectSaleDetail.'.$key.'.target_listing';
		$field_3 	= 'TargetProjectSaleDetail.'.$key.'.target_ebrosur';
		$field_id 	= 'TargetProjectSaleDetail.'.$key.'.id';

		$id 					= Common::hashEmptyField($data, $field_id);
		$global_currency_id 	= Common::hashEmptyField($data, 'TargetProjectSale.currency_id', 1);

		$currencies = !empty($currencies) ? $currencies : array();
		$global_currency_code = Common::hashEmptyField($currencies, $global_currency_id);
?>
<div class="form-group plus">
    <div class="row">
        <div class="col-sm-3 label taright sub-label-form">
            <?php 
                    echo $this->Html->tag('h4', $label);
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
    	<?php
    			if(!empty($id)){
					echo $this->Form->hidden($field_id);
				}

				echo $this->Form->hidden('TargetProjectSaleDetail.'.$key.'.month_target', array(
					'value' => $month_target
				));
    	?>
		<div class="form-group">
	        <div class="row">
	            <div class="col-xl-3 col-sm-3 control-label taright">
	            	<?php
	            			echo $this->Form->label($field, __('Target Penjualan'), array(
	            				'class' => 'control-label'
	            			));
	            	?>
	            </div>
	            <div class="relative col-sm-3 col-xl-3">
	                <div>
	                	<div class="input-group mb0">
	                		<?php
		                			echo $this->Html->div('input-group-addon at-left label-periode', $global_currency_code);

		                			echo $this->Form->input($field, array(
		                				'class' => 'input_price has-side-control at-left form-control',
		                				'autocomplete' => 'off',
		                				'div' => false,
		                				'error' => false,
		                				'label' => false
		                			));
		                	?>
	                	</div>
	                </div>                
	            </div>
	        </div>
	    </div>
	    <div class="form-group">
	        <div class="row">
	            <div class="col-xl-3 col-sm-3 control-label taright">
	            	<?php
	            			echo $this->Form->label($field, __('Target Input Listing'), array(
	            				'class' => 'control-label'
	            			));
	            	?>
	            </div>
	            <div class="col-sm-3 col-xl-3 increment">
	            	<div class="input-group">
	            		<?php
	                			echo $this->Html->link($this->Rumahku->icon('rv4-bold-min mr0 fs085'), '#', array(
	                                'escape' => false,
	                                'class' => 'input-group-addon at-left op-min',
	                                'role' => 'button',
	                                'data-action' => 'min',
	                                'data-target' => 'tmp-increment',
	                            ));

	                            echo $this->Form->input($field_2, array(
	                                'type' => 'text',
	                                'label' => false,
	                                'required' => false,
	                                'error' => false,
	                                'div' => false,
	                                'class' => 'form-control has-side-control at-bothway tmp-increment input_number',
	                            ));
	                            echo $this->Html->link($this->Rumahku->icon('rv4-bold-plus mr0 fs085'), '#', array(
	                                'escape' => false,
	                                'class' => 'input-group-addon at-right op-plus',
	                                'role' => 'button',
	                                'data-action' => 'plus',
	                                'data-target' => 'tmp-increment',
	                            ));
	                	?>
	            	</div>
	            </div>
	        </div>
	    </div>
	    <div class="form-group">
	        <div class="row">
	            <div class="col-xl-3 col-sm-3 control-label taright">
	            	<?php
	            			echo $this->Form->label($field, __('Target Ebrosur'), array(
	            				'class' => 'control-label'
	            			));
	            	?>
	            </div>
	            <div class="col-sm-3 col-xl-3 increment">
	            	<div class="input-group">
	            		<?php
	                			echo $this->Html->link($this->Rumahku->icon('rv4-bold-min mr0 fs085'), '#', array(
	                                'escape' => false,
	                                'class' => 'input-group-addon at-left op-min',
	                                'role' => 'button',
	                                'data-action' => 'min',
	                                'data-target' => 'tmp-increment',
	                            ));

	                            echo $this->Form->input($field_3, array(
	                                'type' => 'text',
	                                'label' => false,
	                                'required' => false,
	                                'error' => false,
	                                'div' => false,
	                                'class' => 'form-control has-side-control at-bothway tmp-increment input_number',
	                            ));
	                            echo $this->Html->link($this->Rumahku->icon('rv4-bold-plus mr0 fs085'), '#', array(
	                                'escape' => false,
	                                'class' => 'input-group-addon at-right op-plus',
	                                'role' => 'button',
	                                'data-action' => 'plus',
	                                'data-target' => 'tmp-increment',
	                            ));
	                	?>
	            	</div>
	            </div>
	        </div>
	    </div>
    </div>
</div>