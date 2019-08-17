<?php 
		$valuesPieChart = !empty($valuesPieChart)?json_encode($valuesPieChart):false;
		$pieFilterOptions = !empty($pieFilterOptions)?$pieFilterOptions:false;
		if( !empty($pieFilterOptions) ) {
			$filterFrameClass = !empty($filterFrameClass)?$filterFrameClass:'col-sm-12';
			$filterLabelClass = !empty($filterLabelClass)?$filterLabelClass:'col-xl-2 col-sm-6 control-label taright';
			$filterClass = !empty($filterClass)?$filterClass:'relative col-sm-4 col-xl-4';
			$filterLabel = !empty($filterLabel)?$filterLabel:false;
		}
		$wrapperColumn = !empty($wrapperColumn)?$wrapperColumn:'col-sm-12';
		$title = !empty($title)?$title:false;
		$url = !empty($url)?$url:'javascript:void(0);';
?>
<div id="wrapper-pie-chart">
	<div class="dashbox">
		<div id="kprAppliedStat" class="chart">
			<div class="chart-head">
				<div class="row">
					<div class="<?php echo $wrapperColumn; ?>">
						<?php
								echo $this->Html->tag('h4', $title);
						?>
					</div>
				</div>
			</div>
			<div class="pie-chart-body">
				<div class="row">
					<div class="<?php echo $wrapperColumn; ?>">
						<div class="chart-body-wrapper mt10">
							<?php
									if( !empty($pieFilterOptions) ) {
										echo $this->Rumahku->buildInputForm('pie_chart_filter', array(
											'id' => 'pie_chart_filter',
											'url' => $this->Html->url($url),
											'frameClass' => $filterFrameClass,
								            'labelClass' => $filterLabelClass,
								            'class' => $filterClass,
							                'label' => $filterLabel,
							                'options' => $pieFilterOptions,
							            ));
									}
							?>
						</div>
					</div>
				</div>
				<div id="pie_chart_div"></div>
			</div>
			<div class="chart-foot"></div>
		</div>
	</div>
	<?php
			echo $this->Form->hidden('pie_chart_value', array(
				'value' => $valuesPieChart,
				'id' => 'pieChartValue',
			));
	?>
</div>