<?php
		$recordID = !empty($recordID) ? $recordID : false;
		$self = !empty($self) ? $self : false;
		$_target = !empty($_target)?$_target:false;

		$urlDelete = array(
        	'controller' => 'users',
            'action' => 'remove_agent',
            'user_id' => empty($self) ? $recordID : false,
            'admin' => true,
    	);

    	$urlAdd = array(
        	'controller' => 'users',
            'action' => 'add',
            'user_id' => empty($self) ? $recordID : false,
            'admin' => true,
    	);

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		$searchUrl = !empty($searchUrl)?$searchUrl:array(
			'controller' => 'users',
			'action' => 'search',
			'user_info',
			'admin' => true,
		);
		$dataColumns = array(
            'checkall' => array(
                'name' => $this->Rumahku->buildCheckOption('User'),
                'class' => 'tacenter',
                'filter' => 'default',
            ),
        );        
        $genderOptions = $this->Rumahku->filterEmptyField($_global_variable, 'gender_options');

		// if( !empty($admin_rumahku) ) {
		// 	$dataColumns = array_merge($dataColumns, array(
	 //            'parent_company' => array(
	 //                'name' => __('Perusahaan'),
  //               	'field_model' => 'UserCompanyParent.name',
  //               	'width' => '200px;',
  //               	'filter' => 'text',
  //           		'display' => false,
	 //            ),
	 //        ));
		// }

		$dataColumns = array_merge($dataColumns, array(
            'name' => array(
                'name' => __('Nama'),
                'field_model' => 'User.full_name',
                'width' => '150px;',
                'filter' => 'text',
            ),
            'parent' => array(
                'name' => __('Atasan'),
                'field_model' => 'User.superior_id',
                'width' => '150px;',
                'filter' => 'text',
                'display' => false,
            ),
			'email' => array(
				'name' => __('Email'),
                'width' => '150px;',
				'field_model' => 'User.email',
                'filter' => 'text',
                'display' => false,
			),
			'group_id' => array(
				'name' => __('Divisi'),
                'width' => '150px;',
				'field_model' => 'Group.name',
				 'filter' => array(
                	'type' => 'select',
                	'options' => $divisiOptions,
                	'empty' => __('Pilih Divisi'),
            	),
			),
            'no_hp' => array(
                'name' => __('No. Hp'),
                'width' => '120px;',
                'field_model' => 'UserProfile.no_hp',
                'filter' => 'text',
                'display' => false,
            ),
            'no_hp_2' => array(
                'name' => __('No. Hp 2'),
                'width' => '120px;',
                'field_model' => 'UserProfile.no_hp_2',
            	'display' => false,
                'filter' => 'text',
            ),
            'phone' => array(
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
            'client_count' => array(
                'name' => __('Klien'),
                'class' => 'tacenter',
                'filter' => 'default',
                'field_model' => 'total_client',
                'width' => '80px;',
            ),
            'property_count' => array(
                'name' => __('Properti'),
                'class' => 'tacenter',
                'filter' => 'default',
                'field_model' => 'total_property',
                'width' => '80px;',
            ),
            'property_count_sold' => array(
                'name' => __('Terjual'),
                'class' => 'tacenter',
                'filter' => 'default',
                'field_model' => 'total_property_sold',
                'width' => '80px;',
            ),
            'total_ebrosur' => array(
                'name' => __('eBrosur'),
                'class' => 'tacenter',
                'filter' => 'default',
                'field_model' => 'total_ebrosur',
                'width' => '80px;',
            ),
            'active' => array(
                'name' => __('Aktif'),
                'class' => 'tacenter',
                'filter' => 'default',
                'field_model' => 'active',
                'width' => '80px;',
            ),
            'log_view' => array(
                'name' => __('Session Terakhir'),
                'class' => 'tacenter',
                'width' => '120px;',
                'field_model' => 'LogView.created',
                'filter' => 'daterange',
                'display' => false,
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
                'style' => 'width:5%;'
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
        	'sorting' => array(
        		'buttonDelete' => array(
		            'text' => __('Hapus').$this->Html->tag('span', '', array(
		            	'class' => 'check-count-target',
	            	)),
		            'url' => $urlDelete,
	            	'options' => array(
	            		'class' => 'check-multiple-delete btn-red ajaxModal',
	            		'title' => __('Hapus User'),
	        		),
	        		'frameOptions' => array(
	        			'class' => 'check-multiple-delete hide',
        			),
		        ),
		        'overflowDelete' => true,
		        'buttonAdd' => array(
		            'text' => __('Tambah User'),
		            'url' => $urlAdd,
	            	'options' => array(
	            		'target' => $_target,
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
		      				// debug($value);die();
		      				$dataTable = array();
		      				$parentName = Common::hashEmptyField($value, 'Parent.full_name', '-');

		      				$id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
		      				$name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
		      				$email = $this->Rumahku->filterEmptyField($value, 'User', 'email');
		      				$gender_id = $this->Rumahku->filterEmptyField($value, 'User', 'gender_id', '-');
		      				$created = $this->Rumahku->filterEmptyField($value, 'User', 'created');
		      				$membership_package_id = $this->Rumahku->filterEmptyField($value, 'User', 'membership_package_id');
		      				$gender = $this->Rumahku->filterEmptyField($genderOptions, $gender_id);
		      				
		      				$phone_profile = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'phone', '-');
		      				$no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
		      				$no_hp_2 = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp_2', '-');
		      				$pin_bb = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'pin_bb', '-');
		      				$line = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'line', '-');

		      				$groupName = Common::hashEmptyField($value, 'Group.name');

		      				$last_login = Common::hashEmptyField($value, 'LogLogin.created');
		      				$log_view = Common::hashEmptyField($value, 'LogView.created');
			                
			                $customLastLogin = !empty($last_login)?$this->Time->niceShort($last_login):'-';
			                $customLogView = !empty($log_view)?$this->Time->niceShort($log_view):'-';

		      				$company = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name', '-');
		      				$client_count = $this->Rumahku->filterEmptyField($value, 'User', 'user_client_count', '-');
		      				$property_count = $this->Rumahku->filterEmptyField($value, 'Property', 'cnt', '-');
		      				$property_count_sold = $this->Rumahku->filterEmptyField($value, 'Property', 'cnt_sold', '-');
		      				$total_ebrosur = $this->Rumahku->filterEmptyField($value, 'UserCompanyEbrochure', 'total', '-');

			                $created = $this->Time->niceShort($created);
			                $actived = $this->User->activeUser($value);

						//	b:set action ======================================================================

							$url	= array($id);
							$urlInfo = array_merge($url, array(
								'action' => 'info',
								'user_id' => $recordID,
							));

							$action	= array(
								array(
									'text'		=> 'Info', 
									'url'		=> $urlInfo, 
									'options'	=> array('target' => $_target),
								), 
								array(
									'text'		=> 'Edit', 
									'url'		=> array_merge($url, array(
										'action' => 'edit_user',
										'user_id' => empty($self) ? $recordID : false,
									)), 
									'options'	=> array('target' => $_target), 
								), 
							);

	      					if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() || Configure::read('User.admin') ) {
		      					$action[] = array(
									'text'		=> 'Password', 
									'url'		=> array_merge($url, array('action' => 'change_password')), 
									'options'	=> array('target' => $_target), 
								);
	      					}

	      					if ( $this->Rumahku->_isAdmin() && $membership_package_id ) {
	      						$action[] = array(
									'text'		=> 'Unpremium', 
									'url'		=> array_merge($url, array('action' => 'unpremium_user')),
									'alert' 	=> __('Atur ulang paket premium pada user ini?'),
									'options'	=> array('target' => $_target), 
								);
	      					}

	      	// 				$action[] = array(
	      	// 					'text'		=> 'Hapus', 
								// 'url'		=> array_merge($url, array('action' => 'remove_user')),
								// 'options'	=> array(
								// 	'target' => $_target,
								// 	'class' => 'ajaxModal',
								// ),
								// // 'alert' 	=> __('Anda yakin ingin menghapus user ini ?'), 
	      	// 				);


	      					$dataTable = array(
		            			array(
				         			$this->Rumahku->buildCheckOption('User', $id, 'default'),
						            array(
						            	'class' => 'tacenter',
						            ),
			         			),
		         			);

		     //     			if( !empty($admin_rumahku) ) {
							// 	$dataTable = array_merge($dataTable, array(
			    //      				$this->Rumahku->_getDataColumn($company, 'parent_company'),
						 //        ));
							// }

							$action	= $this->Rumahku->dropdownButtons($action, array(
								'class' => 'dropdown icon-btn-wrapper', 
							));

	      				//	e:set action ======================================================================

							$email	= $this->Html->link($email, 'mailto:'.$email);
							// $name	= sprintf('%s<br>%s', $name, $email);
							$dataTable = array_merge($dataTable, array(
			         			$this->Rumahku->_getDataColumn($this->Html->link($name, $urlInfo), 'name'),
			         			$this->Rumahku->_getDataColumn($parentName, 'parent'),
			         			$this->Rumahku->_getDataColumn($email, 'email'),
			         			$this->Rumahku->_getDataColumn($groupName, 'group_id'),
			         			$this->Rumahku->_getDataColumn($no_hp, 'no_hp'),
			         			$this->Rumahku->_getDataColumn($no_hp_2, 'no_hp_2'),
			         			$this->Rumahku->_getDataColumn($phone_profile, 'phone'),
			         			$this->Rumahku->_getDataColumn($pin_bb, 'pin_bb'),
			         			$this->Rumahku->_getDataColumn($line, 'line'),
			         			$this->Rumahku->_getDataColumn($gender, 'gender'),
			         			$this->Rumahku->_getDataColumn(!empty($client_count)?$this->Html->link($client_count, array(
			      					'controller' => 'users',
			      					'action' => 'client_info',
			      					$id,
			      					'admin' => true,
			         			), array(
	            					'target' => 'blank',
			         			)):'-', 'client_count', array(
					            	'class' => 'tacenter',
				            	)),
			         			$this->Rumahku->_getDataColumn(!empty($property_count)?$this->Html->link($property_count, array(
			      					'controller' => 'properties',
			      					'action' => 'info',
			      					$id,
			      					'status' => 'active-pending',
			      					'admin' => true,
			         			), array(
	            					'target' => 'blank',
			         			)):'-', 'property_count', array(
					            	'class' => 'tacenter',
				            	)),
			         			$this->Rumahku->_getDataColumn(!empty($property_count_sold)?$this->Html->link($property_count_sold, array(
			      					'controller' => 'properties',
			      					'action' => 'info',
			      					$id,
			      					'status' => 'sold',
			      					'admin' => true,
			         			), array(
	            					'target' => 'blank',
			         			)):'-', 'property_count_sold', array(
					            	'class' => 'tacenter',
				            	)),
			         			$this->Rumahku->_getDataColumn(!empty($total_ebrosur)?$this->Html->link($total_ebrosur, array(
			      					'controller' => 'ebrosurs',
			      					'action' => 'info',
			      					$id,
			      					'admin' => true,
			         			), array(
	            					'target' => 'blank',
			         			)):'-', 'total_ebrosur', array(
					            	'class' => 'tacenter',
				            	)),
				            	$this->Rumahku->_getDataColumn($actived, 'active', array(
				            		'class' => 'tacenter',
				            	)),
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
		));
?>