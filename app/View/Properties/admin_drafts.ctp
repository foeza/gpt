<?php
		$searchUrl = array(
			'controller' => 'properties',
			'action' => 'search',
			'drafts',
			'admin' => true,
		);
		
        echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari berdasarkan Judul, Lokasi'),
        	'url' => $searchUrl,
        	'sorting' => array(
		        'options' => array(
		        	'options' => array(
		        		'PropertyDraft.created-desc' => __('Baru ke Lama'),
		        		'PropertyDraft.created-asc' => __('Lama ke Baru'),
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
					if( !empty($values) ) {
						foreach ($values as $key => $value) {
							echo $this->element('blocks/properties/items', array(
								'value' => $value,
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