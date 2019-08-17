<?php
		$searchUrl = array(
			'controller' => 'pages',
			'action' => 'search',
			'slides',
			'admin' => true,
		);
		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('BannerSlide'),
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'photo' => array(
                'name' => __('Slide Banner'),
                'width' => '80px;',
        		'filter' => 'default',
            ),
            'keyword' => array(
                'name' => __('Judul Banner'),
                'field_model' => 'BannerSlide.title',
                'width' => '120px;',
                'filter' => 'text',
            ),
            'status' => array(
                'name' => __('Masa Tayang'),
                'field_model' => 'BannerSlide.start_date',
                'width' => '120px;',
                'class' => 'tacenter',
                'filter' => array(
                	'type' => 'select',
                	'options' => array(
                		'active' => __('Active'),
                		'inactive' => __('Non-Active'),
            		),
                	'empty' => __('Semua'),
            	),
            ),
            'order' => array(
                'name' => __('Order'),
                'field_model' => 'BannerSlide.order',
                'class' => 'tacenter',
        		'filter' => 'default',
                'width' => '80px;',
            ),
            'is_video' => array(
                'name' => __('Video Youtube?'),
                'field_model' => 'BannerSlide.is_video',
                'class' => 'tacenter',
                'width' => '80px;',
                'filter' => array(
                	'type' => 'select',
                	'options' => array(
                		'yes' => __('Ya'),
                		'no' => __('Tidak'),
            		),
                	'empty' => __('Semua'),
            	),
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'BannerSlide.modified',
                'width' => '120px;',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'field_model' => 'BannerSlide.created',
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
			            'action' => 'delete_multiple_slide',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus kategori ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'add_slide',
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
		      				$id = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'id');
		      				$photo = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'photo');
			                $title = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'title');
			                $start_date = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'start_date');
			                $end_date = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'end_date');
			                $order = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'order', 0);
			                $modified = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'modified');
			                $created = $this->Rumahku->filterEmptyField($value, 'BannerSlide', 'created');

			                $modified = $this->Time->niceShort($modified);
			                $created = $this->Time->niceShort($created);

			                $is_video = Common::hashEmptyField($value, 'BannerSlide.is_video');
			      			$is_video = $this->Rumahku->_callStatusChecked($is_video);
			                
			                $customPhoto = $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.general_folder'), 
								'src' => $photo, 
								'size' => 's',
							)), array(
								'class' => 'user-radius-photo',
							));
			                $customAvailableDate = $this->Rumahku->getCombineDate($start_date, $end_date, '-');
		      				$action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'controller' => 'pages',
		      					'action' => 'edit_slide',
		      					$id,
		      					'admin' => true,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
			            				$this->Rumahku->buildCheckOption('BannerSlide', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($customPhoto, 'photo'),
				         			$this->Rumahku->_getDataColumn($title, 'keyword'),
				         			$this->Rumahku->_getDataColumn($customAvailableDate, 'status', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($order, 'order', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($is_video, 'is_video', array(
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