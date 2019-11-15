<?php
		$modelName = 'PropertyProductCategory';

		$dataColumns = array(
			'checkall' => array(
                'name' 	 => $this->Rumahku->buildCheckOption($modelName),
                'class'  => 'tacenter',
        		'filter' => 'default',
            ),
            'keyword' => array(
                'name'		  => __('Nama Kategori'),
                'field_model' => __('%s.name', $modelName),
                'filter' 	  => 'text',
            ),
            'modified' => array(
                'name' 		  => __('Diubah'),
                'field_model' => __('%s.modified', $modelName),
        		'filter' 	  => 'daterange',
            ),
            'date' => array(
                'name' 		  => __('Dibuat'),
                'field_model' => __('%s.created', $modelName),
        		'filter' 	  => 'daterange',
            ),
            'action' => array(
                'name'  => __('Action'),
                'class' => 'tacenter',
            ),
        );

    	$showHideColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'show-hide' );
        $fieldColumn    = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table', array(
        	'thead' 	  => true,
        	'sortOptions' => array(
        		'ajax' => true,
    		),
			'table_ajax'  => true,
    	));

        // start form
		$searchUrl = array(
			'admin' 	 => true,
			'controller' => 'properties',
			'action' 	 => 'search',
			'product_category',
		);
		echo $this->Form->create('Search', array(
        	'url'   => $searchUrl,
			'class' => 'form-target form-table-search',
		));

    	echo $this->element('blocks/common/forms/search/backend', array(
        	'_form' 			 => false,
        	'with_action_button' => false,
        	'new_action_button'  => true,
        	'fieldInputName' 	 => 'search',
    		'sorting' 			 => array(
        		'buttonDelete' => array(
		            'text' => __('Hapus').$this->Html->tag('span', '', array('class' => 'check-count-target')),
		            'url'  => array(
		            	'controller' => 'properties',
			            'action' 	 => 'delete_multiple_product_category',
			            'admin' 	 => true,
	            	),
	            	'options' => array(
	            		'class' 	 => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus kategori ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' 	 => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url'  => array(
			            'admin' 	 => true,
		            	'controller' => 'properties',
			            'action' 	 => 'add_product_category',
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
			                $id		  = Common::hashEmptyField($value, 'PropertyProductCategory.id');
			                $name	  = Common::hashEmptyField($value, 'PropertyProductCategory.name');
			                $modified = Common::hashEmptyField($value, 'PropertyProductCategory.modified');
			                $created  = Common::hashEmptyField($value, 'PropertyProductCategory.created');

			                $modified = $this->Time->niceShort($modified);
			                $created  = $this->Time->niceShort($created);

			                $action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'admin' 	 => true,
		      					'controller' => 'properties',
		      					'action'     => 'edit_product_category',
		      					$id,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
						         		$this->Rumahku->buildCheckOption($modelName, $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($name, 'keyword'),
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