<?php 
		$data = $this->request->data;
		$is_cancel = $this->Rumahku->filterEmptyField($data, 'CrmProject', 'is_cancel');

		$options = array(
            'wrapperClass' => false,
            'frameClass' => false,
            'labelClass' => false,
            'rowFormClass' => false,
            'class' => false,
        );
?>
<div class="row mb15">
	<div class="col-sm-6">
		<div class="form-group">
		    <div class="cb-custom mt0">
		        <div class="cb-checkmark">
		            <?php   
		                    echo $this->Form->input('is_cancel',array(
		                        'type' => 'checkbox',
		                        'label'=> false,
		                        'required' => false,
		                        'class' => 'trigger-toggle',
		                        'required' => false,
		                        'div' => false,
		                        'data-show' => '.form-cancel-note',
		                    ));
		                    echo $this->Form->label('is_cancel', __('Batalkan project ini'));
		            ?>
		        </div>
		    </div>
		</div>
		<?php 
		        echo $this->Html->tag('div', $this->Rumahku->buildInputForm('completed_date', array_merge($options, array(
		        	'type' => 'text',
		            'label' => __('Tgl Dibatalkan'),
					'inputClass' => 'datepicker',
		        ))), array(
		            'class' => 'form-cancel-note '.(!empty($is_cancel)?'show':''),
		        ));
		        echo $this->Html->tag('div', $this->Rumahku->buildInputForm('note', array_merge($options, array(
		            'type' => 'textarea',
		            'label' => __('Alasan Pembatalan'),
		            'attributes' => array(
		            	'rows' => 3,
	            	),
		        ))), array(
		            'class' => 'form-cancel-note '.(!empty($is_cancel)?'show':''),
		        ));
		?>
	</div>
</div>