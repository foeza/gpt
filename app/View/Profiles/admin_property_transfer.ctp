<?php

	$records			= empty($records) ? array() : $records;
	$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
	$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
	$priceOptions		= Configure::read('Global.Data.price_options');

	$searchUrl = array(
		'admin'			=> true,
		'controller'	=> 'profiles',
		'action'		=> 'search',
		'property_transfer',
	);

	$optionsStatus = array(
		'active'	=> __('Aktif'),
		'inactive'	=> __('Non Aktif'),
	);

	$dataColumns = array(
		'checkall' => array(
			'name'		=> $this->Rumahku->buildCheckOption('Property'),
			'class'		=> 'tacenter',
			'filter'	=> 'default',
		),
		'title' => array(
			'name'			=> __('Judul'),
			'field_model'	=> 'Property.title',
			'filter'		=> 'text',
		),
		'property_action' => array(
			'name'			=> __('Status'),
			'field_model'	=> 'Property.property_action_id',
			'width'			=> '80px;',
			'filter'		=> array(
				'type'		=> 'select',
				'empty'		=> __('Semua'),
				'options'	=> $propertyActions,
			),
		),
		'typeid' => array(
			'name'			=> __('Tipe'),
			'field_model'	=> 'Property.property_type_id',
			'width'			=> '80px;',
			'filter'		=> array(
				'type'		=> 'select',
				'empty'		=> __('Semua'),
				'options'	=> $propertyTypes,
			),
		),
		'price' => array(
			'name'			=> __('Harga'),
			'field_model'	=> 'Property.price_measure',
			'width'			=> '150px;',
			'filter'		=> array(
				'type'		=> 'select', 
				'empty'		=> __('Semua'), 
				'options'	=> $priceOptions, 
			),
			'display'		=> false,
		),
		'modified' => array(
			'name'			=> __('Diubah'),
			'field_model'	=> 'Property.modified',
			'filter'		=> 'daterange',
		//	'display'		=> false,
		),
		'date' => array(
			'name'			=> __('Dibuat'),
			'field_model'	=> 'Property.created',
			'filter'		=> 'daterange',
			'display'		=> false,
		),
		'action' => false,
	);

	$showHideColumn	= $this->Rumahku->_generateShowHideColumn($dataColumns, 'show-hide' );
	$fieldColumn	= $this->Rumahku->_generateShowHideColumn($dataColumns, 'field-table', array(
		'thead'			=> true,
		'table_ajax'	=> true,
		'sortOptions'	=> array(
			'ajax' => true,
		),
	));

	echo($this->Form->create('Search', array(
		'id'	=> 'transfer-form', 
		'url'	=> $searchUrl,
		'class'	=> 'form-target form-table-search',
	)));

	echo($this->element('blocks/common/forms/search/backend', array(
		'_form'					=> false,
		'with_action_button'	=> false,
		'new_action_button'		=> true,
		'fieldInputName'		=> 'search',
		'sorting'				=> array(
			'buttonCustom' => array(
				'text'	=> __('Transfer').$this->Html->tag('span', '', array('class' => 'check-count-target')),
				'url'	=> array(
					'admin'			=> true,
					'controller'	=> 'profiles',
					'action'		=> 'property_transfer',
				),
				'options' => array(
					'class'			=> 'submit-custom-form btn blue',
					'data-form'		=> '#transfer-form', 
					'data-alert'	=> __('Anda yakin ingin mentransfer data terpilih?'),
				),
				'frameOptions' => array(
					'class' => 'check-multiple-delete hide',
				),
			),
			'options' => array(
				'showcolumns' => array(
					'options' => $showHideColumn,
				),
			),
		),
	)));

?>
<div id="table-property" class="table-responsive">
	<table class="table grey">
		<?php

			if($fieldColumn){
				echo($fieldColumn);
			}

		?>
	  	<tbody>
	  		<?php

				if($records){
					$authGroupID	= Configure::read('User.group_id');
					$personalWebURL	= Configure::read('User.data.UserConfig.personal_web_url');

  					foreach($records as $record){
		  				$recordID	= Common::hashEmptyField($record, 'Property.id');
		  				$userID		= Common::hashEmptyField($record, 'Property.user_id');
						$title		= Common::hashEmptyField($record, 'Property.title');
						$slug		= Common::hashEmptyField($record, 'Property.slug');
						$mlsID		= Common::hashEmptyField($record, 'Property.mls_id');
						$price		= Common::hashEmptyField($record, 'Property.price_measure');
						$created	= Common::hashEmptyField($record, 'Property.created');
						$modified	= Common::hashEmptyField($record, 'Property.modified');

						$actionName	= Common::hashEmptyField($record, 'PropertyAction.name');
						$typeName	= Common::hashEmptyField($record, 'PropertyType.name'); 

						$label		= $this->Property->getNameCustom($record);
						$slug		= $this->Rumahku->toSlug($label);
						$created	= $this->Time->niceShort($created);
						$modified	= $this->Time->niceShort($modified);
						$price		= $this->Property->getPrice($record, __('(Harga belum ditentukan)'));

						if($personalWebURL){
							$detailURL = $personalWebURL . $this->Html->url(array(
								'admin'			=> false,
								'controller'	=> 'profiles',
								'action'		=> 'property_detail',
								'mlsid'			=> $mlsID, 
								'slug'			=> $slug, 
							));

							$title = $this->Html->link($title, $detailURL, array('target' => 'blank'));
						}

						$action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
							'admin'			=> true,
							'controller'	=> 'properties',
							'action'		=> 'edit',
							$recordID,
						), array(
							'escape' => false,
						));

						echo($this->Html->tableCells(array(
							array(
								array(
							 		$this->Rumahku->buildCheckOption('Property', $recordID, 'default'),
									array(
										'class' => 'actions tacenter',
									),
								),
								$this->Rumahku->_getDataColumn($title, 'title'),
								$this->Rumahku->_getDataColumn($actionName, 'property_action'),
								$this->Rumahku->_getDataColumn($typeName, 'typeid'),
								$this->Rumahku->_getDataColumn($price, 'price'),
							//	$this->Rumahku->_getDataColumn($category, 'category_name'),
							//	$this->Rumahku->_getDataColumn($full_name, 'author'),
							//	$this->Rumahku->_getDataColumn($short_content, 'short_content'),
								//	$this->Rumahku->_getDataColumn($order, 'order', array(
							//		'class' => 'tacenter',
							//	)),
							//	$this->Rumahku->_getDataColumn($customActive, 'status', array(
							//		'class' => 'tacenter',
							//	)),
								$this->Rumahku->_getDataColumn($created, 'date'),
								$this->Rumahku->_getDataColumn($modified, 'modified'),
								'', 
							//	array(
							//		$action,
							//		array(
							//			'class' => 'actions tacenter',
							//		),
							//	),
							)
						)));
					}
				}

	  		?>
	  	</tbody>
	</table>
	<div class="filter-footer">
		<?php 

			if(empty($records)){
				echo($this->Html->tag('p', __('Data belum tersedia'), array(
					'class' => 'alert alert-warning tacenter', 
				)));
			}

		?>
	</div>
</div>
<?php

	echo($this->Form->end());
	echo($this->element('blocks/common/pagination', array(
		'_ajax' => true,
	)));

?>