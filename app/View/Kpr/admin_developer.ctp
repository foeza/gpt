<?php 
        $urlBack = array(
            'controller' => 'kpr',
            'action' => 'index',
            'admin' => true,
        );

        $mandatory = $this->Html->tag('span',sprintf('(%s)',__('*')),array(
            'class' => 'color-red'
        ));

		echo $this->Form->create('Kpr', array(
            'type' => 'file',
			'class' => 'form-group',
            'id' => 'fast-kpr-form'
		));
?>
<div id="kpr-form" class="crm form-border-error">
    <?php 

            if( !empty($param_booking_code) ) {
                echo $this->element('blocks/kpr/developers/client');
                echo $this->element('blocks/kpr/developers/forms/project');
            } else {
                echo $this->element('blocks/kpr/developers/forms/add');
            }

            echo $this->element('blocks/kpr/forms/document_kpr');
            echo $this->element('blocks/kpr/developers/banks');
            echo $this->element('blocks/common/forms/action_custom', array(
                '_with_submit' => true,
                '_float_class' => 'floleft',
                '_button_text' => __('Lanjut'),
                '_textBack' => __('Batal'),
                '_classBack' => 'btn default floleft',
                '_button_class' => 'floright',
                '_urlBack' => $urlBack,
            ));
	?>
</div>
<?php 
		echo $this->Form->end();

        echo $this->Html->tag('div',
            $this->Html->tag('div', $this->element('blocks/common/templates/renders/dashboard'), array(
                'class' => 'dashboard-chart-loading',
            )), array(
            'class' => 'hide wrapper-chart-loading',
        ));
?>