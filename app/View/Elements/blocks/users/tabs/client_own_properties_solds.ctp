<?php
		$searchUrl = array(
			'controller' => 'properties',
			'action' => 'search',
			'solds',
			'client' => true,
			'admin' => false,
		);
		
        echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari berdasarkan Judul, Alamat dan Lokasi'),
        	'url' => $searchUrl,
        	'sorting' => array(
		        'options' => array(
		        	'options' => array(
		        		'Property.created-desc' => __('Baru ke Lama'),
		        		'Property.created-asc' => __('Lama ke Baru'),
		        		'Property.price_converter-asc' => __('Harga rendah ke tinggi'),
		        		'Property.price_converter-desc' => __('Harga tinggi ke rendah'),
	        		),
	        		'url' => $searchUrl,
	        	),
    		),
    	));
?>
<div class="my-properties">
	<div class="wrapper-border">
		<div id="list-property">
			<?php 
					if( !empty($properties) ) {
						foreach ($properties as $key => $value) {
							echo $this->element('blocks/properties/items', array(
								'value' => $value,
								'fullDisplay' => false,
								'_soldStatus' => true,
							));
						}
					} else {
						echo $this->Html->tag('div', __('Properti belum tersedia'), array(
							'class' => 'alert alert-warning',
						));
					}
			?>
		</div>
	</div>
</div>
<?php 	
		echo $this->element('blocks/common/pagination');
?>