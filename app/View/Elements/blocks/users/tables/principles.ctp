<?php
		$_target = !empty($_target)?$_target:false;
        $genderOptions = $this->Rumahku->filterEmptyField($_global_variable, 'gender_options');
		$searchUrl = !empty($searchUrl)?$searchUrl:array(
			'controller' => 'users',
			'action' => 'search',
			'principles',
			'admin' => true,
		);
		$dataColumns = array(
            'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('User'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
            'parent_company' => array(
                'name' => __('Group'),
                'width' => '150px;',
                'field_model' => 'UserCompanyParent.name',
                'filter' => 'text',
            	'display' => false,
            ),
            'name' => array(
                'name' => __('Nama'),
                'width' => '150px;',
                'field_model' => 'User.full_name',
                'filter' => 'text',
            	'display' => false,
            ),
			'company' => array(
				'name' => __('Perusahaan'),
                'width' => '200px;',
				'field_model' => 'UserCompany.name',
                'filter' => 'text',
			),
			'email' => array(
				'name' => __('Email'),
                'width' => '150px;',
				'field_model' => 'User.email',
                'filter' => 'text',
			),
            'website' => array(
                'name' => __('Website'),
                'width' => '200px;',
                'field_model' => 'UserCompanyConfig.domain',
            	'display' => false,
                'filter' => 'text',
            ),
            'no_hp' => array(
                'name' => __('No. Hp'),
                'width' => '120px;',
                'field_model' => 'UserProfile.no_hp',
            	'display' => false,
                'filter' => 'text',
            ),
            'no_hp_2' => array(
                'name' => __('No. Hp 2'),
                'width' => '120px;',
                'field_model' => 'UserProfile.no_hp_2',
            	'display' => false,
                'filter' => 'text',
            ),
            'phone_profile' => array(
                'name' => __('Telp'),
                'width' => '120px;',
                'field_model' => 'UserProfile.phone',
            	'display' => false,
                'filter' => 'text',
            ),
            'pin_bb' => array(
                'name' => __('PIN BB'),
                'width' => '100px;',
                'field_model' => 'UserProfile.pin_bb',
            	'display' => false,
                'filter' => 'text',
            ),
            'line' => array(
                'name' => __('Line'),
                'width' => '120px;',
                'field_model' => 'UserProfile.line',
            	'display' => false,
                'filter' => 'text',
            ),
            'phone_company' => array(
                'name' => __('Telp Kantor'),
                'width' => '120px;',
                'field_model' => 'UserCompany.phone',
            	'display' => false,
                'filter' => 'text',
            ),
            'phone_company_2' => array(
                'name' => __('Telp Kantor 2'),
                'width' => '120px;',
                'field_model' => 'UserCompany.phone_2',
            	'display' => false,
                'filter' => 'text',
            ),
            'fax_company' => array(
                'name' => __('Fax'),
                'width' => '120px;',
                'field_model' => 'UserCompany.fax',
            	'display' => false,
                'filter' => 'text',
            ),
            'gender' => array(
                'name' => __('Gender'),
                'width' => '120px;',
                'field_model' => 'User.gender_id',
            	'display' => false,
                'filter' => array(
                	'type' => 'select',
                	'options' => $genderOptions,
                	'empty' => __('Pilih Gender'),
            	),
            ),
            'contact_name' => array(
                'name' => __('Contact Person'),
                'width' => '200px;',
                'field_model' => 'UserCompany.contact_name',
            	'display' => false,
                'filter' => 'text',
            ),
            'contact_email' => array(
                'name' => __('Email Contact'),
                'width' => '200px;',
                'field_model' => 'UserCompany.contact_email',
            	'display' => false,
                'filter' => 'text',
            ),
            'division_count' => array(
                'name' => __('Divisi'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
            'user_count' => array(
                'name' => __('User'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
            'client_count' => array(
                'name' => __('Klien'),
                'class' => 'tacenter',
                'filter' => 'default',
                'allow_acl' => array(
                    'controller' => 'users',
                    'action' => 'client_info',
                    'admin' => true,
                ),  
            ),
            'property_count' => array(
                'name' => __('Properti'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
            'log_view' => array(
                'name' => __('Session Terakhir'),
                'class' => 'tacenter',
                'width' => '120px;',
                'field_model' => 'LogView.created',
                'filter' => 'daterange',
            ),
            'last_login' => array(
                'name' => __('Login Terakhir'),
                'class' => 'tacenter',
                'width' => '120px;',
                'field_model' => 'LogLogin.created',
                'filter' => 'daterange',
            	'display' => false,
            ),
            'contract_date' => array(
                'name' => __('Sign Contract'),
                'width' => '120px;',
                'field_model' => 'UserCompanyConfig.contract_date',
                'filter' => 'daterange',
                'display' => false,
            ),
            'user_company_status' => array(
                'name' => __('Tgl Live'),
                'width' => '120px;',
                'field_model' => 'UserCompanyConfig.live_date',
            	'display' => false,
                'filter' => array(
                    'type' => 'select',
                    'options' => Configure::read('__Site.UserCompany.Status'),
                    'empty' => __('Semua'),
                ),
            ),
            'date' => array(
                'name' => __('Tgl Terdaftar'),
                'class' => 'tacenter',
                'width' => '120px;',
                'field_model' => 'User.created',
                'filter' => 'daterange',
                'display' => false,
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
    			'overflowDelete' => true,
        		'buttonDelete' => array(
		            'text' => __('Hapus').$this->Html->tag('span', '', array(
		            	'class' => 'check-count-target',
	            	)),
		            'url' => array(
		            	'controller' => 'users',
			            'action' => 'remove_principle',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'data-alert' => __('Anda yakin ingin menghapus user ini?'),
	            		'class' => 'check-multiple-delete btn-red',
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'buttonAdd' => array(
                    'text' => __('Tambah Principal'),
		            'url' => !empty($urlAddPrinciple)?$urlAddPrinciple:array(
		            	'controller' => 'users',
			            'action' => 'add_principle',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'target' => $_target,
            		),
		        ),
		        'options' => array(
		        	// 'options' => array(
		        	// 	'User.created-desc' => __('Baru ke Lama'),
		        	// 	'User.created-asc' => __('Lama ke Baru'),
	        		// ),
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
		      				$name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
		      				$email = $this->Rumahku->filterEmptyField($value, 'User', 'email');
		      				$gender_id = $this->Rumahku->filterEmptyField($value, 'User', 'gender_id', '-');
		      				$created = $this->Rumahku->filterEmptyField($value, 'User', 'created');
		      				$gender = $this->Rumahku->filterEmptyField($genderOptions, $gender_id);
		      				
		      				$phone_profile = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'phone', '-');
		      				$no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
		      				$no_hp_2 = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp_2', '-');
		      				$pin_bb = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'pin_bb', '-');
		      				$line = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'line', '-');

		      				$company = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name', '-');
		      				$phone = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'phone', '-');
		      				$phone_2 = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'phone_2', '-');
		      				$fax = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'fax', '-');
		      				$contact_name = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'contact_name', '-');
		      				$contact_email = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'contact_email', '-');

                            $live_date = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'live_date');
                            $end_date = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'end_date');
                            $live_date = Common::getCombineDate($live_date, $end_date, '-');
                            $contract_date = Common::hashEmptyField($value, 'UserCompanyConfig.contract_date');
                            $contract_date = !empty($contract_date)?Common::formatDate($contract_date, 'd M Y'):'-';

                            $last_login = $this->Rumahku->filterEmptyField($value, 'LogLogin', 'created');
		      				$log_view = $this->Rumahku->filterEmptyField($value, 'LogView', 'created');
                            $customLastLogin = !empty($last_login)?$this->Time->niceShort($last_login):'-';
			                $customLogView = !empty($log_view)?$this->Time->niceShort($log_view):'-';

		      				$domain = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'domain');
							$packageID = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'membership_package_id');

		      				$property_count = $this->Rumahku->filterEmptyField($value, 'Property', 'cnt');
		      				$agent_count = $this->Rumahku->filterEmptyField($value, 'Agent', 'cnt');
		      				$admin_count = $this->Rumahku->filterEmptyField($value, 'Admin', 'cnt', '-');
		      				$division_count = $this->Rumahku->filterEmptyField($value, 'divisionCount', false, '-');
		      				$user_count = $this->Rumahku->filterEmptyField($value, 'UserCount', false, '-');
		      				$client_count = $this->Rumahku->filterEmptyField($value, 'ClientCount', false, '-');

		      				$group = $this->Rumahku->filterEmptyField($value, 'UserCompanyParent', 'name', '-');
			                $created = $this->Time->niceShort($created);

						//	b:set action ======================================================================

							$url	= array($id);
							$action	= array(
								array(
									'text'		=> 'Info', 
									'url'		=> array_merge($url, array('action' => 'info')), 
									'options'	=> array('target' => $_target), 
								), 
								array(
									'text'		=> 'Edit', 
									'url'		=> array_merge($url, array('action' => 'edit_principle')), 
									'options'	=> array('target' => $_target), 
								), 
							);

	      					if( $this->Rumahku->_isAdmin()) {
		      					$action[] = array(
									'text'		=> 'Password', 
									'url'		=> array_merge($url, array('action' => 'change_password')), 
									'options'	=> array('target' => $_target), 
								);

								if($packageID){
									$action[] = array(
										'text'		=> 'Renewal', 
										'url'		=> array_merge($url, array(
											'controller'	=> 'membership_orders', 
											'action'		=> 'add', 
										)), 
									);	
								}
	      					}

	      					$action	= $this->Rumahku->dropdownButtons($action, array(
								'class' => 'dropdown icon-btn-wrapper', 
							));

	      				//	e:set action ======================================================================

							$email	= $this->Html->link($email, array_merge($url, array('action' => 'info')));
		      				echo $this->Html->tableCells(array(
			            		array(
			            			array(
			            				$this->Rumahku->buildCheckOption('User', $id, 'default', false, 'check-option trigger-bg'),
							            array(
							            	'class' => 'tacenter',
						            	),
				         			),
				         			$this->Rumahku->_getDataColumn($group, 'parent_company'),
				         			$this->Rumahku->_getDataColumn($name, 'name'),
				         			$this->Rumahku->_getDataColumn($this->Html->link($company, array(
				         				'controller' => 'users',
				         				'action' => 'info',
				         				$id,
				         				'admin' => true,
				         			)), 'company'),
				         			$this->Rumahku->_getDataColumn($email, 'email'),
				         			$this->Rumahku->_getDataColumn($this->Html->link($domain, $domain, array(
				         				'target' => '_blank',
			         				)), 'website'),
				         			$this->Rumahku->_getDataColumn($no_hp, 'no_hp'),
				         			$this->Rumahku->_getDataColumn($no_hp_2, 'no_hp_2'),
				         			$this->Rumahku->_getDataColumn($phone_profile, 'phone_profile'),
				         			$this->Rumahku->_getDataColumn($pin_bb, 'pin_bb'),
				         			$this->Rumahku->_getDataColumn($line, 'line'),
				         			$this->Rumahku->_getDataColumn($phone, 'phone_company'),
				         			$this->Rumahku->_getDataColumn($phone_2, 'phone_company_2'),
				         			$this->Rumahku->_getDataColumn($fax, 'fax_company'),
				         			$this->Rumahku->_getDataColumn($gender, 'gender'),
				         			$this->Rumahku->_getDataColumn($contact_name, 'contact_name'),
				         			$this->Rumahku->_getDataColumn($contact_email, 'contact_email'),
				         			$this->Rumahku->_getDataColumn(!empty($division_count) ? $this->Html->link($division_count, array(
                                        'controller' => 'groups',
                                        'action' => 'index',
                                        'admin' => true,
                                        $id,
                                        'cookie' => true,
                                    )) : '-', 'division_count', array(
						            	'class' => 'tacenter',
					            	)),
					            	$this->Rumahku->_getDataColumn($this->Html->link($user_count, array(
                                        'controller' => 'users',
                                        'action' => 'user_info',
                                        $id,
                                        'admin' => true,
                                        'cookie' => true,
                                    )), 'user_count', array(
						            	'class' => 'tacenter',
					            	)),
					            	$this->Rumahku->_getDataColumn( !empty($client_count) ? $this->Html->link( $client_count, array(
					            		'controller' => 'users',
					            		'action' => 'client_info',
					            		$id,
					            		'admin' => true,
                                        'cookie' => true,
					            	)) : '-', 'client_count', array(
                                        'class' => 'tacenter',
                                    )),
				         			$this->Rumahku->_getDataColumn(!empty($property_count)?$this->Html->link($property_count, array(
					         			'controller' => 'properties',
					         			'action' => 'info',
					         			$id,
					         			'admin' => true,
                                        'cookie' => true,
				         			), array(
		            					'target' => 'blank',
				         			)):'-', 'property_count', array(
						            	'class' => 'tacenter',
					            	)),
					         		$this->Rumahku->_getDataColumn($customLogView, 'log_view', array(
						            	'class' => 'tacenter',
				         			)),
					         		$this->Rumahku->_getDataColumn($customLastLogin, 'last_login', array(
						            	'class' => 'tacenter',
				         			)),
                                    $this->Rumahku->_getDataColumn($contract_date, 'contract_date'),
                                    $this->Rumahku->_getDataColumn($live_date, 'user_company_status'),
					         		$this->Rumahku->_getDataColumn($created, 'date', array(
						            	'class' => 'tacenter',
				         			)),
							        array(
						         		$action,
							            array(
							            	'class' => 'tacenter actions',
						            	),
							        ),
		            			),
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