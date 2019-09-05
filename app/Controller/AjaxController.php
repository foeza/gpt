<?php
class AjaxController extends AppController {
	public $uses = array(
		'PropertyAddress',
	);

	public $components = array(
		'RmImage', 
		'RmProperty', 
		'RmKpr',
		'RmCrm', 
		'RmRecycleBin', 
		'RmEbroschure',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'api_info_property' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data',
				 	),
			 	),
	            'api_info_user' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data',
				 	),
			 	),
			 	'property_photo_title' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'property_photo_delete' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'property_photo_primary' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'property_video_title' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'property_video_delete' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'get_form_ebrosur' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data', 'property_medias'
				 	),
			 	),
			 	'list_company_properties' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'list_users' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
					  	'data'
					)
			 	),
			 	'admin_contact' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
					  	'validationErrors'
					)
			 	),
			 	'admin_theme' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device'
					)
			 	),
			 	'admin_theme_launcher' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device'
					)
			 	),
			 	'change_request_ebrosur_period' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device'
					)
			 	),
			 	'list_data' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
				 		'data',
					)
			 	),
			 	'get_data_client' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
				 		'data',
					)
			 	),
			 	'get_properties' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
				 		'paging', 'data',
					)
			 	)
		 	),
	 	),
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'get_subareas', 'get_zip', 'get_kpr_installment_payment',
			'get_kpr_calculation', 'list_users', 'admin_get_dashboard_table',
			'get_list_subareas', 'get_ebrochure', 
			'save_property_video', 'api_info_property',
			'api_info_user', 'set_sorting', 'slide_tour', 
			'get_location', 'set_location', 'share', 'get_property', 'get_user', 'get_ebrochure_template', 
		));
		$this->autoLayout = false;
		$this->autoRender = false;
		
		$this->draft_id = Configure::read('__Site.PropertyDraft.id');

		/*kalo di ajax controller, tidak ketahuan prefixnya, makanya mesti di beginiin*/
		$prefix = Configure::read('App.prefix');
		if(empty($prefix)){
			if(Configure::read('User.group_id') != 10){
				$prefix = 'admin';
			}else{
				$prefix = 'client';
			}
		}

		$this->prefix = $prefix;
   	}

	public function save_property_video($propertyID = NULL){
		$isAdmin = Configure::read('User.admin');
		$data	= $this->request->data;
		$result	= array(
			'status'	=> 'error', 
			'msg'		=> __('Proses gagal. Tidak ada data untuk disimpan'), 
			'data'		=> $data, 
		);

		if($data){
			$propertyID	= $this->RmCommon->filterEmptyField($data, 'property_id');
			$continue	= TRUE;

			if($propertyID){
				$property = $this->User->Property->getData('first', 
					array(
						'conditions' => array(
							'Property.id' => $propertyID,
						),
					), 
					array(
						'status'		=> 'all',
						'admin_mine'	=> TRUE,
					)
				);

				$active = $this->RmCommon->filterEmptyField($property, 'Property', 'active');
				
				$continue = $property ? TRUE : FALSE;
			}

			if($continue){
				$sessionID	= $this->RmCommon->filterEmptyField($data, 'session_id');
				$title		= $this->RmCommon->filterEmptyField($data, 'title');
				$videoURL	= $this->RmCommon->filterEmptyField($data, 'url');

				if($videoURL){
					$videoURL	= $this->RmCommon->filterEmptyField($data, 'url');
					$videoID	= $this->RmCommon->_callGetYoutubeID($videoURL);
				}
				else{
					$videoID	= $this->RmCommon->filterEmptyField($data, 'video_id');
					$videoURL	= sprintf('www.youtube.com/watch?v=%s', $videoID);
				}

				$data = array(
					'PropertyVideos' => array(
						'session_id'	=> $sessionID ?: '', 
						'title'			=> $title, 
						'youtube_id'	=> $videoID, 
						'url'			=> $videoURL, 
					)
				);

				if($propertyID){
					$data = array_merge_recursive($data, array(
							'PropertyVideos' => array(
								'property_id' => $propertyID, 
							)
						)
					);
				}

				$data	= array($data);
				$result	= $this->User->Property->PropertyVideos->doSaveVideo($data, $propertyID);
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if( $status == 'success' ) {
					$log_msg = __('Berhasil unggah video properti');
					$error = false;

					if( !empty($propertyID) && empty($isAdmin) && !empty($active) ){
						$this->User->Property->inUpdateChange($propertyID);
					}
				} else {
					$log_msg = __('Gagal unggah video properti');
					$error = true;
				}

            	$this->RmCommon->_saveLog(__('%s #%s', $log_msg, $videoID), $data, $propertyID, $error);
			}
			else{
				$result['msg'] = __('Proses gagal. Properti tidak ditemukan.');
			}
		}

		$this->autoLayout = FALSE;
		$this->autoRender = FALSE;

		return json_encode($result);
	}

   	function get_subareas($region_id = false, $city_id = false) {
		$this->loadModel('Subarea');
		$title = __('Pilih Area');
		$output = $this->Subarea->getData('list', array(
			'conditions'=>array(
				'Subarea.region_id' => $region_id,
				'Subarea.city_id' => $city_id,
				'Subarea.status' => 1,
			), 
			'fields' => array(
				'Subarea.id', 'Subarea.name'
			),
			'order' => array(
				'Subarea.order' => 'ASC',
				'Subarea.name' => 'ASC'
			),
		), false);

		$this->set(compact('output', 'title'));
		$this->render('get_options');
	}

   	function get_list_subareas($region_id = false, $city_id = false, $fieldNameArea = false) {
   		if( !empty($region_id) && !empty($city_id) ) {
			$values = $this->User->UserProfile->Subarea->getSubareas('list', $region_id, $city_id);
		}

		$this->set(compact('values', 'fieldNameArea'));
		$this->render('/Elements/blocks/common/forms/search/get_list_subareas');
	}

	function get_zip( $id = null ) {
		$this->loadModel('Subarea');
		$output = $this->Subarea->getData('list', array(
			'conditions' => array(
				'Subarea.id' => $id,
				'Subarea.status' => 1,
			),
			'fields' => array(
				'id', 'zip'
			),
			'order' => array(
				'Subarea.order' => 'ASC',
				'Subarea.name' => 'ASC'
			),
		), false);
		$output = array_values($output);
			
		if( isset($output[0]) ) {
			$output = $output[0];
		} else {
			$output = '';
		}

		$this->set(compact('output'));
		$this->render('get_results');
	}

	public function property_photo( $id = false ) {
        $isAdmin = Configure::read('User.admin');
		$options = array(
			'error' => true,
			'message' => __('Mohon upload foto terlebih dahulu'),
		);
		
		if( !empty($this->request->data['files']) ) {
			$queryParams	= (array) $this->params->query;
			$appendSession	= Common::hashEmptyField($queryParams, 'append_session', false);
			$session_id		= Common::hashEmptyField($queryParams, 'session_id');

			if( !empty($id) ) {
				$property = $this->User->Property->getData('first', array(
		        	'conditions' => array(
		        		'Property.id' => $id,
		    		),
		    		'fields' => array(
		    			'Property.id',
		    			'Property.active',
		    			'Property.session_id',
		    			'Property.photo',
		    		),
		    	), array(
		    		'status' => 'all',
		    		'admin_mine' => true,
		    	));

		    	$active = $this->RmCommon->filterEmptyField($property, 'Property', 'active');
			} else {
				$property = array();
			}

			if( !empty($property) ){
				$session_id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id');
				$dataProperty = array(
					'session_id' => $session_id,
					'property_id' => $id,
				);
				$primary_photo = $this->User->Property->PropertyMedias->getData('first', array(
	                'conditions' => array(
	                    'PropertyMedias.property_id' => $id,
	                )
	            ), array(
	                'status' => 'primary'
	            ));
			} else {
	        	$dataBasic		= $this->RmProperty->_callGetAllSession('Basic');
	        	$session_id		= Common::hashEmptyField($dataBasic, 'Property.session_id', $session_id);
				$dataProperty	= array(
					'session_id' => $session_id,
				);
			}

			$files = $this->request->data['files'];
			$info = array();
			$medias = array();
			$propertyFolder = Configure::read('__Site.property_photo_folder');

			foreach ($files as $key => $value) {
				$prefixImage = String::uuid();
				$file_name = $this->RmCommon->filterEmptyField($value, 'name');

				$data = $this->RmImage->upload($value, $propertyFolder, $prefixImage);
				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');
				$data = array_merge($data, array(
					'PropertyMedias' => array_merge(array(
						'alias' => $file_name,
						'name' => $photo_name,
					), $dataProperty),
				));

				$file = $this->User->Property->PropertyMedias->doSave($data, $session_id);
				$file_id = !empty($file->id)?$file->id:false;
				$media = $this->User->Property->PropertyMedias->read(null, $file_id);

				if( !empty($property) && empty($primary_photo) ) {
                    $this->User->Property->PropertyMedias->doRePrimary($id, $property, false, $media);
					$file->primary = 1;
					$primary_photo = true;
				}

				if( !empty($file_id) ) {
					$log_msg = __('Berhasil unggah foto properti');
					$error = false;
				} else {
					$log_msg = __('Gagal unggah foto properti');
					$error = true;
				}

            	$this->RmCommon->_saveLog(__('%s #%s', $log_msg, $file_id), $data, $id, $error);

            	$medias[]	= $media;
				$info[]		= $file;
			}

  			if(!empty($id) && empty($isAdmin) && !empty($active) ){
  				$this->User->Property->inUpdateChange($id);
	        }

			if($appendSession){
				$sessionName = Configure::read('__Site.Property.SessionName');

				CakeSession::write(sprintf($sessionName, $this->mediaLabel), array(
					'PropertyMedias' => Hash::extract($medias, '{n}.PropertyMedias'), 
				));
			}

	  		return json_encode($info);
		} else {
			return false;
		}
	}

	public function property_photo_title( $session_id = false, $id = false, $category_id = false ) {
		$media = $this->User->Property->PropertyMedias->getData('first', array(
			'conditions' => array(
				'PropertyMedias.session_id' => $session_id,
				'PropertyMedias.id' => $id,
			),
		), array(
			'status' => 'all',
		));

		$property_id = $this->RmCommon->filterEmptyField($media, 'PropertyMedias', 'property_id');

	//	tambahan kalo request lewat easy mode ===================================================

		$isAjax			= $this->RequestHandler->isAjax();
		$postParams		= Common::hashEmptyField($this->request->data, 'params', array());
		$isEasyMode		= Common::hashEmptyField($this->request->data, 'is_easy_mode');
		$wrapperAjax	= Common::hashEmptyField($this->request->data, '_wrapper_ajax');

		$postParams		= Hash::combine($postParams, '{n}.name', '{n}.value');
		$isEasyMode		= Common::hashEmptyField($postParams, 'is_easy_mode', $isEasyMode);
		$wrapperAjax	= Common::hashEmptyField($postParams, '_wrapper_ajax', $wrapperAjax);
		$resultOptions	= array(
			'ajaxFlash'		=> true,
			'ajaxRedirect'	=> true,
		);

		if($isAjax && $isEasyMode && $wrapperAjax){
			$this->layout = 'ajax';
			$this->set(array(
				'_wrapper_ajax'	=> $wrapperAjax, 
				'_data_reload'	=> false, 
				'is_easy_mode'	=> true, 
			));
		}

		$this->request->data = Hash::remove($this->request->data, 'params');
		$this->request->data = Hash::remove($this->request->data, 'is_easy_mode');
		$this->request->data = Hash::remove($this->request->data, '_wrapper_ajax');
		$this->request->data = Hash::remove($this->request->data, 'CrmProjectDocument');

		$urlRedirect = array(
			'admin'			=> true, 
			'controller'	=> 'properties', 
		);

		if($isEasyMode && $property_id){
			$urlRedirect = array_merge($urlRedirect, array(
				'action' => 'easy_media',
				$property_id, 
			));

			$this->set('property_id', $property_id);
		}
		else if($property_id){
			$urlRedirect = array_merge($urlRedirect, array(
				'action' => 'edit_medias',
				$property_id, 
				'loadasset' => 0, 
			));
		}
		else{
			$urlRedirect = array_merge($urlRedirect, array(
				'action' => 'medias',
				$this->draft_id
			));
		}

	//	=========================================================================================

		if( is_numeric($category_id) ) {
			$result = $this->User->Property->PropertyMedias->doTitle($id, $media, $category_id);
			$this->RmCommon->setProcessParams($result, $urlRedirect, $resultOptions);
		} else {
			if( !empty($this->request->data) ) {
				$data = $this->request->data;
				$title = $this->RmCommon->filterEmptyField($data, 'PropertyMedias', 'title');

				$result = $this->User->Property->PropertyMedias->doTitleForm($id, $media, $title);
				$this->RmCommon->setProcessParams($result, $urlRedirect, $resultOptions);
			}

			if(!$this->Rest->isActive()){
				$this->render('property_photo_title');
			}
		}

		$this->RmCommon->renderRest();
	}

	public function property_photo_delete( $session_id = false, $property_id = false ) {
		$this->loadModel('PropertyMedias');

		$data = $this->request->data;
		$media_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'media_id');
		$dataMedia = $this->RmCommon->filterEmptyField($data, 'PropertyMedias', 'options_id', $media_id);
		
		if(is_array($dataMedia)){
			$media_id = array_filter($dataMedia);
		}else{
			$media_id = $dataMedia;
		}

		if( !empty($property_id) ) {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'edit_medias',
				$property_id,
				'admin' => true,
			);
		} else {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'medias',
				'draft' => $this->draft_id,
				'admin' => true,
			);
		}

		$result = $this->PropertyMedias->doToggle($media_id, $session_id);
		$media_name = $this->RmCommon->filterEmptyField($result, 'media_name');
		$status = $this->RmCommon->filterEmptyField($result, 'status');

		if( $status == 'success' ){
			$property = $this->User->Property->getData('first', array(
	        	'conditions' => array(
	        		'Property.id' => $property_id,
	    		),
	    	), array(
	    		'status' => 'all',
	    		'admin_mine' => true,
	    	));

			$this->User->Property->PropertyMedias->doRePrimary($property_id, $property, $media_name);

			//	delete media image file
			$propertyMedias = $this->PropertyMedias->getData('all', 
				array('conditions' => array('PropertyMedias.id' => $media_id)), 
				array('status' => 'non-active')
			);

			if($propertyMedias){
				$savePath	= Configure::read('__Site.property_photo_folder');
				$permanent	= FALSE;

				foreach($propertyMedias as $propertyMedia){
					$mediaName = isset($propertyMedia['PropertyMedias']['name']) ? $propertyMedia['PropertyMedias']['name'] : NULL;
					$isDeleted = $this->RmRecycleBin->delete($mediaName, $savePath, NULL, $permanent);
				}
			}
		}

		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->RmCommon->renderRest();
	}

	public function property_photo_primary( $id = false, $session_id = false ) {
		$this->loadModel('PropertyMedias');

		$value = $this->PropertyMedias->getFromSessionID($id, $session_id);
		$media_id = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'id');
		$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'property_id', 0);

		if( !empty($property_id) ) {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'edit_medias',
				$property_id,
				'admin' => true,
			);
		} else {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'medias',
				'draft' => $this->draft_id,
				'admin' => true,
			);
		}

		$result = $this->PropertyMedias->doPrimary($media_id, $property_id, $session_id);
		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->RmCommon->renderRest();
	}

	public function property_photo_order( $session_id = false, $property_id = false ) {
		$this->loadModel('PropertyMedias');

		$media_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'media_id');
		$media_id = explode(',', $media_id);
		$media_id = array_filter($media_id);

		$value = $this->PropertyMedias->getFromSessionID($media_id, $session_id);

		if( !empty($property_id) ) {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'edit_medias',
				$property_id,
				'admin' => true,
			);
		} else {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'medias',
				'draft' => $this->draft_id,
				'admin' => true,
			);
		}

		$result = $this->PropertyMedias->doOrder($media_id, $session_id);
		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));
	}

	public function property_video_title( $session_id = false, $media_id = false, $property_id = 0 ) {
		$this->loadModel('PropertyVideos');

		$data = $this->request->data;
		$title = $this->RmCommon->filterEmptyField($data, 'PropertyVideos', 'title');
		$media = $this->PropertyVideos->getData('paginate', array(
			'conditions' => array(
				'PropertyVideos.property_id' => $property_id,
				'PropertyVideos.session_id' => $session_id,
				'PropertyVideos.id' => $media_id,
			),
		));

		if( !empty($property_id) ) {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'edit_videos',
				$property_id,
				'admin' => true,
			);
		} else {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'videos',
				'draft' => $this->draft_id,
				'admin' => true,
			);
		}

		$result = $this->PropertyVideos->doTitle($media_id, $media, $title);
		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->RmCommon->renderRest();
	}

	public function property_video_delete( $session_id = false, $property_id = false ) {
		$this->loadModel('PropertyVideos');

		$data = $this->request->data;
		$media_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'media_id');
		$dataMedia = $this->RmCommon->filterEmptyField($data, 'PropertyVideos', 'options_id', $media_id);
		
		if(is_array($dataMedia)){
			$media_id = array_filter($dataMedia);
		}else{
			$media_id = $dataMedia;
		}

		if( !empty($property_id) ) {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'edit_videos',
				$property_id,
				'admin' => true,
			);
		} else {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'videos',
				'draft' => $this->draft_id,
				'admin' => true,
			);
		}

		$result = $this->PropertyVideos->doToggle($property_id, $media_id, $session_id);
		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->RmCommon->renderRest();
	}

	public function property_video_order( $session_id = false, $property_id = false ) {
		$this->loadModel('PropertyVideos');

		$media_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'media_id');
		$media_id = explode(',', $media_id);
		$media_id = array_filter($media_id);

		$value = $this->PropertyVideos->getFromSessionID($media_id, $session_id);

		if( !empty($property_id) ) {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'edit_videos',
				$property_id,
				'admin' => true,
			);
		} else {
			$urlRedirect = array(
				'controller' => 'properties',
				'action' => 'videos',
				'draft' => $this->draft_id,
				'admin' => true,
			);
		}

		$result = $this->PropertyVideos->doOrder($media_id, $session_id);
		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));
	}

	public function profile_photo() {
		if( !empty($this->request->data['files']) ) {
			$info = array();
        	$userFolder = Configure::read('__Site.profile_photo_folder');
			$prefixImage = String::uuid();
			$files = $this->request->data['files'];

		//	capture old photo, image file has to be deleted when new image file uploaded
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $this->user_id
				),
			), array(
				'company' => true,
				'admin' => true,
			));
			
			$oldPhoto	= NULL;

			if($user){
				$oldPhoto = isset($user['User']['photo']) && $user['User']['photo'] ? $user['User']['photo'] : NULL;
			}

			foreach ($files as $key => $value) {

				$file_name = $this->RmCommon->filterEmptyField($value, 'name');
				$data = $this->RmImage->upload($value, $userFolder, $prefixImage);
				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');

				$data = array_merge($data, array(
					'User' => array(
						'photo' => $photo_name,
					),
				));
				
				$file = $this->User->doSavePhoto($data, $this->user_id);

				if($file_name && $oldPhoto && (!isset($file->error) || !$file->error)){
				//	delete old photo
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $userFolder, NULL, $permanent);
				}

				$info[] = $file;
			}

			$this->RmUser->refreshAuth($this->user_id);
	  		return json_encode($info);
		} else {
			return false;
		}	
	}

	public function user_company_logo() {

		if( !empty($this->request->data['files']) ) {

			$this->loadModel('UserCompany');

			$files = $this->request->data['files'];
			$info = array();
        	$logoFolder = Configure::read('__Site.general_folder');
			$prefixImage = String::uuid();

		//	capture old photo, image file has to be deleted when new image file uploaded
			$userCompany	= $this->UserCompany->getData('first', array('conditions' => array('UserCompany.user_id' => $this->user_id)));
			$oldPhoto		= NULL;
			if($userCompany){
				$oldPhoto = isset($userCompany['UserCompany']['logo']) && $userCompany['UserCompany']['logo'] ? $userCompany['UserCompany']['logo'] : NULL;
			}

			foreach ($files as $key => $value) {

				$file_name = $this->RmCommon->filterEmptyField($value, 'name');
				$data = $this->RmImage->upload($value, $logoFolder, $prefixImage);
				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');

				$data = array_merge($data, array(
					'UserCompany' => array(
						'logo' => $photo_name,
					),
				));
				
				$file = $this->UserCompany->doSaveLogo($data, $this->user_id);

				if($file_name && $oldPhoto && (!isset($file->error) || !$file->error)){
				//	delete old photo
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $logoFolder, NULL, $permanent);
				}

				$info[] = $file;
			}

	  		return json_encode($info);
		} else {
			return false;
		}	
	}

	public function banner_photo() {

		if( !empty($this->request->data['files']) ) {

			$this->loadModel('BannerSlide');

			$files = $this->request->data['files'];
			$info = array();
        	$generalFolder = Configure::read('__Site.general_folder');
			$prefixImage = String::uuid();

			foreach ($files as $key => $value) {

				$file_name = $this->RmCommon->filterEmptyField($value, 'name');
				$data = $this->RmImage->upload($value, $generalFolder, $prefixImage);
				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');

				$data = array_merge($data, array(
					'thumbnail_url' => $data['imagePath'],
					'name' => $photo_name
				));
			}

	  		return json_encode($data);
		} else {
			return false;
		}	
	}

	function list_users ( $group_id = false, $format_by_name = false, $parent_id = false) {
		$params = $this->params->params;
		$admin_rumahku = Configure::read('User.admin');
		$keyword = $this->RmCommon->filterEmptyField($this->request->data, 'Query', 'keyword');
		$keyword = $this->RmCommon->filterEmptyField($this->request->data, 'query', false, $keyword);

		$type_array = Common::hashEmptyField($params, 'named.type');
		$cobroke_id = Common::hashEmptyField($params, 'named.cobroke_id');
		$parent_id = Configure::read('Principle.id');
		$dataUser = array();
		
		$not_id = $this->RmCommon->filterEmptyField($this->params->params, 'named', 'not_id');
		if(!empty($not_id)){
			$not_id = explode(',', $not_id);
		}
		
		$is_company = true;
		$groups = array();
		if($group_id != 'false' && $group_id != false){
			$groups = explode(',', $group_id);
		}
		
		if (!empty($keyword) && strpos($keyword, ',') !== false) {
		    $keyword = explode(',', $keyword);

		    $keyword = $keyword[count($keyword)-1];
		}
		if( !empty($groups) ) {
			$groups = array_filter($groups);
		}

		if(!empty($type_array) && $type_array == 'raw'){
			$find = 'all';
		}else{
			$find = 'list';
		}

		if( in_array(10, $groups) ) {
        	if( !empty($admin_rumahku) ) {
        		$this->User->UserClient->virtualFields['email_name'] = 'CONCAT(User.email, \' | \', UserClient.first_name, \' \', IFNULL(UserClient.last_name, \'\'))';
				$options = array(
					'conditions' => array(
						'UserClient.company_id' => Configure::read('Principle.id'),
						'User.group_id' => 10,
						'User.status' => 1,
						'User.deleted' => 0,
						'OR' => array(
							'User.email LIKE' => '%'.$keyword.'%',
							'CONCAT(UserClient.first_name, \' \', IFNULL(UserClient.last_name, \'\')) LIKE' => '%'.$keyword.'%',
						),
					),
					'fields' => array(
						'UserClient.user_id', 'UserClient.email_name',
					),
					'contain' => array(
						'User',
					),
					'limit' => 10,
				);

				if(!empty($not_id)){
					$options['conditions']['User.id NOT'] = $not_id;
				}

				$dataUser = $this->User->UserClient->getData($find, $options);
			} else {
        		$this->User->UserClientRelation->virtualFields['email_name'] = 'CONCAT(Client.email, \' | \', UserClient.first_name, \' \', IFNULL(UserClient.last_name, \'\'))';
				$options = array(
					'conditions' => array(
						'UserClientRelation.agent_id' => $this->user_id,
						'UserClient.status' => 1,
						'Client.group_id' => 10,
						'Client.status' => 1,
						'Client.deleted' => 0,
						'OR' => array(
							'Client.email LIKE' => '%'.$keyword.'%',
							'CONCAT(UserClient.first_name, \' \', IFNULL(UserClient.last_name, \'\')) LIKE' => '%'.$keyword.'%',
						),
					),
					'fields' => array(
						'UserClient.user_id', 'UserClientRelation.email_name',
					),
					'contain' => array(
						'Client',
						'UserClient',
					),
					'limit' => 10,
				);

				$dataUser = $this->User->UserClientRelation->getData($find, $options);
			}
		} else if( !empty($parent_id) ) {
			$fields = array(
				'User.id', 'User.email_name'
			);

        	if(!empty($type_array) && $type_array == 'raw'){
        		array_push($fields, 'User.photo');
        	}

			$options = array(
				'conditions' => array(
					'OR' => array(
						'User.email LIKE' => '%'.$keyword.'%',
						'CONCAT(User.first_name, \' \', IFNULL(User.last_name, \'\')) LIKE' => '%'.$keyword.'%',
					),
				),
				'fields' => $fields,
				'limit' => 10,
			);

			if(!empty($not_id)){
				$options['conditions']['User.id NOT'] = $not_id;
			}
			
			if(!empty($groups)){
				$options['conditions']['User.group_id'] = $groups;
			}

			if( in_array(4, $groups) || $this->allow_origin == true) {
				$is_company = false;
			} else if( !in_array(3, $groups) ) {
				$parent_id = Configure::read('Principle.id');

				// cek user adalah atasan dari agent set di superior_id
				$group_id = Configure::read('User.group_id');

				if($group_id > 20){
					$user_login_id = Configure::read('User.id');
					$data_arr = $this->User->getUserParent($user_login_id);

					$is_sales = Common::hashEmptyField($data_arr, 'is_sales');
					
					if($is_sales){
						$options['conditions']['User.superior_id'] = $user_login_id;
					}
				}
				// 

				$param_parent_id = Common::hashEmptyField($params, 'named.parent_id');
				$param_user_id = Common::hashEmptyField($params, 'named.user_id');
				$param_document_status = Common::hashEmptyField($params, 'named.document_status');

				if( !empty($parent_id) || ( $type_array == 'active-inactive' && !empty($param_parent_id) ) ) {
					$options['conditions']['User.parent_id'] = !empty($param_parent_id) ? $param_parent_id : $parent_id;
				}

				if(!empty($param_user_id)){
					$options['conditions']['User.id <>'] = $param_user_id;
				}
			}
			
			if( !empty($format_by_name) ) {
	        	$this->User->virtualFields['email_name'] = 'CONCAT(User.email, \' | \', User.first_name, \' \', IFNULL(User.last_name, \'\'))';
	        } else {
	        	$this->User->virtualFields['email_name'] = 'User.email';
	        }

	        $document_status = !empty($param_document_status) ? $param_document_status : 'semi-active';
			$dataUser = $this->User->getData($find, $options, array(
				'company' => $is_company,
				'status' => $document_status,
				'admin' => true,
			));
		}
		$dataUser = $this->RmCommon->convertDataAutocomplete($dataUser);

		if($this->Rest->isActive()){
			$this->set('data', $dataUser);

			$this->RmCommon->renderRest();
		}else{
			return json_encode($dataUser);
		}
	}

	public function admin_get_dashboard_table( $action_type = 'active-or-sold', $fromDate = false, $toDate = false ) {
		
		$this->loadModel('Property');
		$params = array(
			'date_from' => $fromDate,
			'date_to' => $toDate,
		);
		$title = false;
		$wrapperClass = false;
		$daterangeClass = false;
		$url = false;
        $urlTitle = __('Lihat semua');
        $elements = false;
        $fieldName = 'Property.created';

        if( $this->RmCommon->_isAdmin() || $this->RmCommon->_isCompanyAdmin() ) {
			$url = array(
				'controller' => 'reports',
				'action' => 'generate',
				'agents',
				'title' => 'Agen Berdasarkan Properti terbanyak',
				'sort' => 'total_property',
				'direction' => 'desc',
				'date_from' => $fromDate,
				'date_to' => $toDate,
				'admin' => true,
	        );
		}

		if( $action_type == 'active' ) {
			$title = __('5 Agen dengan properti terbanyak');
			$wrapperClass = 'wrapper-dashboard-table-property-active';
			$daterangeClass = 'daterange-dasboard-table-property-active';
			$url['sort'] = 'total_property';
			$url['direction'] = 'desc';
		} else if ( $action_type == 'sold' ) {
			$title = __('5 Agen dengan properti terjual terbanyak');
			$wrapperClass = 'wrapper-dashboard-table-property-sold';
			$daterangeClass = 'daterange-dasboard-table-property-sold';
			$elements = array(
	            'status' => false,
	            'sold_mine' => true,
	        );
        	$fieldName = 'PropertySold.sold_date';
			$url['sort'] = 'total_property_sold';
			$url['direction'] = 'desc';
		}

		$values = $this->Property->get_total_listing_per_agent($this->parent_id, $elements, 'range', $params, $fieldName);
		$this->set(compact(
			'title', 'wrapperClass', 'daterangeClass', 'url', 'urlTitle', 'action_type', 'values',
			'fromDate', 'toDate'
		));
		$this->render('/Elements/blocks/users/dashboards/table');
	}

	public function admin_get_dashboard_ebrosurs( $fromDate = false, $toDate = false ) {
		$isAdmin = Configure::read('User.admin');
        $urlTitle = __('Lihat semua');

		$title = __('5 Agen dengan eBrosur terbanyak');
		$wrapperClass = 'wrapper-dashboard-table-ebrosur';
		$daterangeClass = 'daterange-dasboard-table-ebrosur';
		$urlAjax = array(
			'controller' => 'ajax',
			'action' => 'get_dashboard_ebrosurs',
			'admin' => true,
		);
        $labelName = __('eBrosur');
        $modelName = 'UserCompanyEbrochure';
        $fieldName = 'total';

        if( !empty($isAdmin) ) {
			$url = array(
				'controller' => 'reports',
				'action' => 'generate',
				'agents',
				'title' => 'Agen Berdasarkan eBrosur terbanyak',
				'sort' => 'total_ebrosur',
				'direction' => 'desc',
				'date_from' => $fromDate,
				'date_to' => $toDate,
				'admin' => true,
	        );
        }

    	$values = $this->User->UserCompanyEbrochure->_callTopEbrosurs($fromDate, $toDate);
		$this->set(compact(
			'title', 'wrapperClass', 'daterangeClass', 
			'url', 'urlTitle', 'values',
			'urlAjax', 'labelName', 'modelName',
			'fieldName', 'fromDate', 'toDate'
		));
		$this->render('/Elements/blocks/users/dashboards/table');
	}

	public function admin_get_dashboard_report( $action_type = false, $fromDate = false, $toDate = false ) {
		$this->loadModel('Property');
		$default_options = false;
		
		if( $action_type == 'visitors' ) {
			$default_options = $this->User->Property->getData('paginate', 
				array(
					'order' => array(
						'PropertyView.created' => 'DESC',
					),
				), 
				array(
					'status' => 'active-pending-sold',
					'admin_mine' => true,
					'company' => false,
				)
			);
			$default_options['contain'][] = 'Property';
		} else if( $action_type == 'commissions' ) {
			$default_options = array(
				'conditions' => array(),
				'contain' => array(
					'PropertySold',
				),
				'order' => array(
					'Property.id' => 'DESC',
				),
			);
			$this->set('hiddenForm', true);
		}
		$chartProperties = $this->Property->_callChartProperties( false, $action_type, $fromDate, $toDate, $default_options );
		
		if( $action_type == 'commissions' ) {
			$user = Configure::read('User.data');
			$targetCommission = $this->RmCommon->filterEmptyField($user, 'UserConfig', 'commission');
			$url = array(
            	'controller' => 'reports',
                'action' => 'commission_add',
                'admin' => true,
    		);
    		$data = $this->request->data;

            if( !empty($toDate) ) {
            	$data['filter_commission']['year'] = $this->RmCommon->formatDate($toDate, 'Y');
            	$data['mob']['month'] = $this->RmCommon->formatDate($toDate, 'm');
            }
    		
    		$this->request->data = $data;

            $urlTitle = __('Lihat semua');
            $ajaxUrl = array(
            	'controller' => 'ajax',
                'action' => 'get_dashboard_report',
                'commissions',
            );

			$this->set('chartCommission', $chartProperties);
			$this->set(compact(
				'targetCommission','url', 'urlTitle', 'ajaxUrl', 'action_type'
			));
			$this->render('/Elements/blocks/users/dashboards/chart');
		} else {
			$this->set(compact(
				'chartProperties', 'action_type'
			));
			$this->render('/Elements/blocks/users/dashboards/multiple_chart');
		}
	}

	public function admin_get_properties_report( $property_id = false, $action_type = false, $fromDate = false, $toDate = false ) {
		$this->loadModel('Property');
		$chartProperties = $this->Property->_callChartProperties( $property_id, $action_type, $fromDate, $toDate );
		if( !empty($property_id) ) {
			$options = array(
				'conditions' => array(
					'Property.id' => $property_id,
				),
			);
			$status = array(
				'status' => 'all',
				'company' => false,
			);
			if( !Configure::read('User.admin') ) {
				$status['company'] = true;
			}
			$property = $this->Property->getData('first', $options, $status);

			if( !empty($property) ) {
				$property = $this->Property->getDataList($property, array(
					'contain' => array(
						'PropertyAddress',
					),
				));
				$property_action_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_action_id');
				$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');
				$property = $this->Property->PropertyAction->getMerge($property, $property_action_id, 'PropertyAction.id', array(
					'cache' => array(
						'name' => __('PropertyAction.%s', $property_action_id),
					),
				));
				$property = $this->User->getMerge($property, $user_id);
				$this->set(compact('property'));
			}
		}
		
		$this->set('_ajax', true);
		$this->set(compact(
			'chartProperties', 'action_type'
		));
		$this->render('/Elements/blocks/properties/report/multiple_chart');
	}

	public function client_get_properties_report( $property_id = false, $action_type = false, $fromDate = false, $toDate = false ) {
		$this->loadModel('Property');
		$chartProperties = $this->Property->_callChartProperties( $property_id, $action_type, $fromDate, $toDate, false );
		if( !empty($property_id) ) {
			$options = array(
				'conditions' => array(
					'Property.id' => $property_id,
				),
			);
			$status = array(
				'status' => 'all',
				'company' => false,
			);
			$property = $this->Property->getData('first', $options, $status);

			if( !empty($property) ) {
				$property = $this->Property->getDataList($property, array(
					'contain' => array(
						'PropertyAddress',
					),
				));
				$property_action_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_action_id');
				$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');
				$property = $this->Property->PropertyAction->getMerge($property, $property_action_id, 'PropertyAction.id', array(
					'cache' => array(
						'name' => __('PropertyAction.%s', $property_action_id),
					),
				));
				$property = $this->User->getMerge($property, $user_id);
				$this->set(compact('property'));
			}
		}
		
		$this->set(compact(
			'chartProperties', 'action_type'
		));
		$this->render('/Elements/blocks/properties/report/client_multiple_chart');
	}

	function list_company_properties(){
		$data = $this->request->data;

		$restrict_active = $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'is_restrict_approval_property', false);

		$progress_kpr = $this->RmCommon->filterEmptyField($this->params, 'named', 'progress_kpr', false);
		$keyword = $this->RmCommon->filterEmptyField($data, 'Query');
		$keyword = $this->RmCommon->filterEmptyField($data, 'query', false, $keyword);

		if($this->Rest->isActive()){
			$progress_kpr = 1;
		}
		
		$properties = $this->User->Property->getListCompanyProperties($this->parent_id, $keyword, array(
			'skip_is_sales' => true,
		), $progress_kpr, $restrict_active);
		
		$properties = $this->RmCommon->convertDataAutocomplete($properties);

		if(!$this->Rest->isActive()){
			return json_encode($properties);
		}else{
			$this->set('data', $properties);
		}

		$this->RmCommon->renderRest();
	}

	function get_properties( $action_type = 'all' ){
		$params = $this->params->params;
		$user_id = Common::hashEmptyField($params, 'named.user_id');
		$keyword = $this->RmCommon->filterEmptyField($this->request->data, 'query');

		if( $this->Rest->isActive() ) {
			$this->paginate = $this->User->Property->getListCompanyProperties($this->parent_id, $keyword, array(
				'type' => 'all',
				'admin_mine' => true,
				'action_type' => $action_type,
			), true, false, array(), 'paginate');
			$properties = $this->paginate('Property');
			
			$this->autoRender = true;
			$this->RmCommon->_callDataForAPI($properties, 'manual');
	        $this->RmCommon->renderRest(array(
	            'is_paging' => true
	        ));
		} else {
			if( !empty($user_id) ) {
				$options = array(
					'conditions' => array(
						'Property.sold' => 0,
						'Property.user_id' => $user_id,
					),
					'fields' => array(
						'Property.id', 'Property.name_property'
					),
					'limit' => 10,
				);
			} else {
				$options = array();
			}

			$properties = $this->User->Property->getListCompanyProperties($this->parent_id, $keyword, array(
				'admin_mine' => true,
				'action_type' => $action_type,
			), true, false, $options);
			$properties = $this->RmCommon->convertDataAutocomplete($properties);

			return json_encode($properties);
		}
	}

	function get_crm_property( $mls_id = false ){
		$params = $this->params;
		$this->theme = false;

		$template = Common::hashEmptyField($params->params, 'named.template', 'property');

		$crm_project_id = $this->RmCommon->filterEmptyField($params, 'named', 'crm_project_id', 0);
		$kpr = $this->RmCommon->filterEmptyField($params, 'named', 'kpr');

		$value = $this->User->Property->_callPropertyMerge(array(), $mls_id, 'Property.mls_id');
		$value = $this->User->CrmProject->getMerge($value, $crm_project_id);
		$value = $this->User->CrmProject->CrmProjectPayment->getMerge($value, $crm_project_id, 'CrmProjectPayment.crm_project_id');
		
		$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id', 0);
		$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');
		$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
		$value = $this->User->getMerge($value, $owner_id, false, 'Owner');

		$price = $this->RmCommon->filterEmptyField($value, 'Property', 'price_measure');
		$price = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'price', $price);

		$value['Kpr']['property_price'] = $price;
		$value['Kpr']['sold_date'] = date('d/m/Y');
		$value['Kpr']['kpr_date'] = date('d/m/Y');

		$documentCategories = $this->RmKpr->_callDocumentCategories(array(
			'DocumentCategory.is_required' => 1,
			'DocumentCategory.id' => array( 1,5 ),
		), array(
			'crm_project_id' => $crm_project_id,
			'property_id' => $property_id,
		));

		$client = $this->RmCommon->filterEmptyField($value, 'Client');
		$value['User'] = $client;

		$mandatory = __('*');
		$this->request->data = $value;

		$this->set(compact(
			'mandatory', 'kpr', 'documentCategories', 'user_id'
		));

		$this->render('/Elements/blocks/crm/'.$template);
	}

	function get_form_ebrosur($mls_id, $color = false){
		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.mls_id' => $mls_id
			)
		), array(
			'status' => 'active-pending-sold',
			'restrict_api' => false,
			'skip_is_sales' => true,
		));

		if(!empty($property)){
			$property = $this->User->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
					'User'
				),
			));

			$property_medias = $this->User->Property->PropertyMedias->getData('all', array(
				'conditions' => array(
					'PropertyMedias.property_id' => $property['Property']['id']
				)
			), array(
				'status' => 'all'
			));

			$this->RmEbroschure->setDataForm($property);

			$this->set('property_medias', $property_medias);
		}

		$propertyActions = $this->User->Property->PropertyAction->getData('list', array(
			'cache' => __('PropertyAction.List'),
		));
		$propertyTypes = $this->User->Property->PropertyType->getData('list', array(
			'cache' => __('PropertyType.List'),
		));
		$color_scheme = $this->RmCommon->getGlobalVariable('color_banner_option');
		$currencies = $this->User->Property->Currency->getData('list', array(
			'fields' => array(
				'Currency.id', 'Currency.alias',
			),
			'cache' => __('Currency.alias'),
		));

		$periods = $this->User->Property->PropertyPrice->Period->getData('list', array(
			'cache' => __('Period.List'),
		));

		$lotUnits = $this->User->Property->PropertyAsset->LotUnit->getData('list', array(
			'fields' => array(
				'LotUnit.id', 'LotUnit.slug',
			),
			'group' => array(
				'LotUnit.slug',
			),
			'cache' => __('LotUnit.GroupSlug.List'),
		), array(
			'is_lot' => true,
		));

		$this->RmCommon->_callRequestSubarea('UserCompanyEbrochure');

		$dataUser = Configure::read('User.data');

		$this->set(compact(
			'propertyActions', 'propertyTypes', 'module_title', 'color_scheme',
			'subareas', 'currencies', 'periods', 'lotUnits', 'list_agent'
		));

		$this->render('/Elements/blocks/ebrosurs/forms/ebrosur');

		$this->RmCommon->renderRest();
	}

	function admin_approve_media($media_id, $property_id){
		if(!empty($media_id) && !empty($property_id)){
			$result = $this->User->Property->PropertyMedias->doApprove($media_id);

			if($this->is_ajax){
				if($result['status'] == 'success'){
					$medias = $this->User->Property->PropertyMedias->getData('all', array(
						'conditions' => array(
							'PropertyMedias.property_id' => $property_id
						)
					), array(
						'status' => 'all'
					));

					$this->set(compact('medias', 'property'));

					$this->render('/Elements/blocks/common/image_carousel_approval');
				}else{
					echo $result['msg'];
					die();
				}
			}else{
				$this->RmCommon->redirectReferer($result['msg'], $result['status']);
			}
		}else{
			if($this->is_ajax){
				echo 'Gagal melakukan proses';
				die();
			}else{
				$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
			}
		}
	}

	function admin_delete_media($media_id, $property_id){
		if(!empty($media_id) && !empty($property_id)){
			$result = $this->User->Property->PropertyMedias->doToggle($media_id);

			if($this->is_ajax){
				if($result['status'] == 'success'){
					$medias = $this->User->Property->PropertyMedias->getData('all', array(
						'conditions' => array(
							'PropertyMedias.property_id' => $property_id
						)
					), array(
						'status' => 'all'
					));

					$property = $this->User->Property->getData('first', array(
						'conditions' => array(
							'Property.id' => $property_id
						),
					), array(
						'status' => 'all',
					));

					$this->set(compact('medias', 'property'));

					$this->render('/Elements/blocks/common/image_approval');
				}else{
					echo $result['msg'];
					die();
				}
			}else{
				$this->RmCommon->redirectReferer($result['msg'], $result['status']);
			}
		}else{
			if($this->is_ajax){
				echo 'Gagal melakukan proses';
				die();
			}else{
				$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
			}
		}
	}

	function admin_delete_video($media_id, $property_id){
		if(!empty($media_id) && !empty($property_id)){
			$result = $this->User->Property->PropertyVideos->doToggle($property_id, $media_id);

			if($this->is_ajax){
				if($result['status'] == 'success'){
					$videos = $this->User->Property->PropertyVideos->getData('all', array(
						'conditions' => array(
							'PropertyVideos.property_id' => $property_id
						)
					), array(
						'status' => 'all'
					));

					$property = $this->User->Property->getData('first', array(
						'conditions' => array(
							'Property.id' => $property_id
						),
					), array(
						'status' => 'all',
					));

					$this->set(compact('videos', 'property'));

					$this->render('/Elements/blocks/common/video_approval');
				}else{
					echo $result['msg'];
					die();
				}
			}else{
				$this->RmCommon->redirectReferer($result['msg'], $result['status']);
			}
		}else{
			if($this->is_ajax){
				echo 'Gagal melakukan proses';
				die();
			}else{
				$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
			}
		}
	}

	function admin_image($id){
		if(!empty($id)){
			$media = $this->User->Property->PropertyMedias->getData('first', array(
				'conditions' => array(
					'PropertyMedias.id' => $id
				)
			), array(
				'status' => 'all'
			));

			$this->set('media', $media);

			$this->render('admin_image');
		}
	}

	function get_kpr_installment_payment( $property_price = false, $loan_amount = false, $credit_fix = false, $interest_rate = false ) {

		if( !empty($property_price) && !empty($loan_amount) && !empty($credit_fix) && !empty($interest_rate) ) {
			
			$property_price = $this->RmCommon->safeTagPrint($property_price);
			$loan_amount = $this->RmCommon->safeTagPrint($loan_amount);
			$credit_fix = $this->RmCommon->safeTagPrint($credit_fix);
			$interest_rate = $this->RmCommon->safeTagPrint($interest_rate);

			$total_dp =  $property_price - $loan_amount;
			$total_first_credit = $this->RmKpr->creditFix($loan_amount, $interest_rate, $credit_fix );
		} else {
			$total_first_credit = 0;
		}

		$this->set(compact(
			'total_first_credit'
		));
		$this->render('get_kpr_installment_payment');
	}

	function get_kpr_calculation() {
		$bank_apply_category_id = 1;
		$loan_summary = $this->request->data;
		$property_price = $this->RmCommon->filterEmptyField($loan_summary, 'Kpr', 'property_price');
		$loan_price = $this->RmCommon->filterEmptyField($loan_summary, 'Kpr', 'loan_price');
		$down_payment = $this->RmCommon->filterEmptyField($loan_summary, 'Kpr', 'down_payment');
		$credit_fix = $this->RmCommon->filterEmptyField($loan_summary, 'Kpr', 'credit_fix', 0);
		$credit_total = $this->RmCommon->filterEmptyField($loan_summary, 'Kpr', 'credit_total', 0);
		$bankKpr = $this->User->Kpr->KprBank->Bank->getKpr();

		$loan_summary['Kpr']['property_price'] = $this->RmCommon->convertPriceToString($property_price);
		$loan_summary['Kpr']['loan_price'] = $this->RmCommon->convertPriceToString($loan_price);
		$loan_summary['Kpr']['down_payment'] = $this->RmCommon->convertPriceToString($down_payment);
		$kpr_data = $this->RmKpr->calculate_kpr_installment_detail( $bankKpr, $loan_summary );

		if(!empty($this->params['named']['mls_id'])){
			$property = $this->User->Property->getData('first', array(
	        	'conditions' => array(
	        		'Property.mls_id' => $this->params['named']['mls_id']
	    		),
	    	), array(
				'status' => 'active-pending-sold',
	    		'company' => true,
				'skip_is_sales' => true,
	    	));

	    	$property = $this->User->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
				),
			));

			$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'id');
			$currency_id = $this->RmCommon->filterEmptyField($property, 'Property', 'currency_id');
			$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id');
			$bank_apply_category_id = $this->RmKpr->_callGetBankApplyCategory($property_type_id);

			$loan_summary['Kpr']['property_id'] = $property_id;

			if( !empty($currency_id) ) {
				$loan_summary['Kpr']['currency_id'] = $currency_id;
			}

			$this->set('property', $property);
		}

		$data = $loan_summary['Kpr'];

		if(!empty($property['Property'])){
			$data = array_merge($data, $property['Property']);

			if(!empty($property['PropertyAddress'])){
				$data = array_merge($data, $property['PropertyAddress']);
			}
		}

		$data = $this->RmCommon->_callUnset(array(
			'id',
			'created',
			'modified',
		), $data);

		if( !empty($bankKpr['BankSetting']) ) {
			$bankKpr = $this->RmCommon->_callUnset(array(
				'id',
				'created',
				'modified',
			), $bankKpr);

			$data = array_merge($data, $bankKpr);
		}

		$data_log = $this->RmKpr->beforeSaveKprLog($data);
		$id_log_kpr = $this->User->Kpr->KprBank->doSaveLog($data_log);

		$this->set(compact(
			'loan_summary', 'kpr_data', 'id_log_kpr',
			'bankKpr'
		));
		$this->render('get_kpr_calculation');
	}

	function get_ebrochure($maxid){
		if(!empty($maxid)){
			$agents = $this->User->getAgents($this->parent_id, true, 'list', false, array('role' => 'all'));

			$conditions = array(
	            'OR' => array(
					'UserCompanyEbrochure.user_id' => $agents,
				),
				'NOT' => array(
	                'UserCompanyEbrochure.code' => NULL,
	            ),
	            'UserCompanyEbrochure.id >' => $maxid,
	        );

			$brosurs = $this->User->UserCompanyEbrochure->getData('all', array(
	            'conditions' => $conditions,
	            'order' => array(
	            	'UserCompanyEbrochure.id' => 'desc',
	        	),
	        ), array(
	        	'status' => 'active'
	        ));

			$this->set(compact('brosurs'));

			$this->render('/Elements/blocks/ebrosurs/ebrosur_slider');
		}
	}

	public function admin_theme_launcher( $id = false ) {
		$launcher = $this->User->UserCompanyLauncher->getData('first', array(
			'conditions' => array(
				'UserCompanyLauncher.theme_launcher_id' => $id,
				'UserCompanyLauncher.user_id' => $this->parent_id,
			),
		));
		$launcher_id = $this->RmCommon->filterEmptyField($launcher, 'UserCompanyLauncher', 'id');

		$urlRedirect = array(
			'controller' => 'settings',
			'action' => 'launcher',
			'admin' => true,
		);

		$result = $this->User->UserCompanyLauncher->doChosen($id, $launcher_id);
		$this->RmCommon->setProcessParams($result, $urlRedirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->RmCommon->renderRest();
	}

	public function admin_theme( $themeID = null, $userID = null ) {
		$result = array(
			'status'	=> 'error', 
			'msg'		=> __('Tema tidak valid'), 
		);

		$theme = $this->User->UserConfig->Theme->getData('first', array(
			'conditions' => array(
				'Theme.id' => $themeID, 
			), 
		), array(
			'owner_type' => 'all', 
		));

		$ownerType		= Common::hashEmptyField($theme, 'Theme.owner_type');
		$redirectAction	= $ownerType == 'agent' ? 'personal_theme_selection' : 'theme_selection';
		$redirectUrl	= array(
			'admin'			=> true,
			'controller'	=> 'settings',
			'action'		=> $redirectAction, 
		);

		if($ownerType == 'agent' && $userID){
		//	personal page theme
		//	setting theme di web masing2, data company pasti kosong
			$userID		= Configure::read('User.id');
			$configID	= Configure::read('User.data.UserConfig.id');
			$saveFlag	= $this->User->UserConfig->doSave(Configure::read('User.data'), array(
				'UserConfig' => array(
					'id'		=> $configID, 
					'user_id'	=> $userID, 
					'theme_id'	=> $themeID, 
				), 
			), $userID);

			$result = array(
				'status'	=> $saveFlag ? 'success' : 'error', 
				'msg'		=> __('%s memilih tema', $saveFlag ? 'Berhasil' : 'Gagal'), 
			);
		}
		else if($ownerType == 'company'){
		//	company web theme
		//	setting theme di web masing2, data company pasti ada
			$userID		= Common::hashEmptyField($this->data_company, 'User.id');
			$configID	= Common::hashEmptyField($this->data_company, 'UserCompanyConfig.id');
			$result		= $this->User->UserCompanyConfig->doChosen($themeID, $configID);
		}

		$this->RmCommon->setProcessParams($result, $redirectUrl, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->RmCommon->renderRest();
	}

	function change_request_ebrosur_period($ebrosur_request_id, $cronjob_period_id){
		$this->loadModel('CronjobPeriod');

		$result = $this->User->EbrosurRequest->change_period($ebrosur_request_id, $cronjob_period_id);

		$ebrosur = $this->User->EbrosurRequest->getData('first', array(
			'conditions' => array(
				'EbrosurRequest.id' => $ebrosur_request_id
			)
		));

		$is_client = false;
		if(Configure::write('User.admin') && $this->prefix == 'admin'){
			$is_client = true;
		}

		if(!empty($ebrosur)){
			if($is_client){
				$ebrosur = $this->User->EbrosurRequest->EbrosurClientRequest->getMerge($ebrosur, 5);	
			}else{
				$ebrosur = $this->User->EbrosurRequest->EbrosurAgentRequest->getMerge($ebrosur, 5);
			}
			
			$ebrosur = $this->User->EbrosurRequest->EbrosurTypeRequest->getMerge($ebrosur);

			$ebrosur = $this->User->EbrosurRequest->getMergeDefault($ebrosur);
		}

		$periods = $this->CronjobPeriod->getData('list', array(
			'cache' => __('Period.List'),
		));

		$this->set(compact('ebrosur', 'periods', 'is_client'));

		if(!$this->Rest->isActive()){
			$this->render('/Elements/blocks/ebrosurs/content_request_ebrosur');
		}else{
			if($result){
				$result = array(
					'status' => 'success',
					'msg' => __('Berhasil mengubah periode request')
				);
			}else{
				$result = array(
					'status' => 'error',
					'msg' => __('Gagal mengubah periode request')
				);
			}

			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true
			));

			$this->RmCommon->renderRest();
		}
	}

	function get_data_client( $email = false ){
		$params = $this->params->params;

		$email = $this->RmCommon->getEmailConverter($email);
		$value = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'User.email' => $email,
			),
			'contain' => array(
				'User',
			),
		));

		$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
		$value = $this->User->UserProfile->getMerge($value, $id);

		// $value = $this->User->UserClient->getMerge($value, $id);
		$action_type 	= Common::hashEmptyField($params, 'named.action_type');
		$is_editable	= Common::hashEmptyField($params, 'named.is_editable', true, array('isset' => true));
		$modelName 		= Common::hashEmptyField($params, 'named.model_name', 'CrmProject');
		$template 		= Common::hashEmptyField($params, 'named.template');

		if( !empty($value) ) {
			$disabledClient = true;
		}

		$clientJobTypes = $this->User->Kpr->KprApplication->JobType->getList();
		$client_type_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'client_type_id', null);

		$user_client_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'id');
		$birthday = $this->RmCommon->filterEmptyField($value, 'UserClient', 'birthday');
		$birthday = $this->RmCommon->getDate($birthday, true);

		$value['UserClient']['birthday'] = !empty($birthday)?$birthday:NULL;

		if( in_array($modelName, array('Kpr', 'CrmProject'))  && !empty($value['UserClient']) ){
			$full_name = $this->RmCommon->filterEmptyField($value, 'UserClient', 'full_name');
			
			$value[$modelName]['client_email'] = __('%s | %s', urldecode($email), $full_name);

			$value[$modelName]['client_name'] = $full_name;
		    $value[$modelName]['client_hp'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'no_hp');
		    $value[$modelName]['client_job_type_id'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'job_type', 0);
		    $value[$modelName]['ktp'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'ktp');
		    $value[$modelName]['birthplace'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'birthplace');
		    $value[$modelName]['birthday'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'birthday');
			$value[$modelName]['birthplace'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'birthplace');
			$value[$modelName]['address'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'address');
			$value[$modelName]['gender_id'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'gender_id');
			$value[$modelName]['region_id'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'region_id');
			$value[$modelName]['city_id'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'city_id');
			$value[$modelName]['subarea_id'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'subarea_id');
			$value[$modelName]['zip'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'zip');
			$value[$modelName]['status_marital'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'status_marital');

		}
		$this->request->data = $value;

		if($modelName == 'Kpr'){
			$this->RmCommon->_callRequestSubarea('Kpr');
			$regions = $this->User->Kpr->KprApplication->Region->getSelectList();
			$cities = $this->User->Kpr->KprApplication->City->getSelectList(array(
				'region_id' => !empty($value[$modelName]['region_id']) ? $value[$modelName]['region_id'] : false,
			));
			$clientTypes = $this->User->ClientType->find('list');

			$documentCategories = $this->RmKpr->_callDocumentCategories(array(
				'DocumentCategory.is_required' => 1,
				'DocumentCategory.id' => Configure::read('__Site.Global.Variable.KPR.document_client'),
			), array(
				'client_id' => $id,
				'document_type' => 'client',
			));
		}
		
		$this->RmCommon->_callDataForAPI($value, 'manual');

		$this->set(compact(
			'clientTypes', 'action_type', 'is_editable', 'regions', 'cities',
			'disabledClient', 'modelName', 'clientJobTypes', 'template',
			'documentCategories'
		));

		$this->render('get_data_client');
	}

	public function admin_crm_change_status() {
		$attribute_set_id = $this->RmCommon->filterEmptyField($this->request->data, 'CrmProjectActivity', 'attribute_set_id', 3);

		$attributeSetValue = $this->User->CrmProject->AttributeSet->getData('first', array(
			'conditions' => array(
				'AttributeSet.id' => $attribute_set_id,
			),
		));

		if( !empty($attributeSetValue) ) {
			$attributeSetValue = $this->User->CrmProject->AttributeSet->getDataList($attributeSetValue);
			$attributeSets = $this->User->CrmProject->AttributeSet->getData('list', array(
				'conditions' => array(
                	'AttributeSet.show' => 1,
					'AttributeSet.scope' => 'crm',
				),
			));

			$this->set(compact(
				'attributeSetValue', 'attributeSets'
			));

			$this->render('/Elements/blocks/crm/forms/add_activity');
		} else {
			$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
		}
	}

	public function admin_project_document_upload( $id = false ) {
    	$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
    	
		if( !empty($value) ) {
			$options = array(
				'error' => true,
				'message' => __('Mohon ungah dokumen terlebih dahulu'),
			);

			if( !empty($this->request->data['files']) ) {
				$files = $this->request->data['files'];
				$info = array();
				$saveFolder = Configure::read('__Site.file_folder');
				$prefixImage = String::uuid();
				$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');

				Configure::write('__Site.allowed_ext', array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx'));

				foreach ($files as $key => $val) {
					$file_name = $this->RmCommon->filterEmptyField($val, 'name');

					$data = $this->RmImage->upload($val, $saveFolder, $prefixImage);
					$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');
					$data = array_merge($data, array(
						'CrmProjectDocument' => array(
							'name' => $file_name,
							'file' => $photo_name,
							'crm_project_id' => $id,
							'attribute_set_id' => $attribute_set_id,
						),
					));
					$file = $this->User->CrmProject->CrmProjectDocument->doSave($data);
					$info[] = $file;
				}

		  		return json_encode($info);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function admin_project_document_change( $id = false, $share = false, $title = false ) {
		$media = $this->User->CrmProject->CrmProjectDocument->getData('first', array(
			'conditions' => array(
				'CrmProjectDocument.id' => $id,
			),
		), array(
			'company' => true,
		));
		$crm_project_id = $this->RmCommon->filterEmptyField($media, 'CrmProjectDocument', 'crm_project_id');
		$media = $this->User->CrmProject->getMerge($media, $crm_project_id);

		if( !empty($media['CrmProject']) ) {
			$data['CrmProjectDocument'] = array(
				'is_share' => $share,
				'title' => $title,
			);

			$result = $this->User->CrmProject->CrmProjectDocument->doChange($id, $data);
			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
		}
	}

	public function admin_crm_payment_method() {
		$data = $this->request->data;

		$payment_type = $this->RmCommon->filterEmptyField($data, 'CrmProjectPayment', 'type');

		$this->set(compact(
			'payment_type'
		));

		$this->render('/Elements/blocks/crm/forms/payment_method');
	}

	public function admin_contact() {
		$this->loadModel('Contact');
		$data = $this->request->data;

		if( !empty($this->user_id) ) {
			$dataUser = Configure::read('User.data');
			$data['Contact']['name'] = $this->RmCommon->filterIssetField($dataUser, 'full_name');
			$data['Contact']['email'] = $this->RmCommon->filterIssetField($dataUser, 'email');
			$data['Contact']['phone'] = $this->RmCommon->filterIssetField($dataUser, 'UserProfile', 'no_hp');
		}

		$result = $this->Contact->doSave($data, $this->user_id);
		
		$option_result = array();
		if(!$this->Rest->isActive()){
			$option_result = array(
				'ajaxFlash' => true,
				'ajaxRedirect' => false,
			);
		}

		$this->RmCommon->setProcessParams($result, false, $option_result);

		if(!$this->Rest->isActive()){
			$this->set('_open', true);
			$this->set('_flash', false);
			$this->set('result', $result);
			$this->autoLayout = true;
			$this->render('/Elements/widgets/help');
			$this->layout = 'ajax';
		}else{
			$this->RmCommon->renderRest();
		}
	}

	public function admin_document_upload( $id = false, $session_id = false, $attribute_option_id = false ) {
    	$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
    	$value = $this->User->CrmProject->CrmProjectActivity->AttributeOption->getMerge($value, $attribute_option_id, 'AttributeOption', 'first');
    	
		$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id');
		$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');

		$document_category_id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'document_category_id');
		$value = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getMerge($value, $document_category_id);
		$title = $this->RmCommon->filterEmptyField($value, 'DocumentCategory', 'name');

		$options = array(
			'error' => true,
			'message' => __('Mohon ungah dokumen terlebih dahulu'),
		);

		if( !empty($this->request->data['files']) ) {
			$files = $this->request->data['files'];
			$info = array();
			$saveFolder = Configure::read('__Site.document_folder');

			foreach ($files as $key => $val) {
				$prefixImage = String::uuid();
				$file_name = $this->RmCommon->filterEmptyField($val, 'name');

				$data = $this->RmImage->upload($val, $saveFolder, $prefixImage, array(
					'fullsize' => true,
					'allowed_all_ext' => true
				));
				
				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');
				$data = array_merge($data, array(
					'CrmProjectDocument' => array(
						'owner_id' => !empty($id)?$id:0,
						'session_id' => $session_id,
						'document_category_id' => $document_category_id,
						'save_path' => $saveFolder,
						'name' => $file_name,
						'file' => $photo_name,
						'title' => $title,
					),
				));
				$file = $this->User->CrmProject->CrmProjectDocument->doSave($data);
				$info[] = $file;
			}

	  		return json_encode($info);
		} else {
			return false;
		}
	}

	function admin_document_delete($media_id, $session_id){
		if( !empty($media_id) && !empty($session_id) ){
			$result = $this->User->CrmProject->CrmProjectDocument->doDelete($media_id, $session_id);

			$this->RmCommon->setProcessParams($result);
		}else{
			$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
		}
	}

	function get_ebrosur_data_agent(){
		$email = $this->RmCommon->filterEmptyField($this->params, 'named', 'email');

		$options = array(
			'conditions' => array(
				'User.email' => $email,
			),
		);

		$user = $this->User->getData('first', $options, array(
			'company' => true,
			'status' => 'active',
		));

		if(!empty($user['User']['id'])){
			$user = $this->User->UserProfile->getMerge($user, $user['User']['id']);
		}

		$this->set('user', $user);
		$this->render('get_ebrosur_data_agent');
	}

	function api_info_property(){
		if( $this->Rest->isActive() ) {
			$data = $this->request->data;
			$mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');
			$id = $this->RmCommon->filterEmptyField($data, 'Property', 'id');

			$data = $this->User->Property->getData('first', array(
				'conditions' => array(
					'OR' => array(
						'Property.id' => $id,
						'Property.mls_id' => $mls_id,
					),
				),
			), array(
				'admin_mine' => true,
			));
			$data = $this->User->Property->getDataList($data, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertyMedias',
					'User',
				),
			));
			$this->set('data', $data);
			$this->autoRender = true;
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function api_info_user(){
		if( $this->Rest->isActive() ) {
			$data = $this->request->data;
			$email = $this->RmCommon->filterEmptyField($data, 'User', 'email');
			$id = $this->RmCommon->filterEmptyField($data, 'User', 'id');

			$data = $this->User->getData('first', array(
				'conditions' => array(
					'OR' => array(
						'User.id' => $id,
						'User.email' => $email,
					),
				),
			), array(
				'company' => true,
				'status' => 'semi-active',
			));
			$data = $this->User->getDataList($data);
			$this->set('data', $data);
			$this->autoRender = true;
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function set_sorting( $modelName = null, $type = null ) {
		$data = $this->request->data;
		$colview = $this->RmCommon->filterEmptyField($data, 'Search', 'colview');

		if( !empty($colview) && is_array($colview) ) {
			$colview = array_filter($colview);
			$result = array_keys($colview);
			$result = implode(',', $result);

			$modelName = ucwords($modelName);
			$type = strtolower($type);

			$this->Session->write(__('Sort.%s.%s', $modelName, $type), $result);
		}
	}

	function list_data ( $type = false ) {
		switch ($type) {
			case 'company':
				$data =  $this->RmCommon->_callCompanies('all');
				$data = $this->RmCommon->_callGenerateDataModel($data, 'UserCompany');
				break;
			case 'pic':
				$data =  $this->RmCommon->_callPIC();
				$data = $this->RmCommon->_callGenerateDataModel($data, 'User');
				break;
			case 'principle':
				$data =  $this->RmCommon->_callCompanies();
				$data = $this->RmCommon->_callGenerateDataModel($data, 'UserCompany');
				break;
			default:
				$data = array();
				break;
		}

		if($this->Rest->isActive()){		
			$this->set('data', $data);
			$this->set('status', 1);

			$this->RmCommon->renderRest();
		}else{
			return json_encode($data);
		}
	}

	function slide_tour ( $value = 1 ) {
		$user_id = Configure::read('User.data.UserConfig.id');
		
		$this->User->UserConfig->set('slide_tour', $value);
		$this->User->UserConfig->id = $user_id;
		$this->User->UserConfig->save();
	}

	public function get_location(){
		$params		= $this->params->params;
		$isAdmin	= Configure::read('User.admin');
		$keyword	= Common::hashEmptyField($this->request->data, 'Query.keyword');
		$keyword	= Common::hashEmptyField($this->request->data, 'query', $keyword);

		$this->loadModel('Location');

		$keyword = str_replace(array(', ', ','), '|', $keyword);
		$results = $this->Location->getData('all', array(
			'limit'			=> 10, 
			'conditions'	=> array(
				'OR' => array(
					'Location.keyword LIKE' => '%' . $keyword . '%', 
					'REPLACE(REPLACE(Location.keyword, \'-\', \'-\'), \'|\', \', \') LIKE' => '%' . $keyword . '%', 
				),
			//	sprintf('MATCH(Location.keyword) AGAINST("+\'%s\'" IN BOOLEAN MODE)', $keyword)
			), 
			'order' => array(
				'Location.keyword' => 'ASC', 
			), 
		));

		if($results){
			$temp = array();
			foreach($results as $result){
				$regionID		= Common::hashEmptyField($result, 'Location.region_id');
				$regionName		= Common::hashEmptyField($result, 'Location.region_name');
				$cityID			= Common::hashEmptyField($result, 'Location.city_id');
				$cityName		= Common::hashEmptyField($result, 'Location.city_name');
				$subareaID		= Common::hashEmptyField($result, 'Location.subarea_id');
				$subareaName	= Common::hashEmptyField($result, 'Location.subarea_name');
				$zipCode		= Common::hashEmptyField($result, 'Location.zip');

				$location	= array_filter(array($subareaName, $cityName, $regionName));
				$location	= implode(', ', $location);
				$temp[]		= array(
					'id'			=> $subareaID, 
					'name'			=> $location, 
					'region_id'		=> $regionID,
					'city_id'		=> $cityID,
					'subarea_id'	=> $subareaID,
					'zip'			=> $zipCode, 
				);
			}

			$results = $temp;
		}

		$this->autoLayout = false;
		$this->autoRender = false;

		return json_encode($results);
	}

	public function set_location($model = null, $subareaID = null){
		$model		= empty($model) ? 'PropertyAddress' : Inflector::camelize($model);
		$isAjax		= $this->RequestHandler->isAjax();
		$options	= Common::hashEmptyField($this->request->data, 'Search.location_picker_options');
		$options	= json_decode($options, true);

		if($subareaID && is_numeric($subareaID)){
			$this->loadModel('ViewLocation');

			$location = $this->ViewLocation->getData('first', array(
				'conditions' => array(
					'ViewLocation.subarea_id' => $subareaID, 
				), 
			));

			if($location){
				$fieldName		= Common::hashEmptyField($options, 'field', 'location_name');
				$fieldPrefix	= Common::hashEmptyField($options, 'field_prefix');
				$fieldName		= $fieldPrefix.$fieldName;

				$regionName		= Common::hashEmptyField($location, 'ViewLocation.region_name');
				$cityName		= Common::hashEmptyField($location, 'ViewLocation.city_name');
				$subareaName	= Common::hashEmptyField($location, 'ViewLocation.subarea_name');
				$locationName	= array_filter(array($subareaName, $cityName, $regionName));

				$this->request->data = array(
					$model => array(
						$fieldName					=> implode(', ', $locationName), 
						$fieldPrefix.'subarea_id'	=> $subareaID, 
						$fieldPrefix.'city_id'		=> Common::hashEmptyField($location, 'ViewLocation.city_id'), 
						$fieldPrefix.'region_id'	=> Common::hashEmptyField($location, 'ViewLocation.region_id'), 
						$fieldPrefix.'zip'			=> Common::hashEmptyField($location, 'ViewLocation.zip'), 
					), 
				);
			}
		}

		$this->autoLayout = false;
		$this->set(array(
			'model'		=> $model, 
			'options'	=> $options, 
		));

		$this->render('/Elements/blocks/properties/forms/location_picker');
	}

	function share ( $document_id = null, $type = null, $sosmed = null ) {
		$params = $this->params->query;
		$url = Common::hashEmptyField($params, 'url');

		$result = $this->RmCommon->_saveShare(array(
			'ShareLog' => array(
				'document_id' => $document_id,
				'url' => $url,
				'type' => $type,
				'sosmed' => $sosmed,
			),
		));

		if( !empty($result) ) {
			if($type == 'ebrosur'){
				$ebrochure = $this->User->UserCompanyEbrochure->getData('first', array(
					'conditions' => array(
						'UserCompanyEbrochure.id' => $document_id, 
					), 
				));

			//	open listing : send notification and email to property owner if logged in user not equal property owner
				$notifications = $this->User->UserCompanyEbrochure->prepareNotification($ebrochure, $sosmed);

				if($notifications){
					$this->RmCommon->_saveNotification($notifications);
					$this->RmCommon->validateEmail($notifications);
				}
			}

			echo __('Berhasil menyimpan log share');
		} else {
			echo __('Gagal menyimpan log share');
		}
	}

	public function get_property($actionType = 'all', $propertyID = null){
		$keyword = Common::hashEmptyField($this->data, 'query');
		$keyword = Common::hashEmptyField($this->data, 'Search.keyword', $keyword);

		$isAdmin			= Configure::read('User.Admin.Rumahku');
		$authUserID			= Configure::read('User.id');
		$authGroupID		= Configure::read('User.group_id');
		$isCompanyAgent		= Common::validateRole('company_agent', $authGroupID);
		$isIndependentAgent	= Common::validateRole('independent_agent', $authGroupID);

		$results	= array();
		$elements	= array(
			'status' => 'active-pending', 
		);

		$companyData	= Configure::read('Config.Company.data');
		$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id');
		$isOpenListing	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_open_listing');
		$ebrochure_lang	= Common::hashEmptyField($companyData, 'UserCompanyConfig.ebrochure_lang');

		if($isIndependentAgent){
		//  personal page
			$elements = array_merge($elements, array(
				'mine'			=> true, 
				'action_type'	=> $actionType,
			));
		}
		else{
			if($isOpenListing){
				$elements = array_merge($elements, array(
				//	https://basecamp.com/1789306/projects/10415456/todos/366370462
					'mine'			=> false, 
					'parent'		=> true,
					'skip_is_sales'	=> true,
				));
			}
			else{
				$elements = array_merge($elements, array(
					'mine'			=> $isCompanyAgent, 
					'parent'		=> true,
					'admin_mine'	=> !$isAdmin,
					'skip_is_sales'	=> true,
				));
			}
		}

		$conditions = array();

		if($propertyID){
			$limit		= 1;
			$conditions = array_merge($conditions, array(
				'Property.id' => $propertyID, 
			));
		}
		else if($keyword){
			$limit		= 10;
			$conditions = array(
				'or' => array(
					'Property.title LIKE'		=> '%'.$keyword.'%', 
					'Property.mls_id LIKE'		=> '%'.$keyword.'%', 
				//	'Property.keyword LIKE'		=> '%'.$keyword.'%', 
				//	'Property.description LIKE'	=> '%'.$keyword.'%', 
				), 
			);
		}

		$properties = $this->User->Property->getData('all', array(
			'conditions'	=> $conditions, 
			'limit'			=> $limit, 
		), $elements);

		if($properties){
			if($propertyID){
				$propertyUserID = Common::hashEmptyField($properties, '0.Property.user_id');

				if($isCompanyAgent && ($authUserID != $propertyUserID) && $isOpenListing){
				//	https://basecamp.com/1789306/projects/10415456/todos/378197695
				//	[RQ] - 5 - Studio - Buat ebrosur milik orang lain

				//	rule  :
				//	kalo yang login agen company && beda sama pemilik properti && company boleh open listing
				//	data agen pemilik properti diganti sama data agen yang login

				//	replace disini, jadi pas getmerge data pake agent login
					$properties = Hash::insert($properties, '0.Property.user_id', $authUserID);
				}

				$properties = $this->User->Property->getDataList($properties, array(
					'contain' => array(
						'MergeDefault',
						'PropertySold',
						'PropertyAddress',
						'PropertyAsset',
						'User',
					),
				));

				$properties = $this->User->Property->PropertyAsset->getMergeList($properties, array(
					'contain' => array(
						'PropertyDirection',
					),
				));				

				$properties = $this->User->getMergeList($properties, array(
					'contain' => array(
						'UserProfile', 
					), 
				));

				$properties = array_shift($properties);

				$price = $this->RmProperty->getPrice($properties);

				$propertyID		= Common::hashEmptyField($properties, 'Property.id');
				$mlsID			= Common::hashEmptyField($properties, 'Property.mls_id');
				$regionName		= Common::hashEmptyField($properties, 'PropertyAddress.Region.name');
				$cityName		= Common::hashEmptyField($properties, 'PropertyAddress.City.name');
				$subareaName	= Common::hashEmptyField($properties, 'PropertyAddress.Subarea.name');
				$zipCode		= Common::hashEmptyField($properties, 'PropertyAddress.Subarea.zip');
				$zipCode		= Common::hashEmptyField($properties, 'PropertyAddress.zip', $zipCode);

				$location = array_filter(array($subareaName, $cityName, $regionName));
				$location = implode(', ', $location);

				$location = array_filter(array($location, $zipCode));
				$location = implode(' ', $location);

				$propertySlug	= $this->RmProperty->getNameCustom($properties, false, true);
				$specification	= $this->RmProperty->getSpesification($properties, array('to_string' => true));

				$viewURL = Router::url(array(
					'admin'			=> false, 
					'controller'	=> 'properties', 
					'action'		=> 'detail', 
					'mlsid'			=> $mlsID, 
					'slug'			=> $propertySlug, 
				), true);

			//	$source		= 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.$viewURL;
				$source		= 'https://chart.googleapis.com/chart?cht=qr&chs=200x200&choe=UTF-8&chld=L|2&chl='.$viewURL; 
				$propertyQR	= Common::getQRCode($source, array(
					'filename'	=> sprintf('%s-qr-code.jpg', $mlsID), 
					'replace'	=> true, 
				));

				$agentID		= Hash::get($properties, 'User.id', '');
				$agentName		= Hash::get($properties, 'User.full_name', '');
				$agentEmail		= Hash::get($properties, 'User.email', '');
				$agentPhoto		= Hash::get($properties, 'User.photo', '');
				$agentPhones	= array_filter(array(
					Hash::get($properties, 'UserProfile.phone'), 
					Hash::get($properties, 'UserProfile.no_hp'), 
					Hash::get($properties, 'UserProfile.no_hp_2'), 
				));

				$agentLabel		= __('%s | %s', $agentName, $agentEmail);
				$agentPhones	= implode(', ', $agentPhones);

			//	medias ===============================================================================================

				App::import('Helper', 'Rumahku');
				$RumahkuHelper = new RumahkuHelper(new View());

				$savePath		= Configure::read('__Site.profile_photo_folder');
				$agentPhoto		= $RumahkuHelper->photo_thumbnail(array(
					'save_path'	=> $savePath, 
					'src'		=> $agentPhoto, 
					'size'		=> 'pxl',
					'url'		=> true,
					'fullbase'	=> true, 
				));

			//	get media data
				$propertyMedias = $this->User->Property->PropertyMedias->getData('all', array(
					'contain'		=> array('CategoryMedias'), 
					'conditions'	=> array(
						'PropertyMedias.property_id' => $propertyID, 
					), 
				), array(
					'status' => 'all', 
				));

				if($propertyMedias){
					$savePath = Configure::read('__Site.property_photo_folder');

					foreach($propertyMedias as &$media){
						$name		= Common::hashEmptyField($media, 'PropertyMedias.name');
						$category	= Common::hashEmptyField($media, 'CategoryMedias.name');

						if($name){
							$media = $RumahkuHelper->photo_thumbnail(array(
								'save_path'	=> $savePath, 
								'src'		=> $name, 
								'size'		=> 'company',
								'url'		=> true,
								'fullbase'	=> true, 
							));

							$alias = __($category ?: 'Foto Properti');
							$media = array('text' => $alias, 'url' => $media);
						}
						else{
							$media = null;
						}
					}
				}

			//	======================================================================================================
				if( $ebrochure_lang == 'en' ) {
					$property_action = Hash::get($properties, 'PropertyAction.name_en', '');
				} else {
					$property_action = Hash::get($properties, 'PropertyAction.name', '');
				}

				$results = array(
					'property_type'				=> Hash::get($properties, 'PropertyType.name', ''), 
					'property_action'			=> $property_action, 
					'property_id'				=> Hash::get($properties, 'Property.mls_id', ''), 
					'property_title'			=> Hash::get($properties, 'Property.title', ''), 
					'property_keyword'			=> Hash::get($properties, 'Property.keyword', ''), 
					'property_description'		=> Hash::get($properties, 'Property.description', ''), 
					'property_price'			=> $price, 
					'property_specification'	=> $specification, 
					'property_location'			=> $location, 
					'property_qr'				=> $propertyQR, 
					'property_photo'			=> $propertyMedias, 
					'agent_id'					=> $agentID, 
					'agent_label'				=> $agentLabel, 
					'agent_full_name'			=> $agentName, 
					'agent_name'				=> $agentName, 
					'agent_email'				=> $agentEmail, 
					'agent_phone'				=> $agentPhones, 
					'agent_photo'				=> array(
					//	format as multiple
						array(
							'text'	=> 'Foto Agen', 
							'url'	=> $agentPhoto, 
						), 
					), 
				);

				$results = array_replace($results, $this->RmEbroschure->getCompanyData());
			}
			else{
				foreach($properties as $property){
					$recordID	= Common::hashEmptyField($property, 'Property.id');
					$mlsID		= Common::hashEmptyField($property, 'Property.mls_id');
					$title		= Common::hashEmptyField($property, 'Property.title');
					$results[]	= array(
						'reference'	=> $recordID,
						'label'		=> __('%s %s', $mlsID, $title),
					);
				}
			}
		}

		return json_encode($results);
	}

	public function get_user($role = 'agent', $userID = null){
		$roles = array(
			'director'			=> 'Direktur', 
			'admin-director'	=> 'Admin Direktur', 
			'agent'				=> 'Agen', 
			'admin'				=> 'Administrator', 
			'principle'			=> 'Principal', 
			'client'			=> 'Klien', 
			'user'				=> 'User', 
		);

		$role		= in_array($role, array_keys($roles)) ? $role : 'agent';
		$results	= array();

		$keyword	= Common::hashEmptyField($this->data, 'Search.keyword');
		$conditions	= array();

		if($userID){
			$conditions = array(
				'User.id' => $userID, 
			);
		}

		if($keyword){
			$conditions = array(
				'or' => array(
					'User.full_name LIKE'	=> '%'.$keyword.'%', 
					'User.email LIKE'		=> '%'.$keyword.'%', 
				), 
			);
		}

		$results = $this->User->getData('all', array(
			'conditions'	=> $conditions, 
			'limit'			=> 10, 
		), array(
			'role'		=> $role, 
			'status'	=> 'active', 
			'company'	=> true, 
		));

		$results = $this->User->getMergeList($results, array(
			'contain' => array(
				'UserProfile', 
			), 
		));

		if($userID){
			$results = array_shift($results);

			$fullName	= Hash::get($results, 'User.full_name', '');
			$email		= Hash::get($results, 'User.email', '');
			$photo		= Hash::get($results, 'User.photo', '');
			$phones		= array_filter(array(
				Hash::get($results, 'UserProfile.phone'), 
				Hash::get($results, 'UserProfile.no_hp'), 
				Hash::get($results, 'UserProfile.no_hp_2'), 
			));

			App::import('Helper', 'Rumahku');
			$RumahkuHelper = new RumahkuHelper(new View());

			$savePath	= Configure::read('__Site.profile_photo_folder');
			$photo		= $RumahkuHelper->photo_thumbnail(array(
				'save_path'	=> $savePath, 
				'src'		=> $photo, 
				'size'		=> 'pxl',
				'url'		=> true,
				'fullbase'	=> true, 
			));

			$roleName	= Hash::get($roles, $role, '');
			$results	= array(
				$role.'_full_name'	=> $fullName, 
				$role.'_name'		=> $fullName, 
				$role.'_email'		=> $email, 
				$role.'_phone'		=> implode(', ', $phones), 
				$role.'_photo'		=> array(
				//	format as multiple
					array(
						'text'	=> __('Foto %s', $roleName), 
						'url'	=> $photo, 
					), 
				), 
			);

			$results = array_replace($results, $this->RmEbroschure->getCompanyData());
		}
		else{
			foreach($results as &$result){
				$recordID	= Common::hashEmptyField($result, 'User.id');
				$fullName	= Common::hashEmptyField($result, 'User.full_name');
				$email		= Common::hashEmptyField($result, 'User.email');

				$result = array(
					'reference'	=> $recordID,
					'label'		=> __('%s | %s', $fullName, $email),
				);
			}
		}

		return json_encode($results);
	}

	public function get_ebrochure_template(){
		$this->loadModel('EbrochureTemplate');

		$userID			= Common::config('User.id');
		$principleID	= Common::config('Principle.id');
		$isAdmin		= Common::validateRole('admin');
		$isCompanyAdmin	= Common::validateRole('company_admin');

		$limit		= Configure::read('__Site.config_new_table_pagination');
		$conditions	= array();
		$elements	= array(
			'company' => true, 
		);

		$type = Common::hashEmptyField($this->params->named, 'type');

		if($isAdmin || $isCompanyAdmin || $type == 'company'){
			$conditions = array(
				'EbrochureTemplate.principle_id' => array(0, $principleID), 
			);
		}
		else{
			$conditions = array(
				'or' => array(
				//	made by prime admin / company admin
					array(
						'EbrochureTemplate.principle_id'	=> array(0, $principleID), 
					//	'EbrochureTemplate.user_id'			=> 0, 
					), 
				//	made by user
					'EbrochureTemplate.user_id' => $userID,  
				), 
			);
		}

		$options = $this->EbrochureTemplate->_callRefineParams($this->params->params, array(
			'limit'			=> false, //$limit,
			'conditions'	=> $conditions, 
			'cache'			=> sprintf('EbrochureTemplate.Data.%s.%s', $principleID, $userID), 
		));

		$this->RmCommon->_callRefineParams($this->params);

		$templates = $this->EbrochureTemplate->getData('all', $options, $elements);
		$templates = $this->EbrochureTemplate->getMergeList($templates, array(
			'contain' => array(
				'User', 
			), 
		));

		if($this->params->requested){
		//	$this->request->params['requested'] isi nya 1 kalo controller request controller
			return $templates;
		}
		else{
			$this->layout = false;
			$this->set(array('templates' => $templates));
			$this->render('/Elements/blocks/ebrosurs/ebrochure_builder/panels/template-panel');
		}
	}
}
?>