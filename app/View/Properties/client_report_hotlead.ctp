<?php
		$property = !empty($property)?$property:false;
		
		echo $this->element('blocks/properties/report_detail_action', array(
			'_isClient' => true,
			'_backUrl' => array(
            	'controller' => 'properties',
                'action' => 'index',
                'client' => true,
                'admin' => false,
            )
		));
?>
<div class="row">
    <div class="col-sm-12">
		<div class="mt30 mb30">
			<div id="list-property" class="report-detail">
				<?php 
						echo $this->element('blocks/properties/items', array(
							'value' => $property,
							'fullDisplay' => false,
							'_soldStatus' => true,
						));
				?>
			</div>
		</div>
	</div>
</div>
<div class="row mt10 admin-report">
	<div class="col-sm-12">
		<?php 
				echo $this->element('blocks/properties/report/client_multiple_chart');
		?>
	</div>
</div>
<div class="mt30">
<?php
		$searchUrl = array(
			'controller' => 'properties',
			'action' => 'search',
			'report_hotlead',
			'param_id' => $id,
			'client' => true,
		);
    	echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari laporan properti berdasarkan id properti / tipe'),
        	'url' => $searchUrl,
        	'exportExcel' => !empty($values) ? true : false,
        	'datePicker' => true,
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