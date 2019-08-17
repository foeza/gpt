<?php

	$record		= empty($record) ? array() : $record;
	$histories	= empty($histories) ? array() : $histories;
	$histories	= Common::hashEmptyField($record, 'UserHistory', $histories);

	if($record){
		$options		= empty($options) ? array() : $options;
		$historyOptions	= Common::config('Global.Data.history_options', array());

		?>
		<div class="table-responsive">
			<table class="table grey">
				<?php

					$columns = array(
						'type' => array(
							'name'			=> __('Keterangan'),
							'attributes'	=> array('width' => 250), 
						),
						'principle' => array(
							'name' => __('Principal'),
						),
						'company' => array(
							'name' => __('Perusahaan'),
						),
						'created' => array(
							'name'			=> __('Periode'),
							'attributes'	=> array('width' => 200), 
						),
						'duration' => array(
							'name'			=> __('Masa Menjabat'),
							'attributes'	=> array('width' => 200), 
						),
					);

					echo($this->Rumahku->_generateShowHideColumn($columns, 'field-table', array(
						'thead'			=> true,
						'table_ajax'	=> true,
						'no_reset'		=> true, 
					//	'sortOptions'	=> array(
					//		'ajax' => true, 
					//	),
					)));

				?>
				<tbody>
					<?php

						if($histories){
							$dateOptions = array(
								'short'	=> true, 
								'type'	=> 'day', 
							//	'time'	=> 'H:i', 
							//	'zone'	=> false, 
							);

							$currentDate = date('Y-m-d H:i:s');

							foreach($histories as $key => $history){
								$historyID			= Common::hashEmptyField($history, 'UserHistory.id');
								$historyType		= Common::hashEmptyField($history, 'UserHistory.type', '');
								$historyCreated		= Common::hashEmptyField($history, 'UserHistory.created', '');
								$historyModified	= Common::hashEmptyField($history, 'UserHistory.modified', '');
								$historyDuration	= Common::hashEmptyField($history, 'UserHistory.duration', '');

								$groupID			= Common::hashEmptyField($history, 'UserHistory.Group.id');
								$groupName			= Common::hashEmptyField($history, 'UserHistory.Group.name', 'N/A');

								$principleID		= Common::hashEmptyField($history, 'UserHistory.Principle.id');
								$principleName		= Common::hashEmptyField($history, 'UserHistory.Principle.full_name', 'N/A');

								$companyID			= Common::hashEmptyField($history, 'UserHistory.Principle.UserCompany.id');
								$companyName		= Common::hashEmptyField($history, 'UserHistory.Principle.UserCompany.name', 'N/A');

								$historyTypeName	= Common::hashEmptyField($historyOptions, $historyType, '');

								if($historyDuration && $historyType != 'resign'){
									$durationFrom	= Common::hashEmptyField($history, 'UserHistory.duration_from', false);
									$durationTo		= Common::hashEmptyField($history, 'UserHistory.duration_to', $currentDate);
									$historyCreated	= Common::getCombineDate($durationFrom, $durationTo, 'N/A', '');
								}
								else{
									$historyCreated		= $historyCreated ? $this->Rumahku->getIndoDateCutom($historyCreated, $dateOptions) : '';
									$historyModified	= $historyModified ? $this->Rumahku->getIndoDateCutom($historyModified, $dateOptions) : '';
									$historyDuration	= 'N/A';
								}

								$groupContent = '';

								if($historyType == 'promotion'){
									$oldData		= Common::hashEmptyField($history, 'UserHistory.old_data', '');
									$oldData		= unserialize($oldData);
									$oldGroupID		= Common::hashEmptyField($oldData, 'Group.id', '');
									$oldGroupName	= Common::hashEmptyField($oldData, 'Group.name', 'N/A');

									$groupContent = __('%s > %s', $oldGroupName, $groupName);
								}
								else if($historyType != 'update'){
									$groupContent = $groupName;
								}

								$groupContent = $groupContent ? $this->Html->tag('small', $groupContent, array(
									'class' => 'disblock', 
								)) : $groupContent;

								$historyTypeName = __('%s %s', $historyTypeName, $groupContent);

							//	b:set action ======================================================================

								$url		= array($historyID);
								$urlInfo	= array_merge($url, array(
									'action' => 'info',
									'user_id' => $recordID,
								));

							//	e:set action ======================================================================

								echo $this->Html->tableCells(array(
									array(
										$this->Rumahku->_getDataColumn($historyTypeName, 'type'),
										$this->Rumahku->_getDataColumn($principleName, 'principle'),
										$this->Rumahku->_getDataColumn($companyName, 'company'),
										$this->Rumahku->_getDataColumn($historyCreated, 'created'),
										$this->Rumahku->_getDataColumn($historyDuration, 'duration'),
									), 
								));
							}
						}

					?>
				</tbody>
			</table>
			<div class="filter-footer">
				<?php 

					if(empty($histories)){
						echo($this->Html->tag('p', __('Data belum tersedia'), array(
							'class' => 'alert alert-warning tacenter', 
						)));
					}

				?>
			</div>
		</div>
		<?php

		if($histories){
			echo($this->Html->tag('div', $this->element('blocks/common/pagination', array(
				'_ajax' => true,
			)), array(
				'class' => 'mt15 mb15', 
			)));
		}
	}

?>