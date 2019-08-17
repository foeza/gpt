<?php
		$params = $this->params;
		$slug = $this->Rumahku->filterEmptyField($params, 'slug');
        $genderOptions = $this->Rumahku->filterEmptyField($_global_variable, 'gender_options');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
		$searchUrl = array(
			'controller' => 'users',
			'action' => 'search',
			'admins',
			'slug' => $slug,
			'admin' => true,
		);

		$dataColumns = array(
            'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('User'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
        );

		if( !empty($admin_rumahku) ) {
			$dataColumns = array_merge($dataColumns, array(
	            'parent_company' => array(
	                'name' => __('Perusahaan'),
	                'width' => '200px;',
					'field_model' => 'UserCompanyParent.name',
	                'filter' => 'text',
	            ),
	        ));
		}

		$dataColumns = array_merge($dataColumns, array(
            'name' => array(
                'name' => __('Nama'),
                'width' => '150px;',
                'field_model' => 'User.full_name',
                'filter' => 'text',
            	'display' => false,
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
                'field_model' => 'UserProfile.no_hp',
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
                'style' => 'width:17%;'
            ),
        ));

		$here_url_params = Router::parse($this->here);
    	$showHideColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'show-hide' );
        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table', array(
        	'thead' => true,
        	'sortOptions' => array(
        		'url' => $here_url_params,
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
			            'action' => 'delete_multiple',
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
		            'url' => array(
		            	'controller' => 'users',
			            'action' => 'add_admin',
						'slug' => $slug,
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
			                $created = $this->Time->niceShort($created);

                            $last_login = $this->Rumahku->filterEmptyField($value, 'LogLogin', 'created');
		      				$log_view = $this->Rumahku->filterEmptyField($value, 'LogView', 'created');
                            $customLastLogin = !empty($last_login)?$this->Time->niceShort($last_login):'-';
			                $customLogView = !empty($log_view)?$this->Time->niceShort($log_view):'-';

		      				// Set Action
							$action	= array(
								array(
									'text' => 'Edit', 
									'url' => array(
				      					'controller' => 'users',
				      					'action' => 'edit_admin',
				      					$id,
										'slug' => $slug,
				      					'admin' => true,
			      					), 
								),
							);

	      					if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
								$action[] = array(
									'text' => 'Password', 
									'url' => array(
				      					'controller' => 'users',
				      					'action' => 'change_password',
				      					$id,
				      					'admin' => true,
			      					), 
									'options' => array(
										'target' => '_blank'
									),
								);
	      					}

							$action	= $this->Rumahku->dropdownButtons($action, array(
								'class' => 'dropdown icon-btn-wrapper', 
							));
	      					$dataTable = array(
		            			array(
		            				$this->Rumahku->buildCheckOption('User', $id, 'default'),
						            array(
						            	'class' => 'tacenter',
					            	)
			         			),
		         			);

		         			if( !empty($admin_rumahku) ) {
								$dataTable = array_merge($dataTable, array(
			         				$this->Rumahku->_getDataColumn($company, 'parent_company'),
						        ));
							}

							$dataTable = array_merge($dataTable, array(
			         			$this->Rumahku->_getDataColumn($name, 'name'),
			         			$this->Rumahku->_getDataColumn($email, 'email'),
			         			$this->Rumahku->_getDataColumn($no_hp, 'no_hp'),
			         			$this->Rumahku->_getDataColumn($no_hp_2, 'no_hp_2'),
			         			$this->Rumahku->_getDataColumn($phone_profile, 'phone_profile'),
			         			$this->Rumahku->_getDataColumn($pin_bb, 'pin_bb'),
			         			$this->Rumahku->_getDataColumn($line, 'line'),
			         			$this->Rumahku->_getDataColumn($gender, 'gender'),
				         		$this->Rumahku->_getDataColumn($customLogView, 'log_view', array(
					            	'class' => 'tacenter',
			         			)),
                                $this->Rumahku->_getDataColumn($customLastLogin, 'last_login', array(
                                    'class' => 'tacenter',
                                )),
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
			'options' => array(
				'url' => array(
					'controller' => 'users',
					'action' => 'admins',
					'slug' => $slug,
					'admin' => true,
					'plugin' => false, 
				),
			),
		));
?>