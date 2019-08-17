<?php

	$currency	= Configure::read('__Site.config_currency_code');
	$currency	= $currency ? trim($currency) : NULL;
	$record		= isset($record) ? $record : NULL;
	$places		= 2;
	
	// debug($record);die();
	if($record){
	//	invoice detail
		$invoiceNumber 	  = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.invoice_number');
		$recordID 		  = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.id');
		$userID			  = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.user_id');
		
		$addon_r123 	  = Common::hashEmptyField($record, 'UserIntegratedOrder.addon_r123');
		$addon_olx 		  = Common::hashEmptyField($record, 'UserIntegratedOrder.addon_olx');

		$linkUnduh = array(
			'controller' => $this->params->controller,
			'action' => 'view_detail_checkout',
			$recordID,
			$invoiceNumber,
			'admin' => TRUE,
			'export' => 'excel'
		);

		?>
		<div class="box box-success">
			<div class="box-header with-border hidden-print">
				<?php

					$filename = __('Detail-Invoice-%s-%s', $invoiceNumber, date('Ymdhi'));

				//	echo($this->Html->div('floleft', $this->Html->tag('h3', __('INVOICE '))));
					echo($this->Html->div('action-group', $this->Html->div('btn-group floright hidden-print', 
						$this->Html->link(
							$this->Html->tag('i', '', array('class' => 'rv4-doc')).__('Unduh Dokumen'), 
							$linkUnduh,
							array('class' => 'btn default', 'escape' => FALSE)
						).
						$this->Html->link(
							$this->Html->tag('i', '', array('class' => 'rv4-print')).__('Cetak'), 
							'', 
							array(
								'data-name'	=> $filename, 
								'class'		=> 'btn default hidden-print rku-print', 
								'escape'	=> FALSE, 
							)
						)
					)));

				?>
			</div>
			<div class="box-body">
				<div id="user-detail" class="row">
					<?php
							echo $this->element('blocks/users/partner_medias/detail_invoice_header', array(
								'invoiceNumber' => $invoiceNumber,
							));
					?>
				</div>
				<div id="document-detail" class="row">
					<div class="col-xs-12 col-md-6">
						<?php
								echo $this->element('blocks/users/partner_medias/detail_order', array(
									'addon_r123' => $addon_r123,
									'addon_olx'  => $addon_olx,
								));
						?>
					</div>
					<div class="col-xs-12 col-md-6">
						<?php
								echo $this->element('blocks/users/partner_medias/detail_invoice', array(
									'invoiceNumber' => $invoiceNumber,
									'currency'   => $currency,
									'places' 	 => $places,
									'addon_r123' => $addon_r123,
									'addon_olx'  => $addon_olx,
								));
						?>
					</div>
				</div>
			</div>
			<div class="box-footer show-print">
				<div class="row">
					<div class="col-sm-12">
						<?php

							$fullname = Configure::read('User.data.full_name');
							echo($this->Html->tag('strong', __('Printed By : %s', $fullname)));
							echo($this->Html->tag('strong', date('d/m/Y H:i'), array('class' => 'floright')));

						?>
					</div>
				</div>
			</div>
			<div class="box-footer hidden-print">
				<div class="row">
					<div class="col-sm-12">
						<div class="action-group">
							<div class="btn-group floright">
								<?php

									echo $this->Html->link(__('Kembali'), array(
										'controller' => 'users',
										'action' => 'list_registrant',
										'admin' => true,
									), array(
										'class' => 'btn default'
									));

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

	}
	else{
		echo($this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border')));
	}

?>