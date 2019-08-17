<?php 
		$hiddenForm = !empty($hiddenForm)?$hiddenForm:false;
		$ajaxUrl = !empty($ajaxUrl)?$ajaxUrl:false;
		$parent_id = Configure::read('Principle.id');

		if( is_array($hiddenForm) ) {
			$urlForm = $this->Rumahku->filterEmptyField($hiddenForm, 'urlForm');
		} else {
			$urlForm = false;
		}

		if( !empty($url['link']) ) {
			$urlAttributes = $this->Rumahku->filterEmptyField($url, 'attributes');
			$url = $this->Rumahku->filterEmptyField($url, 'link');
		} else {
			$urlAttributes = array();
		}

		$values = !empty($chartCommission['values'])?json_encode($chartCommission['values']):false;
		$fromDate = !empty($chartCommission['fromDate'])?$chartCommission['fromDate']:date('01 M Y');
		$toDate = !empty($chartCommission['toDate'])?$chartCommission['toDate']:date('d M Y');
		$customDate = $this->Rumahku->getCombineDate($fromDate, $toDate);
		$periode = __('%s - %s', $this->Rumahku->formatDate($fromDate, 'd/m/Y'), $this->Rumahku->formatDate($toDate, 'd/m/Y'));

		$action_type = !empty($action_type)?$action_type:'commissions';

		if( $action_type == 'commissions' ) {
			// UNDER DEVELOPMENT
			
			// $totalVisitor = !empty($chartCommission['totalVisitor'])?$chartCommission['totalVisitor']:0;
			// $totalMessage = !empty($chartCommission['totalMessage'])?$chartCommission['totalMessage']:0;
			// $totalClient = !empty($chartCommission['totalClient'])?$chartCommission['totalClient']:0;
			$label = __('Komisi');
			$total_commission = !empty($chartCommission['total'])?$chartCommission['total']:0;
			$custom_total = $this->Rumahku->getCurrencyPrice($total_commission);
			$target_commission = !empty($targetCommission)?$targetCommission:0;
			$custom_target_commission = $this->Rumahku->getCurrencyPrice($target_commission);
		}
?>
<div id="wrapper-chart-commission" class="wrapper-selector">
	<div class="dashbox">
		<div id="kprAppliedStat" class="chart">
			<div class="chart-head">
				<div class="row">
					<div class="col-sm-9 col-xs-10">
						<ul class="tabs">
							<?php 
									$classActive = ( $action_type == 'commissions' )?'active':false;
									echo $this->Html->tag('li', $this->Html->link(__('Komisi'), '#', array(
	                                	'escape' => false,
		                                'class' => $classActive,
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
		                                'class' => 'daterange-dasboard-custom',
		                                'trigger-element' => 'filter-year-commission',
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
							
							if( $action_type == 'commissions' ) {	
								$class = '';
								if( $total_commission < $target_commission ) {
									$class = 'red';
								}
								echo $this->Html->tag('h4', $custom_total, array(
									'class' => $class,
								));

								if( !$this->Rumahku->_isCompanyAdmin() && !$this->Rumahku->_isAdmin() && !empty($target_commission) ) {
									echo $this->Html->tag('p', sprintf(__('( Target : %s )'), $custom_target_commission), array(
										'class' => 'mt10 fbold',
									));
								}
							}
					?>
				</div>
				<?php 
						if( $action_type == 'commissions' ) {
							if( !empty($hiddenForm) ) {
								echo $this->Form->create('Search', array(
									'id' => __('hid-form-%s', $action_type),
									'url' => $url,
								));
								echo $this->Form->hidden('Search.Periode.date', array(
									'value' => $periode,
								));
								echo $this->Form->hidden('Search.Perusahaan.principle_id', array(
									'value' => $parent_id,
								));
								echo $this->Form->button(sprintf(__('%s &raquo;'), $urlTitle), array_merge(array(
									'type' => 'submit',
									'class' => 'see-all btn default transparent',
								), $urlAttributes));
								echo $this->Form->end(); 
							}
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

			if( $action_type == 'commissions' ) {
	?>

	<div class="dropdown-menu daterange-custom filter-year-commission">
		<div class="user-information">
			<div class="form-group">
			    <div class="row">
			        <div class="col-sm-12 mt20">
			            <div class="row">
			                <?php
			                		echo $this->Html->tag('div', 
			                			$this->Form->label('filter_commission', __('Tahun'), array(
			                				'class' => 'control-label')
			                			), 
			                			array(
		                                'class' => 'col-xl-2 col-sm-3 control-label taright',
		                            ));
		                    ?>
		                    <div class="relative col-sm-8 col-xl-4">
		                    <?php
		                            echo $this->Rumahku->year('filter_commission', date('Y')-50, date('Y'), null, array(
	                                    'class' => 'form-control', 
	                                    'empty' => __('Pilih Tahun'),
	                                    'required' => false,
	                                ), 'filter_commission');
			                ?>
			            	</div>
			            </div>
			        </div>
			    </div>
			</div>
			<div class="form-group">
			    <div class="row">
			        <div class="col-sm-12">
			            <div class="row">
			                <?php
			                		echo $this->Html->tag('div', 
			                			$this->Form->label('filter_commission_month', __('Bulan'), array(
			                				'class' => 'control-label')
			                			), 
			                			array(
		                                'class' => 'col-xl-2 col-sm-3 control-label taright',
		                            ));
		                    ?>
		                    <div class="relative col-sm-8 col-xl-4">
		                    <?php
		                            echo $this->Form->month('mob', array(
		                            	'id' => 'filter_commission_month',
		                            	'class' => 'form-control',
		                            	'empty' => __('Pilih Bulan'),
		                            	// 'default' => '01',
		                            	// 'empty' => false,
		                            ));
			                ?>
			            	</div>
			            </div>
			        </div>
			    </div>
			</div>
		</div>
		<div class="user-action">
			<?php
					echo $this->Html->tag('div', $this->Html->link(__('Apply'), '#', array(
						'escape' => false,
						'url' => $this->Html->url($ajaxUrl),
						'class' => 'btn blue fs085 kpr-chart',
					)), array(
						'class' => 'floright'
					));
			?>
		</div>
	</div>

	<?php
			}
	?>
</div>