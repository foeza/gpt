<?php
		$searchUrl = array(
			'controller' => 'blogs',
			'action' => 'search',
			'index',
			'admin' => true,
		);
    	$optionsStatus = array(
    		'inactive' => __('Non Aktif'),
    		'active' => __('Aktif'),
    	);
		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('Advice'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
            'title' => array(
                'name' => __('Judul'),
                'field_model' => 'Advice.title',
                'width' => '150px;',
                'filter' => 'text',
            ),
            'category_name' => array(
                'name' => __('Kategori'),
                'field_model' => 'AdviceCategory.name',
                'width' => '100px;',
                'filter' => 'text',
            ),
            'author' => array(
                'name' => __('Penulis'),
                'field_model' => 'Author.full_name',
                'width' => '150px;',
                'filter' => 'text',
            ),
            'short_content' => array(
                'name' => __('Keterangan Singkat'),
                'field_model' => 'Advice.short_content',
                'width' => '250px;',
            	'display' => false,
                'filter' => 'text',
            ),
            'order' => array(
            	'name' => __('Order'),
            	'field_model' => 'Advice.order',
            	'class' => 'tacenter',
            	'filter' => 'text',
            ),
            'status' => array(
                'name' => __('Aktif'),
                'field_model' => 'Advice.active',
                'class' => 'tacenter',
                'filter' => array(
                	'type' => 'select',
                	'options' => $optionsStatus,
                	'empty' => __('Status'),
            	),
                'width' => '80px;',
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'Advice.modified',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Advice.created',
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
		            	'controller' => 'blogs',
			            'action' => 'delete_multiple_advice',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus data ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'blogs',
			            'action' => 'add',
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

<div id="table-advice" class="table-responsive">
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
		      				$id = $this->Rumahku->filterEmptyField($value, 'Advice', 'id');
		      				$user_id = $this->Rumahku->filterEmptyField($value, 'Advice', 'author_id');
		      				$photo = $this->Rumahku->filterEmptyField($value, 'Advice', 'photo');
			                $title = $this->Rumahku->filterEmptyField($value, 'Advice', 'title');
			                $slug = $this->Rumahku->filterEmptyField($value, 'Advice', 'slug');
			                $short_content = $this->Rumahku->filterEmptyField($value, 'Advice', 'short_content');
			                $active = $this->Rumahku->filterEmptyField($value, 'Advice', 'active');
			                $order = $this->Rumahku->filterEmptyField($value, 'Advice', 'order', '-');
			                $modified_date = $this->Rumahku->filterEmptyField($value, 'Advice', 'modified');
			                $created_date = $this->Rumahku->filterEmptyField($value, 'Advice', 'created');

			                $custom_modified_date = $this->Time->niceShort($modified_date);
			                $custom_created_date = $this->Time->niceShort($created_date);

			                $full_name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
		      				$username = $this->Rumahku->filterEmptyField($value, 'User', 'username');
			                $category = $this->Rumahku->filterEmptyField($value, 'AdviceCategory', 'name');

			                if( !empty($active) ) {
			      				$exclusiveMsg = __('non aktifkan');
			      			} else {
			      				$exclusiveMsg = __('aktifkan');
			      			}

			      			$customActive = $this->Rumahku->_callStatusChecked($active);
		      				$customActive = $this->Rumahku->_callLinkLabel($customActive, array(
			           			'controller' => 'blogs',
			           			'action' => 'actived',
			           			$id,
			           			'admin' => true,
		           			), array(
		           				'escape' => false,
		           			), sprintf(__('Anda yakin ingin %s data ini ?'), $exclusiveMsg));
		      				
			                $customPhoto = $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.advice_photo_folder'), 
								'src' => $photo, 
								'size' => 's',
							)), array(
								'class' => 'user-radius-photo',
							));
			                $custom_title = $this->Html->link($title, array(
			                	'controller' => 'blogs',
		      					'action' => 'read',
		      					$id,
		      					$this->Rumahku->toSlug($title),
		      					'admin' => false,
			                ), array(
				                'target' => 'blank'
				            ));
				            
		      				$action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'controller' => 'blogs',
		      					'action' => 'edit',
		      					$id,
		      					'admin' => true,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
						         		$this->Rumahku->buildCheckOption('Advice', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($custom_title, 'title'),
				         			$this->Rumahku->_getDataColumn($category, 'category_name'),
				         			$this->Rumahku->_getDataColumn($full_name, 'author'),
				         			$this->Rumahku->_getDataColumn($short_content, 'short_content'),
				         			$this->Rumahku->_getDataColumn($order, 'order', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($customActive, 'status', array(
						            	'class' => 'tacenter',
					            	)),
				         			$this->Rumahku->_getDataColumn($custom_modified_date, 'modified'),
				         			$this->Rumahku->_getDataColumn($custom_created_date, 'date'),
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