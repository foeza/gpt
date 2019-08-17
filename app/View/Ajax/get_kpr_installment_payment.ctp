<?php
		$total_first_credit = !empty($total_first_credit)?$total_first_credit:0;
    	echo $this->Html->tag('span', $this->Rumahku->getCurrencyPrice($total_first_credit), array(
            'class' => 'pay-btn',
            'escape' => false
        ));
?>