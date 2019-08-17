<?php

	$authUserID			= Configure::read('User.group_id');
	$controller			= $this->params->controller;
	$action				= $this->params->action;
	$records			= empty($records) ? array() : $records;
	$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
	$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
	$years				= empty($years) ? array() : $years;
	$months				= empty($months) ? array() : $months;
	$activeTab			= empty($activeTab) ? false : $activeTab;
	$tabItems			= array();
	$currentActionID	= Common::hashEmptyField($this->params->named, 'actionid', 1);
	$currentTypeID		= Common::hashEmptyField($this->params->named, 'typeid', 1);

	echo($this->Form->create('Search', array(
		'id'	=> 'filter-form', 
		'class'	=> 'form-target form-table-search',
		'url'	=> array_replace_recursive(array(
			'admin'			=> true, 
			'controller'	=> $controller, 
			'action'		=> 'search', 
			'price_movement'
		), $this->params->named),
	)));

	echo($this->element('blocks/common/forms/pushstate_url'));
	echo($this->Form->hidden('Search.typeid', array(
		'value' => $currentTypeID, 
	)));

?>
<div class="container-fluid mb30">
	<div class="row">
		<div class="col-md-6 no-pleft">
			<?php

				echo($this->Html->link(__('Generate'), array_replace_recursive(array(
					'admin'			=> true, 
					'controller'	=> $controller, 
					'action'		=> 'generate_rank', 
					'referer'		=> $action, 
				), $this->params->named), array(
					'confirm'	=> 'Apa Anda yakin ingin meng-generate data ? (Data akan diperbarui sesuai dengan data saat ini)', 
					'class'		=> 'btn bg green btn-lg floleft', 
					'escape'	=> false, 
				)));

			?>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-4 no-pright">
					<?php

						$options = Hash::combine($propertyActions, '{n}.PropertyAction.id', '{n}.PropertyAction.name');

						echo($this->Form->input('Search.actionid', array(
							'options'	=> $options, 
							'default'	=> 1, 
							'label'		=> false, 
							'empty'		=> false, 
							'div'		=> 'form-group', 
							'class' 	=> 'fullwidth ajax-change',

						//	'data-wrapper-write' => '.order-package',
							'data-form'			=> '#filter-form',
							'data-loadingbar'	=> 'true', 
							'data-pushstate'	=> 'true',
							'data-url'			=> $this->Html->url(array(
								'admin'			=> true, 
								'controller'	=> $controller, 
								'action'		=> 'search', 
								'price_movement', 
							)),
						)));

					?>
				</div>
				<div class="col-md-4 no-pright">
					<?php

						echo($this->Form->input('Search.period_month', array(
							'options'	=> $months, 
							'default'	=> date('m'), 
							'label'		=> false, 
							'empty'		=> false, 
							'div'		=> 'form-group', 
							'class' 	=> 'fullwidth ajax-change',

						//	'data-wrapper-write' => '.order-package',
							'data-form'			=> '#filter-form',
							'data-loadingbar'	=> 'true', 
							'data-pushstate'	=> 'true',
							'data-url'			=> $this->Html->url(array(
								'admin'			=> true, 
								'controller'	=> $controller, 
								'action'		=> 'search', 
								'price_movement', 
							)),
						)));

					?>
				</div>
				<div class="col-md-4 no-pright">
					<?php

						echo($this->Form->input('Search.period_year', array(
							'options'	=> $years, 
							'default'	=> date('Y'), 
							'label'		=> false, 
							'empty'		=> false, 
							'div'		=> 'form-group', 
							'class' 	=> 'fullwidth ajax-change',

						//	'data-wrapper-write' => '.order-package',
							'data-form'			=> '#filter-form',
							'data-loadingbar'	=> 'true', 
							'data-pushstate'	=> 'true',
							'data-url'			=> $this->Html->url(array(
								'admin'			=> true, 
								'controller'	=> $controller, 
								'action'		=> 'search', 
								'price_movement', 
							)),
						)));

					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php

	echo($this->Form->end());

?>
<div class="crm agent-rank">
	<?php

		if($propertyTypes){
			foreach($propertyTypes as $key => $propertyType){
				$typeID		= Common::hashEmptyField($propertyType, 'PropertyType.id');
				$typeSlug	= Common::hashEmptyField($propertyType, 'PropertyType.slug');
				$typeName	= Common::hashEmptyField($propertyType, 'PropertyType.name');
				$tabItems	= Hash::insert($tabItems, $typeSlug, array(
					'text'	=> __($typeName),
					'url'	=> array_replace_recursive($this->params->named, array(
						'admin'			=> true, 
						'controller'	=> $controller, 
						'action'		=> 'price_movement', 
						'typeid'		=> $typeID, 
					)),
				));

				if($typeID == $currentTypeID){
					$activeTab = $typeSlug;
				}
			}

			$typeSlugs = Hash::extract($propertyTypes, '{n}.PropertyType.slug');

			if(empty($activeTab) && $typeSlugs){
				$activeTab = array_shift($typeSlugs);
			}

			echo($this->element('blocks/common/tab', array(
				'active'	=> $activeTab, 
				'items'		=> $tabItems, 
			)));
		}

	?>
	<div class="detail-project-content">
		<div class="project-table">
			<div class="detail-project-table">
				<?php

					$inactiveName = Hash::extract($propertyActions, sprintf('{n}.PropertyAction[id=%s].inactive_name', $currentActionID));
					$inactiveName = array_shift($inactiveName);

					$dataColumns = array(
						'region' => array(
							'name'			=> __('Provinsi'), 
							'field_model'	=> 'Region.name',
							'width'			=> '150px',
							'filter'		=> 'text', 
						),
						'city' => array(
							'name'			=> __('Kota'), 
							'field_model'	=> 'City.name',
							'width'			=> '150px',
							'filter'		=> 'text', 
						),
						'subarea' => array(
							'name'			=> __('Area'), 
							'field_model'	=> 'Subarea.name',
							'width'			=> '150px',
							'filter'		=> 'text', 
						),
						'price_measure_min' => array(
							'name'			=> __('Termurah'), 
							'field_model'	=> 'AgentRank.price_measure_min',
							'width'			=> '150px',
							'class'			=> 'taright',
							'filter'		=> 'default', 
						),
						'price_measure_max' => array(
							'name'			=> __('Termahal'), 
							'field_model'	=> 'AgentRank.price_measure_max',
							'width'			=> '150px',
							'class'			=> 'taright',
							'filter'		=> 'default', 
						),
						'price_measure_average' => array(
							'name'			=> __('Rata-rata'), 
							'field_model'	=> 'AgentRank.price_measure_average',
							'width'			=> '150px',
							'class'			=> 'taright',
							'filter'		=> 'default', 
						),
					//	'sold_price_measure_min' => array(
					//		'name'			=> __('%s Termurah', $inactiveName), 
					//		'field_model'	=> 'AgentRank.sold_price_measure_min',
					//		'width'			=> '150px',
					//		'class'			=> 'taright',
					//		'filter'		=> 'default', 
					//	),
					//	'sold_price_measure_max' => array(
					//		'name'			=> __('%s Termahal', $inactiveName), 
					//		'field_model'	=> 'AgentRank.sold_price_measure_max',
					//		'width'			=> '150px',
					//		'class'			=> 'taright',
					//		'filter'		=> 'default', 
					//	),
					//	'sold_price_measure_average' => array(
					//		'name'			=> __('%s Rata-rata', $inactiveName), 
					//		'field_model'	=> 'AgentRank.sold_price_measure_average',
					//		'width'			=> '150px',
					//		'class'			=> 'taright',
					//		'filter'		=> 'default', 
					//	),
					);

					$showHideColumn	= $this->Rumahku->_generateShowHideColumn($dataColumns, 'show-hide');
					$fieldColumn	= $this->Rumahku->_generateShowHideColumn($dataColumns, 'field-table', array(
						'thead'			=> true,
						'table_ajax'	=> true,
						'no_clear_link'	=> true,
						'sortOptions'	=> array(
							'ajax' => true,
						),
					));

					echo($this->Form->create('Search', array(
						'class'	=> 'form-target form-table-search',
						'url'	=> array_replace_recursive(array(
							'admin'			=> true, 
							'controller'	=> $controller, 
							'action'		=> 'search', 
							'price_movement'
						), $this->params->named),
					)));

				//	$sorting = array(
				//		'options' => array(
				//			'showcolumns' => array(
				//				'options' => $showHideColumn,
				//			),
				//		),
				//	);

				//	echo($this->element('blocks/common/forms/search/backend', array(
				//		'_form' => false,
				//		'with_action_button'	=> false,
				//		'new_action_button'		=> true,
				//		'sorting'				=> $sorting,
				//	)));

				?>
				<div class="table-responsive">
					<table class="table grey">
						<?php

							if($fieldColumn){
								echo($fieldColumn);
							}

						?>
						<tbody>
							<?php

								if($records){
									$savePath	= Configure::read('__Site.profile_photo_folder');
									$page		= Common::hashEmptyField($this->params->paging, 'User.page', 1);
									$limit		= Common::hashEmptyField($this->params->paging, 'User.limit');
									$counter	= ($limit * ($page - 1)) + 1;

									foreach($records as $key => $record){
										$regionID		= Common::hashEmptyField($record, 'Region.id');
										$regionSlug		= Common::hashEmptyField($record, 'Region.slug');
										$regionName		= Common::hashEmptyField($record, 'Region.name');
										$cityID			= Common::hashEmptyField($record, 'City.id');
										$citySlug		= Common::hashEmptyField($record, 'City.slug');
										$cityName		= Common::hashEmptyField($record, 'City.name');
										$subareaID		= Common::hashEmptyField($record, 'Subarea.id');
										$subareaSlug	= Common::hashEmptyField($record, 'Subarea.slug');
										$subareaName	= Common::hashEmptyField($record, 'Subarea.name');

										$parentID					= Common::hashEmptyField($record, 'AgentRank.parent_id');
										$propertyCount				= Common::hashEmptyField($record, 'AgentRank.property_count', 0);
										$priceMeasure				= Common::hashEmptyField($record, 'AgentRank.price_measure', 0);
										$priceMeasureMin			= Common::hashEmptyField($record, 'AgentRank.price_measure_min', 0);
										$priceMeasureMax			= Common::hashEmptyField($record, 'AgentRank.price_measure_max', 0);
										$priceMeasureAverage		= Common::hashEmptyField($record, 'AgentRank.price_measure_average', 0);

										$soldPropertyCount			= Common::hashEmptyField($record, 'AgentRank.sold_property_count', 0);
										$soldPriceMeasure			= Common::hashEmptyField($record, 'AgentRank.sold_price_measure', 0);
										$soldPriceMeasureMin		= Common::hashEmptyField($record, 'AgentRank.sold_price_measure_min', 0);
										$soldPriceMeasureMax		= Common::hashEmptyField($record, 'AgentRank.sold_price_measure_max', 0);
										$soldPriceMeasureAverage	= Common::hashEmptyField($record, 'AgentRank.sold_price_measure_average', 0);

										$priceMeasure				= $this->Rumahku->getCurrencyPrice($priceMeasure);
										$priceMeasureMin			= $this->Rumahku->getCurrencyPrice($priceMeasureMin);
										$priceMeasureMax			= $this->Rumahku->getCurrencyPrice($priceMeasureMax);
										$priceMeasureAverage		= $this->Rumahku->getCurrencyPrice($priceMeasureAverage);

										$soldPriceMeasure			= $this->Rumahku->getCurrencyPrice($soldPriceMeasure);
										$soldPriceMeasureMin		= $this->Rumahku->getCurrencyPrice($soldPriceMeasureMin);
										$soldPriceMeasureMax		= $this->Rumahku->getCurrencyPrice($soldPriceMeasureMax);
										$soldPriceMeasureAverage	= $this->Rumahku->getCurrencyPrice($soldPriceMeasureAverage);

									//	$counterClass = $propertyCount || $soldPropertyCount ? 'green' : '';
									//	$counterLabel = $this->Html->tag('span', $rank, array(
									//		'class' => sprintf('rank-counter centered %s', $counterClass), 
									//	));

										$content = array(
											$this->Rumahku->_getDataColumn($regionName, 'region', array('nowrap' => true)),
											$this->Rumahku->_getDataColumn($cityName, 'city', array('nowrap' => true)),
											$this->Rumahku->_getDataColumn($subareaName, 'subarea', array('nowrap' => true)),
										//	$this->Rumahku->_getDataColumn($propertyCount, 'property_count', array('class' => 'taright')),
											$this->Rumahku->_getDataColumn($priceMeasureMin, 'price_measure_min', array('class' => 'taright')),
											$this->Rumahku->_getDataColumn($priceMeasureMax, 'price_measure_max', array('class' => 'taright')),
											$this->Rumahku->_getDataColumn($priceMeasureAverage, 'price_measure_average', array('class' => 'taright')),
										//	$this->Rumahku->_getDataColumn($propertyCount, 'sold_property_count', array('class' => 'taright')),
										//	$this->Rumahku->_getDataColumn($soldPriceMeasureMin, 'sold_price_measure_min', array('class' => 'taright')),
										//	$this->Rumahku->_getDataColumn($soldPriceMeasureMax, 'sold_price_measure_max', array('class' => 'taright')),
										//	$this->Rumahku->_getDataColumn($soldPriceMeasureAverage, 'sold_price_measure_average', array('class' => 'taright')),
										);

										echo($this->Html->tableCells(array($content)));
										$counter++;
									}
								}

							?>
						</tbody>
					</table>
					<div class="filter-footer">
						<?php 

							if(empty($records)){
								echo($this->Html->tag('p', __('Data belum tersedia'), array(
									'class' => 'alert alert-warning tacenter fullwidth', 
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
			</div>
		</div>
	</div>
</div>
<?php
/*
	$authUserID		= Configure::read('User.group_id');
	$controller		= $this->params->controller;
	$records		= empty($records) ? array() : $records;
	$propertyTypes	= empty($propertyTypes) ? array() : $propertyTypes;
	$activeTab		= empty($activeTab) ? false : $activeTab;
	$contents		= array();

	if($propertyTypes){
		foreach($propertyTypes as $key => $propertyType){
			$typeID		= Common::hashEmptyField($propertyType, 'PropertyType.id');
			$typeSlug	= Common::hashEmptyField($propertyType, 'PropertyType.slug');
			$typeName	= Common::hashEmptyField($propertyType, 'PropertyType.name');
			$contents	= Hash::insert($contents, $typeSlug, array(
				'title_tab'	=> __($typeName),
				'url_tab'	=> array(
					'admin'			=> true, 
					'controller'	=> $controller, 
					'action'		=> 'price_movement', 
					'typeid'		=> $typeID, 
				),
			));
		}

		$typeSlugs = Hash::extract($propertyTypes, '{n}.PropertyType.slug');

		if(empty($activeTab) && $typeSlugs){
			$activeTab = array_shift($typeSlugs);
		}

		echo($this->element('blocks/common/tab_link', array('active_tab' => $activeTab, 'content' => $contents)));
	}

?>
<div class="tabs-box">
	<?php

		$records		= empty($records) ? array() : $records;
		$controller		= $this->params->controller;
		$dataColumns	= array(
			'rank' => array(
				'name'			=> __('Rank'), 
			//	'field_model'	=> 'User.rank',
			),
			'full_name' => array(
				'name'			=> __('Nama Lengkap'), 
				'field_model'	=> 'User.full_name',
			),
			'sell_count' => array(
				'name'			=> __('Jumlah Listing Dijual'), 
				'field_model'	=> 'User.sell_count',
			),
			'sell_sold' => array(
				'name'			=> __('Jumlah Terjual'), 
				'field_model'	=> 'User.sell_sold',
				'filter'		=> 'default',
			),
			'rent_count' => array(
				'name'			=> __('Jumlah Listing Disewakan'), 
				'field_model'	=> 'User.rent_count',
				'display'		=> false, 
			),
			'rent_sold' => array(
				'name'			=> __('Jumlah Tersewa'), 
				'field_model'	=> 'User.rent_sold',
				'display'		=> false, 
			),
		);

		$showHideColumn	= $this->Rumahku->_generateShowHideColumn($dataColumns, 'show-hide');
		$fieldColumn	= $this->Rumahku->_generateShowHideColumn($dataColumns, 'field-table', array(
			'thead'			=> true,
			'table_ajax'	=> true,
			'no_reset'		=> true,
			'sortOptions'	=> array(
				'ajax' => true,
			),
		));

		echo($this->Form->create('Search', array(
			'class'	=> 'form-target form-table-search',
			'url'	=> array(
				'admin'			=> true, 
				'controller'	=> $controller, 
				'action'		=> 'search', 
				'groups', 
			),
		)));

		$sorting = array(
			'options' => array(
				'showcolumns' => array(
					'options' => $showHideColumn,
				),
			),
		);

		echo($this->element('blocks/common/forms/search/backend', array(
			'_form' => false,
			'with_action_button'	=> false,
			'new_action_button'		=> true,
			'sorting'				=> $sorting,
		)));

	?>
	<div class="table-responsive mt20">
		<table class="table grey">
			<?php

				if($fieldColumn){
					echo($fieldColumn);
				}

			?>
			<tbody>
				<?php

					if($records){
						$page		= Common::hashEmptyField($this->params->paging, 'User.page', 1);
						$limit		= Common::hashEmptyField($this->params->paging, 'User.limit');
						$counter	= ($page * $limit) - $limit + 1;

						foreach($records as $key => $record){
							$userID			= Common::hashEmptyField($record, 'User.user_id');
							$fullName		= Common::hashEmptyField($record, 'User.full_name');
							$companyID		= Common::hashEmptyField($record, 'User.company_id');
							$propertyCount	= Common::hashEmptyField($record, 'User.property_count', 0);
							$sellCount		= Common::hashEmptyField($record, 'User.sell_count', 0);
							$rentCount		= Common::hashEmptyField($record, 'User.rent_count', 0);
							$sellSold		= Common::hashEmptyField($record, 'User.sell_sold', 0);
							$rentSold		= Common::hashEmptyField($record, 'User.rent_sold', 0);

							$content = array(
								$this->Rumahku->_getDataColumn($counter, 'rank', array('class' => 'tacenter')),
								$this->Rumahku->_getDataColumn($fullName, 'full_name'),
								$this->Rumahku->_getDataColumn($sellCount, 'sell_count', array('class' => 'tacenter')),
								$this->Rumahku->_getDataColumn($sellSold, 'sell_sold', array('class' => 'tacenter')),
								$this->Rumahku->_getDataColumn($rentCount, 'rent_count', array('class' => 'tacenter')),
								$this->Rumahku->_getDataColumn($rentSold, 'rent_sold', array('class' => 'tacenter')),
							);

							echo($this->Html->tableCells(array($content)));
							$counter++;
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
</div>
*/