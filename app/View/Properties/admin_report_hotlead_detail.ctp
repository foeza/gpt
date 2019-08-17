<?php
		$property = !empty($property)?$property:false;

		echo $this->element('blocks/properties/report_detail_action', array(
			'_sharePropertyAction' => 'hotlead',
		));
?>
<div class="row">
    <div class="col-sm-12">
		<div class="mt30 mb30">
			<div id="list-property" class="report-detail">
				<?php 
						echo $this->element('blocks/properties/items', array(
							'value' => $property,
							'_commission' => true,
						));
				?>
			</div>
		</div>
	</div>
</div>
<div class="row mt10 admin-report">
	<div class="col-sm-12">
		<?php 
				echo $this->element('blocks/properties/report/multiple_chart', array(
					'point_tooltip' => 'Total Hotlead',
				));
		?>
	</div>
</div>
<div class="mt30">
<?php
		$searchUrl = array(
			'controller' => 'properties',
			'action' => 'search',
			'report_hotlead_detail',
			'param_id' => $id,
			'admin' => true,
		);
    	echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari laporan properti berdasarkan id properti / tipe'),
        	'url' => $searchUrl,
        	'btnSearchClass' => 'btn-search advanced',
        	'advanced_content' => 'blocks/properties/forms/search_advanced',
        	'datePicker' => true,
        	'exportExcel' => !empty($values) ? true : false,
    		'sorting' => array(
		        'options' => array(
		        	'options' => array(
			    		'Message.created-desc' => __('Terbaru'),
			    		'Message.created-asc' => __('Terlama'),
			    	),
	        		'url' => $searchUrl,
	        	),
    		),
    	));
?>
</div>
<?php
		echo $this->element('blocks/properties/tables/report_hotlead_detail');
?>