<?php
		$data = $this->request->data;

		// search principle
        $autoUrl = $this->Html->url(array(
            'controller' => 'ajax',
            'action' => 'list_company_properties',
            'admin' => false,
        ));

        $model = 'UserCompanyEbrochure';

		echo $this->Form->create($model, array(
			'type' => 'file'
		));

		echo $this->Html->div('form-group', $this->Html->div('info-full alert', $this->Html->tag('p', __('Anda dapat mengatur konten eBrosur sesuai dengan tampilan pratinjau di kanan form.'))));
?>
<div id="custom-ebrosur">
	<div class="row" id="ebrosur-box-form">
		<div class="col-sm-7">
			<?php
					echo $this->Rumahku->buildInputForm('mls_id', array(
						'frameClass' => 'col-sm-12',
			            'label' => __('Cari Dari Judul Properti'),
			            'data_url' => $autoUrl,
			            'id' => 'autocomplete',
			            'type' => 'text',
			            'autocomplete' => 'off',
			            'attributes' => array(
			            	'class' => 'property-option',
			            	'data-highlighter' => false
			            ),
			            'labelClass' => 'col-xl-2 col-sm-4 control-label taleft'
			        ));

					echo $this->element('blocks/ebrosurs/forms/ebrosur');
			?>
		</div>
		<div class="col-sm-5">
			<?php
					echo $this->Html->div('tacenter', $this->Html->tag('h2', __('PRATINJAU')));
			?>
			<div class="live-preview-banner">
				<?php
						if(!empty($_config['UserCompanyConfig']['type_custom_ebrochure']) && $_config['UserCompanyConfig']['type_custom_ebrochure'] == 'potrait'){
							echo $this->element('blocks/ebrosurs/layout_potrait');
						}else{
							echo $this->element('blocks/ebrosurs/layout_landscape');
						}
				?>
			</div>
		</div>
	</div>
	<div class="action-group bottom">
		<div class="btn-group floleft">
			<?php
					echo $this->AclLink->button(__('Lanjutkan'), array(
		                'type' => 'submit',
		                'class' => 'btn blue',
		            ));

		            echo $this->Html->link(__('Batal'), $this->here, array(
	                    'escape' => false,
	                    'class' => 'btn default',
	                ));
			?>
		</div>
	</div>
	<?php
			echo $this->Form->input('name', array(
				'type' => 'hidden',
				'label' => false,
			));
			echo $this->Form->input('phone', array(
				'type' => 'hidden',
				'label' => false,
			));
			echo $this->Form->input('property_media_id', array(
				'type' => 'hidden',
				'label' => false,
				'id' => 'property-media-id'
			));

			echo $this->Form->end();
	?>
</div>