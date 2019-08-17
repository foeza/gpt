<?php
		$recordID = !empty($recordID)?$recordID:false;
		$globalData = Configure::read('Global.Data');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $genderOptions = $this->Rumahku->filterEmptyField($globalData, 'gender_options');

		$searchUrl = array(
			'controller' => 'kpr',
			'action' => 'search',
			'info',
			1,
			$recordID,
			'admin' => true,
		);

		echo $this->element('blocks/users/tabs/info');
?>
<div class="tabs-box">
	<?php			
	        echo $this->element('blocks/common/forms/search/backend', array(
	        	'placeholder' => __('Cari berdasarkan Kode, Nama, dan Email Klien'),
	        	'url' => $searchUrl,
	    	));
	?>
	<div class="table-responsive table-green">
		<?php
				if( !empty($values) ) {
					$dataColumns = array();

					if( !empty($admin_rumahku) ) {
						$dataColumns = array_merge($dataColumns, array(
				            'company' => array(
				                'name' => __('Perusahaan'),
		                		'field_model' => 'UserCompany.name',
		                		'data-options' => 'field:\'company\',width:200',
				            ),
				        ));
					}

					$dataColumns = array_merge($dataColumns, array(
			            'code' => array(
			                'name' => __('Kode'),
	                		'field_model' => 'KprBank.code',
		                	'data-options' => 'field:\'code\',width:150',
			            ),
						'created' => array(
			                'name' => __('Tgl Pengajuan'),
	                		'field_model' => 'Kpr.created',
			                'data-options' => 'field:\'created\',width:100',
						),
						'bank' => array(
			                'name' => __('Bank'),
	                		'field_model' => 'Bank.name',
			                'data-options' => 'field:\'bank\',width:150',
	                		'fix_column' => !empty($admin_rumahku)?true:false,
						),
						'bank_apply_category_id' => array(
			                'name' => __('Jenis'),
	                		'field_model' => 'Kpr.bank_apply_category_id',
			                'data-options' => 'field:\'bank_apply_category_id\',width:80',
	                		'fix_column' => !empty($admin_rumahku)?false:true,
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						'from_web' => array(
			                'name' => __('Asal Aplikasi'),
	                		'field_model' => 'KprBank.from_web',
			                'data-options' => 'field:\'from_web\',width:120',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						'agent' => array(
			                'name' => __('Agen'),
	                		'field_model' => 'AgentProperty.full_name',
			                'data-options' => 'field:\'agent\',width:150',
						),
						'agent_email' => array(
			                'name' => __('Email Agen'),
	                		'field_model' => 'AgentProperty.email',
			                'data-options' => 'field:\'agent_email\',width:250',
						),
						'mls_id' => array(
			                'name' => __('ID Properti'),
	                		'field_model' => 'Kpr.mls_id',
			                'data-options' => 'field:\'mls_id\',width:100',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						'location' => array(
			                'name' => __('Lokasi'),
			                'data-options' => 'field:\'location\',width:100',
						),
						'property_price' => array(
			                'name' => __('Harga (%s)', Configure::read('__Site.config_currency_symbol')),
			                'data-options' => 'field:\'property_price\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
						'loan_price' => array(
			                'name' => __('Nilai Pengajuan (%s)', Configure::read('__Site.config_currency_symbol')),
			                'data-options' => 'field:\'loan_price\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
						'credit_total' => array(
							'name' => __('Lama Pinjaman (Thn.)'),
			                'data-options' => 'field:\'credit_total\',width:150',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						'client' => array(
							'name' => __('Klien'),
			                'data-options' => 'field:\'client\',width:150',
						),
						'email' => array(
							'name' => __('Email Klien'),
			                'data-options' => 'field:\'email\',width:250',
						),
						'ktp' => array(
							'name' => __('KTP'),
			                'data-options' => 'field:\'ktp\',width:150',
						),
						'gender' => array(
							'name' => __('Jenis Kelamin'),
			                'data-options' => 'field:\'gender\',width:150',
						),
						'status_marital' => array(
							'name' => __('Status Menikah'),
			                'data-options' => 'field:\'status_marital\',width:150',
						),
						'birthplace' => array(
							'name' => __('Tempat Lahir'),
			                'data-options' => 'field:\'birthplace\',width:150',
						),
						'birthday' => array(
							'name' => __('Tgl Lahir'),
			                'data-options' => 'field:\'birthday\',width:100',
						),
						'address' => array(
							'name' => __('Alamat Sesuai KTP'),
			                'data-options' => 'field:\'address\',width:250',
						),
						'address_2' => array(
							'name' => __('Alamat Domisili'),
			                'data-options' => 'field:\'address_2\',width:250',
						),
						'phone' => array(
							'name' => __('No Telp'),
			                'data-options' => 'field:\'phone\',width:150',
						),
						'no_hp' => array(
							'name' => __('No Handphone'),
			                'data-options' => 'field:\'no_hp\',width:150',
						),
						'client_company' => array(
							'name' => __('Perusahaan (Tempat Bekerja)'),
			                'data-options' => 'field:\'client_company\',width:250',
						),
						'job_type' => array(
							'name' => __('Jenis Pekerjaan'),
			                'data-options' => 'field:\'job_type\',width:150',
						),
						'income' => array(
							'name' => __('Penghasilan'),
			                'data-options' => 'field:\'income\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
						'household_fee' => array(
							'name' => __('Pengeluaran'),
			                'data-options' => 'field:\'household_fee\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
			        ));

					if( !empty($documentCategories) ) {
						foreach ($documentCategories as $idx => $cat) {
							$cat_slug = $this->Rumahku->filterEmptyField($cat, 'DocumentCategory', 'slug');
							$cat_name = $this->Rumahku->filterEmptyField($cat, 'DocumentCategory', 'name');

							$dataColumns = array_merge($dataColumns, array(
					            $cat_slug => array(
					                'name' => $cat_name,
				                	'data-options' => __('field:\'%s\',width:150', $cat_slug),
					                'align' => 'center',
					                'mainalign' => 'center',
					            )
				            ));
						}
					}

			        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
		?>
		<table class="table grey easyui-datagrid" style="'width: 100%;height: 550px;" singleSelect="true">
	    	<?php
	                if( !empty($fieldColumn) ) {
	                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn), array(
							'frozen' => 'true',
						));
	                }
	        ?>
	      	<tbody>
	      		<?php
		      			foreach( $values as $key => $value ) {
							$created = $this->Rumahku->filterEmptyField($value, 'Kpr', 'created', '-');
							$document_status = $this->Rumahku->filterEmptyField($value, 'Kpr', 'document_status');
			        		$status = $this->Kpr->_callStatus( $document_status );
							$bank_code = $this->Rumahku->filterEmptyField($value, 'Bank', 'code');
							$bank_name = $this->Rumahku->filterEmptyField($value, 'Bank', 'name');
							$category = $this->Rumahku->filterEmptyField($value, 'BankApplyCategory', 'code');
							
							$mls_id = $this->Rumahku->filterEmptyField($value, 'Kpr', 'mls_id', '-');
							$dataAddress = $this->Rumahku->filterEmptyField($value, 'PropertyAddress');
							$region = $this->Rumahku->filterEmptyField($value, 'Region', 'name');
							$city = $this->Rumahku->filterEmptyField($value, 'City', 'name');
							$documents = $this->Rumahku->filterEmptyField($value, 'Document');

							if( !empty($region) ) {
								$location = array();

								if( !empty($city) ) {
									$location[] = $city;
								}

								$location[] = $region;

								if( !empty($location) ) {
									$location = implode(', ', $location);
								}
							}
							
							$installment = !empty($value['KprBankInstallment'][0])?$value['KprBankInstallment'][0]:false;
							$client = !empty($value['KprApplication'][0])?$value['KprApplication'][0]:false;
							$spouse = !empty($value['KprApplication'][1])?$value['KprApplication'][1]:false;
							
							$client_job_type_id = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'job_type_id');
							$property_price = $this->Rumahku->filterEmptyField($installment, 'KprBankInstallment', 'property_price', '-', true, 'currency');
							$loan_price = $this->Rumahku->filterEmptyField($installment, 'KprBankInstallment', 'loan_price', '-', true,'currency');
							$credit_total = $this->Rumahku->filterEmptyField($installment, 'KprBankInstallment', 'credit_total');
							
							$company = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name', '-');

							$agent_name = $this->Rumahku->filterEmptyField($value, 'AgentProperty', 'full_name');
							$agent_email = $this->Rumahku->filterEmptyField($value, 'AgentProperty', 'email');

							$name = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'full_name', '-');
							$email = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'email', '-');
							$gender_id = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'gender_id');
							$gender = $this->Rumahku->filterEmptyField($genderOptions, $gender_id);
							$phone = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'phone', '-');
							$no_hp = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'no_hp');
							$no_hp_2 = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'no_hp_2');
							$ktp = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'ktp', '-');
							$birthplace = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'birthplace', '-');
							$birthday = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'birthday', '-');
							$address = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'address', '-');
							$address_2 = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'address_2', '-');
							$same_as_address_ktp = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'same_as_address_ktp', '-');
							$client_company = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'company', '-');
							$job_type = $this->Rumahku->filterEmptyField($client, 'JobType', 'company', '-');
							$income = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'income', '-', true, 'currency');
							$household_fee = $this->Rumahku->filterEmptyField($client, 'KprApplication', 'household_fee', '-', true, 'currency');

							if( empty($no_hp) ) {
								$no_hp = '-';
							} else if( !empty($no_hp) && !empty($no_hp_2) ) {
								$no_hp = __('%s / %s', $no_hp, $no_hp_2);
							}

							$code = $this->Rumahku->filterEmptyField($value, 'Kpr', 'code', '-');
							$code = $this->Rumahku->filterEmptyField($value, 'KprBank', 'code', $code);
			  				$from_web = $this->Rumahku->filterEmptyField($value, 'KprBank', 'from_web', '-');
							$status_marital = $this->Rumahku->filterEmptyField($value, 'KprBank', 'status_marital', 'single');
							$status_marital = $this->Rumahku->filterEmptyField($globalData, 'status_marital', $status_marital);
	      					$dataTable = array();

							if( !empty($bank_code) ) {

			         			if( !empty($admin_rumahku) ) {
									$dataTable = array_merge($dataTable, array(
							            $company,
							        ));
								}

								$dataTable = array_merge($dataTable, array(
									$code,
									array(
										$created,
										array(
							            	'class' => 'tacenter',
						            	),
									),
									__('%s (%s)', $bank_name, $bank_code),
									array(
										$category,
										array(
							            	'class' => 'tacenter',
						            	),
									),
									$from_web,
									$agent_name,
								 	$agent_email,
									array(
										$mls_id,
										array(
							            	'class' => 'tacenter',
						            	),
									),
									!empty($location)?$location:'-',
									array(
										!empty($property_price)?$property_price:'-',
										array(
							            	'class' => 'taright',
						            	),
									),
									array(
										!empty($loan_price)?$loan_price:'-',
										array(
							            	'class' => 'taright',
						            	),
									),
									array(
										!empty($credit_total)?$credit_total:'-',
										array(
							            	'class' => 'tacenter',
						            	),
									),
									$name,
									$email,
									$ktp,
									$gender,
									array(
										$status_marital,
										array(
							            	'class' => 'tacenter',
						            	),
									),
									$birthplace,
									array(
										$birthday,
										array(
							            	'class' => 'tacenter',
						            	),
									),
									$address,
									$address_2,
									$phone,
									$no_hp,
									$client_company,
									$job_type,
									array(
										!empty($income)?$income:'-',
										array(
							            	'class' => 'taright',
						            	),
									),
									array(
										!empty($household_fee)?$household_fee:'-',
										array(
							            	'class' => 'taright',
						            	),
									),
								));

								if( !empty($documentCategories) ) {
									foreach ($documentCategories as $idx => $cat) {
										$cat_slug = $this->Rumahku->filterEmptyField($cat, 'DocumentCategory', 'slug');
										$cat_name = $this->Rumahku->filterEmptyField($cat, 'DocumentCategory', 'name');

										$dataTable = array_merge($dataTable, array(
								            array(
												!empty($documents[$cat_slug])?'&#8730;':'x',
												array(
									            	'class' => 'tacenter',
								            	),
											),
							            ));
									}
								}

			      				echo $this->Html->tableCells(array(
				            		$dataTable,
						        ));
			      			}
						}
	      		?>
	      	</tbody>
	    </table>
	    <?php 
	    		} else {
	    			echo $this->Html->tag('p', __('Data belum tersedia'), array(
	    				'class' => 'alert alert-warning'
					));
	    		}
		?>
	</div>
	<?php 
			if($values){
				echo $this->element('blocks/common/pagination');			
			}
	?>
</div>