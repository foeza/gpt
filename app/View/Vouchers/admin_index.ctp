<?php

	$cleanActionName	= str_replace('admin_', '', $this->action);
	$controller			= $this->params->controller;
	$searchUrl			= array('controller' => $controller, 'action' => 'search', $cleanActionName, 'admin' => TRUE);

	$codeMechanismOptions = array(
		'manual' => __('Manual'), 
		'auto' => __('Otomatis'),
	);
	$periodTypeOptions = array(
		'periodic' => __('Periodik'), 
		'unlimited' => __('Tidak Terbatas'),
	);
	$applyToOptions = array(
		'all' => __('Semua Paket Membership'),
		'manual' => __('Paket Membership Terpilih'),
	);

	if($this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin()){
		$allowAddDelete = TRUE;
	}
	else{
		$allowAddDelete = FALSE;
	}

	$dataColumns = array();

	if($allowAddDelete === TRUE){
		$dataColumns = array(
			'checkall' => array(
				'name' => $this->Rumahku->buildCheckOption('Voucher'),
				'class' => 'tacenter',
        		'filter' => 'default',
			),
		);
	}

	$dataColumns = array_merge($dataColumns, array(
		'name'			=> array(
			'name' => __('Nama'), 
			'field_model' => 'Voucher.name',
            'width' => '150px;',
            'filter' => 'text',
		),
		'code_mechanism'	=> array(
			'name' => __('Tipe'), 
			'field_model' => 'Voucher.code_mechanism', 
			'class' => 'tacenter',
            'width' => '100px;',
            'filter' => $codeMechanismOptions,
        	'display' => false,
		),
		'period_type'	=> array(
			'name' => __('Masa Berlaku'), 
			'field_model' => 'Voucher.period_type', 
			'class' => 'tacenter',
            'width' => '120px;',
            'filter' => array(
            	'type' => 'select',
            	'options' => $periodTypeOptions,
            	'empty' => __('Pilih'),
        	),
        	'display' => false,
		),
		'date_from'		=> array(
			'name' => __('Periode Mulai'), 
			'field_model' => 'Voucher.date_from', 
			'class' => 'tacenter',
            'width' => '100px;',
            'filter' => 'daterange',
		),
		'date_to'		=> array(
			'name' => __('Periode Selesai'), 
			'field_model' => 'Voucher.date_to', 
			'class' => 'tacenter',
            'width' => '100px;',
            'filter' => 'daterange',
		),
		'apply_to'		=> array(
			'name' => __('Berlaku Untuk'), 
			'field_model' => 'Voucher.apply_to', 
            'width' => '120px;',
            'filter' => array(
            	'type' => 'select',
            	'options' => $applyToOptions,
            	'empty' => __('Pilih'),
        	),
		),
		'modified'		=> array(
			'name' => __('Diubah'), 
			'field_model' => 'Voucher.modified', 
			'class' => 'tacenter',
            'width' => '100px;',
    		'filter' => 'daterange',
		),
		'date'		=> array(
			'name' => __('Dibuat'), 
			'field_model' => 'Voucher.created', 
			'class' => 'tacenter',
            'width' => '100px;',
    		'filter' => 'daterange',
		),
		'action'		=> array(
			'name' => __('Aksi'), 
			'class' => 'tacenter',
		),
	));

	$sorting = array(
		'overflowDelete'	=> TRUE,
		'options'			=> array(
			'url'		=> $searchUrl,
			'optionsStatus' => array(
				'active'	=> __('Aktif'), 
				'disabled'	=> __('Non-Aktif'),
			), 
		),
		'buttonAdd'			=> array(
			'text'		=> __('Tambah'),
			'url'		=> array(
				'controller'	=> $this->params->controller,
				'action'		=> 'add',
				'admin'			=> TRUE,
			),
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

	if($allowAddDelete){
		$sorting['buttonDelete'] = array(
			'text'		=> __('Hapus').$this->Html->tag('span', '', array('class' => 'check-count-target')),
			'url'		=> array('controller' => $this->params->controller, 'action' => 'delete', 'admin' => TRUE),
			'options'	=> array(
        		'class' => 'check-multiple-delete btn-red',
				'data-alert'	=> __('Anda yakin ingin menghapus data terpilih ?'),
			),
    		'frameOptions' => array(
    			'class' => 'check-multiple-delete hide',
			),
		);
	}

	echo($this->element('blocks/common/forms/search/backend', array(
    	'_form' => false,
    	'with_action_button' => false,
    	'new_action_button' => true,
		'sorting' => array_merge($sorting, 
		array(
	        'options' => array(
        		'showcolumns' => array(
        			'options' => $showHideColumn,
    			),
        	),
		)),
	)));

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

				$periodTypes = array('periodic' => __('Periodik'), 'unlimited' => __('Tidak Terbatas'));
				foreach($records as $key => $record){
					$id			= $this->Rumahku->filterEmptyField($record, 'Voucher', 'id');
					$code_mechanism		= $this->Rumahku->filterEmptyField($record, 'Voucher', 'code_mechanism');
					$apply_to	= $this->Rumahku->filterEmptyField($record, 'Voucher', 'apply_to');
					$name		= $this->Rumahku->filterEmptyField($record, 'Voucher', 'name');
					$periodType	= $this->Rumahku->filterEmptyField($record, 'Voucher', 'period_type');
					$dateFrom	= $this->Rumahku->filterEmptyField($record, 'Voucher', 'date_from');
					$dateFrom	= $dateFrom ? $this->Rumahku->customDate($dateFrom, 'd M Y') : '';
					$dateTo		= $this->Rumahku->filterEmptyField($record, 'Voucher', 'date_to');
					$dateTo		= $dateTo ? $this->Rumahku->customDate($dateTo, 'd M Y') : '';
					$created	= $this->Rumahku->filterEmptyField($record, 'Voucher', 'created');
					$modified	= $this->Rumahku->filterEmptyField($record, 'Voucher', 'modified');
					
					$apply_to	= $this->Rumahku->filterEmptyField($applyToOptions, $apply_to);
					$periodType	= $this->Rumahku->filterEmptyField($periodTypeOptions, $periodType);
					$code_mechanism	= $this->Rumahku->filterEmptyField($codeMechanismOptions, $code_mechanism);

					$detailLink	= $this->Html->link($name, $this->Html->url(array(
						'controller' => $this->params->controller, 
						'action' => 'edit', 
						$id, 
						'admin' => TRUE,
					)));
					$action		= $this->Html->link($this->Rumahku->icon('rv4-pencil'), array(
						'controller' => $this->params->controller, 
						'action' => 'edit', 
						$id, 
						'admin' => TRUE
					), array(
						'escape' => false,
					));
					$content	= array();

	                $modified = $this->Time->niceShort($modified);
	                $created = $this->Time->niceShort($created);

					if($allowAddDelete){
						$content = array(
							array(
								$this->Rumahku->buildCheckOption('Voucher', $id, 'default'),
								array(
									'class' => 'tacenter',
								),
							),
						);
					}

					$content = array_merge($content, array(
	         			$this->Rumahku->_getDataColumn($detailLink, 'name'),
	         			$this->Rumahku->_getDataColumn($code_mechanism, 'code_mechanism'),
	         			$this->Rumahku->_getDataColumn($periodType, 'period_type', array('class' => 'tacenter')),
	         			$this->Rumahku->_getDataColumn($dateFrom, 'date_from'),
	         			$this->Rumahku->_getDataColumn($dateTo, 'date_to'),
	         			$this->Rumahku->_getDataColumn($apply_to, 'apply_to'),
	         			$this->Rumahku->_getDataColumn($modified, 'modified'),
	         			$this->Rumahku->_getDataColumn($created, 'date'),
						array($action, array('class' => 'tacenter actions')),
					));

					echo($this->Html->tableCells(array($content)));
				}

			?>
		</tbody>
	</table>
    <div class="filter-footer">
	    <?php 
				if( empty($records) ) {
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