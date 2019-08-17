<?php
App::uses('AppController', 'Controller');

class PartnerMediasController extends AppController{
	public $uses = array('UserIntegratedOrderAddon');

	public function beforeFilter(){
		parent::beforeFilter();

		$this->set('active_menu', 'finance');
		$this->Auth->allow(array(
			'admin_notify', 'admin_finalize', 'complete', 'admin_identify', 'force_paid'
		));

		$this->mallID		= Configure::read('__Site.doku_mall_id');
		$this->sharedKey	= Configure::read('__Site.doku_shared_key');
		$this->whiteListIP	= Configure::read('__Site.doku_ip');
	}

	public function admin_identify(){
	//	persiapan jika suatu saat dipake
		$this->layout		= FALSE;
		$this->autoRender	= FALSE;

		return TRUE;
	}

	public function admin_notify(){
		if($this->request->is('post')){
			$requesterIP = $this->RequestHandler->getClientIP();

			if($this->_isAllowed($requesterIP, TRUE)){
				$data = $this->request->data;

				if($data){
					$totalAmount		= $this->RmCommon->filterEmptyField($data, 'AMOUNT');
					$invoiceNumber		= $this->RmCommon->filterEmptyField($data, 'TRANSIDMERCHANT');
					$responseCode		= $this->RmCommon->filterEmptyField($data, 'RESPONSECODE');
					$responseMessage	= $this->RmCommon->filterEmptyField($data, 'RESULTMSG');
					$verifyID			= $this->RmCommon->filterEmptyField($data, 'VERIFYID');
					$verifyScore		= $this->RmCommon->filterEmptyField($data, 'VERIFYSCORE');
					$verifyStatus		= $this->RmCommon->filterEmptyField($data, 'VERIFYSTATUS');
					$secretWord			= $this->RmCommon->filterEmptyField($data, 'WORDS');

				//	generate internal words
				//	rumus SHA1(AMOUNT + MALLID + <shared key> + TRANSIDMERCHANT + RESULTMSG + VERIFYSTATUS)
					$shaWord = sha1($totalAmount . $this->mallID . $this->sharedKey . $invoiceNumber . $responseMessage . $verifyStatus);

					if($secretWord == $shaWord){
						$approvalCode		= $this->RmCommon->filterEmptyField($data, 'APPROVALCODE');
						$paymentChannel		= $this->RmCommon->filterEmptyField($data, 'PAYMENTCHANNEL');
						$paymentCode		= $this->RmCommon->filterEmptyField($data, 'PAYMENTCODE');
						$sessionID			= $this->RmCommon->filterEmptyField($data, 'SESSIONID');
						$bankIssuer			= $this->RmCommon->filterEmptyField($data, 'BANK');
						$creditcardNumber	= $this->RmCommon->filterEmptyField($data, 'MCN');
						$paymentDatetime	= $this->RmCommon->filterEmptyField($data, 'PAYMENTDATETIME');
						$statusType			= $this->RmCommon->filterEmptyField($data, 'STATUSTYPE');

					//	get invoice data
						$record = $this->UserIntegratedOrderAddon->getData('first', array(
							'recursive'		=> 1,
							'conditions'	=> array(
								'UserIntegratedOrderAddon.invoice_number'	=> $invoiceNumber,
								'UserIntegratedOrderAddon.payment_status'	=> array('process', 'failed', 'waiting')
							)
						));

						if($record){
							if($responseMessage == 'SUCCESS'){
								$invoiceData = array(
									'UserIntegratedOrderAddon' => array(
										'payment_status'	=> 'paid',
										'response_code'		=> $responseCode,
										'response_message'	=> $responseMessage,
										'secret_word'		=> $secretWord,
										'status_type'		=> $statusType,
										'approval_code'		=> $approvalCode,
										'payment_channel'	=> $paymentChannel,
										'payment_code'		=> $paymentCode,
										'session_id'		=> $sessionID,
										'bank_issuer'		=> $bankIssuer,
										'creditcard_number'	=> $creditcardNumber,
										'payment_datetime'	=> $paymentDatetime,
										'verify_id'			=> $verifyID,
										'verify_score'		=> $verifyScore,
										'verify_status'		=> $verifyStatus
									)
								);
							}
							else{
								$invoiceData = array(
									'UserIntegratedOrderAddon' => array(
										'payment_status'	=> 'failed',
										'response_code'		=> $responseCode,
										'response_message'	=> $responseMessage
									)
								);
							}

							$this->UserIntegratedOrderAddon->id = $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'id');

							if($this->UserIntegratedOrderAddon->save($invoiceData)){
								if($responseMessage == 'SUCCESS'){
									$status	= $this->RmCommon->filterEmptyField($result, 'status');

									if($status == 'success'){
										echo(__('CONTINUE'));
									}
									else{
										echo(__('FAILED'));
									}
								}
								else{
									echo(__('CONTINUE'));
								}
							}
							else{
								echo(__('FAILED'));
							}
						}
						else{
							echo(__('INVALID INVOICE'));
						}
					}
					else{
						echo(__('INVALID WORDS'));
					}
				}
				else{
					echo(__('INVALID DATA'));
				}
			}
			else{
				echo(__('INVALID DATA SOURCE'));
			}
		}
		else{
			echo(__('INVALID METHOD'));
		}

		exit;
	}

	public function admin_finalize(){
		$isPost	= !empty($this->request->data);
		$data	= $isPost ? $this->request->data : $this->params->query;

	//	default redirect url
		$redirectURL = Router::url(array(
			'action'	=> 'index', 
			'admin'		=> true, 
		));

		if($data){
			$invoiceNumber	= $this->RmCommon->filterEmptyField($data, 'TRANSIDMERCHANT');
			$record			= $this->UserIntegratedOrderAddon->getData('first', array(
				'conditions' => array(
					'UserIntegratedOrderAddon.invoice_number' => $invoiceNumber, 
				), 
			));

			if($record){
				$recordID	= $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'id');
				$defaultMsg	= 'Pembayaran untuk Invoice';

			//	redirect ===========================================================================================
				$redirectURL = sprintf('%s/PartnerMedias/complete/%s/%s', FULL_BASE_URL, $recordID, $invoiceNumber);
			//	====================================================================================================
			// debug($isPost);
				if($isPost){
				//	HIT BY DOKU
					$paymentCode	= $this->RmCommon->filterEmptyField($data, 'PAYMENTCODE');
					$responseCode	= $this->RmCommon->filterEmptyField($data, 'STATUSCODE');
					$paymentChannel	= $this->RmCommon->filterEmptyField($data, 'PAYMENTCHANNEL');
					
					// debug($data);
					// debug($record);die();
					if($responseCode == '0000'){
					//	kirim email ke user makasih udah membayar ===============================================

						$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
						$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');

						$subject		= 'Informasi pembayaran transaksi';
						$template		= 'paid_transaction_integration';

						$financeEmail	= Configure::read('Global.Data.finance_email');
						$senderEmail	= Configure::read('__Site.send_email_from');
						$bcc =  array(
							// $financeEmail, 
							//	ga usah dinaikin
							'foezaf13@gmail.com',
							// 'finance@primesystem.id', 
							// 'rikarumahku@gmail.com', 
							// 'andriani@rumahku.com', 
							// Configure::read('__Site.prime_leads_email'),
						);

						$params = array_merge($record, array(
							'from'	=> $senderEmail, 
							'bcc'	=> $bcc, 
							// 'debug'	=> 'view', 
						));

						$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

					//	=========================================================================================

						$result = array(
							'status'	=> 'success', 
							'message'	=> __('%s : %s sukses.', $defaultMsg, $invoiceNumber), 
						);
					}
					else if($responseCode == '5510'){
						$result = array(
							'status'	=> 'error', 
							'message'	=> __('%s : %s dibatalkan.', $defaultMsg, $invoiceNumber), 
						);
					}
					else if($responseCode == '5511'){
					//	payment code baru dapet disini, jadi save disini
						$this->UserIntegratedOrderAddon->save(array(
							'UserIntegratedOrderAddon' => array(
								'id'				=> $recordID, 
								'payment_code'		=> $paymentCode, 
								'payment_channel'	=> $paymentChannel, 
							), 
						));

					//	change invoice status to waiting
						$record = $this->UserIntegratedOrderAddon->read(null, $recordID);
						$record = $this->UserIntegratedOrderAddon->setPaymentStatus($record, 'waiting');

					//	kirim email ke user untuk segera membayar ===============================================

						$fullName	= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
						$email		= $this->RmCommon->filterEmptyField($record, 'User', 'email');
						$subject	= 'Informasi pembayaran transaksi';
						$template	= 'transfer_invoice_notification';

						$financeEmail	= Configure::read('Global.Data.finance_email');
						$senderEmail	= Configure::read('__Site.send_email_from');
						$params			= array_merge($record, array(
							'bcc'			=> array(
								// $financeEmail, 
							//	ga usah dinaikin
								'foezaf13@gmail.com',
								// 'finance@primesystem.id', 
								// 'rikarumahku@gmail.com', 
								// 'andriani@rumahku.com', 
								// Configure::read('__Site.prime_leads_email'),
							), 
							'from'			=> $senderEmail, 
							'with_greet'	=> false, 
						//	'debug'			=> 'view', 
						));

						$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

					//	=========================================================================================

						$expDatetime	= $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'transfer_expired_date');
						$expDatetime	= date('d/m/Y H:i', strtotime($expDatetime));
						$paymentPlace	= 'tempat pembayaran';

						if($paymentChannel == '05'){
							$paymentPlace = 'ATM';
						}
						else if($paymentChannel == '14'){
							$paymentPlace = 'Alfamart';
						}

						$message	= 'Silakan lakukan pembayaran untuk Invoice : <b>%s</b> di %s terdekat sebelum <strong>%s</strong>.';
						$result		= array(
							'status'	=> 'success', 
							'message'	=> __($message, $invoiceNumber, $paymentPlace, $expDatetime), 
						);
					}
					else{
						$errorCode	= $responseCode ? sprintf('(Error Code : %s)', $responseCode) : '';
						$result		= array(
							'status'	=> 'error',
							'message'	=> __('%s : %s gagal. %s', $defaultMsg, $invoiceNumber, $errorCode), 
						);
					}
				}
				else{
				//	HIT BY BCA
					$responseCode = $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'response_code');

					if($responseCode == '0000'){
						$result = array(
							'status'	=> 'success',
							'message'	=> __('%s : %s sukses.', $defaultMsg, $invoiceNumber), 
						);
					}
					else{
						$errorCode	= $responseCode ? sprintf('(Error Code : %s)', $responseCode) : '';
						$result		= array(
							'status'	=> 'error',
							'message'	=> __('%s : %s gagal. %s', $defaultMsg, $invoiceNumber, $errorCode), 
						);
					}
				}
			}
			else{
				$result = array(
					'status'	=> 'error',
					'message'	=> __('Invoice tidak ditemukan.'),
				);
			}
		}
		else{
			$result = array(
				'status'	=> 'error',
				'message'	=> __('Invalid Method.'),
			);
		}

		$this->RmCommon->setCustomFlash($result['message'], $result['status']);
		$this->redirect($redirectURL);
	}

	public function complete($recordID = NULL, $invoiceNumber = NULL){
		$redirect = array(
			'controller'	=> 'users',
			'action'		=> 'account',
			'admin'			=> true,
		);

		$record = $this->UserIntegratedOrderAddon->getData('first', array(
			'conditions' => array(
				'UserIntegratedOrderAddon.id'				=> $recordID,
				'UserIntegratedOrderAddon.invoice_number'	=> $invoiceNumber,
				'UserIntegratedOrderAddon.payment_status'	=> array('paid', 'failed', 'waiting'),
			), 
		));

		if($record){
			$this->RmCommon->_layout_file('report');

			$this->set(array(
				'record'			=> $record, 
				'collapseHeader'	=> true, 
				'bodyClass'			=> 'cart', 
				'bubleType' 		=> false,
			));
		}
		else{
			$this->RmCommon->setCustomFlash(__('Invoice tidak ditemukan atau sudah pernah diproses sebelumnya.'), 'error');
			$this->redirect($redirect);
		}
	}

	private function _isAllowed($IPAddress = NULL, $ignoreIP = FALSE){
		if($ignoreIP === TRUE){
			return TRUE;
		}

		if(substr($IPAddress, 0, strlen($this->whiteListIP)) == $this->whiteListIP){
			return TRUE;
		}

		return FALSE;
	}

	function admin_search ( $action, $_admin = true ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'admin' => $_admin,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	public function admin_packages(){

    	$module_title = __('Partner Package');
    	$this->loadModel('UserIntegratedAddonPackage');
		
		$options =  $this->UserIntegratedAddonPackage->_callRefineParams($this->params, array(
			'conditions' => array(
				'UserIntegratedAddonPackage.status' => 1,
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->UserIntegratedAddonPackage->getData('paginate', $options);
		$values = $this->paginate('UserIntegratedAddonPackage');

		$this->set('active_menu', 'partner_package');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	public function admin_add_package() {

    	$module_title = __('Tambah Package');
    	$urlRedirect = array(
            'controller' => 'partner_medias',
            'action' => 'packages',
            'admin' => true
        );
			
		$this->loadModel('UserIntegratedAddonPackage');

		$data = $this->request->data;
		$result = $this->UserIntegratedAddonPackage->doSave( $data );
		
		$this->RmCommon->setProcessParams($result, $urlRedirect);
		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$this->set('active_menu', 'career');
		$this->set(compact(
			'module_title'
		));
		$this->render('partner_package_form');
	}

	public function admin_edit_package( $package_id ) {
        
        $module_title = __('Edit Package');
        $urlRedirect = array(
            'controller' => 'partner_medias',
            'action' => 'packages',
            'admin' => true
        );

        $this->loadModel('UserIntegratedAddonPackage');
        $package = $this->UserIntegratedAddonPackage->getData('first', array(
        	'conditions' => array(
				'UserIntegratedAddonPackage.id' => $package_id,
			),
		));

		if( !empty($package) ) {

			$data = $this->request->data;
			$result = $this->UserIntegratedAddonPackage->doSave( $data, $package, $package_id );
			$this->RmCommon->setProcessParams($result, $urlRedirect);
			$this->RmCommon->_layout_file(array(
				'ckeditor',
			));

			$this->set('active_menu', 'partner_package');
			$this->set(compact(
				'module_title'
			));
			$this->render('partner_package_form');
		} else {
			$this->RmCommon->redirectReferer(__('Package tidak ditemukan'));
		}
    }

    public function admin_delete_multiple_package() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'UserIntegratedAddonPackage', 'id');

		$this->loadModel('UserIntegratedAddonPackage');
    	$result = $this->UserIntegratedAddonPackage->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    // ==================================================================
    // ==================== Start: FUNC OLX PARTNER =====================
    // ==================================================================

	// ============= step 1 : get the access token from olx =============
	/*  ==================================================================
		step 1 : get the access token from olx 
		this is required for Authorization header olx's API
	==================================================================  */
	public function admin_auth_token( $option = array() ){
		$return = Common::hashEmptyField($option, 'return');

		$target_url = 'https://stg-api.oleks.id/api/v2/oauth/token/';
		$post = array(
			'client_id'     => 125,
			'client_secret' => 'e5ccea37452f77cf68e243b044b660cf',
			'username'      => 'lastrelease4@olx-trojan.com',
			'password'      => 'testing',
			'grant_type'    => 'password',
		);

		$response = Common::httpRequest($target_url, $post, array(
			// 'debug'	      => true,
			'method'      => 'POST',
			'ssl_version' => 'false',
			'header'      => array(
				'user_agent' => 'curl/7.47.0',
			),
		));

		if ($return) {
			return $response;
		}

		$this->layout = FALSE;
		$this->render(false);
	}

	/*  ==================================================================
		step 2 : upload images to olx 
		upload image property to their cdn first to get the temporary_key.
		"temporary_key" is used for create the Ad (listing)
		max upload image 8 ( base in olx's documentation )
	==================================================================  */
	public function admin_partner_upload_image(){
		$this->layout = false;
		$this->autoRender = false;
		$this->response->type('png');

		$response = $this->admin_auth_token( array('return' => 1) );

		$access_token = Common::hashEmptyField($response, 'response.access_token');
		$expires_in   = Common::hashEmptyField($response, 'response.expires_in');
		$token_type   = Common::hashEmptyField($response, 'response.token_type');

		$target_url = 'https://stg-api.oleks.id/api/v2/account/temporary-image-storage/';
		$post_image = 'https://agentv2.pasiris.com/img/view/general/fullsize/2019/02/8/5c736796-8460-44fa-a01d-7271ca2ba9b6.jpg';

		$cfile = curl_file_create($post_image,'image/png', 'file');
		$post  = array(
		   'file' => $cfile,
		);

		$response = Common::httpRequest($target_url, $post, array(
			'debug'	    => true,
			'urldecode' => true,
			'method' => 'POST',
			'header' => array(
				'user_agent'    => 'curl/7.47.0',
				'authorization'	=> __('%s %s', $token_type, $access_token),
				// 'authorization'	=> 'bearer 25204ac350e49c0b7bd9e7d9664e81a89c7f4025',
				// 'content_type'  => 'multipart/form-data',
			),
		));

	}

	/*  ==================================================================
		step 3 : upload / create ad to olx 
		add the temporary_key image from step 2 ( base in olx's documentation )

		for edit Ad use same url the different is you need the ad_id that you have been created
	==================================================================  */
	public function admin_partner_create_ad($ad_id = 0){
		// $response = $this->admin_auth_token( array('return' => 1) );

		// $access_token = Common::hashEmptyField($response, 'response.access_token');
		// $expires_in   = Common::hashEmptyField($response, 'response.expires_in');
		// $token_type   = Common::hashEmptyField($response, 'response.token_type');
		// debug($response);die();

		$link_auth = 'https://stg-api.oleks.id/api/v2/account/advert/';
		$data = array(
			'title'       => "Primesystem Post Ad 1",
			'description' => "Post ad description description goes here",
			'params'      => array(
				'p_bathroom' => 4,
				'p_floor'        => 21,
				'p_certificate'  => "shm-sertifikathakmilik",
				'p_facility'     => array('ac'),
				'p_sqr_building' => "221",
				'p_sqr_land'     => "300",
				'price'          => array('arranged', '12345'),
				'p_bedroom'      => 2
			),
			"city_id" => 217,
			"category_id" => 5158,
			"photos_group_key" => 440567394,
		);

		if ($ad_id) {
			$data['ad_id'] = $ad_id;
		}

		$response = Common::httpRequest($link_auth, $data, array(
			'debug'	 => true,
			'method' => 'POST',
			'header' => array(
				'authorization'	=> 'bearer 25204ac350e49c0b7bd9e7d9664e81a89c7f4025',
				'user_agent' => 'curl/7.47.0',
				// 'authorization'	=> __('%s %s', $token_type, $access_token),
			), 
			'data_type'   => 'json',
		));

	}

    // ==================================================================
    // ====================== End: FUNC OLX PARTNER =====================
    // ==================================================================

    public function force_paid( $invoiceNumber = false ){
	//	default redirect url
		$redirectURL = Router::url(array(
			'action'	=> 'index', 
			'admin'		=> true, 
		));

		$record = $this->UserIntegratedOrderAddon->getData('first', array(
			'conditions' => array(
				'UserIntegratedOrderAddon.invoice_number' => $invoiceNumber, 
			), 
		));

		if($record){
			$isAdmin	= Configure::read('User.Admin');
			$recordID 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.id');
			$refererURL = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.referer_url', FULL_BASE_URL);

			$defaultMsg	= 'Pembayaran untuk Invoice';

		//	redirect ===========================================================================================

			$redirectURL = $refererURL;
			$redirectURL.= substr($redirectURL, -1) == '/' ? null : '/';

			if(strpos($refererURL, '/admin') !== false){
				$redirectURL.= sprintf('partner_medias/?ref=%s', $invoiceNumber);
			}
			else{
				$redirectURL.= sprintf('partner_medias/complete/%s/%s', $recordID, $invoiceNumber);
			}

		//	====================================================================================================
			$paymentCode 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.payment_code');
			$responseCode 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.response_code');
			$paymentChannel	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.payment_channel');
			$paymentChannel	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.payment_channel');

			if($responseCode == '0000' || $responseCode == NULL){
			// test proses paid
				$invoiceData = array(
					'UserIntegratedOrderAddon' => array(
						'payment_status'	=> 'paid',
						'response_message'  => 'SUCCESS',
						'status_type'		=> 'P',
						'response_code'		=> '0000',
						'payment_channel'	=> $paymentChannel,
						'payment_code'		=> $paymentCode
					)
				);

				$this->UserIntegratedOrderAddon->id = $recordID;
				if($this->UserIntegratedOrderAddon->save($invoiceData)){
					echo(__('CONTINUE'));
				}
				else{
					echo(__('FAILED'));
				}

			//	kirim email ke user makasih udah membayar ===============================================

				$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
				$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
				$principleEmail	= $this->RmCommon->filterEmptyField($record, 'Principle', 'email');
				$subject		= 'Informasi pembayaran transaksi';
				$template		= 'paid_invoice_notification';

				$financeEmail	= Configure::read('Global.Data.finance_email');
				$senderEmail	= Configure::read('__Site.send_email_from');
				// $bcc =  array(
				// 	$financeEmail, 
				// );

				// if( !empty($principleEmail) ) {
				// 	$bcc[] = $principleEmail;
				// }

				$params			= array_merge($record, array(
					'from'	=> $senderEmail, 
					// 'bcc'	=> $bcc, 
					// 'debug'	=> 'view', 
				));

				// $emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

			//	=========================================================================================

				$result = array(
					'status'	=> 'success', 
					'message'	=> __('%s : %s sukses.', $defaultMsg, $invoiceNumber), 
				);
			}
			else if($responseCode == '5510'){
				$result = array(
					'status'	=> 'error', 
					'message'	=> __('%s : %s dibatalkan.', $defaultMsg, $invoiceNumber), 
				);
			}
			else if($responseCode == '5511'){
			//	payment code baru dapet disini, jadi save disini
				$this->UserIntegratedOrderAddon->save(array(
					'UserIntegratedOrderAddon' => array(
						'id'				=> $recordID, 
						'payment_code'		=> $paymentCode, 
						'payment_channel'	=> $paymentChannel, 
					), 
				));

			//	change invoice status to waiting
				$record = $this->UserIntegratedOrderAddon->read(null, $recordID);
				$record = $this->UserIntegratedOrderAddon->setPaymentStatus($record, 'waiting');

			//	kirim email ke user untuk segera membayar ===============================================

				$fullName	= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
				$email		= $this->RmCommon->filterEmptyField($record, 'User', 'email');
				$subject	= 'Informasi pembayaran transaksi';
				$template	= 'transfer_invoice_notification';

				$financeEmail	= Configure::read('Global.Data.finance_email');
				$senderEmail	= Configure::read('__Site.send_email_from');
				$params			= array_merge($record, array(
					'bcc'			=> array(
						$financeEmail, 
					), 
					'from'			=> $senderEmail, 
					'with_greet'	=> false, 
				//	'debug'			=> 'view', 
				));

				// $emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

			//	=========================================================================================

				$expDatetime	= $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'transfer_expired_date');
				$expDatetime	= date('d/m/Y H:i', strtotime($expDatetime));
				$paymentPlace	= 'tempat pembayaran';

				if($paymentChannel == '05'){
					$paymentPlace = 'ATM';
				}
				else if($paymentChannel == '14'){
					$paymentPlace = 'Alfamart';
				}

				$message	= 'Silakan lakukan pembayaran untuk Invoice : <b>%s</b> di %s terdekat sebelum <strong>%s</strong>.';
				$result		= array(
					'status'	=> 'success', 
					'message'	=> __($message, $invoiceNumber, $paymentPlace, $expDatetime), 
				);
			}
			else{
				$errorCode	= $responseCode ? sprintf('(Error Code : %s)', $responseCode) : '';
				$result		= array(
					'status'	=> 'error',
					'message'	=> __('%s : %s gagal. %s', $defaultMsg, $invoiceNumber, $errorCode), 
				);
			}
		}
		else{
			$result = array(
				'status'	=> 'error',
				'message'	=> __('Invoice tidak ditemukan.'),
			);
		}

		$this->RmCommon->setCustomFlash($result['message'], $result['status']);
		$this->redirect($redirectURL);
	}

}
