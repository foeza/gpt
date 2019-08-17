<?php
		$label = !empty($label) ? $label : false;
        $value = !empty($value) ? $value : false;
		$label_control = !empty($label_control) ? $label_control : false;
		$labelClass = !empty($labelClass) ? $labelClass : 'col-xl-2 col-sm-12 control-label taright';
		$valueClass = !empty($valueClass) ? $valueClass : 'col-xl-2 col-sm-12 control-label taright';
?>
<div class="col-sm-12">
    <div class="row">
        <div class="<?php echo $labelClass; ?>">
        	<label  class="<?php echo $label_control; ?>"><?php echo $label; ?></label>
        </div>   
        <div class="<?php echo $valueClass; ?>">
        	<span  class="<?php echo $label_control; ?>"><?php echo $value; ?></label>
        </div>               
    </div>
</div>