<?php
		$searchUrl = array(
			'controller' => 'users',
			'action' => 'search',
			'agents',
			'client' => true,
			'admin' => false,
		);
		$optionsFilter = array(
    		'User.created-desc' => __('Baru ke Lama'),
			'User.created-asc' => __('Lama ke Baru'),
			'User.full_name-asc' => __('Nama ( A - Z )'),
			'User.full_name-desc' => __('Nama ( Z - A )'),
    	);	
    	echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari agen berdasarkan nama / email'),
        	'url' => $searchUrl,
    		'sorting' => array(
        		'buttonDelete' => array(
		            'text' => __('Hapus FAQ').$this->Html->tag('span', '', array(
		            	'class' => 'check-count-target',
	            	)),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'delete_multiple_faq',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete',
	            		'data-alert' => __('Anda yakin ingin menghapus FAQ ini?'),
	        		),
	        		'class' => 'btn red'
		        ),
		        'options' => array(
		        	'options' => $optionsFilter,
	        		'url' => $searchUrl,
	        	),
    		),
    	));
?>

<div id="table-faq" class="table-responsive">
	<?php
			echo $this->Form->create('Faq', array(
        		'class' => 'form-target',
    		));

			if( !empty($values) ) {
				$dataColumns = array(
                    'name' => array(
                        'name' => __('Nama'),
                    ),
                    'telp' => array(
                        'name' => __('No. Telepon'),
                    ),
                    'email' => array(
                        'name' => __('Email'),
                    ),
                    'location' => array(
                        'name' => __('Lokasi'),
                    ),
                );
	
		        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
	?>
	<table class="table grey">
    	<?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
      	<tbody>
      		<?php
      			foreach( $values as $key => $value ) { 
                    $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
                    $telp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
                    $email = $this->Rumahku->filterEmptyField($value, 'User', 'email');

                    $location = '-';
                    $region = $this->Rumahku->filterEmptyField($value, 'Region', 'name');
                    $city = $this->Rumahku->filterEmptyField($value, 'City', 'name');

                    if( !empty($region) ) {
                        $location = $region;
                    }
                    if( !empty($city) ) {
                        $location = $region.', '.$city;
                    }
                    $custom_link = $this->Rumahku->_callUserFullName($value);

                    echo $this->Html->tableCells(array(
                        array(
                            $custom_link,
                            $telp,
                            $email,
                            $location,
                        ),
                    ));
                }
      		?>
      	</tbody>
    </table>
    <?php 
    		} else {
    			echo $this->Html->tag('p', __('Data belum tersedia'), array(
    				'class' => 'alert alert-warning'
				));
    		}

        	echo $this->Form->end(); 
    ?>
</div>
<?php 	
		echo $this->element('blocks/common/pagination');
?>