<?php

		$modelName		 = 'Search';
		$propertyTypes	 = empty($propertyTypes) ? NULL : $propertyTypes;
		$propertyActions = empty($propertyActions) ? NULL : $propertyActions;
		$options	     = empty($options) ? NULL : $options;
		$title		     = $this->Rumahku->filterEmptyField($options, 'title', NULL, 'Pencarian Properti');

		$globalVars		= empty($_global_variable) ? NULL : $_global_variable;
		$roomOptions	= $this->Rumahku->filterEmptyField($globalVars, 'room_options');
		$lotOptions		= $this->Rumahku->filterEmptyField($globalVars, 'lot_options');
		$priceOptions	= $this->Rumahku->filterEmptyField($globalVars, 'price_options');

		$formClass = 'adv-search-form';

?>
<section class="no-pad section-light section-both-shadow locations-trigger">
	<?php
		// title search
		// echo $this->Html->div('container',
		// 	$this->Html->div('row',
  //                       $this->Html->div('col-xs-12 col-sm-9',
  //                           $this->Html->tag('h1', $title, array('class' => 'fontsize30'))
  //                       ).
  //                       $this->Html->div('col-xs-12',$this->Html->div('title-separator-primary',''))
  //       ));

		// BEGIN SEARCH

		echo($this->Form->create($modelName, array(
			'class'	=> $formClass, 
			'role'	=> 'form', 
			'url'	=> array(
				'admin'			=> FALSE, 
				'controller'	=> 'properties',
				'action'		=> 'search',
				'find',
			), 
			'inputDefaults'	=> array(
				'class'		=> 'form-control', 
				'required'	=> FALSE, 
				'div'		=> array(
					'class' => 'form-group', 
				), 
			),
		)));

?>
	<div id="simple-search" class="adv-search-cont">
		<div class="container">
			<div class="row tab-content">
				<div class="col-xs-12 adv-search-outer">
<?php 
					echo $this->Html->div('col-md-2', 
						$this->Html->div('form-group title-search', $title
					));

					echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.property_action', $modelName), array(
						// 'label'		=> __('Jenis Properti'),
						'label'	=> false, 
						'options'	=> $propertyActions,
						'class'     => 'form-control sold',
						'div' => array(
							'class' => 'form-group'
						),
					)));

		            echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.typeid', $modelName), array(
						'id'		=> 'propertyType',
						// 'label'		=> __('Tipe Properti'), 
						'label'	=> false, 
						'empty'		=> __('Semua'),
						'options'	=> $propertyTypes,
						'class'     => 'form-control clearit',
						'div' => array(
							'class' => 'form-group'
						), 
					)));

					// echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.property_status_id', $modelName), array(
					// 	'label'		=> __('Status Kategori'), 
					// 	'empty'		=> __('Pilih Status'),
					// 	'options'	=> $categoryStatus,
					// 	'class'     => 'form-control clearit',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	), 
					// )));

					echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.region', $modelName), array(
						'type'	=> 'select',
						// 'label'	=> __('Provinsi'), 
						'label'	=> false, 
						'empty'	=> __('Semua'),
						'class'     => 'form-control clearit regionId',
						'div' => array(
							'class' => 'form-group'
						),
					)));

					echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.city', $modelName), array(
						'type'	=> 'select',
						// 'label'	=> __('City'), 
						'label'	=> false, 
						'empty'	=> __('Semua'),
						'class'     => 'form-control clearit cityId',
						'div' => array(
							'class' => 'form-group'
						), 
					)));

					// echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.subarea', $modelName), array(
					// 	'type'	=> 'select',
					// 	'label'	=> __('Area'), 
					// 	'empty'	=> __('Semua'),
					// 	'class'     => 'form-control clearit subareaId',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	),
					// )));

					// echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.price', $modelName), array(
					// 	'label'		=> __('Range Harga'), 
					// 	'empty'		=> __('Pilih Range Harga'),
					// 	'options'	=> $priceOptions,
					// 	'class'     => 'form-control clearit',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	), 
					// )));

					// echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.beds', $modelName), array(
					// 	'id'		=> 'beds',
					// 	'label'		=> __('Kamar Tidur'), 
					// 	'empty'		=> __('Semua'),
					// 	'options'	=> $roomOptions,
					// 	'class'     => 'form-control clearit',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	), 
					// )));

					// echo $this->Html->div('col-md-2', $this->Form->input(sprintf('%s.baths', $modelName), array(
					// 	'id'		=> 'baths',
					// 	'label'		=> __('Kamar Mandi'), 
					// 	'empty'		=> __('Semua'),
					// 	'options'	=> $roomOptions,
					// 	'class'     => 'form-control clearit',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	), 
					// )));

					// echo $this->Html->div('col-md-3', $this->Form->input(sprintf('%s.lot_size', $modelName), array(
					// 	'id'		=> 'lotSizeId',
					// 	'label'		=> __('Luas Tanah'), 
					// 	'empty'		=> __('Pilih Luas Tanah'),
					// 	'options'	=> $lotOptions,
					// 	'class'     => 'form-control clearit',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	), 
					// )));

					// echo $this->Html->div('col-md-3', $this->Form->input(sprintf('%s.building_size', $modelName), array(
					// 	'id'		=> 'buildingSizeId',
					// 	'label'		=> __('Luas Bangunan'), 
					// 	'empty'		=> __('Pilih Luas Bangunan'),
					// 	'options'	=> $lotOptions,
					// 	'class'     => 'form-control clearit',
					// 	'div' => array(
					// 		'class' => 'form-group'
					// 	), 
					// )));
					echo $this->Html->div('col-md-2',
							$this->Form->button(__('Search'), array(
				                'type' => 'submit', 
				                'class' => 'form__submit btn btn-primary btn-block',
		            )));
?>
				</div>
			</div>

		</div>
	</div>
	<?php 
        echo $this->Form->end();
	?>
</section>