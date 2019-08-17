<?php
		$searchUrl = array(
			'controller' => 'users',
			'action' => 'search',
			'sosmed_reference',
			'admin' => true,
		);

        $dataColumns = array(
            'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('Search'),
                'class' => 'tacenter',
            	'filter' => 'default',
            ),
            'name' => array(
                'name' => __('Nama'),
                'width' => '120px;',
                'field_model' => 'UserClientSosmedReference.name',
                'filter' => 'text',
            ),
            'url_sosmed' => array(
                'name' => __('URL Sosmed'),
                'width' => '120px;',
                'field_model' => 'UserClientSosmedReference.url',
                'filter' => 'text',
            ),
            'modified' => array(
                'name' => __('Tgl Diubah'),
                'width' => '120px;',
                'field_model' => 'UserClientSosmedReference.modified',
                'filter' => 'daterange',
                'display' => false,
            ),
            'date' => array(
                'name' => __('Tgl Dibuat'),
                'width' => '120px;',
                'field_model' => 'UserClientSosmedReference.created',
                'filter' => 'daterange',
            ),
			'status' => array(
				'name' => __('Semua'), 
				'field_model' => 'UserClientSosmedReference.active', 
	            'width' => '80px;',
	            'filter' => array(
	            	'type' => 'select',
	            	'options' => array(
			    		'inactive' => __('Non Aktif'),
			    		'active' => __('Aktif'),
			    	),
	            	'empty' => __('Status'),
	        	),
			),
            'action' => array(
                'name' => __('Action'),
                'class' => 'align-center',
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
		            	'controller' => 'users',
			            'action' => 'sosmed_reference_delete',
			            'admin' => true,
	            	),
	            	'options' => array(
	        			'data-alert' => __('Anda yakin ingin menghapus sosmed ini?'),
	            		'class' => 'check-multiple-delete btn-red',
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
	    			),
		        ),
		        'buttonAdd' => array(
		        	'text' => __('Tambah Sosmed'),
		            'url' => array(
		            	'controller' => 'users',
			            'action' => 'add_sosmed_reference',
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
			  				$id = Common::hashEmptyField($value, 'UserClientSosmedReference.id');
			  				$name = Common::hashEmptyField($value, 'UserClientSosmedReference.name');
			  				$url_sosmed = Common::hashEmptyField($value, 'UserClientSosmedReference.url');
							$active = Common::hashEmptyField($value, 'UserClientSosmedReference.active');
			  				$created = Common::hashEmptyField($value, 'UserClientSosmedReference.created');
			  				$modified = Common::hashEmptyField($value, 'UserClientSosmedReference.modified');

			                if( !empty($active) ) {
			      				$exclusiveMsg = __('non aktifkan');
			      			} else {
			      				$exclusiveMsg = __('aktifkan');
			      			}

			                $created = $this->Time->niceShort($created);
			                $modified = $this->Time->niceShort($modified);
			  				$status = $this->Rumahku->_callStatus($active);
		      				$status = $this->Rumahku->_callLinkLabel($status, array(
			           			'controller' => 'users',
			           			'action' => 'sosmed_reference_toggle',
			           			$id,
			           			'admin' => true,
		           			), array(
		           				'escape' => false,
		           			), sprintf(__('Anda yakin ingin %s sosmed ini ?'), $exclusiveMsg));

			  				$link_sosmed = $this->Html->link($url_sosmed, $url_sosmed, array(
			  					'target' => '_blank',
			  				));

							$action	= $this->Rumahku->dropdownButtons(array(
								array(
									'text'	=> 'Edit', 
									'url'	=> array(
										'action' => 'edit_sosmed_reference',
										$id,
									), 
								),
							), array(
								'class' => 'dropdown icon-btn-wrapper', 
							));

      						$content = array(
		            			array(
		            				$this->Rumahku->buildCheckOption('Search', $id, 'default'),
						            array(
						            	'class' => 'tacenter',
					            	),
			         			),
			         			$this->Rumahku->_getDataColumn($name, 'name'),
			         			$this->Rumahku->_getDataColumn($link_sosmed, 'url_sosmed'),
			         			$this->Rumahku->_getDataColumn($modified, 'modified'),
			         			$this->Rumahku->_getDataColumn($created, 'date'),
			         			$this->Rumahku->_getDataColumn($status, 'status', array(
					            	'class' => 'tacenter',
				            	)),
						        array(
					         		$action,
						            array(
						            	'class' => 'tacenter actions',
					            	),
						        ),
	            			);

		      				echo $this->Html->tableCells(array(
			            		$content,
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