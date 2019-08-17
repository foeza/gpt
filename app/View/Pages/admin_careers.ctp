<?php
		$searchUrl = array(
			'controller' => 'pages',
			'action' => 'search',
			'careers',
			'admin' => true,
		);

		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('Career'),
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'name' => array(
                'name' => __('Jenis Pekerjaan'),
                'field_model' => 'Career.name',
                'filter' => 'text',
            ),
            'email' => array(
                'name' => __('Email Tujuan'),
                'field_model' => 'Career.email',
                'filter' => 'text',
            ),
            'description' => array(
                'name' => __('Deskripsi'),
                'field_model' => 'Career.description',
                'width' => '150px;',
                'filter' => 'text',
            	'display' => false,
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'Career.modified',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Career.created',
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
			            'action' => 'delete_multiple_career',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus karir ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'add_career',
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
		      				$id = $this->Rumahku->filterEmptyField($value, 'Career', 'id');
		      				$name = $this->Rumahku->filterEmptyField($value, 'Career', 'name');
			                $email = $this->Rumahku->filterEmptyField($value, 'Career', 'email');
			                $description = $this->Rumahku->filterEmptyField($value, 'Career', 'description');
			                $modified = $this->Rumahku->filterEmptyField($value, 'Career', 'modified');
			                $created = $this->Rumahku->filterEmptyField($value, 'Career', 'created');

			                $modified = $this->Time->niceShort($modified);
			                $created = $this->Time->niceShort($created);                
		      				$action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'controller' => 'pages',
		      					'action' => 'edit_career',
		      					$id,
		      					'admin' => true,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
						         		$this->Rumahku->buildCheckOption('Career', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($name, 'name'),
				         			$this->Rumahku->_getDataColumn($email, 'email'),
				         			$this->Rumahku->_getDataColumn($description, 'description'),
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