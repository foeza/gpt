<?php 
		$values = !empty($chartProperties['values'])?json_encode($chartProperties['values']):false;
		$fromDate = !empty($chartProperties['fromDate'])?$chartProperties['fromDate']:date('01 M Y');
		$toDate = !empty($chartProperties['toDate'])?$chartProperties['toDate']:date('d M Y');
		$customDate = $this->Rumahku->getCombineDate($fromDate, $toDate);
		$action_type = !empty($action_type)?$action_type : 'visitors';
		$point_tooltip = isset($point_tooltip)?$point_tooltip : false;

		if( $action_type == 'properties' ) {
			$label = __('Laporan Properti');
			
			$totalPropertySold = $this->Rumahku->filterEmptyField($chartProperties, 'totalPropertySold', false, 0);
			$totalPropertyLeased = $this->Rumahku->filterEmptyField($chartProperties, 'totalPropertyLeased', false, 0);
			$totalPropertyActive = $this->Rumahku->filterEmptyField($chartProperties, 'totalPropertyActive', false, 0);

			if( !empty($totalPropertySold) ) {
				$totalPropertySold = $this->Html->link($totalPropertySold, array(
					'controller' => 'reports',
					'action' => 'generate',
					'commissions',
					'title' => __('Properti Terjual'),
					'date_from' => $fromDate,
					'date_to' => $toDate,
					'property_action' => 1,
					'admin' => true,
				), array(
					'target' => '_blank',
				));
			}

			if( !empty($totalPropertyLeased) ) {
				$totalPropertyLeased = $this->Html->link($totalPropertyLeased, array(
					'controller' => 'reports',
					'action' => 'generate',
					'commissions',
					'title' => __('Properti Tersewa'),
					'date_from' => $fromDate,
					'date_to' => $toDate,
					'property_action' => 2,
					'admin' => true,
				), array(
					'target' => '_blank',
				));
			}

			if( !empty($totalPropertyActive) ) {
				$totalPropertyActive = $this->Html->link($totalPropertyActive, array(
					'controller' => 'reports',
					'action' => 'generate',
					'properties',
					'title' => __('Properti'),
					'date_from' => $fromDate,
					'date_to' => $toDate,
					'status' => 'active-pending',
					'admin' => true,
				), array(
					'target' => '_blank',
				));
			}

		} else if( $action_type == 'visitors' ) {
			$label = __('Laporan Pengunjung GPT');
			$total = $this->Rumahku->filterEmptyField($chartProperties, 'total', false, 0);

			if( !empty($total) ) {
				$total = $this->Html->link($total, array(
					'controller' => 'reports',
					'action' => 'generate',
					'visitors',
					'title' => __('Pengunjung'),
					'date_from' => $fromDate,
					'date_to' => $toDate,
					'admin' => true,
				), array(
					'target' => '_blank',
				));
			}
		}
		// UNDER DEVELOPMENT
?>
<div id="wrapper-chart-kpr" class="wrapper-selector" chart-point-tooltip="<?php echo $point_tooltip; ?>">
	<div class="dashbox">
		<div id="kprAppliedStat" class="chart">
			<div class="chart-head">
				<div class="row">
					<div class="col-sm-9 col-xs-10">
						<ul class="tabs">
							<?php 
									// $classActive = ( $action_type == 'properties' )?'active':false;
									// echo $this->Html->tag('li', $this->Html->link(__('Properti'), '#', array(
	        //                         	'escape' => false,
		       //                          'class' => 'kpr-chart '.$classActive,
		       //                          'url' => $this->Html->url(array(
		       //                              'controller' => 'ajax',
		       //                              'action' => 'get_dashboard_report',
		       //                              'properties',
		       //                          )),
		       //                          'chart-for' => 'properties',
		       //                      )), array(
		       //                          'role' => 'presentation',
		       //                      ));
								
									$classActive = ( $action_type == 'visitors' )?'active':false;
									echo $this->Html->tag('li', $this->Html->link(__('Pengunjung'), '#', array(
	                                	'escape' => false,
		                                'class' => 'kpr-chart '.$classActive,
		                                'url' => $this->Html->url(array(
		                                    'controller' => 'ajax',
		                                    'action' => 'get_dashboard_report',
		                                    'visitors',
		                                )),
		                                'chart-for' => 'visitors',
		                            )), array(
		                                'role' => 'presentation',
		                            ));
							?>
						</ul>
					</div>
					<div class="col-sm-3 col-xs-2">
						<div class="form-group taright">
							<?php 
									echo $this->Html->link($this->Rumahku->icon('rv4-calendar'), 'javascript:void(0);', array(
	                                	'escape' => false,
		                                'class' => 'daterange-dasboard',
		                                'title' => __('Tanggal'),
		                                'url' => $this->Html->url(array(
		                                    'controller' => 'ajax',
		                                    'action' => 'get_dashboard_report',
		                                    $action_type,
		                                )),
		                            ));
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="chart-body">
				<div id="chart_div"></div>
			</div>
			<div class="chart-foot">
				<div class="summary-applied">
					<?php 
							echo $this->Html->tag('h3', $label);
							echo $this->Html->tag('span', sprintf(__('(tanggal %s)'), $customDate));
							
							if( $action_type == 'visitors' ) {
								echo $this->Html->tag('h4', $total);
							}
					?>
				</div>
				<?php 
						if( $action_type == 'properties' ) {
				?>
				<div class="summary property-description">
					<div class="row">
						<?php 
								$itemContent = $this->Html->tag('div', '', array(
									'class' => 'item-holder green',
								));
								$itemContent .= $this->Html->tag('div', __('Properti Terjual'), array(
									'class' => 'item-description',
								));
								echo $this->Html->tag('div', $itemContent, array(
									'class' => 'item-container',
								));

								$itemContent = $this->Html->tag('div', '', array(
									'class' => 'item-holder yellow',
								));
								$itemContent .= $this->Html->tag('div', __('Properti Tersewa'), array(
									'class' => 'item-description',
								));
								echo $this->Html->tag('div', $itemContent, array(
									'class' => 'item-container',
								));
								
								$itemContent = $this->Html->tag('div', '', array(
									'class' => 'item-holder pink',
								));
								$itemContent .= $this->Html->tag('div', __('Pertambahan Properti'), array(
									'class' => 'item-description',
								));
								echo $this->Html->tag('div', $itemContent, array(
									'class' => 'item-container',
								));
						?>
					</div>
				</div>
				<div class="summary">
					<div class="row">
						<?php 
								$tagPropertySold = $this->Html->tag('h5', $totalPropertySold);
								$tagPropertySold .= $this->Html->tag('span', __('Total Properti Terjual'));
								echo $this->Html->tag('div', $tagPropertySold, array(
									'class' => 'col-sm-3',
								));
							
								$tagPropertyLeased = $this->Html->tag('h5', $totalPropertyLeased);
								$tagPropertyLeased .= $this->Html->tag('span', __('Total Properti Tersewa'));
								echo $this->Html->tag('div', $tagPropertyLeased, array(
									'class' => 'col-sm-3',
								));

								$tagPropertyActive = $this->Html->tag('h5', $totalPropertyActive);
								$tagPropertyActive .= $this->Html->tag('span', __('Total Properti Aktif'));
								echo $this->Html->tag('div', $tagPropertyActive, array(
									'class' => 'col-sm-3',
								));
						?>
					</div>
				</div>
				<?php 
						}
				?>
			</div>
		</div>
	</div>
	<?php 
			echo $this->Form->hidden('chart_value', array(
				'value' => $values,
				'id' => 'chartValue',
			));
	?>
</div>