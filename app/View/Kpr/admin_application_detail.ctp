<?php
        $document_type = Common::hashEmptyField($value, 'Kpr.document_type');
?>
<div class="crm">
    <?php 
            echo $this->element('blocks/kpr/header');

            echo $this->element('blocks/kpr/timeline');
            echo $this->element('blocks/kpr/application_banks', array(
                'notification' => 'notification',
                'note_simulation' => true,
            ));       
            echo $this->element('blocks/kpr/application_info');

            switch ($document_type) {
                case 'developer':
                    echo $this->element('blocks/kpr/detail/unit_info');
                    break;
                
                default:
                    echo $this->element('blocks/properties/info');
                    echo $this->element('blocks/kpr/application_property');
                    break;
            }
    ?>
</div>