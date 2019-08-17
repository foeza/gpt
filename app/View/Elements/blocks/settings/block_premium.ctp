<?php
		$display_item  = !empty($display_item)?$display_item:false;
		$custom_item   = !empty($custom_item)?$custom_item:false;

		$params 	= $this->params->params;
		$controller = Common::hashEmptyField($params, 'controller');
		
		$label_name	= Common::hashEmptyField($custom_item, 'label_name', __('Block Premium Listing Agen'));

?>

<div class="form-group">
    <div class="row">
        <div class="col-sm-12 col-md-12">
        	<div class="form-group">
	            <div class="row">
	                <div class="col-xl-1 col-sm-4 col-md-3 control-label taright">
	                	<label class="control-label"><?php echo $label_name; ?></label>
	                </div>
	                <div class="relative col-sm-8 col-xl-4">
	                	<?php if ($controller == 'settings'): ?>
	                		
			                <div class="cb-custom mt0 pd-top7 mb10">
							    <div class="cb-checkmark">
							        <?php   
							                echo $this->Form->input('is_block_premium_listing',array(
							                    'div' => false,
							                    'label'=> false,
							                    'required' => false,
							                    'class' => 'trigger-toggle',
							                    'data-show' => '#wrapper-package',
							                    'type' => 'checkbox',
							                ));
							                echo $this->Form->label('is_block_premium_listing', __('Atur Default Premium Listing'));
							        ?>
							    </div>
							</div>

	                	<?php endif ?>

					    <?php
					    		echo $this->element('blocks/settings/block_premium_item', array(
					    			'display_item' => $display_item,
					    			'custom_item'  => $custom_item,
					    		));
					    ?>
	                </div>
	            </div>
            </div>
        </div>
    </div>
</div>