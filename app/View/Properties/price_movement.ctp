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
<div class="agent-rank" style="background-image: url('/img/bg-rank.jpg');">
	<div class="agent-rank-container">
		<div class="agent-rank-table">
			<div class="container-fluid mt30 mb30">
				<div class="row">
					<div class="col-md-offset-6 col-md-6">
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
											'class' 	=> 'form-control ajax-change',
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
										'class' 	=> 'form-control ajax-change',
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
										'class' 	=> 'form-control ajax-change',
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
										),
										'city' => array(
											'name'			=> __('Kota'), 
											'field_model'	=> 'City.name',
											'width'			=> '150px',
										),
										'subarea' => array(
											'name'			=> __('Area'), 
											'field_model'	=> 'Subarea.name',
											'width'			=> '150px',
										),
										'price_measure_min' => array(
											'name'			=> __('Termurah'), 
											'field_model'	=> 'AgentRank.price_measure_min',
											'width'			=> '150px',
											'class'			=> 'taright',
										),
										'price_measure_max' => array(
											'name'			=> __('Termahal'), 
											'field_model'	=> 'AgentRank.price_measure_max',
											'width'			=> '150px',
											'class'			=> 'taright',
										),
										'price_measure_average' => array(
											'name'			=> __('Rata-rata'), 
											'field_model'	=> 'AgentRank.price_measure_average',
											'width'			=> '150px',
											'class'			=> 'taright',
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
										'url'	=> array_replace_recursive(array(
											'admin'			=> true, 
											'controller'	=> $controller, 
											'action'		=> 'search', 
											'price_movement'
										), $this->params->named),
									)));

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
														$regionID	= Common::hashEmptyField($record, 'Region.id');
														$regionSlug	= Common::hashEmptyField($record, 'Region.slug');
														$regionName	= Common::hashEmptyField($record, 'Region.name');
														$cityID		= Common::hashEmptyField($record, 'City.id');
														$citySlug	= Common::hashEmptyField($record, 'City.slug');
														$cityName	= Common::hashEmptyField($record, 'City.name');
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

														$content = array(
															$this->Rumahku->_getDataColumn($regionName, 'region'),
															$this->Rumahku->_getDataColumn($cityName, 'city'),
															$this->Rumahku->_getDataColumn($subareaName, 'subarea'),
															$this->Rumahku->_getDataColumn($priceMeasureMin, 'price_measure_min', array('class' => 'taright')),
															$this->Rumahku->_getDataColumn($priceMeasureMax, 'price_measure_max', array('class' => 'taright')),
															$this->Rumahku->_getDataColumn($priceMeasureAverage, 'price_measure_average', array('class' => 'taright')),
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
			</div>
		</div>
	</div>
</div>
<?php

	echo($this->Form->end());

?>