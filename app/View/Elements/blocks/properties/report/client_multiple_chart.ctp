<?php 
		$valuesDashboard = !empty($chartProperties['values'])?json_encode($chartProperties['values']):false;
		$fromDate = !empty($chartProperties['fromDate'])?$chartProperties['fromDate']:date('01 M Y');
		$toDate = !empty($chartProperties['toDate'])?$chartProperties['toDate']:date('d M Y');
		$customDate = $this->Rumahku->getCombineDate($fromDate, $toDate);
		$action_type = !empty($action_type)?$action_type : 'visitors';

		$property = isset($property) ? $property : false;
		$property_id = $this->Rumahku->filterEmptyField($property, 'Property', 'id', 0);
		$mls_id = $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id', 0);

		$url = false;
		$_ajax = !empty($_ajax)? 'kpr-chart ' : '';

		$default_url_visitor = $this->Html->url(array(
            'controller' => 'properties',
            'action' => 'report_visitor',
            $property_id,
            'client' => true,
        ));
        $default_url_lead = $this->Html->url(array(
            'controller' => 'properties',
            'action' => 'report_lead',
            $property_id,
            'client' => true,
        ));
        $default_url_hotlead = $this->Html->url(array(
            'controller' => 'properties',
            'action' => 'report_hotlead',
            $property_id,
            'client' => true,
        ));
		
		if( $action_type == 'visitors' ) {
			$label = __('Laporan Pengunjung Properti');
			$totalVisitor = !empty($chartProperties['total'])?$chartProperties['total']:0;
			$averageVisitor = !empty($chartProperties['averageVisitor'])?$chartProperties['averageVisitor']:0;
		} else if( $action_type == 'lead' ) {
			$label = __('Laporan Lead');
			$totalLead = !empty($chartProperties['total'])?$chartProperties['total']:0;
			$averageLead = !empty($chartProperties['averageLead'])?$chartProperties['averageLead']:0;
		} else if( $action_type == 'hotlead' ) {
			$label = __('Laporan Hot Lead');
			$totalHotlead = !empty($chartProperties['total'])?$chartProperties['total']:0;
			$averageHotlead = !empty($chartProperties['averageHotlead'])?$chartProperties['averageHotlead']:0;
		}

		if( !empty($mls_id) ){
			$label = sprintf('%s ID: %s', $label, $mls_id);
		}
		// UNDER DEVELOPMENT
?>
<!-- <div id="wrapper-chart-kpr" class="wrapper-selector"> -->
<div data-id="content-wrapper" id="wrapper-chart" class="wrapper-selector">
	<div class="dashbox">
		<div id="kprAppliedStat" class="chart">
			<div class="chart-head">
				<div class="row">
					<div class="col-sm-9">
						<ul class="tabs">
							<?php
									$classActive = ( $action_type == 'visitors' )?'active':false;
									if( !empty($_ajax) ){
							 			$url = $this->Html->url(array(
							                'controller' => 'ajax',
							                'action' => 'get_properties_report',
							                $property_id,
							                'visitors',
							            ));
									}
									echo $this->Html->tag('li', $this->Html->link(__('Pengunjung'), 
	                            		$default_url_visitor, array(
		                                'url' => $url,
	                                	'escape' => false,
		                                'chart-for' => 'visitors',
		                                'class' => $_ajax.$classActive,
		                            )), array(
		                                'role' => 'presentation',
		                            ));
								
									$classActive = ( $action_type == 'lead' )?'active':false;
									if( !empty($_ajax) ){
							 			$url = $this->Html->url(array(
							                'controller' => 'ajax',
							                'action' => 'get_properties_report',
							                $property_id,
							                'lead',
							            ));
									}
									echo $this->Html->tag('li', $this->Html->link(__('Lead'), 
	                            		$default_url_lead, array(
		                                'url' => $url,
	                                	'escape' => false,
		                                'chart-for' => 'lead',
		                                'class' => $_ajax.$classActive,
		                            )), array(
		                                'role' => 'presentation',
		                            ));

		                            $classActive = ( $action_type == 'hotlead' )?'active':false;
		                            if( !empty($_ajax) ){
							 			$url = $this->Html->url(array(
							                'controller' => 'ajax',
							                'action' => 'get_properties_report',
							                $property_id,
							                'hotlead',
							            ));
									}
		                            echo $this->Html->tag('li', $this->Html->link(__('Hotlead'), 
	                            		$default_url_hotlead, array(
		                                'url' => $url,
	                                	'escape' => false,
		                                'chart-for' => 'hotlead',
		                                'class' => $_ajax.$classActive,
		                            )), array(
		                                'role' => 'presentation',
		                            ));
							?>
						</ul>
					</div>
					<div class="col-sm-3 floright">
						<div class="form-group taright">
							<?php 
		                            echo $this->Rumahku->setFormDateRange();
									echo $this->Html->link($this->Rumahku->icon('rv4-calendar'), 'javascript:void(0);', array(
	                                	'escape' => false,
		                                'class' => 'daterange-report',
		                                'title' => __('Tanggal'),
		                                'url' => $this->Html->url(array(
		                                    'controller' => 'properties',
		                                    'action' => 'report_visitor',
		                                    'client' => true,
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
					?>
				</div>	
				<?php 
						if( $action_type == 'visitors' ) {
				?>
				<div class="summary">
					<div class="row">
						<?php 
								$tagTotalVisitor = $this->Html->tag('h5', $totalVisitor);
								$tagTotalVisitor .= $this->Html->tag('span', __('Total Pengunjung'));
								echo $this->Html->tag('div', $tagTotalVisitor, array(
									'class' => 'col-sm-3',
								));
							
								$tagAverageVisitor = $this->Html->tag('h5', $averageVisitor);
								$tagAverageVisitor .= $this->Html->tag('span', __('Pengunjung per Hari'));
								echo $this->Html->tag('div', $tagAverageVisitor, array(
									'class' => 'col-sm-3',
								));
						?>
					</div>
				</div>

				<?php 
						} 

						if( $action_type == 'lead' ) {
				?>
				<div class="summary">
					<div class="row">
						<?php 
								$tagTotalLead = $this->Html->tag('h5', $totalLead);
								$tagTotalLead .= $this->Html->tag('span', __('Total Lead'));
								echo $this->Html->tag('div', $tagTotalLead, array(
									'class' => 'col-sm-3',
								));
							
								$tagAverageLead = $this->Html->tag('h5', $averageLead);
								$tagAverageLead .= $this->Html->tag('span', __('Lead per Hari'));
								echo $this->Html->tag('div', $tagAverageLead, array(
									'class' => 'col-sm-3',
								));
						?>
					</div>
				</div>

				<?php 
						}

						if( $action_type == 'hotlead' ) {
				?>
				<div class="summary">
					<div class="row">
						<?php 
								$tagTotalHotlead = $this->Html->tag('h5', $totalHotlead);
								$tagTotalHotlead .= $this->Html->tag('span', __('Total Hot Lead'));
								echo $this->Html->tag('div', $tagTotalHotlead, array(
									'class' => 'col-sm-3',
								));

								$tagAverageHotlead = $this->Html->tag('h5', $averageHotlead);
								$tagAverageHotlead .= $this->Html->tag('span', __('Hot Lead per Hari'));
								echo $this->Html->tag('div', $tagAverageHotlead, array(
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
				'value' => $valuesDashboard,
				'id' => 'chartValue',
			));
	?>
</div>