<?php
		$_action = isset($_action)?$_action:true;
		$_target = !empty($_target)?$_target:false;
		$is_refresh_listing = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_refresh_listing');
		$is_easy_mode = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_easy_mode');

		$authGroupID	= Configure::read('User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $authGroupID);
		$statusOptions	= array();

		$companyData	= Configure::read('Config.Company.data');
		$isOpenListing	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_open_listing');

		if($isOpenListing){
			$statusOptions['mine'] = __('Milik Saya');
		}

		$statusOptions = array_merge($statusOptions, array(
			'premium'			=> __('Premium'),
			'active-pending'	=> __('Tayang/Aktif'),
			'pending'			=> __('Pratinjau'),
			'update'			=> __('Updated'),
			'sold'				=> __('Terjual/Tersewa'),
			'cobroke'			=> __('Co-Broke'),
			'inactive'			=> __('Tidak Aktif/Rejected'),
		));

		$searchUrl = !empty($searchUrl)?$searchUrl:array(
			'controller' => 'properties',
			'action' => 'search',
			'index',
			'admin' => true,
		);
		$sortingOptions = array(
    		'buttonDelete' => array(
	            'text' => __('Hapus').$this->Html->tag('span', '', array(
	            	'class' => 'check-count-target',
            	)),
	            'url' => array(
	            	'controller' => 'properties',
		            'action' => 'delete_multiple',
		            'admin' => true,
            	),
            	'options' => array(
            		'class' => 'check-multiple-delete',
            		'data-alert' => __('Anda yakin ingin menghapus Properti ini?'),
        		),
	        ),
	        'overflowDelete' => true,
	        'buttonAdd' => !empty($_action)?array(
	            'text' => __('Tambah %s', $this->Html->tag('span', __('Properti'))),
				'url' => array(
					'controller' => 'properties',
					'action' => $is_easy_mode ? 'easy_add' : 'sell', 
					'admin' => true,
				),
            	'class' => 'btn-sell',
            	'options' => array(
            		'title' => __('Tambah Properti %s', $is_easy_mode ? '(Easy Mode)' : ''),
            		'target' => $_target,
        		),
	        ):false,
	        'options' => array(
	        	'optionsFilter' => array(
	        		'Property.created-desc' => __('Baru ke Lama'),
	        		'Property.created-asc' => __('Lama ke Baru'),
	        		'property_updated-desc' => __('Terupdate'),
	        		'Property.price_converter-asc' => __('Harga rendah ke tinggi'),
	        		'Property.price_converter-desc' => __('Harga tinggi ke rendah'),
        		),
	        	'optionsStatus' => $statusOptions,
        		'url' => $searchUrl,
        	),
		);

		if( (!empty($refresh_all) && !empty($is_refresh_listing)) || $isIndependent ) {
			$sortingOptions['buttonCustom'] = array(
	            'text' => __('Refresh Semua'),
	            'url' => array(
	            	'controller' => 'properties',
		            'action' => 'refresh_all',
		            'admin' => true,
            	),
            	'alert' => __('Anda yakin ingin me-refresh semua Properti?'),
	        );
		}
		
        echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari berdasarkan Judul, Alamat dan Lokasi'),
        	'url' => $searchUrl,
        	'btnSearchClass' => 'btn-search advanced',
        	'advanced_content' => 'blocks/properties/forms/search_advanced',
        	'sorting' => $sortingOptions,
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
								'_target' => $_target,
								'_action' => $_action,
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
		if($properties){
			echo $this->element('blocks/common/pagination');
		}
?>