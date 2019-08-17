<?php
		$searchUrl = array(
			'controller' => 'settings',
			'action' => 'search',
			'mobile_app_versions',
			'admin' => true,
		);
		$dataColumns = array(
			'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('MobileAppVersion'),
                'class' => 'tacenter',
        		'filter' => 'default',
            ),
            'appversion' => array(
                'name' => __('Nama Kategori'),
                'field_model' => 'MobileAppVersion.appversion',
                'filter' => 'text',
            ),
            'version_code' => array(
                'name' => __('Version Code'),
                'field_model' => 'MobileAppVersion.version_code',
                'filter' => 'text',
            ),
            'device' => array(
                'name' => __('Device'),
                'field_model' => 'MobileAppVersion.device',
                'filter' => 'text',
            ),
            'type' => array(
                'name' => __('Tipe Update'),
                'field_model' => 'MobileAppVersion.type',
                'filter' => 'text',
            ),
            'message' => array(
                'name' => __('Pesan'),
                'field_model' => 'MobileAppVersion.message',
                'filter' => 'text',
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'MobileAppVersion.modified',
        		'filter' => 'daterange',
            ),
            'date' => array(
                'name' => __('Dibuat'),
                'field_model' => 'MobileAppVersion.created',
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
		            	'controller' => 'settings',
			            'action' => 'mobile_app_version_delete',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red',
	            		'data-alert' => __('Anda yakin ingin menghapus mobile version ini?'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Tambah'),
		            'url' => array(
		            	'controller' => 'settings',
			            'action' => 'mobile_app_version_add',
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
		      				$id 		= Common::hashEmptyField($value, 'MobileAppVersion.id');
			                $appversion = Common::hashEmptyField($value, 'MobileAppVersion.appversion');
			                $device 	= Common::hashEmptyField($value, 'MobileAppVersion.device');
			                $type 		= Common::hashEmptyField($value, 'MobileAppVersion.type');
			                $version_code = Common::hashEmptyField($value, 'MobileAppVersion.version_code');
			                $message 	= Common::hashEmptyField($value, 'MobileAppVersion.message');

			                $appversion = $this->Rumahku->_callLinkLabel($appversion, array(
			                	'controller' => 'settings',
			                	'action' => 'mobile_app_version_view',
			                	$id,
			                	'admin' => true
			                ));

			                $modified 	= Common::hashEmptyField($value, 'MobileAppVersion.modified');
			                $created 	= Common::hashEmptyField($value, 'MobileAppVersion.created');

			                $modified = $this->Time->niceShort($modified);
			                $created = $this->Time->niceShort($created);

			                $action = $this->AclLink->link($this->Rumahku->icon('rv4-pencil'), array(
		      					'controller' => 'settings',
		      					'action' => 'mobile_app_version_edit',
		      					$id,
		      					'admin' => true,
		  					), array(
								'escape' => false,
							));

							echo $this->Html->tableCells(array(
								array(
						        	array(
						         		$this->Rumahku->buildCheckOption('MobileAppVersion', $id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
				         			$this->Rumahku->_getDataColumn($appversion, 'appversion'),
				         			$this->Rumahku->_getDataColumn($device, 'device'),
				         			$this->Rumahku->_getDataColumn($type, 'type'),
				         			$this->Rumahku->_getDataColumn($version_code, 'version_code'),
				         			$this->Rumahku->_getDataColumn($message, 'message'),
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