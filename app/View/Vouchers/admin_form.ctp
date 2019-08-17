<?php echo($this->Form->create('Voucher')); ?>
<?php

	$options = array(
		'frameClass'	=> 'col-sm-8',
		'labelClass'	=> 'col-xl-2 col-sm-4 control-label taright',
		'class'			=> 'relative col-sm-8 col-xl-4',
	);

	$action = $this->params->action;
	echo($this->Html->tag('h2', __('Informasi Dasar'), array('class' => 'sub-heading')));
	echo($this->element(sprintf('blocks/vouchers/forms/%s_form', $action), array('options' => $options)));

?>
<div class="row">
	<div class="col-sm-12">
		<div class="action-group bottom">
			<div class="btn-group floright">
				<?php

					echo($this->Html->link(__('Kembali'), array('action' => 'index', 'admin' => TRUE), array('class' => 'btn default')));
					echo($this->Form->button(__('Simpan'), array('id' => 'voucher-submit-button', 'type' => 'submit', 'class'=> 'btn blue')));

				?>
			</div>
		</div>
	</div>
</div>
<?php echo($this->Form->end()); ?>