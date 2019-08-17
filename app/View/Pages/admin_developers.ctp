<?php
		$searchUrl = array(
			'controller' => 'pages',
			'action' => 'search',
			'developers',
			'admin' => true,
		);
		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('BannerDeveloper'),
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'photo' => array(
                'name' => __('Logo Developer'),
                'field_model' => false,
                'width' => '80px;',
        		'filter' => 'default',
            ),
            'title' => array(
                'name' => __('Nama Project'),
                'field_model' => 'BannerDeveloper.title',
                'width' => '120px;',
                'filter' => 'text',
            ),
            'status' => array(
                'name' => __('Masa Tayang'),
                'field_model' => 'BannerDeveloper.start_date',
                'width' => '120px;',
                'class' => 'tacenter',
                'filter' => array(
                	'type' => 'select',
                	'options' => array(
                		'developer_active' => __('Active'),
                		'developer_inactive' => __('Non-Active'),
            		),
                	'empty' => __('Status'),
            	),
            ),
            'short_description' => array(
                'name' => __('Keterangan Singkat'),
                'field_model' => 'BannerDeveloper.short_description',
                'width' => '150px;',
                'filter' => 'text',
            	'display' => false,
            ),
            'order' => array(
                'name' => __('Order'),
                'field_model' => 'BannerDeveloper.order',
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'is_article' => array(
            	'name' => __('Artikel'),
                'field_model' => 'BannerDeveloper.is_article',
                'width' => '100px;',
                'class' => 'tacenter',
                'filter' => array(
                	'type' => 'select',
                	'options' => array(
                		'yes' => __('Ya'),
                		'no' => __('Tidak'),
            		),
                	'empty' => __('Status'),
            	),
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'BannerDeveloper.modified',
                'width' => '120px;',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'field_model' => 'BannerDeveloper.created',
                'width' => '120px;',
        		'filter' => 'daterange',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'tacenter',
            ),
        );

    	$showHideColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'show-hide' );
        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table', array(
        	'thead' => true,
        	'sortOptions' => array(
        		'ajax' => true,
    		),
			'table_ajax' => true,
    	));

		echo $this->Form->create('Search', array(
        	'url' => $searchUrl,
			'class' => 'form-target form-table-search',
		));
    	echo $this->element('blocks/common/forms/search/backend', array(
        	'_form' => false,
        	'with_action_button' => false,
        	'new_action_button' => true,
        	'fieldInputName' => 'search',
    		'sorting' => array(
        		'buttonDelete' => array(
		            'text' => __('Hapus').$this->Html->tag('span', '', array(
		            	'class' => 'check-count-target',
	            	)),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'delete_multiple_developer',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus banner ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'add_developer',
			            'admin' => true,
	            	),
		        ),
		        'options' => array(
	        		'showcolumns' => array(
	        			'options' => $showHideColumn,
        			),
	        	),
    		),
    	));
?>

<div class="table-responsive">
	<table class="table grey">
    	<?php
                if( !empty($fieldColumn) ) {
                    echo $fieldColumn;
                }
        ?>
      	<tbody>
      		<?php
					if( !empty($values) ) {
		      			foreach( $values as $key => $value ) {
		      				$id = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'id');
		      				$photo = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'photo');
			                $title = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'title');
			                $short_description = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'short_description');
			                $start_date = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'start_date');
			                $end_date = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'end_date');
			                $order = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'order', 0);
			                $status = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'is_article');
			                $modified = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'modified');
			                $created = $this->Rumahku->filterEmptyField($value, 'BannerDeveloper', 'created');

			                $modified = $this->Time->niceShort($modified);
			                $created = $this->Time->niceShort($created);

			                $customStatus = $this->Rumahku->_callStatusChecked($status);
			                $customPhoto = $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.general_folder'), 
								'src' => $photo, 
								'size' => 's',
							)), array(
								'class' => 'user-radius-photo',
							));
			                $customAvailableDate = $this->Rumahku->getCombineDate($start_date, $end_date, '..');
		      				$action = $this->Html->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'controller' => 'pages',
		      					'action' => 'edit_developer',
		      					$id,
		      					'admin' => true,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
						         		$this->Rumahku->buildCheckOption('BannerDeveloper', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($customPhoto, 'photo'),
				         			$this->Rumahku->_getDataColumn($title, 'title'),
				         			$this->Rumahku->_getDataColumn($customAvailableDate, 'status', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($short_description, 'short_description'),
				         			$this->Rumahku->_getDataColumn($order, 'order', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($customStatus, 'is_article', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($modified, 'modified'),
				         			$this->Rumahku->_getDataColumn($created, 'date'),
						         	array(
						         		$action,
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
						        )
						    ));
						}
					}
      		?>
      	</tbody>
    </table>
    <div class="filter-footer">
	    <?php 
				if( empty($values) ) {
	    			echo $this->Html->tag('p', __('Data belum tersedia'), array(
	    				'class' => 'alert alert-warning tacenter'
					));
	    		}

	    ?>
    </div>
</div>
<?php 	
    	echo $this->Form->end(); 
		echo $this->element('blocks/common/pagination', array(
			'_ajax' => true,
		));
?>