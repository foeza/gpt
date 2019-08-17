<?php
		$searchUrl = array(
			'controller' => 'pages',
			'action' => 'search',
			'partnerships',
			'admin' => true,
		);
		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('Partnership'),
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'photo' => array(
                'name' => __('Logo Partner'),
        		'filter' => 'default',
            ),
            'keyword' => array(
                'name' => __('Nama Partner'),
                'field_model' => 'Partnership.title',
                'filter' => 'text',
            ),
            'url' => array(
                'name' => __('URL'),
                'field_model' => 'Partnership.url',
                'filter' => 'text',
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'Partnership.modified',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Partnership.created',
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
			            'action' => 'delete_multiple_partnership',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus partnership ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'add_partnership',
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
		      				$id = $this->Rumahku->filterEmptyField($value, 'Partnership', 'id');
		      				$photo = $this->Rumahku->filterEmptyField($value, 'Partnership', 'photo');
			                $customPhoto = $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.logo_photo_folder'), 
								'src' => $photo, 
								'size' => 'xsm',
							)), array(
								'class' => 'user-radius-photo',
							));
			                $title = $this->Rumahku->filterEmptyField($value, 'Partnership', 'title');
			                $url = $this->Rumahku->filterEmptyField($value, 'Partnership', 'url');
			                $modified = $this->Rumahku->filterEmptyField($value, 'Partnership', 'modified');
			                $created = $this->Rumahku->filterEmptyField($value, 'Partnership', 'created');

			                $custom_url = $this->Rumahku->wrapWithHttpLink($url);
			                $modified = $this->Time->niceShort($modified);
			                $created = $this->Time->niceShort($created);
		      				$action = $this->AclLink->link(__('Edit'), array(
		      					'controller' => 'pages',
		      					'action' => 'edit_partnership',
		      					$id,
		      					'admin' => true,
		  					));

							echo $this->Html->tableCells(array(
								array(
						        	array(
			            				$this->Rumahku->buildCheckOption('Partnership', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($customPhoto, 'photo'),
				         			$this->Rumahku->_getDataColumn($title, 'keyword'),
				         			$this->Rumahku->_getDataColumn($custom_url, 'url'),
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