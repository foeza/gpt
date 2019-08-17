<?php
		$id = !empty($id)?$id:false;
		
		echo $this->element('blocks/settings/attributes/tab_action');
        echo $this->Form->create('AttributeSet', array(
            'class' => 'form-horizontal',
        ));
?>
<div class="user-fill attibute-set-form">
	<div class="row mt30 mb30">
		<div class="col-sm-5">
			<?php 
		            echo $this->Form->label('target', __('Attribute Set'));
		            echo $this->Form->select('target', $targets, array(
			    		'multiple' => true,
			    		'id' => 'options-target',
		    		));
		    ?>
		</div>
		<div class="col-sm-2">
			<div class="mt60">
				<?php
			            echo $this->Html->link($this->Rumahku->icon('rv4-arrow-left').__('Add'), '#', array(
			            	'escape' => false,
			                'class'=> 'btn green mb10 multiple-options',
			                'data-target' => '#options-target',
			                'data-default' => '#options-default',
			            ));
			            echo $this->Html->link($this->Rumahku->icon('rv4-arrow-right').__('Remove'), '#', array(
			            	'escape' => false,
			                'class'=> 'btn default multiple-options',
			                'data-target' => '#options-default',
			                'data-default' => '#options-target',
			            ));
		        ?>
			</div>
		</div>
		<div class="col-sm-5">
			<?php 
		            echo $this->Form->label('target', __('Choose Attribute'));
		            echo $this->Form->select('options', $defaults, array(
			    		'multiple' => true,
			    		'id' => 'options-default',
		    		));
		    ?>
	    </div>
	</div>
</div>
<div class="action-group bottom">
    <div class="btn-group floright">
        <?php
            echo $this->Form->button(__('Save'), array(
                'type' => 'submit', 
                'class'=> 'btn blue btn-multiple-select',
                'data-target' => '#options-target',
            ));

            echo $this->Html->link(__('Back'), $urlBack, array(
                'class'=> 'btn default',
            ));
        ?>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>