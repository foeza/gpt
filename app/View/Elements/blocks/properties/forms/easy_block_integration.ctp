<?php		
		$options	     = empty($options) ? array() : $options;
		$label_class	 = Common::hashEmptyField($options, 'label_class', 'col-sm-4 col-md-3 no-pright');
		$value_class	 = Common::hashEmptyField($options, 'value_class', 'col-sm-8 col-md-9 no-pleft');

		// layout toggle sync r123
		$allow_sync = $this->Rumahku->callAllowSync($this->data);

		if ($allow_sync) {

?>

		<div class="row mb20">
			<div class="col-xs-12">
				<?php
						echo($this->Html->tag('h3', __('Sync Properti'), array(
							'class' => 'custom-heading', 
						)));

						$message = $this->Html->tag('p', __('Info: Data properti yang lengkap menunjang mempercepat proses sinkronisasi, pastikan bahwa data properti sudah lengkap"'));
						echo $this->Html->tag('div', $message, array(
							'class' => 'info-full alert mb20', 
						));
				?>

				<div class="form-group">
					<div class="row">
						<div class="<?php echo($label_class); ?>">
							<?php
									echo($this->Html->tag('label', __('Sync Properti ke Rumah123'), array(
										'class' => 'control-label', 
									)));
							?>
						</div>
						<div class="<?php echo($value_class); ?>">
							<?php
									echo $this->Rumahku->checkbox('UserIntegratedSyncProperty.do_sync', array(
										'mt' => 'mt10',
										'checked' => 1,
									));
							?>
						</div>
					</div>
				</div>

			</div>
		</div>

<?php
		}
?>