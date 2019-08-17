<?php

	$authUserID			= Configure::read('User.group_id');
	$controller			= $this->params->controller;
	$action				= $this->params->action;
	$records			= empty($records) ? array() : $records;
	$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
	$years				= empty($years) ? array() : $years;
	$months				= empty($months) ? array() : $months;
	$activeTab			= empty($activeTab) ? false : $activeTab;
	$tabItems			= array();
	$currentTypeID		= Common::hashEmptyField($this->params->named, 'typeid', 1);

//	echo($this->element('blocks/common/forms/search/backend', array(
//		'placeholder'		=> __('Cari berdasarkan Nama Lengkap'),
//		'url'				=> array(
//			'admin'			=> true,
//			'controller'	=> 'properties',
//			'action'		=> 'search',
//			'agent_rank',
//		), 
//	)));

	echo($this->Form->create('Search', array(
		'id'	=> 'period-form', 
		'class'	=> 'form-target form-table-search',
		'url'	=> array_replace_recursive(array(
			'admin'			=> true, 
			'controller'	=> $controller, 
			'action'		=> 'search', 
			'agent_rank'
		), $this->params->named),
	)));

	echo($this->element('blocks/common/forms/pushstate_url'));
	echo($this->Form->hidden('Search.typeid', array(
		'value' => $currentTypeID, 
	)));

?>
<div class="container-fluid mb30">
	<div class="row">
		<div class="col-md-8 no-pleft">
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
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-6 no-pright">
					<?php

						echo($this->Form->input('Search.period_month', array(
							'options'	=> $months, 
							'default'	=> date('m'), 
							'label'		=> false, 
							'empty'		=> false, 
							'div'		=> 'form-group', 
							'class' 	=> 'fullwidth ajax-change',

						//	'data-wrapper-write' => '.order-package',
							'data-form'			=> '#period-form',
							'data-loadingbar'	=> 'true', 
							'data-pushstate'	=> 'true',
							'data-url'			=> $this->Html->url(array(
								'admin'			=> true, 
								'controller'	=> $controller, 
								'action'		=> 'search', 
								'agent_rank', 
							)),
						)));

					?>
				</div>
				<div class="col-md-6 no-pright">
					<?php

						echo($this->Form->input('Search.period_year', array(
							'options'	=> $years, 
							'default'	=> date('Y'), 
							'label'		=> false, 
							'empty'		=> false, 
							'div'		=> 'form-group', 
							'class' 	=> 'fullwidth ajax-change',

						//	'data-wrapper-write' => '.order-package',
							'data-form'			=> '#period-form',
							'data-loadingbar'	=> 'true', 
							'data-pushstate'	=> 'true',
							'data-url'			=> $this->Html->url(array(
								'admin'			=> true, 
								'controller'	=> $controller, 
								'action'		=> 'search', 
								'agent_rank', 
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
						'action'		=> 'agent_rank', 
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

					$dataColumns = array(
						'rank' => array(
							'name'			=> __('Rank'),
							'field_model'	=> 'AgentRank.rank', 
							'width'			=> '50px',
							'class'			=> 'tacenter',
						),
						'photo' => array(
							'name'			=> __('Foto'), 
							'width'			=> '50px',
							'class'			=> 'tacenter',
						),
						'full_name' => array(
							'name'			=> __('Nama Agen'), 
							'field_model'	=> 'User.full_name',
						),
						'sold_property_count' => array(
							'name'			=> __('Jumlah Terjual'), 
							'field_model'	=> 'AgentRank.sold_property_count',
							'width'			=> '120px',
							'class'			=> 'taright',
						),
						'property_count' => array(
							'name'			=> __('Jumlah Listing'), 
							'field_model'	=> 'AgentRank.property_count',
							'width'			=> '120px',
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
							'agent_rank'
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
										$userID					= Common::hashEmptyField($record, 'User.id');
										$parentID				= Common::hashEmptyField($record, 'User.parent_id');
										$firstName				= Common::hashEmptyField($record, 'User.first_name');
										$lastName				= Common::hashEmptyField($record, 'User.last_name');
										$fullName				= Common::hashEmptyField($record, 'User.full_name');
										$photo					= Common::hashEmptyField($record, 'User.photo');
										$rank					= Common::hashEmptyField($record, 'AgentRank.rank');
										$propertyCount			= Common::hashEmptyField($record, 'AgentRank.property_count', 0);
										$soldPropertyCount		= Common::hashEmptyField($record, 'AgentRank.sold_property_count', 0);

										$propertyCount			= $this->Rumahku->getFormatPrice($propertyCount);
										$soldPropertyCount		= $this->Rumahku->getFormatPrice($soldPropertyCount);

										$photo = $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
											'save_path'	=> $savePath, 
											'src'		=> $photo, 
											'size'		=> 'pxl',
										)), array(
											'class' => 'user-photo centered',
										));

										$counterClass = $propertyCount || $soldPropertyCount ? 'green' : '';
										$counterLabel = $this->Html->tag('span', $rank, array(
											'class' => sprintf('rank-counter centered %s', $counterClass), 
										));

										$content = array(
											$this->Rumahku->_getDataColumn($counterLabel, 'rank', array('class' => 'tacenter')),
											$this->Rumahku->_getDataColumn($photo, 'photo', array('class' => 'tacenter')),
											$this->Rumahku->_getDataColumn($fullName, 'full_name'),
											$this->Rumahku->_getDataColumn($soldPropertyCount, 'sold_property_count', array('class' => 'taright')),
											$this->Rumahku->_getDataColumn($propertyCount, 'property_count', array('class' => 'taright')),
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

				//	echo($this->Form->end()); 
				//	echo($this->element('blocks/common/pagination', array(
				//		'_ajax' => true,
				//	)));

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
					'action'		=> 'agent_rank', 
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