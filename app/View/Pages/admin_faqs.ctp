<?php
		$searchUrl = array(
			'controller' => 'pages',
			'action' => 'search',
			'faqs',
			'admin' => true,
		);
		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('Faq'),
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'category' => array(
                'name' => __('Kategori'),
                'field_model' => 'FaqCategory.name',
                'width' => '120px;',
                'filter' => array(
                	'type' => 'select',
                	'options' => $categories,
                	'empty' => __('Pilih Kategori'),
            	),
            ),
            'keyword' => array(
                'name' => __('Pertanyaan'),
                'width' => '150px;',
                'field_model' => 'Faq.question',
                'filter' => 'text',
            ),
            'answer' => array(
                'name' => __('Jawaban'),
                'width' => '150px;',
                'field_model' => 'Faq.answer',
                'filter' => 'text',
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'width' => '120px;',
                'field_model' => 'Faq.modified',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'width' => '120px;',
                'field_model' => 'Faq.created',
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
			            'action' => 'delete_multiple_faq',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus FAQ ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'pages',
			            'action' => 'add_faq',
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

<div id="table-faq" class="table-responsive">
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
		      				$id = $this->Rumahku->filterEmptyField($value, 'Faq', 'id');
			                $question = $this->Rumahku->filterEmptyField($value, 'Faq', 'question');
			                $answer = $this->Rumahku->filterEmptyField($value, 'Faq', 'answer');
			                $faq_category = $this->Rumahku->filterEmptyField($value, 'FaqCategory', 'name');
			                $modified = $this->Rumahku->filterEmptyField($value, 'Faq', 'modified');
			                $created = $this->Rumahku->filterEmptyField($value, 'Faq', 'created');

			                $modified = $this->Time->niceShort($modified);
			                $created = $this->Time->niceShort($created);

		      				$action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'controller' => 'pages',
		      					'action' => 'edit_faq',
		      					$id,
		      					'admin' => true,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
						         		$this->Rumahku->buildCheckOption('Faq', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($faq_category, 'category'),
				         			$this->Rumahku->_getDataColumn($question, 'keyword'),
				         			$this->Rumahku->_getDataColumn($answer, 'answer'),
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