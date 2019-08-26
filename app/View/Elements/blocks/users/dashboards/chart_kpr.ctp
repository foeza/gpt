<?php 
	$total_kpr = !empty($total_kpr)?$total_kpr:false;

	$options = array(
		'controller' => $controller,
		'action' => $action,
	);

	$wrapperClass = !empty($wrapperClass)?$wrapperClass:'wrapper-selector mb30 mt30';
?>
<div class="<?php echo $wrapperClass; ?>">
	<div class="dashbox">
		<div id="kprAppliedStat" class="chart">
			<div class="chart-head">
				<div class="row">
					<div class="col-sm-12 col-xs-12 mb15">
							<?php
								echo $this->Html->tag('strong',__('Quick Review'));
							?>
					</div>
				</div>
				<?php
						echo $this->element('blocks/users/dashboards/chart_quick_data', array(
							'value' => $total_kpr,
							'options' => $options,
						));
				?>
			</div>
		</div>
	</div>
</div>