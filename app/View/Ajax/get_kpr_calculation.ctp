<?php 
        $bankKpr = !empty($bankKpr)?$bankKpr:false;
        $bank_code = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'code', 'rumahku');
?>
<div id="data_content">
	<div id="loan-summary-content">
    	<?php 
    		echo $this->element('blocks/kpr/loan_summary', array(
    			'params' => $loan_summary
    		)); 
    	?>
    </div>
    <div id="installment-payment-content">
    	<?php 
    		echo $this->element('blocks/kpr/installment_payment', array(
    			'params' => $kpr_data
    		)); 
    	?>
    </div>
    <div id="url-kpr-share">
        <?php
                echo $this->Html->url(array(
                    'controller' => 'kpr',
                    'action' => 'share_kpr',
                    'mls_id' => (!empty($property['Property']['mls_id']) ? $property['Property']['mls_id'] : ''),
                    (!empty($id_log_kpr) ? $id_log_kpr : 0)
                ));
        ?>
    </div>
    <div id="url-kpr-excel">
        <?php
                echo $this->Html->url(array(
                    'controller' => 'btn',
                    'action' => 'kalkulator-kpr',
                    'mls_id' => (!empty($property['Property']['mls_id']) ? $property['Property']['mls_id'] : ''),
                    'logid' => (!empty($id_log_kpr) ? $id_log_kpr : 0),
                    'export' => 'excel'
                ));
        ?>
    </div>
    <div id="url-kpr-apply">
        <?php
                echo $this->Html->url(array(
                    'controller' => 'btn',
                    'action' => 'apply_kpr',
                    'bank_code' => $bank_code,
                    (!empty($property['Property']['mls_id']) ? $property['Property']['mls_id'] : ''),
                    'log_kpr_id' => (!empty($id_log_kpr) ? $id_log_kpr : 0),
                ));
        ?>
    </div>
</div>