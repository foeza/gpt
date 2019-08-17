<?php 
		$currency = Configure::read('__Site.config_currency_symbol');
		$actionId = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'id');
		$actionName = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'inactive_name');
		$payment_type = $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment', 'type');
		$step = $this->Rumahku->filterEmptyField($value, 'CrmProjectActivity', 'step');

	    // Set Build Input Form
	    $options = array(
	    	'formGroupClass' => false,
	    	'wrapperClass' => 'wrapper-input',
	        'frameClass' => false,
	        'labelClass' => false,
	        'class' => false,
	    );
?>
<div id="wrapper-modal-write" class="crm-payment-form calculator-kpr-credit">
	<?php 
		
			if( !empty($payment_type) && $step != 'payment' ) {
	?>
	<div class="col-sm-12 mt15" id="formApplyKPR">
		<div id="application-kpr">
			<div class="row">
				<div class="col-sm-8">
					<?php 
							echo $this->Html->tag('p', __('Lengkapi seluruh form dokumen pengajuan KPR dibawah ini:'));
							echo $this->element('blocks/kpr/forms/application');
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
			} else if( empty($step) || $step == 'payment' ) {
				if( $actionId != 2 ) {
					echo $this->Html->tag('div', $this->Form->label('CrmProjectPayment.type', __('Tipe Pembayaran *')).$this->Html->tag('div', $this->Html->tag('div', $this->Rumahku->buildInputForm('CrmProjectPayment.type', array_merge($options, array(
		                'label' => false,
		                'error' => false,
		                'empty' => __('- Pilih Pembayaran -'),
		                'inputClass' => 'form-control',
		            	'options' => array(
		            		'kpr' => __('KPR'),
		            		'cash' => __('Cash'),
		        		),
		        		'attributes' => array(
		        			'data-form' => '.crm-project-form',
		        			'data-wrapper-write' => '#wrapper-kpr-write',
		            		'href' => $this->Html->url(array(
		            			'controller' => 'ajax',
		            			'action' => 'crm_payment_method',
		            			'admin' => true,
		        			)),
		    			),
		            ))).$this->Rumahku->icon('rv4-angle-down', false, 'span'), array(
		            	'class' => 'select'
		            )), array(
		            	'class' => 'input-group side'
		            )).$this->Form->error('CrmProjectPayment.type'), array(
		            	'class' => 'col-sm-6 mt15'
		            ));
				}


	            if( $actionId == 2 ) {
		            echo $this->Html->tag('div', $this->Form->label('CrmProjectPayment.end_date', sprintf(__('Tgl %s *'), $actionName)).$this->Rumahku->buildInputMultiple('CrmProjectPayment.sold_date', 'CrmProjectPayment.end_date', array(
		                'label' => false,
		                'divider' => 'rv4-bold-min small',
		                'groupClass' => false,
		                'frameClass' => 'col-sm-12',
		                'inputClass' => 'datepicker',
		                'inputClass2' => 'to-datepicker',
		                'labelDivClass' => 'col-sm-12',
		                'class' => 'col-sm-5 mb0',
		                'attributes' => array(
		            		'type' => 'text',
	                	),
		            )), array(
		            	'class' => 'col-sm-12 mt15',
		           	));
		        } else {
					echo $this->Html->tag('div', $this->Form->label('CrmProjectPayment.sold_date', __('Tgl Terjual *')).$this->Html->tag('div', $this->Form->input('CrmProjectPayment.sold_date', array(
						'type' => 'text',
						'label' => false,
		                'required' => false,
		                'div' => false,
		                'error' => false,
		                'class' => 'datepicker large',
		                'placeholder' => __('Tanggal Properti Terjual'),
		            )), array(
		            	'class' => 'input-group side',
		            )).$this->Form->error('CrmProjectPayment.sold_date'), array(
		            	'class' => 'col-sm-6 mt15'
		            ));
		        }


				echo $this->Html->tag('div', $this->Rumahku->buildInputForm('CrmProjectPayment.price', array_merge($options, array(
					'type' => 'text',
	                'label' => sprintf(__('Harga %s *'), $actionName),
	                'inputClass' => 'input_price KPR-price',
	        		'textGroup' => $currency,
	        		'positionGroup' => 'left',
	            ))), array(
	            	'class' => 'col-sm-12 mt15'
	            ));

		  //       echo $this->element('blocks/crm/forms/payment_method', array(
				// 	'payment_type' => $payment_type,
				// ));
			}
   	?>
</div>