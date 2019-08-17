<?php 
        $data = $this->request->data;
        $tag_mandatory = $this->Html->tag('span',sprintf('(%s)', '*'),array(
            'class' => 'color-red'
        ));
        $kpr_id = $this->Rumahku->filterEmptyField($data, 'Kpr', 'id');
        // $labelMandatory = !empty($labelMandatory)?$labelMandatory:$tag_mandatory;
        $kpr_application = $this->Rumahku->filterEmptyField($data, 'KprApplication');
        $kpr_application_id = !empty($kpr_application[0]['id'])?$kpr_application[0]['id']:false;
        $status_marital = !empty($kpr_application[0]['status_marital'])?$kpr_application[0]['status_marital']:false;

        $display_particular = ($status_marital == 'marital')?"block":'none';

		echo $this->Form->create('KprApplication', array(
            'type' => 'file',
			'class' => 'form-group',
		));
?>
<div id="kpr-form" class="crm form-border-error application-form locations-root">
    <?php
            echo $this->element('blocks/kpr/forms/contact',array(
                // 'labelMandatory' => $labelMandatory,
                'modelName' => 'KprApplication',
                'error' => false,
            ));

            echo $this->element('blocks/kpr/forms/address',array(
                // 'labelMandatory' => $labelMandatory,
                'modelName' => 'KprApplication',
                'error' => false,
            ));

            echo $this->element('blocks/kpr/forms/personal',array(
                // 'labelMandatory' => $labelMandatory,
                'modelName' => 'KprApplication',
                'error' => false,
            ));
    ?>
    <div id="spouse-particular" style="display:<?php echo $display_particular; ?>">
        <?php
                echo $this->element('blocks/kpr/forms/spouse_particular', array(
                    'aditionals' => '-spouse',
                ));

        ?>
    </div>
        <?php    
            echo $this->element('blocks/common/forms/action_custom', array(
                '_with_submit' => true,
                '_button_text' => __('Simpan Aplikasi'),
                '_textBack' => __('Batal'),
                '_classBack' => 'btn plain',
                '_button_class' => 'floright',
                '_urlBack' => array(
                    'controller' => 'kpr',
                    'action' => 'index',
                    'admin' => true,
                ),
            ));
        ?>
    
</div>
<?php 
		echo $this->Form->end();
?>