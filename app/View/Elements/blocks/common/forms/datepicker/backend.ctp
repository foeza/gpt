<div class="col-sm-3 col-xs-12 pull-right">
	<div class="datepicker-type datepicker-style-1">
        <div class="form-group">
        	<div class="relative input-group">
	        	<?php 
	        			echo $this->Form->input('date', array(
	        				'label' => false,
	        				'class' => 'form-control date-range has-side-control at-right',
	        				'div' => false,
	        				'data-event' => 'submit',
	    				));
	    				echo $this->Html->tag('div', $this->Rumahku->icon('rv4-calendar'), array(
	        				'class' => 'input-group-addon at-right icon-picker',
	    				));
	        	?>
			</div>
		</div>
    </div>
</div>