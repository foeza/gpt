<div class="col-sm-2">
	<div class="excel-type excel-style-1">
        <div class="form-group">
        	<?php 
        			echo $this->Html->link($this->Rumahku->icon('rv4-doc').__('Excel'), array(
                        'export' => 'excel',
                    ), array(
                        'escape' => false,
                        'id' => 'kpr-export-excel',
        				'class' => 'btn green',
    				));
        	?>
		</div>
    </div>
</div>