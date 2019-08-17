<?php
class ApiController extends AppController {
	var $uses = array(
		'ApiUser', 'Property'
	);

	public $components = array(
		'RmImage', 'RmProperty',
		'Rest.Rest' => array(
			'actions' => array(
	            'add_property' => array(
	                'extract' => array(
	                	'msg', 'status', 
	                	'id', 'validationErrors'
	            	),
	            ),
	            'add_user' => array(
	                'extract' => array(
	                	'msg', 'status', 
	                	'id', 'validationErrors'
	            	),
	            ),
	            'add_ebrosur' => array(
	                'extract' => array(
	                	'msg', 'status', 
	                	'id', 'validationErrors'
	            	),
	            ),
	            'add_berita' => array(
	                'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'add_career' => array(
	                'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'add_banner_slider' => array(
	                'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'add_developer_slider' => array(
	            	'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'add_faq' => array(
	            	'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'add_partnership' => array(
	            	'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'add_message' => array(
	            	'extract' => array(
	                	'msg', 'status'
	            	),
	            ),
	            'properties' => array(
	            	'extract' => array(
	                	'msg', 'status', 'data',
	                ),
	            ),
			),
            'debug' => 2,
        ),
	);

	function beforeFilter() {
		parent::beforeFilter();
		$data = $this->RmCommon->filterEmptyField($this->request->data, 'ApiUser');

		$msg = __('API Tidak bisa di akses');
		$status = 'error';
		$valid = false;

		if( !empty($data['apikey']) && !empty($data['apipass']) && !empty($data['merge_vars']) ) {
			$apikey = $data['apikey'];
			$api_secret = $data['apipass'];
			$action = $data['action'];
			
			$cek_api_registration = $this->ApiUser->get_access($apikey, $api_secret);

			if($cek_api_registration){
				$this->Auth->allow($action);
				$valid = true;

				$this->request->data = $this->RmCommon->filterEmptyField($data, 'merge_vars', false, array());
			}
		}

		if(!$valid){
			$this->set(compact('msg', 'status'));
		}

		$this->layout = 'ajax';	
		$this->autoLayout = false;
		$this->autoRender = true;
   	}

	function add_property(){
		$data = $this->request->data;

		if(!empty($data)){
			$this->Property 	= $this->User->Property;

			$mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');

			$property = $this->Property->getData('first', array(
				'conditions' => array(
					'Property.mls_id' => array($mls_id, 'C'.$mls_id)
				)
			), array(
				'status' => 'all'
			));

			$data['is_api'] = true;

			$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'id');

			if(empty($property)){
				if( empty($this->data_company['UserCompanyConfig']['is_approval_property']) ){
					$active 	= $this->RmCommon->filterEmptyField($data, 'Property', 'active');
					$status 	= $this->RmCommon->filterEmptyField($data, 'Property', 'status');
					$sold 		= $this->RmCommon->filterEmptyField($data, 'Property', 'sold');
					$published 	= $this->RmCommon->filterEmptyField($data, 'Property', 'published');
					$deleted 	= $this->RmCommon->filterEmptyField($data, 'Property', 'deleted');

					if( empty($active) && !empty($status) && empty($sold) && !empty($published) && empty($deleted) ){
						$data['Property']['active'] = 1;
					}
				}

				$data = $this->RmProperty->_callBeforeSave($data);

				$validateBasic = $this->Property->doBasic( $data, false, true, false, false );
				
				if(!empty($validateBasic['status']) && $validateBasic['status'] == 'success'){
					$validateBasic = $this->Property->doBasic( $data, false, false, false, false );
				}

				$property_id = !empty($validateBasic['id'])?$validateBasic['id']:false;

				$text = 'menambah';
				
				if(!empty($data['PropertyRevision']) && !empty($property_id)){
					foreach ($data['PropertyRevision'] as $key => $value) {
						$data['PropertyRevision'][$key]['property_id'] = $property_id;
					}

					$rev = $this->Property->PropertyRevision->doSave($data['PropertyRevision'], $data, $property_id);
				}

				$validationErrors = array();
				if(!empty($validateBasic['validationErrors'])){
					$validationErrors = $validateBasic['validationErrors'];
				}

				if( !empty($property_id) ) {
					$data['PropertyAddress']['property_id'] = $property_id;
					$data['PropertyAsset']['property_id'] = $property_id;

					if(!empty($data['PropertySold'])){
						$data['PropertySold']['property_id'] = $property_id;

						$data = $this->RmCommon->dataConverter($data, array(
							'date' => array(
								'PropertySold' => array(
									'sold_date',
									'end_date',
								),
							),
							'price' => array(
								'PropertySold' => array(
									'price_sold',
								),
							)
						), false);
					}
			
					// Just Taken Data for Asset
					$asset = $this->Property->PropertyAsset->getData('first', array(
						'conditions' => array(
							'PropertyAsset.property_id' => $property_id
						)
					));

					// Just Taken Data for Sold
					$address = $this->Property->PropertyAddress->getData('first', array(
						'conditions' => array(
							'PropertyAddress.property_id' => $property_id
						)
					));

					$address_id 	= $this->RmCommon->filterEmptyField($address, 'PropertyAddress', 'id');
					$asset_id 		= $this->RmCommon->filterEmptyField($asset, 'PropertyAsset', 'id');

					$dataAsset 		= $this->RmProperty->_callChangeToRequestData( $data, 'PropertyFacility', 'facility_id' );
					$dataAsset 		= $this->RmProperty->_callChangeToRequestData( $data, 'PropertyPointPlus', 'name' );
					
					$validateAsset 	 = $this->Property->PropertyAsset->doSave( $data, false, false, $property_id, $asset_id );
					$validateAddress = $this->Property->PropertyAddress->doAddress( $data, false, false, $property_id, $address_id );
					
					$validateSold = false;
					if(!empty($data['PropertySold'])){
						$validateSold = $this->Property->PropertySold->doSave( $data, $property_id );
					}

					$statusBasic 	= !empty($validateBasic['status'])?$validateBasic['status']:'error';
					$statusAddress 	= !empty($validateAddress['status'])?$validateAddress['status']:'error';
					$statusAsset 	= !empty($validateAsset['status'])?$validateAsset['status']:'error';

					$validateAssetError 	= !empty($validateAsset['validationErrors']) ? $validateAsset['validationErrors'] : array();
					$validateAddressError 	= !empty($validateAddress['validationErrors']) ? $validateAddress['validationErrors'] : array();
					$validateSoldError 		= !empty($validateSold['validationErrors']) ? $validateSold['validationErrors'] : array();

					if(!empty($validateAssetError)){
						$validationErrors = array_merge($validationErrors, $validateAssetError);
					}
					if(!empty($validateAddressError)){
						$validationErrors = array_merge($validationErrors, $validateAddressError);
					}
					if(!empty($validateSoldError)){
						$validationErrors = array_merge($validationErrors, $validateSoldError);
					}

					if( $statusBasic == 'error' ) {
						$this->RmCommon->setProcessParams(array(
							'msg' => __('Mohon lengkapi info dasar properti Anda'),
							'status' => 'error',
							'validationErrors' => $validationErrors
						), false, array(
							'noRedirect' => true
						));
					} else if ( $statusAddress == 'error' ) {
						$this->RmCommon->setProcessParams(array(
							'msg' => __('Mohon lengkapi info alamat properti Anda'),
							'status' => 'error',
							'validationErrors' => $validationErrors
						), false, array(
							'noRedirect' => true
						));
					} else if ( $statusAsset == 'error' ) {
						$this->RmCommon->setProcessParams(array(
							'msg' => __('Mohon lengkapi info spesifikasi properti Anda'),
							'status' => 'error',
							'validationErrors' => $validationErrors
						), false, array(
							'noRedirect' => true
						));
					} else {
						if(!empty($data['PropertyMedias'])){
							$data_medias = $data['PropertyMedias'];

							$this->Property->PropertyMedias->deleteAll(array(
								'PropertyMedias.property_id' => $property_id
							));

							foreach ($data_medias as $key => $value_medias) {
								$value_medias['PropertyMedias']['property_id'] = $property_id;

								$data['name'] = $this->RmCommon->filterEmptyField($value_medias, 'PropertyMedias', 'name');

								$result = $this->Property->PropertyMedias->doSave($value_medias);

								if(empty($result->error) && !empty($data['name'])){
									$options = array(
										'fullsize' => true
									);

									$extension = $this->RmImage->_getExtension($data['name']);

									$this->RmImage->_generateThumbnail($data, 'name', Configure::read('__Site.property_photo_folder'), $options, $extension);
								}
							}
						}

						if(!empty($data['PropertyVideos'])){
							$data_medias = $data['PropertyVideos'];

							$this->Property->PropertyVideos->deleteAll(array(
								'PropertyVideos.property_id' => $property_id
							));

							foreach ($data_medias as $key => $value_medias) {
								$data_medias[$key]['PropertyVideos']['property_id'] = $property_id;
							}

							$this->Property->PropertyVideos->doSave($data_medias);
						}

						$this->RmCommon->setProcessParams(array(
							'msg' => sprintf(__('Berhasil %s properti'), $text),
							'status' => 'success'
						), false, array(
							'noRedirect' => true
						));
					}
				}else{
					$this->RmCommon->setProcessParams(array(
						'msg' => sprintf(__('Gagal menambah properti %s'), $mls_id),
						'status' => 'error',
						'validationErrors' => $validationErrors
					), false, array(
						'noRedirect' => true
					));
				}
			}else{
				$this->RmCommon->setProcessParams(array(
					'msg' => sprintf(__('Properti %s sudah pernah di migrasikan sebelumnya dengan ID Properti PRIME %s'), $mls_id, $property_id),
					'status' => 'success'
				), false, array(
					'noRedirect' => true
				));
			}

			$this->set('id', $property_id);
		}
	}

	function add_user(){
		$data = $this->request->data;
		$email = $this->RmCommon->filterEmptyField($data, 'User', 'email');
		
		if(!empty($data) && !empty($email)){
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $email
				)
			), array(
				'status' => 'all'
			));
			
			$id 		= $this->RmCommon->filterEmptyField($user, 'User', 'id');

			$group_id 	= $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
			$parent_id 	= $this->RmCommon->filterEmptyField($user, 'User', 'parent_id');

			if(!empty($id)){
				$data['User']['id'] = $id;
			}
			
			$data['is_api'] = true;

			if(empty($id)){
				$group_id 	= $this->RmCommon->filterEmptyField($data, 'User', 'group_id');
				$parent_id 	= $this->RmCommon->filterEmptyField($data, 'User', 'parent_id');
				
				$data['User']['auth_password'] = $this->RmCommon->filterEmptyField($data, 'User', 'password');
				// debug($data);die();
				$result 	= $this->User->doAdd( $data, $parent_id, false, $group_id );
				
				if(!empty($result['status']) && !empty($result['id']) && $result['status'] == 'success'){
					$id = $result['id'];

					if(empty($user)){
						$user = $this->User->getData('first', array(
							'conditions' => array(
								'User.id' => $id
							)
						));
					}

					$this->User->doSaveProfession( $id, $user, $data );
				}

				if(!empty($data['UserCompany']) && !empty($user) && $group_id == 3){
					$user	= $this->User->UserCompany->getMerge($user, $id);

					$company_id	= $this->RmCommon->filterEmptyField($user, 'UserCompany', 'id');

					$result_comp = $this->User->UserCompany->doSave( $id, $user, $data, $company_id );

					if(!empty($result_comp['validationErrors']) && !empty($result['validationErrors'])){
						$result['validationErrors'] = array_merge($result['validationErrors'], $result_comp['validationErrors']);
					}else if(!empty($result_comp['validationErrors'])){
						$result['validationErrors'] = $result_comp['validationErrors'];
					}
				}
			}else{
				$result = array(
					'msg' => sprintf(__('User %s sudah pernah di migrasikan sebelumnya.'), $id),
					'status' => 'success'
				);
			}

			$this->set('id', $id);
			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect' => true
			));
		}
	}

	function add_ebrosur(){
		$data 			= $this->request->data;
		$user_id 		= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'user_id');
		$code 			= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'code');
		$property_id 	= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_id');
		$ebrosur_photo 	= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'ebrosur_photo');
		$property_photo	= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_photo');
		$id 			= false;
		
		if(!empty($data) && !empty($user_id)){
			unset($data['UserCompanyEbrochure']['id']);

			if(!empty($code)){
				$ebrosur = $this->User->UserCompanyEbrochure->getData('first', array(
					'conditions' => array(
						'UserCompanyEbrochure.code' => $code
					)
				), array(
					'status' => 'all',
					'mine' => false,
					'company' => false
				));
				
				$data['UserCompanyEbrochure']['id'] = $id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'id');
			}

			if(!empty($property_id)){
				$property = $this->User->Property->getData('first', array(
					'conditions' => array(
						'Property.id' => $property_id
					)
				), array(
					'company' => false,
					'status' => 'all'
				));

				$data['UserCompanyEbrochure']['mls_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'mls_id');
			}
			
			$image_name = $this->RmImage->copy_image_to_uploads($ebrosur_photo);
			
			if(!empty($ebrosur_photo) && !empty($image_name)){
				$data['UserCompanyEbrochure']['ebrosur_photo'] = $image_name;
			}

			if(!empty($property_photo)){
				$image_name_property = $this->RmImage->copy_image_to_uploads($property_photo, Configure::read('__Site.property_photo_folder'), Configure::read('__Site.ebrosurs_photo'), 'filename');
			
				if(!empty($image_name_property)){
					$data['UserCompanyEbrochure']['filename'] = $image_name_property;
				}
			}

			$result = $this->User->UserCompanyEbrochure->doSave($data, false, false, $id, true);

			if(!empty($result['id'])){
				$id = $result['id'];
			}

			$this->set('id', $id);
			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect' => true
			));
		}
	}

	function add_berita(){
		$data 		= $this->request->data;
		$user_id 	= $this->RmCommon->filterEmptyField($data, 'Advice', 'user_id');

		if(!empty($data)){
			$result = $this->User->Advice->doSave($data, $user_id, false, false, true);

			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect' => true
			));
		}
	}

	function add_career(){
		$data 		= $this->request->data;
		$user_id 	= $this->RmCommon->filterEmptyField($data, 'Career', 'user_id');

		if(!empty($data)){
			$this->User->bindModel(array(
	            'hasMany' => array(
	                'Career' => array(
	                    'className' => 'Career',
	                    'foreignKey' => 'user_id'
	                ),
	            )
	        ), false);

			$result = $this->User->Career->doSave($data, false, false, true);
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'noRedirect' => true
		));
	}

	function add_banner_slider(){
		$data = $this->request->data;

		if(!empty($data)){
			$this->loadModel('BannerSlide');

			$photo = $this->RmCommon->filterEmptyField($data, 'BannerSlide', 'photo');

			$image_name = $this->RmImage->copy_image_to_uploads($photo, 'banner_web_principle', Configure::read('__Site.general_folder'), 'photo');
			
			if(!empty($photo) && !empty($image_name)){
				$data['BannerSlide']['photo'] = $image_name;
			}

			$result = $this->BannerSlide->doSave($data);
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'noRedirect' => true
		));
	}

	function add_developer_slider(){
		$data = $this->request->data;

		if(!empty($data)){
			$this->loadModel('BannerDeveloper');

			$photo = $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'photo');

			$image_name = $this->RmImage->copy_image_to_uploads($photo, 'banner_web_principle', Configure::read('__Site.general_folder'), 'photo');

			if(!empty($photo) && !empty($image_name)){
				$data['BannerDeveloper']['photo'] = $image_name;
			}

			$result = $this->BannerDeveloper->doSave($data, false, false, true);
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'noRedirect' => true
		));
	}

	function add_faq(){
		$data = $this->request->data;
		
		if(!empty($data)){
			$this->loadModel('Faq');
			
			$name 		= $this->RmCommon->filterEmptyField($data, 'FaqCategory', 'name');
			$user_id 	= $this->RmCommon->filterEmptyField($data, 'FaqCategory', 'user_id');
			
			$faq_category = $this->Faq->FaqCategory->getData('first', array(
				'conditions' => array(
					'FaqCategory.name' => $name,
					'FaqCategory.user_id' => $user_id,
				)
			), array(
				'company' => false
			));
			
			$faq_category_id = $this->RmCommon->filterEmptyField($faq_category, 'FaqCategory', 'id');

			if(empty($faq_category_id)){
				$result = $this->Faq->FaqCategory->doSave($data, false, false, true);

				$faq_category_id = $this->RmCommon->filterEmptyField($result, 'id');
			}

			$data['Faq']['faq_category_id'] = $faq_category_id;

			if(!empty($faq_category_id)){
				$result = $this->Faq->doSave($data, false, false, true);
			}else{
				$result = array(
					'msg' => __('FAQ Kategori gagal di masukkan'),
					'status' => 'error'
				);
			}
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'noRedirect' => true
		));
	}

	function add_partnership(){
		$data 		= $this->request->data;
		$user_id 	= $this->RmCommon->filterEmptyField($data, 'Partnership', 'user_id');

		if(!empty($data)){
			$this->loadModel('Partnership');

			$photo = $this->RmCommon->filterEmptyField($data, 'Partnership', 'photo');

			$image_name = $this->RmImage->copy_image_to_uploads($photo, 'partner_web_principle', Configure::read('__Site.logo_photo_folder'), 'photo');

			if(!empty($photo) && !empty($image_name)){
				$data['Partnership']['photo'] = $image_name;
			}

			$result = $this->Partnership->doSave($data, $user_id, false, false, true);
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'noRedirect' => true
		));
	}

	function add_message(){
		$data 			= $this->request->data;
		$from_id 		= $this->RmCommon->filterEmptyField($data, 'Message', 'from_id');
		$to_id 			= $this->RmCommon->filterEmptyField($data, 'Message', 'to_id');
		$property_id 	= $this->RmCommon->filterEmptyField($data, 'Message', 'property_id');

		if(!empty($data)){
			$data = $this->RmUser->_callMessageBeforeSave($to_id, $property_id);
			
			$result = $this->User->Message->doSend($data);
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'noRedirect' => true
		));
	}

	public function properties(){
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated');
		
		$options = $this->Property->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
			'order' => array(
				// 'Property.featured' => 'DESC', 
				'Property.change_date' => 'DESC',
				'Property.id' => 'DESC',
			),
		));

		if( !empty($lastupdated) ) {
			$options['conditions'] = array(
				'Property.modified >' => $lastupdated,
			);
		}

		$properties = $this->Property->getData('all', $options, array(
			'status' => 'all-condition',
			'company' => false,
		));
		$properties = $this->Property->getDataList($properties, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyNotification',
				'PropertyMedias',
				'User',
				'Approved',
			),
		));

		if(!empty($properties)){
			foreach ($properties as $key => $value) {
				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
				$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');

				$value = $this->User->getAllNeed($value, $user_id);
				$parent = $this->User->getMerge(array(), $parent_id, true, 'Parent');
				$parent = $this->User->UserCompany->getMerge($parent, $parent_id);
				$value['Parent'] = $parent;

	    		$value = $this->User->UserClientType->getMerge($value, $user_id);
	    		$value = $this->User->UserPropertyType->getMerge($value, $user_id);
	    		$value = $this->User->UserSpecialist->getMerge($value, $user_id);
	    		$value = $this->User->UserLanguage->getMerge($value, $user_id);
	    		$value = $this->User->UserAgentCertificate->getMerge($value, $user_id);

				if(!empty($value['PropertySold'])){
					$period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');
					
					$value['PropertySold'] = $this->Property->Period->getMerge($value['PropertySold'], $period_id);
				}

				$properties[$key] = $value;
			}
		}
		$this->RmCommon->_callDataForAPI($properties);
	}
}
?>