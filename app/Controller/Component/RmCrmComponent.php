<?php
class RmCrmComponent extends Component {
	var $components = array(
		'Auth', 'RmCommon', 'RmImage',
		'RmUser', 'RmProperty', 'RmKpr',
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callDocumentOwner ( $type, $value ) {	
		$kpr_application_id = Common::hashEmptyField($value, 'kpr_application_id', 0);

		switch ($type) {
			case 'client':
				$owner_id = Common::hashEmptyField($value, 'client_id', 0);
				break;
			case 'project':
				$owner_id = Common::hashEmptyField($value, 'crm_project_id', 0);

				if( empty($owner_id) ) {
					$owner_id = $kpr_application_id;
				}
				break;
			case 'owner':
				$owner_id = Common::hashEmptyField($value, 'owner_id', 0);
				break;
			case 'agent':
				$owner_id = Common::hashEmptyField($value, 'agent_id', 0);
				break;
			case 'property':
				$owner_id = Common::hashEmptyField($value, 'property_id', 0);
				break;
			case 'kpr_application':
				$owner_id = Common::hashEmptyField($value, 'kpr_application_id', 0);
				break;
			case 'kprs':
				$owner_id = Common::hashEmptyField($value, 'kpr_id', 0);
				break;
			default:
				$owner_id = $kpr_application_id;
				break;
		}

		return $owner_id;
	}

	function _callBeforeRenderActivity ( $data ) {
		$dataOptions = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivityAttributeOption');
		$dataSetOptions = array();

		if( !empty($dataOptions) ) {
			foreach ($dataOptions as $key => $option) {
				$attr_id = $this->RmCommon->filterEmptyField($option, 'Attribute', 'id');
				$root = $this->RmCommon->filterEmptyField($option, 'AttributeOption', 'parent_id');
				$type = $this->RmCommon->filterEmptyField($option, 'AttributeOption', 'type');
				$opt_id = $this->RmCommon->filterEmptyField($option, 'CrmProjectActivityAttributeOption', 'attribute_option_id');

				$value = $this->RmCommon->filterEmptyField($option, 'CrmProjectActivityAttributeOption', 'attribute_option_value', $opt_id);

				if( !in_array($type, array( 'select', 'radio' )) ) {
					$value = $this->RmCommon->filterEmptyField($option, 'AttributeOptionChild', 'name', $value);
				}

				if( !empty($attr_id) ) {
					if( empty($root) ) {
						$data['CrmProjectActivityAttributeOption']['attribute_option_id'][$attr_id] = $value;
					} else {
						$data['CrmProjectActivityAttributeOption']['attribute_option_child_id'][$attr_id][$opt_id] = $value;
						$dataSetOptions[] = $this->controller->User->CrmProject->AttributeSet->AttributeSetOption->Attribute->AttributeOption->getChids(array(), $root);
					}
				}
			}
		}

		if( !empty($dataSetOptions) ) {
			$data['AttributeSetOption'] = $dataSetOptions;
		}
		$data = $this->RmCommon->dataConverter($data, array(
			'date' => array(
				'CrmProjectActivity' => array(
					'activity_date',
				),
				'CrmProjectPayment' => array(
					'sold_date',
				),
				'KprApplication' => array(
							'birthday'
						),
			),
		), true);

		return $data;
	}

	function _callBeforeSaveKPR ( $data, $value ) {

		if( !empty($data['KprApplication']) ) {
			$save_path = Configure::read('__Site.document_folder');
			$id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'id', null);
			$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id', null);

			$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
			$agent_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id', null);
			$client_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'user_id', null);

			$persen_loan = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'persen_loan');
			$interest_rate = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'interest_rate');
			$credit_total = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'credit_total');

			$price = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'price');
			$down_payment = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'down_payment');
			$credit_total = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'credit_total');
			$same_as_address_ktp = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'same_as_address_ktp');
			$loan = $price - $down_payment;

			$name = $this->RmUser->_getUserFullName($data, 'reverse', 'KprApplication', 'name');
			$first_name = $this->RmCommon->filterEmptyField($name, 'first_name', false, '');
			$last_name = $this->RmCommon->filterEmptyField($name, 'last_name', false, '');

			$data = $this->RmImage->_uploadPhoto( $data, 'KprApplication', 'ktp_file', $save_path );
			$data = $this->RmImage->_uploadPhoto( $data, 'KprApplication', 'income_file', $save_path );

			$data['KprApplication']['first_name'] = $first_name;
			$data['KprApplication']['last_name'] = $last_name;

			$data['KprApplication']['mls_id'] = $mls_id;
			$data['KprApplication']['crm_project_id'] = $id;
			$data['KprApplication']['property_id'] = $property_id;
			$data['KprApplication']['user_id'] = $client_id;
			$data['KprApplication']['agent_id'] = $agent_id;

			$data['KprApplication']['property_price'] = $price;
			$data['KprApplication']['persen_loan'] = $persen_loan;
			$data['KprApplication']['interest_rate'] = $interest_rate;
			$data['KprApplication']['credit_fix'] = $credit_total;

			$data['KprApplication']['down_payment'] = $down_payment;
			$data['KprApplication']['credit_total'] = $credit_total;
			$data['KprApplication']['loan_price'] = $loan;

			if($same_as_address_ktp){
				$this->controller->User->CrmProject->KprApplication->removeValidator(array(
					'address2'
				));
			}


			if( !empty($data['KprApplication']['ktp_file']) || !empty($data['KprApplication']['income_file']) ) {
				$dataUpload = $this->RmCommon->filterEmptyField($data, 'Upload');
				$dataDocument = array();

				if( !empty($data['KprApplication']['ktp_file']) ) {
					$dataDocument['CrmProjectDocument']['document_category_id'] = 2;
					$dataDocument['CrmProjectDocument']['crm_project_id'] = $id;
					$dataDocument['CrmProjectDocument']['property_id'] = $property_id;
					$dataDocument['CrmProjectDocument']['user_id'] = $client_id;
					$dataDocument['CrmProjectDocument']['file'] = $data['KprApplication']['ktp_file'];
					$dataDocument['CrmProjectDocument']['name'] = $this->RmCommon->filterEmptyField($dataUpload, 'ktp_file', 'baseName');
					$dataDocument['CrmProjectDocument']['title'] = __('KTP');
					$dataDocument['CrmProjectDocument']['category'] = 'ktp';

					$data['CrmProjectDocument'][] = $dataDocument;
				}

				if( !empty($data['KprApplication']['income_file']) ) {
					$dataDocument['CrmProjectDocument']['document_category_id'] = 4;
					$dataDocument['CrmProjectDocument']['crm_project_id'] = $id;
					$dataDocument['CrmProjectDocument']['property_id'] = $property_id;
					$dataDocument['CrmProjectDocument']['user_id'] = $client_id;
					$dataDocument['CrmProjectDocument']['file'] = $data['KprApplication']['income_file'];
					$dataDocument['CrmProjectDocument']['name'] = $this->RmCommon->filterEmptyField($dataUpload, 'income_file', 'baseName');
					$dataDocument['CrmProjectDocument']['title'] = __('Slip Gaji');
					$dataDocument['CrmProjectDocument']['category'] = 'slip_gaji';

					$data['CrmProjectDocument'][] = $dataDocument;
				}
			}
			
			$data = $this->RmCommon->dataConverter($data, array(
				'price' => array(
					'KprApplication' => array(
						'income',
						'household_fee',
						'other_installment',
						'loan_price',
						'down_payment',
					),
				),
				'date' => array(
					'KprApplication' => array(
						'birthday',
					),
				),
			));

			$this->controller->set('classCol', 'col-sm-8');
		}

		return $data;
	}
	
	function _callBeforeSavePayment ( $data, $value ) {
		if( !empty($data) ) {
			$client_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'client_id', null);
			$kpr_application_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id');
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'id');
			
			$data['CrmProject']['id'] = $crm_project_id;
			$data['CrmProjectPayment']['client_id'] = $client_id;

			$data = $this->RmCommon->dataConverter($data, array(
				'price' => array(
					'CrmProjectPayment' => array(
						'price',
						'down_payment',
					),
				),
				'date' => array(
					'CrmProjectPayment' => array(
						'sold_date',
						'end_date',
					),
				),
			));

			if( !empty($kpr_application_id) ) {
				$data['KprApplication'] = array(
					'id' => $kpr_application_id,
					'sold_date' => $this->RmCommon->filterEmptyField($data, 'CrmProjectPayment', 'sold_date'),
					'property_price' => $this->RmCommon->filterEmptyField($data, 'CrmProjectPayment', 'price'),
					'credit_total' => $this->RmCommon->filterEmptyField($data, 'CrmProjectPayment', 'credit_total'),
				);
			}
		}
		
		return $data;
	}
	
	function _callBeforeViewPayment ( $data ) {
		if( !empty($data) ) {
			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'CrmProjectPayment' => array(
						'sold_date',
						'end_date',
					),
				),
			), true);
		}
		
		return $data;
	}
	
	function _callBeforeViewKPR ( $value, $data = array() ) {

		$dataKpr = $this->RmCommon->filterEmptyField($value, 'KprApplication');

		if( !empty($dataKpr) ) {
			$ktp_file = $this->RmCommon->filterEmptyField($dataKpr, 'ktp_file');
			$income_file = $this->RmCommon->filterEmptyField($dataKpr, 'income_file');

			$dataKpr['ktp_file_hide'] = $ktp_file;
			$dataKpr['income_file_hide'] = $income_file;

			$data['KprApplication'] = $dataKpr;
		} else {
			$property_type = $this->RmCommon->filterEmptyField($value, 'Property', 'property_action_id');

			$client_name = $this->RmCommon->filterEmptyField($value, 'UserClient', 'full_name');
			$dataUser = $this->RmCommon->filterEmptyField($value, 'UserClient');

			$data['KprApplication'] = $dataUser;
			$data['KprApplication']['name'] = $client_name;

			if($property_type == 3){
				$data['KprApplication']['bank_apply_category_id'] = 2;
			}else if($property_type == 1){
				$data['KprApplication']['bank_apply_category_id'] = 1;
			}
		}

		$data['CrmProjectActivity']['activity_date'] = $this->RmCommon->currentDate('d/m/Y');
		$data['CrmProjectActivity']['activity_time'] = $this->RmCommon->currentDate('H:i');
		
		return $data;
	}

    function _callBeforeSave ( $data ) {
        if( !empty($data) ) {
			$is_cancel = $this->RmCommon->filterEmptyField($data, 'CrmProject', 'is_cancel');
			$user = Common::hashEmptyField($data, 'CrmProject.user');

			if( !empty($user) ) {
				$user = explode('|', $user);
				$user = reset($user);
				$email = trim($user);

				$value = $this->controller->User->getData('first', array(
					'conditions'	=> array(
						'User.email' => $email, 
					), 
				), array(
					'role'		=> 'agent', 
					'status'	=> 'active', 
					'company'	=> true, 
				));

			 	$data = Hash::insert($data, 'CrmProject.user_id', Common::hashEmptyField($value, 'User.id'));
			}

			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'CrmProject' => array(
						'project_date',
						'completed_date',
					),
				),
			));
			$data = $this->RmUser->_callGetClientData($data);

			if( !empty($is_cancel) ) {
				$data['CrmProject']['attribute_set_id'] = Configure::read('__Site.Global.Variable.CRM.Cancel');
			}
        }

        return $data;
    }

    function _callBeforeSaveActivity ( $data, $value, $status_id = false, $attribute_set_id = null ) {
		$save_path = Configure::read('__Site.document_folder');
    	$completed = Configure::read('__Site.Global.Variable.CRM.Completed');
    	$finalisasi = Configure::read('__Site.Global.Variable.CRM.Finalisasi');

    	if( empty($attribute_set_id) ) {
			$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
		}

		$dataPayment = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment');
		$crmProjectPayment = Common::hashEmptyField($data, '$dataPayment');

        if( !empty($data) ) {
			$attribute_set_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivity', 'attribute_set_id', $attribute_set_id);
			
			$data['CrmProjectActivity']['attribute_set_id'] = $status_id;

			if( !empty($dataPayment) ) {
				$data['CrmProjectPayment']['type'] = $this->RmCommon->filterEmptyField($data, 'CrmProjectPayment', 'type', 'cash');
			}

			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'CrmProjectActivity' => array(
						'activity_date',
					),
					'CrmProjectPayment' => array(
						'sold_date',
						'end_date',
					),
				),
				'price' => array(
					'CrmProjectPayment' => array(
						'price',
						'down_payment',
					),
				),
			));

			$bookingFee = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivity', 'booking_fee');

			if( !empty($bookingFee) ) {
				$data = $this->RmImage->_uploadPhoto($data, 'CrmProjectActivity', 'booking_fee', $save_path, false, Configure::read('__Site.allowed_all_ext'));
			}
        }

        if(!empty($data['KprApplication'])){
        	$data = $this->RmImage->_uploadPhoto($data, 'KprApplication', 'ktp_file', $save_path, false, Configure::read('__Site.allowed_all_ext'));
        	$data = $this->RmImage->_uploadPhoto($data, 'KprApplication', 'income_file', $save_path, false, Configure::read('__Site.allowed_all_ext'));
        }

		if( in_array($attribute_set_id, $completed) && !empty($dataPayment) ) {
			$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id', null);
			$sold_by_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'user_id');

			$property_action_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_action_id', null);
			$currency_id = $this->RmCommon->filterEmptyField($value, 'Property', 'currency_id');
			$commission = $this->RmCommon->filterEmptyField($value, 'Property', 'commission');

			$price_sold = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'price');
			$sold_date = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'sold_date');
			$end_date = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'end_date');

			$client_name = $this->RmCommon->filterEmptyField($value, 'UserClient', 'full_name');
			$client_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'user_id', null);

			$client_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivity', 'client_id', $client_id);
			$agent_email = $this->RmCommon->filterEmptyField($value, 'Agent', 'email');

			if( !empty($property_id) ) {
				$data['PropertySold'] = array(
					'client_id' => $client_id,
					'client_name' => $client_name,
					'sold_by_name' => $agent_email,
					'property_id' => $property_id,
					'property_action_id' => $property_action_id,
					'currency_id' => 1,
					'price_sold' => $price_sold,
					'sold_date' => $sold_date,
					'end_date' => $end_date,
				);
			}

			$data = $this->RmProperty->_callBeforeSold($data, $property_id);
		} else if( in_array($attribute_set_id, $finalisasi) && empty($crmProjectPayment) ) {
			$value = $this->RmKpr->_callDataByCRM($value);

			$crm_project_id = Common::hashEmptyField($value, 'CrmProject.id', null);
			$client_id = Common::hashEmptyField($value, 'CrmProject.client_id', null);
			$kpr_id = Common::hashEmptyField($value, 'Kpr.id', null);
			$created = Common::hashEmptyField($value, 'Kpr.created', null, array(
				'date' => 'Y-m-d',
			));
			$created_time = Common::hashEmptyField($value, 'Kpr.created', null, array(
				'date' => 'H:i',
			));
			$sold_date = Common::hashEmptyField($value, 'Kpr.sold_date', $created);
			$property_price = Common::hashEmptyField($value, 'Kpr.property_price', 0);

			$data['Payment'] = array(
				'CrmProjectActivity' => array(
					'client_id' => $client_id,
					'activity_date' => $created,
					'activity_time' => $created_time,
					'attribute_set_id' => $attribute_set_id,
					'crm_project_id' => $crm_project_id,
					'step' => 'payment',
				),
				'CrmProjectPayment' => array(
					'type' => 'kpr',
					'sold_date' => $sold_date,
					'price' => $property_price,
					'crm_project_id' => $crm_project_id,
					'client_id' => $client_id,
				),
				'CrmProject' => array(
					'id' => $crm_project_id,
					'Kpr' => array(
						'id' => $kpr_id,
						'crm_project_id' => $crm_project_id,
					),
				),
			);
		}

        return $data;
    }

    function _callBeforeSaveDocument ( $data, $value ) {
        if( !empty($data) ) {
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'id', 0);
			$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id', 0);
			$client_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'client_id', 0);
			$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id', 0);
			$agent_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'user_id', 0);

			$document_category_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'document_category_id', null);
			$session_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'session_id');

			$documentCategory = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('first', array(
				'conditions' => array(
					'DocumentCategory.id' => $document_category_id,
				),
			));
			$type = $this->RmCommon->filterEmptyField($documentCategory, 'DocumentCategory', 'type');
			$category_name = $this->RmCommon->filterEmptyField($documentCategory, 'DocumentCategory', 'name');
			$title = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'title', $category_name);

			$documents = $this->controller->User->CrmProject->CrmProjectDocument->getMerge(array(), $session_id, 'CrmProjectDocument.session_id');
			$dataDocuments = $this->RmCommon->filterEmptyField($documents, 'CrmProjectDocument');
			$dataSaveDocuments = array();
        	if( !empty($dataDocuments) ) {
        		foreach ($dataDocuments as $key => $doc) {
					$doc_id = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'id');
					$save_path = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'save_path');
					$file = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'file');

					$dataTemp = array(
						'id' => $doc_id,
						'document_category_id' => $document_category_id,
						'title' => $title,
						'save_path' => $save_path,
						'document_type' => $type,
						'file' => $file,
					);

					switch ($type) {
						case 'client':
							$dataTemp['owner_id'] = $client_id;
							break;
						case 'project':
							$dataTemp['owner_id'] = $crm_project_id;
							break;
						case 'owner':
							$dataTemp['owner_id'] = $owner_id;
							break;
						case 'agent':
							$dataTemp['owner_id'] = $agent_id;
							break;
						case 'property':
							$dataTemp['owner_id'] = $property_id;
							break;
					}

        			$dataSaveDocuments[]['CrmProjectDocument'] = $dataTemp;
        		}

        		$data['SaveDocument'] = $dataSaveDocuments;
        	}
        }

        return $data;
    }

    function _callGetListClient ( $data ) {
    	$values = array();

    	if( !empty($data['Owner']) ) {
            $client_id = $this->RmCommon->filterEmptyField($data, 'Owner', 'user_id');
            $client_name = $this->RmCommon->filterEmptyField($data, 'Owner', 'full_name');

    		$values[$client_id] = sprintf(__('%s | Vendor'), $client_name);
    	}
    	if( !empty($data['UserClient']) ) {
            $client_id = $this->RmCommon->filterEmptyField($data, 'UserClient', 'user_id');
            $client_name = $this->RmCommon->filterEmptyField($data, 'UserClient', 'full_name');

    		$values[$client_id] = sprintf(__('%s | Klien'), $client_name);
    	}

    	return $values;
    }

	function _callGlobal ( $value, $attribute_set_id = false ) {
		if( empty($attribute_set_id) ) {
			$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
		}

		$attributeSetValue = $this->controller->User->CrmProject->AttributeSet->getData('first', array(
			'conditions' => array(
				'AttributeSet.id' => $attribute_set_id,
			),
		));
		$attributeSetValue = $this->controller->User->CrmProject->AttributeSet->getDataList($attributeSetValue, $value);
		$clients = $this->_callGetListClient($value);
		$attributeSets = $this->controller->User->CrmProject->AttributeSet->getData('list', array(
			'conditions' => array(
            	'AttributeSet.show' => 1,
				'AttributeSet.scope' => 'crm',
				'AttributeSet.id <>' => Configure::read('__Site.Global.Variable.CRM.Cancel'),
			),
		));

		$this->controller->set(compact(
			'attributeSetValue', 'clients',
			'attributeSets'
		));
	}

	function _callSetApplicationType ( $property_type_id ) {
		if( $property_type_id == 3 ) {
			return 2;
		} else {
			return 1;
		}
	}

    function _callSetColActivity ( $attribute = false, $payment_type = false ) {

        switch ($attribute) {
            case 'submission-kpr' :
            	if( $payment_type == 'kpr' ) {
                    $classCol = 'col-sm-12';
                } else {
                    $classCol = 'col-sm-8';
                }
                break;

            default:
                $classCol = 'col-sm-8';
                break;
        }

        return $classCol;
    }

    function _callBeforeRender ( $value ) {
		$projectStatus = $this->RmCommon->filterEmptyField($value, 'AttributeSet', 'slug');
    	if( $projectStatus == 'finalisasi' ) {
			$documentCategories = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('all', array(
				'conditions' => array(
					'DocumentCategory.is_required' => 1,
				),
			));
			$document_category_id = Set::extract('/DocumentCategory/id', $documentCategories);
			$documentCategories = $this->controller->User->CrmProject->CrmProjectDocument->getByCategories($documentCategories, $value);
			// GET Variable Bank
			$property_type_id 		= $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id');
			$region_id 				= $this->RmCommon->filterEmptyField($value, 'PropertyAddress', 'region_id');
			$city_id 				= $this->RmCommon->filterEmptyField($value, 'PropertyAddress', 'city_id');
			$price 					= $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_price');
				// GET Bank
			$kpr_id 	= $this->RmCommon->filterEmptyField($value,'Kpr','id');
			$credit_total 			= $this->RmCommon->filterEmptyField($value,'CrmProjectPayment','credit_total');

			$kpr_bank_data = $this->controller->User->Kpr->KprBank->getData('all', array(
				'conditions' => array(
					'KprBank.kpr_id' => $kpr_id,
				)
			));
			$kpr_bank = Set::extract('/KprBank/bank_id', $kpr_bank_data);

			$banks = $this->controller->User->Kpr->KprBank->Bank->getData('all',array(
				'conditions' => array(
					'Bank.id NOT' => $kpr_bank,
					'BankSetting.periode_installment >= ' => $credit_total
				),
				'contain' => array(
					'BankSetting',
				),
			));
			$banks = $this->controller->User->Kpr->KprBank->Bank->BankCommissionSetting->getKomisi($banks, array(
				'property_type_id' => $property_type_id,
				'region_id' => $region_id,
				'city_id' => $city_id,
				'price' => $price,
			));
			$banks = $this->RmKpr->getSummaryKpr($value, $banks);
			// End Bank
		}

		$bankApplyCategories = $this->controller->User->Kpr->BankApplyCategory->getData('list', array(
			'fields' => array(
				'BankApplyCategory.id', 'BankApplyCategory.category_name',
			),
		));
			
		$this->controller->set(compact(
			'bankApplyCategories', 'documentCategories', 'banks', 
			'kpr_bank', 'kpr_bank_data'
		));

    }

	function _callAttributeOptions () {
		$attributeSets = $this->controller->User->CrmProject->AttributeSet->getData('list', array(
			'conditions' => array(
				'AttributeSet.scope' => 'crm',
			),
		));
		$attributeOptions = $this->controller->User->CrmProject->CrmProjectActivity->CrmProjectActivityAttributeOption->AttributeOption->getData('list', false, array(
			'parent' => true,
		));

		$this->controller->set('attributeSets', $attributeSets);
		$this->controller->set('attributeOptions', $attributeOptions);
	}
}
?>