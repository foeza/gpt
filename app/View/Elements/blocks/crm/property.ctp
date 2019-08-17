<?php 
		$i = 0;
		$data = $this->request->data;
    	$kpr = !empty($kpr)?$kpr:false;
    	$user_id = !empty($user_id)?$user_id:false;
		$isAgent = Common::isAgent();

    	$error = isset($error)?$error:true;
    	$errorMsg = $this->Form->error('CrmProject.property_id');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$crm_id = $this->Rumahku->filterEmptyField($data, 'CrmProject', 'id');
		$crm_id = $this->Rumahku->filterEmptyField($data, 'KprApplication', 'crm_project_id', $crm_id);
		$documents = $this->Rumahku->filterEmptyField($data, 'CrmProjectDocument');

		if( !empty($kpr) ) {
			$_action = 'sell';
		} else {
			$_action = '';
		}

		if( !empty($isAgent) ) {
			$user_id = Configure::read('User.id');
		}
?>
<div id="wrapper-write-crm">
	<?php
			if( !empty($user_id) ) {
	?>
	<div class="mb30">
		<div class="info-wrapper mb0">
			<?php 
					echo $this->Html->tag('h1', __('Informasi Properti (Optional)'), array(
						'class' => 'info-title',
					));

					if( !empty($data['Property']['id']) ) {
						$price = $this->Rumahku->filterEmptyField($data, 'Property', 'price_measure');
						$price = $this->Rumahku->filterEmptyField($data, 'CrmProjectPayment', 'price', $price);

						$mls_id = $this->Rumahku->filterEmptyField($data, 'Property', 'mls_id');
						$title = $this->Rumahku->filterEmptyField($data, 'Property', 'title');
						$contract_date = $this->Rumahku->filterEmptyField($data, 'Property', 'contract_date');
						$created = $this->Rumahku->filterEmptyField($data, 'Property', 'created');
						$commission = $this->Rumahku->filterEmptyField($data, 'Property', 'commission');
						$dataAsset = $this->Rumahku->filterEmptyField($data, 'PropertyAsset');

						$building_size = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'building_size');
						$lot_size = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_size');
						$lot_unit = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'slug');

			    		$lotUnitName = $this->Property->getLotUnit($lot_unit);

			    		if( !empty($commission) ) {
			    			$commission = $commission.' %';
			    		}

						$this->request->data['Property']['commission'] = $commission;
						$this->request->data['Property']['title'] = sprintf('%s, %s', $mls_id, $title);
						$this->request->data['Property']['created'] = $this->Rumahku->formatDate($created, 'd M Y');
						$this->request->data['Property']['contract_date'] = $this->Rumahku->formatDate($contract_date, 'd M Y');

						$this->request->data['PropertyAsset']['building_size'] = sprintf('%s %s', $building_size, $lotUnitName);
						$this->request->data['PropertyAsset']['lot_size'] = sprintf('%s %s', $lot_size, $lotUnitName);

						$priceFormat = $this->Property->getPrice($data);
						$this->request->data['Property']['price'] = $priceFormat;
					} else {
						$price = false;
					}
			?>
			<div class="row">
				<?php 
						echo $this->Rumahku->buildInputGroup('Property.title', __('Properti'), array(
							'placeholder' => __('Masukkan judul, lokasi dan ID properti'),
							'divClass' => 'col-sm-12',
							'errorFieldName' => 'property_id',
							'error' => $error,
							'attributes' => array(
			            		'id' => 'autocomplete',
					            'autocomplete' => 'off',
					            'data-ajax-url' => $this->Html->url(array(
					            	'controller' => 'ajax',
					            	'action' => 'get_properties',
					            	$_action,
					            	'user_id' => $user_id,
					            	'admin' => false,
				            	)),
				            	'href' => $this->Html->url(array(
					            	'controller' => 'ajax',
					            	'action' => 'get_crm_property',
					            	'crm_project_id' => $crm_id,
					            	'kpr' => $kpr,
					            	'admin' => false,
				            	)),
				            	'data-change' => 'true',
				            	'data-wrapper-write' => '#wrapper-write-crm',
				            	'data-clear' => 'true',
							),
						));
				?>
			</div>
			<div class="row">
				<?php   


						echo $this->Rumahku->buildInputGroup('PropertyType.name', __('Tipe Properti'), array(
							'placeholder' => __('Tipe Properti yang dipasarkan'),
							'divClass' => 'col-sm-4 pr0',
							'disabled' => true,
						));

						
						echo $this->Rumahku->buildInputGroup('PropertyAction.name', __('Status Properti'), array(
							'placeholder' => __('Status Properti'),
							'divClass' => 'col-sm-4 pr0 pl0',
							'disabled' => true,
						));
						echo $this->Rumahku->buildInputGroup('Property.price', __('Harga Pemasaran'), array(
							'placeholder' => __('Harga Properti yang dipasarkan'),
							'divClass' => 'col-sm-4 pl0',
							'disabled' => true,
						));
				?>
			</div>
			<div class="row">
				<?php 
						echo $this->Rumahku->buildInputGroup('PropertyAsset.building_size', __('Luas Bangunan'), array(
							'placeholder' => __('Luas Bangunan Properti'),
							'divClass' => 'col-sm-4 pr0',
							'disabled' => true,
						));
						echo $this->Rumahku->buildInputGroup('PropertyAsset.lot_size', __('Luas Tanah'), array(
							'placeholder' => __('Luas Tanah Properti'),
							'divClass' => 'col-sm-4 pr0 pl0',
							'disabled' => true,
						));
						echo $this->Rumahku->buildInputGroup('Property.commission', __('Komisi Properti'), array(
							'placeholder' => __('Komisi Properti'),
							'divClass' => 'col-sm-4 pl0',
							'disabled' => true,
						));
				?>
			</div>
			<div class="row">
				<?php 
						echo $this->Rumahku->buildInputGroup('Owner.full_name', __('Vendor'), array(
							'divClass' => 'col-sm-4 pr0',
							'placeholder' => __('Nama Vendor'),
							'disabled' => true,
						));
						echo $this->Rumahku->buildInputGroup('Property.contract_date', __('Tanggal Kontrak'), array(
							'placeholder' => __('Tanggal kontrak/kesepakatan dengan Vendor'),
							'divClass' => 'col-sm-4 pr0 pl0',
							'disabled' => true,
						));
						echo $this->Rumahku->buildInputGroup('Property.created', __('Tgl Tayang'), array(
							'placeholder' => __('Tanggal Penayangan Properti'),
							'divClass' => 'col-sm-4 pl0',
							'disabled' => true,
						));
				?>
			</div>
			<?php
					echo $this->Form->hidden('Property.id');
					echo $this->Form->hidden('Property.sold_price', array(
						'value' => $price,
						'class' => 'sold-price',
					));
			?>
		</div>
		<?php
			    if( !empty($errorMsg) ) {
			    	echo $this->Html->tag('div', $errorMsg, array(
			    		'class' => 'mt10',
			    	));
			    }
		?>
	</div>
	<?php 
				if( !empty($kpr) ) {
			        echo $this->element('blocks/kpr/forms/kpr', array(
			            'mandatory' => $mandatory,
			            'error' => false,
			        ));

			        echo $this->element('blocks/kpr/forms/document_kpr', array(
			            'mandatory' => $mandatory,
			        ));
			    }
			}
	?>
</div>