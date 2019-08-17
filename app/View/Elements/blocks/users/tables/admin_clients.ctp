<?php
		$_action = isset($_action)?$_action:true;
		$_target = !empty($_target)?$_target:false;
		$recordID = !empty($recordID) ? $recordID : false;

		$searchUrl = !empty($searchUrl)?$searchUrl:array(
			'controller' => 'users',
			'action' => 'search',
			'clients',
			$recordID,
			'admin' => true,
		);
		$priceOptions = $this->Rumahku->filterEmptyField($_global_variable, 'price_options');
		$genderOptions = $this->Rumahku->filterEmptyField($_global_variable, 'gender_options');

		if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
			$allowAddDelete = true;
		} else {
			$allowAddDelete = false;
		}

		if( !empty($allowAddDelete) ) {
			$optionDelete = array(
	            'text' => __('Hapus').$this->Html->tag('span', '', array(
	            	'class' => 'check-count-target',
            	)),
	            'url' => array(
	            	'controller' => 'users',
		            'action' => 'delete_client_multiple',
		            'admin' => true,
            	),
            	'options' => array(
        			'data-alert' => __('Anda yakin ingin menghapus user ini?'),
            		'class' => 'check-multiple-delete btn-red',
        		),
        		'frameOptions' => array(
        			'class' => 'check-multiple-delete hide',
    			),
	        );
		} else {
			$optionDelete = null;
		}

		$dataColumns = array();

		if( !empty($allowAddDelete) ) {
        	$dataColumns = array(
	            'checkall' => array(
	                'name' => $this->Rumahku->buildCheckOption('User'),
	                'class' => 'tacenter',
                	'filter' => 'default',
	            ),
            );
        }

		// if( $this->Rumahku->_isAdmin() ) {
		// 	$dataColumns = array_merge($dataColumns, array(
	 //            'company' => array(
	 //                'name' => __('Perusahaan'),
	 //                'width' => '200px;',
		// 			'field_model' => 'UserCompany.name',
	 //                'filter' => 'text',
	 //            ),
  //           ));
		// }

		 $dataColumns = array_merge($dataColumns, array(
            'name' => array(
                'name' => __('Nama'),
                'width' => '150px;',
                'field_model' => 'UserClient.full_name',
                'filter' => 'text',
            ),
            'client_type' => array(
                'name' => __('Tipe Klien'),
                'width' => '100px;',
                'field_model' => 'ClientType.name',
                'filter' => array(
                	'type' => 'select',
                	'options' => $clientTypes,
                	'empty' => __('Pilih Tipe'),
            	),
            ),
            'email' => array(
                'name' => __('Email'),
                'width' => '150px;',
				'field_model' => 'User.email',
                'filter' => 'text',
            ),
            'no_hp' => array(
                'name' => __('No. Hp'),
                'width' => '120px;',
                'field_model' => 'UserClient.no_hp',
                'filter' => 'text',
            ),
            'no_hp_2' => array(
                'name' => __('No. Hp 2'),
                'width' => '120px;',
                'field_model' => 'UserClient.no_hp_2',
            	'display' => false,
                'filter' => 'text',
            ),
            'phone_profile' => array(
                'name' => __('No. Telepon'),
                'width' => '120px;',
                'field_model' => 'UserClient.phone',
                'filter' => 'text',
            	'display' => false,
            ),
            'pin_bb' => array(
                'name' => __('PIN BB'),
                'width' => '100px;',
                'field_model' => 'UserClient.pin_bb',
            	'display' => false,
                'filter' => 'text',
            ),
            'line' => array(
                'name' => __('Line'),
                'width' => '120px;',
                'field_model' => 'UserClient.line',
            	'display' => false,
                'filter' => 'text',
            ),
            'gender' => array(
                'name' => __('Gender'),
                'width' => '150px;',
                'field_model' => 'User.gender_id',
            	'display' => false,
                'filter' => array(
                	'type' => 'select',
                	'options' => $genderOptions,
                	'empty' => __('Pilih Gender'),
            	),
            ),
            'date' => array(
                'name' => __('Tgl Terdaftar'),
                'class' => 'tacenter',
                'width' => '120px;',
                'field_model' => 'UserClient.created',
                'filter' => 'daterange',
            	'display' => false,
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'tacenter',
            ),
        ));

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
        		'buttonDelete' => $optionDelete,
		        'buttonAdd' => array(
		        	'text' => __('Tambah Klien'),
		            'url' => array(
		            	'controller' => 'users',
			            'action' => 'add_client',
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
		      				$id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
		      				$email = $this->Rumahku->filterEmptyField($value, 'User', 'email');
		      				$group_id = $this->Rumahku->filterEmptyField($value, 'User', 'group_id');
		      				$gender_id = $this->Rumahku->filterEmptyField($value, 'User', 'gender_id', '-');
		      				$gender = $this->Rumahku->filterEmptyField($genderOptions, $gender_id);

		      				$client_id = $this->Rumahku->filterEmptyField($value, 'UserClient', 'id');
		      				$company_id = $this->Rumahku->filterEmptyField($value, 'UserClient', 'company_id');
		      				$name = $this->Rumahku->filterEmptyField($value, 'UserClient', 'full_name');
		      				$telp = $this->Rumahku->filterEmptyField($value, 'UserClient', 'no_hp', '-');
		      				$no_hp_2 = $this->Rumahku->filterEmptyField($value, 'UserClient', 'no_hp_2', '-');
		      				$phone = $this->Rumahku->filterEmptyField($value, 'UserClient', 'phone', '-');
		      				$pin_bb = $this->Rumahku->filterEmptyField($value, 'UserClient', 'pin_bb', '-');
		      				$line = $this->Rumahku->filterEmptyField($value, 'UserClient', 'line', '-');
		      				$created = $this->Rumahku->filterEmptyField($value, 'UserClient', 'created');

		      				$company = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name');
		      				$type = $this->Rumahku->filterEmptyField($value, 'ClientType', 'name');
		      				$is_pic_agent = $this->Rumahku->filterEmptyField($value, 'UserClientRelation', 'primary');

			                $created = $this->Time->niceShort($created);
		      				$custom_link_detail = $this->Html->link($name, $this->Html->url(array(
		      					'controller' => 'users',
		      					'action' => 'client_properties',
		      					$client_id,
		      					'admin' => true
		      				)));

		      			//	b:set action ======================================================================

		      				$url	= array($client_id);
							$action	= array();

		      				if( in_array($logged_group, array( 3,5,11,19,20 )) ) {
		      					$changed_password = $this->Rumahku->filterEmptyField($value, 'UserClient', 'change_password');
		      					
		      					if( !empty($changed_password) ) {
		      						$action[] = $this->Html->link(__('Kirim Ulang Invitasi'), array_merge($url, array(
										'action' => 'invite_client', 
									)), null, __('Anda yakin ingin mengirim ulang invitasi?'));
		      					} else {
		      						$action[] = $this->Html->link(__('Kirim Invitasi'), array_merge($url, array(
										'action' => 'invite_client', 
									)));
		      					}
		      				}

							$action[] = array(
								'text'	=> 'Edit', 
								'url'	=> array_merge($url, array('action' => 'edit_client')), 
							);

							$action[] = array(
								'text'	=> 'Manage', 
								'url'	=> array_merge($url, array('action' => 'client_properties')), 
							);

	      					if( in_array($logged_group, array( 3,5,11,19,20 )) ) {
	      						$action[] = array(
									'text'	=> 'Relasi', 
									'url'	=> array_merge($url, array('action' => 'client_relation')), 
								);
	      					}

	      					if( ($this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin()) && $group_id == 10 ) {
		      					$action[] = array(
									'text'		=> 'Password', 
									'url'		=> array_merge($url, array('action' => 'change_password', 'UserClient')), 
									'options'	=> array('target' => '_blank'), 
								);
	      					}

							$action	= $this->Rumahku->dropdownButtons($action, array(
								'class' => 'dropdown icon-btn-wrapper', 
							));

						//	e:set action ======================================================================

							$content = array();
	      					if( !empty($allowAddDelete) ) {
	      						$content = array(
			            			array(
			            				$this->Rumahku->buildCheckOption('User', $client_id, 'default'),
							            array(
							            	'class' => 'tacenter',
						            	),
				         			),
		            			);
	      					}

							// if( $this->Rumahku->_isAdmin() ) {
							// 	$content = array_merge($content, array(
				   //       			$this->Rumahku->_getDataColumn($company, 'company'),
					  //           ));
							// }

	            			$content = array_merge($content, array(
			         			$this->Rumahku->_getDataColumn($custom_link_detail, 'name'),
			         			$this->Rumahku->_getDataColumn($type, 'client_type'),
			         			$this->Rumahku->_getDataColumn($email, 'email'),
			         			$this->Rumahku->_getDataColumn($telp, 'no_hp'),
			         			$this->Rumahku->_getDataColumn($no_hp_2, 'no_hp_2'),
			         			$this->Rumahku->_getDataColumn($phone, 'phone_profile'),
			         			$this->Rumahku->_getDataColumn($pin_bb, 'pin_bb'),
			         			$this->Rumahku->_getDataColumn($line, 'line'),
			         			$this->Rumahku->_getDataColumn($gender, 'gender'),
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