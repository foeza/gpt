<div class="info-wrapper">
	<?php 
			echo $this->Html->tag('h1', __('Informasi Project'), array(
				'class' => 'info-title',
			));
	?>
	<div class="row">
		<?php 
				echo $this->Rumahku->buildInputGroup('name', __('Nama Project'), array(
					'placeholder' => __('Berikan nama project'),
					'divClass' => 'col-sm-8 pr0',
					'error' => false,
				));
				echo $this->Rumahku->buildInputGroup('project_date', __('Tgl CRM'), array(
					'placeholder' => __('Tanggal project dimulai'),
					'divClass' => 'col-sm-4 pl0',
					'inputClass' => 'datepicker',
					'error' => false,
				));
		?>
	</div>
</div>
<div class="info-wrapper">
	<?php 
			echo $this->Html->tag('h1', __('Informasi Klien'), array(
				'class' => 'info-title',
			));
			echo $this->element('blocks/crm/forms/crm_client_buyer', array(
				'mandatory' => $mandatory,
				'error' => false,
			));
	?>
</div>