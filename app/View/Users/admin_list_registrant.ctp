<?php
		$cleanActionName = str_replace('admin_', '', $this->action);
		$controller		 = $this->params->controller;
		$searchUrl		 = array('controller' => $controller, 'action' => 'search', $cleanActionName, 'admin' => TRUE);
		$isAdmin 		 = $this->Rumahku->_isAdmin();

        $genderOptions = $this->Rumahku->filterEmptyField($_global_variable, 'gender_options');

        $optionsStatus = array(
			'pending'	=> __('Pending'), 
			'process'	=> __('Process'),
			'expired'	=> __('Expired'), 
			'paid'		=> __('Paid'), 
			'failed'	=> __('Failed'), 
			'waiting'	=> __('Waiting'), 
		);

        $url_export = sprintf('%s%s', FULL_BASE_URL, $this->here);
		$sorting['buttonCustom'] = array(
			'text'	=> __('Export Data'),
			'url'	=> sprintf('%s/export:excel', $url_export),
		);

		$sorting['buttonAdd'] = array(
			'text'	=> __('Import'),
			'url'	=> array(
				'controller' => 'users',
				'action' => 'import_data_registrant',
				'admin' => true,
			),
		);

        $currency = Configure::read('__Site.config_currency_code');
		$dataColumns = array();
		
		if( $isAdmin ){
			$dataColumns = array_merge($dataColumns, array(
				'checkall' => array(
					'name' => $this->Rumahku->buildCheckOption('UserIntegratedOrderAddon'), 
					'class' => 'tacenter',
		    		'filter' => 'default',
				),
			));
		}

		$dataColumns = array_merge($dataColumns, array(
			'invoice_number' => array(
				'name' => __('No. Invoice'), 
				'field_model' => 'UserIntegratedOrderAddon.invoice_number',
	    		'width' => '150px;',
	    		'filter' => 'text',
			),
            'name' => array(
                'name' => __('Nama Agent'),
                'field_model' => 'UserIntegratedOrder.name_applicant',
                'width' => '150px;',
                'filter' => 'text',
            ),
            'phone' => array(
                'name' => __('Telp'),
                'width' => '120px;',
                'field_model' => 'UserIntegratedOrder.phone',
                'filter' => 'text',
            	'display' => false,
            ),
			'company_name'	=> array(
				'name' => __('Perusahaan'), 
				'field_model' => 'UserIntegratedOrder.company_name',
	    		'width' => '120px;',
	    		'filter' => 'text',
			),
			'total_price' => array(
				'name' =>sprintf('%s (%s)', __('Total'), trim($currency)), 
				'field_model' => 'UserIntegratedOrderAddon.total_price', 
				'class' => 'tacenter',
	    		'width' => '100px;',
	    		'filter' => 'default',
			),
			'status' => array(
				'name' => __('Status'), 
				'field_model' => 'UserIntegratedOrderAddon.payment_status', 
				'class' => 'tacenter',
	    		'width' => '100px;',
	            'filter' => array(
	            	'type' => 'select',
	            	'options' => $optionsStatus,
	            	'empty' => __('Status'),
	        	),
			),
            'date' => array(
                'name' => __('Tgl Mendaftar'),
                'class' => 'tacenter',
                'width' => '120px;',
                'field_model' => 'UserIntegratedOrder.created',
                'filter' => 'daterange',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'tacenter',
                'style' => 'width:17%;'
            ),
		));

    	$showHideColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'show-hide' );
        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table', array(
        	'thead' => true,
        	'sortOptions' => array(
        		'ajax' => true,
    		),
    		'table_ajax' => true,
    		'custom_wrapper_search' => '.table-header .pagination-info > span,.pagination-content ul.pagination,.filter-footer,.table.grey > thead > tr:first-child,.form-table-search table tbody, .table-header'
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
        	'sorting' => array_merge($sorting, array(
		        'options' => array(
	        		'showcolumns' => array(
	        			'options' => $showHideColumn,
	    			),
	        	),
			)),
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
						$decimalPlaces = 2;
		      			foreach( $values as $key => $value ) {
		      				$id 		 = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.id');
		      				$status  	 = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.payment_status');
		      				$invNumber   = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.invoice_number');
		      				$totalPrice  = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.total_price', 0);
		      				$totalPrice  = $this->Number->currency($totalPrice, '', array('places' => $decimalPlaces));
		      				
		      				$name 		  = Common::hashEmptyField($value, 'UserIntegratedOrder.name_applicant');
		      				$companyName  = Common::hashEmptyField($value, 'UserIntegratedOrder.company_name');
		      				$phone 	  	  = Common::hashEmptyField($value, 'UserIntegratedOrder.phone');
		      				$created 	  = Common::hashEmptyField($value, 'UserIntegratedOrder.created');
		      				$created 	  = $this->Time->niceShort($created);

		      				$badgeClass = NULL;
							switch($status){
								case 'pending' : 
									$badgeClass = NULL; 
								break;
								case 'process' : 
									$badgeClass = 'badge-warning'; 
								break;
								case 'cancelled' :
									$badgeClass = 'badge-danger'; 
								break;
								case 'expired' :
									$badgeClass = 'badge-inverse'; 
								break;
								case 'paid' : 
									$badgeClass = 'badge-success'; 
								break;
								case 'failed' :
									$badgeClass = 'badge-danger'; 
								break;
								case 'waiting' :
									$badgeClass = 'badge-warning'; 
								break;
							}

		      				$status = isset($optionsStatus[$status]) ? $this->Html->tag('span', $optionsStatus[$status], array('class' => sprintf('badge %s', $badgeClass))) : '-';

		      				// Set Action
		      				$action = $this->Html->link(__('Detail'), array(
		      					'controller' => 'users',
		      					'action' => 'view_detail_checkout',
		      					'admin' => true,
		      					$id,
		      					$invNumber,
		  					), array(
								'escape' => false,
							));

	      					$dataTable = array(
		            			array(
				         			$this->Rumahku->buildCheckOption('User', $id, 'default'),
						            array(
						            	'class' => 'tacenter',
						            ),
			         			),
		         			);

							$dataTable = array_merge($dataTable, array(
			         			$this->Rumahku->_getDataColumn($invNumber, 'invoice_number'),
			         			$this->Rumahku->_getDataColumn($name, 'name'),
			         			$this->Rumahku->_getDataColumn($phone, 'phone'),
			         			$this->Rumahku->_getDataColumn($companyName, 'company_name'),
			         			$this->Rumahku->_getDataColumn($totalPrice, 'total_price', array('class' => 'taright')),
			         			$this->Rumahku->_getDataColumn($status, 'status', array('class' => 'tacenter')),
				         		$this->Rumahku->_getDataColumn($created, 'date', array(
					            	'class' => 'tacenter',
			         			)),
						        array(
					         		$action,
						            array(
						            	'class' => 'tacenter actions',
					            	),
						        ),
	            			));

		      				echo $this->Html->tableCells(array(
			            		$dataTable,
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