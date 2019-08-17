<?php
App::uses('AppController', 'Controller');

class PaymentsController extends AppController{
	public $uses = array('Payment');

//	parameter doku (default), setting bisa dari RmCommon
	private $mallID			= '2762';
	private $sharedKey		= '4W1JgkN98eDf';
	private $whiteListIP	= '103.10.129.';

	public function beforeFilter(){
		parent::beforeFilter();

	//	kirim email ke user makasih udah membayar ===============================================
	//	NOTE : jangan dinaikin, buat debug display desain email, biar ga usah proses2 mulu
	/*
		$record = $this->Payment->getData('first', array(
			'conditions' => array(
				'Payment.invoice_number' => 'CW-INV201612180004', 
			), 
		));

		$userID			= $this->RmCommon->filterEmptyField($record, 'Payment', 'user_id');
		$principleID	= $this->RmCommon->filterEmptyField($record, 'Payment', 'principle_id');

		$record = $this->User->UserProfile->getMerge($record, $principleID);
		$record = $this->User->UserCompany->getMerge($record, $principleID);
		$record = $this->User->UserCompanyConfig->getMerge($record, $principleID);

	//	test proses paid
	//	$this->Payment->processPaidInvoice($record);exit;

		$fullName	= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
		$email		= $this->RmCommon->filterEmptyField($record, 'User', 'email');
		$subject	= 'Contoh isi subject';
		$template	= 'paid_invoice_notification';

		$financeEmail	= Configure::read('Global.Data.finance_email');
		$senderEmail	= Configure::read('__Site.send_email_from');
		$params			= array_merge($record, array(
			'bcc'			=> $financeEmail, 
			'from'			=> $senderEmail, 
			'debug'			=> 'view', 
			'with_greet'	=> false, 
		));

		$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);
	*/
	//	=========================================================================================

		$this->set('active_menu', 'finance');
		$this->Auth->allow(array(
			'checkout', 		// jangan di apus, buat payment frontend
			'complete', 		// jangan di apus, buat payment frontend
			'admin_notify', 	// jangan di apus, buat doku request ke kita
			'admin_finalize', 	// jangan di apus, buat doku request ke kita
			'admin_identify', 	// jangan di apus, buat doku request ke kita
			'admin_send_invitation', 
			'test',
		));

		$dokuMallID		= Configure::read('__Site.doku_mall_id');
		$dokuSharedKey	= Configure::read('__Site.doku_shared_key');
		$dokuIP			= Configure::read('__Site.doku_ip');

		$this->mallID		= $dokuMallID ? $dokuMallID : $this->mallID;
		$this->sharedKey	= $dokuSharedKey ?  $dokuSharedKey : $this->sharedKey;
		$this->whiteListIP	= $dokuIP ? $dokuIP : $this->whiteListIP;
	}

	public function admin_search($action, $_admin = TRUE){
		$data	= $this->request->data;
		$named	= $this->RmCommon->filterEmptyField($this->params, 'named');
		$params	= array('action' => $action, 'admin' => $_admin);

		if(!empty($named)){
			$params = array_merge($params, $named);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	public function admin_index(){
		$conditions = array(
			'contain' => array(
				'User',
				'MembershipPackage',
				'MembershipOrder',
			),
			'order' => array(
				'Payment.created' => 'DESC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		);

		if(!$this->RmCommon->_isAdmin()){
			$userID		= Configure::read('User.data.id');
			$parentID	= Configure::read('User.data.parent_id');
			$arrUserID	= array($userID);

		//	admin perusahaan bisa ngeliat semua invoice 1 perusahaan
			if($this->RmCommon->_isCompanyAdmin() && $parentID){
				$arrUserID[] = $parentID;

				$siblings = $this->User->getData('all', array(
					'conditions' => array(
						'User.parent_id' => $parentID, 
					)
				), array('status' => 'active'));

				$siblingID = Hash::extract($siblings, '{n}.User.id');
				$siblingID = array_unique(array_filter($siblingID));
				$arrUserID = array_merge($arrUserID, $siblingID);
			}

			$conditions = array_merge_recursive($conditions, array(
				'conditions' => array(
					'Payment.user_id' => $arrUserID, 
				),
			));
		}

		$options = $this->Payment->_callRefineParams($this->params, $conditions);

		$this->RmCommon->_callRefineParams($this->params);
		$this->paginate	= $this->Payment->getData('paginate', $options);
		$records		= $this->paginate('Payment');

	//	untuk handle balikan dari www.primesystem.id, supaya ada flash message nya ======================================

		$refInvoiceNumber = $this->RmCommon->filterEmptyField($this->params->query, 'ref');
		if($refInvoiceNumber){
			$this->_setInvoiceNotice($refInvoiceNumber);
		}
		$packages = $this->Payment->MembershipPackage->getData('list', array(
			'conditions' => array(
				// 'MembershipPackage.status' => 1, 
				'MembershipPackage.is_deleted' => 0,
			),
		));

	//	=================================================================================================================

		$this->set(array(
			'module_title'		=> __('Invoice Membership'),
			'title_for_layout'	=> __('Invoice Membership'),
			'records'			=> $records,
			'packages'			=> $packages,
		));
	}

	public function _setInvoiceNotice($invoiceNumber = null){
		if($invoiceNumber){
		//	list invoice yang udah di pernah di redirect (jadi kalo di refresh ga usah notice lagi)
			$displayedInvoices = $this->Session->read('RedirectInvoice.data');
			$displayedInvoices = $displayedInvoices ? $displayedInvoices : array();

			if(in_array($invoiceNumber, $displayedInvoices) === false){
				$displayedInvoices = array_merge($displayedInvoices, array($invoiceNumber));
				$this->Session->write('RedirectInvoice.data', $displayedInvoices);

				$refInvoice = $this->Payment->getData('first', array(
					'recursive'		=> -1, 
					'conditions'	=> array(
						'Payment.invoice_number' => $invoiceNumber, 
					), 
				));

				$status	= $this->RmCommon->filterEmptyField($refInvoice, 'Payment', 'payment_status');
				$result	= null;

				switch($status){
					case 'paid' : 
						$result = array(
							'status'	=> 'success', 
							'message'	=> __('Invoice <strong>%s</strong> telah dibayar', $invoiceNumber), 
						);
					break;
					case 'waiting' : 
						$expired = $this->RmCommon->filterEmptyField($refInvoice, 'Payment', 'transfer_expired_date');
						if($expired && $expired != '0000-00-00 00:00:00'){
							$expired = date('d M Y H:i', strtotime($expired));
							$expired = sprintf(' sebelum <strong>%s</strong>', $expired);
						}

						$result	= array(
							'status'	=> 'success', 
							'message'	=> __('Silakan lakukan pembayaran untuk Invoice <strong>%s</strong> %s', $invoiceNumber, $expired), 
						);
					break;
					case 'cancelled' : 
						$result = array(
							'status'	=> 'error', 
							'message'	=> __('Invoice <strong>%s</strong> telah dibatalkan', $invoiceNumber), 
						);
					break;
					case 'expired' : 
						$result = array(
							'status'	=> 'error', 
							'message'	=> __('Invoice <strong>%s</strong> telah kadaluarsa', $invoiceNumber), 
						);
					break;
				}

				if($result){
					$this->RmCommon->setCustomFlash($result['message'], $result['status']);
				}
			}
		}
	}

	public function admin_view($recordID = NULL, $invoiceNumber = NULL){
		$authUserID		= $this->Auth->user('id');
		$authGroupID	= $this->Auth->user('group_id');
		$authParentID	= $this->Auth->user('parent_id');
		$isCompanyAdmin	= $this->RmCommon->_isCompanyAdmin();
		$conditions		= array(
			'conditions' => array(
				'Payment.id'				=> $recordID,
				'Payment.invoice_number'	=> $invoiceNumber,
			)
		);

		if($isCompanyAdmin && $authGroupID != 4){
			$arrUserID = array($authUserID);

			if($authGroupID == 3){
				$principleID = $authUserID;
			}
			else{
				$principleID = $authParentID;
			}

		//	admin perusahaan / principle bisa ngeliat semua invoice 1 perusahaan
			if($principleID){
				$siblings = $this->User->getData('all', array(
					'conditions' => array(
						'User.parent_id' => $principleID, 
					)
				), array('status' => 'active'));

				$siblingID = Hash::extract($siblings, '{n}.User.id');
				$siblingID = array_unique(array_filter($siblingID));
				$arrUserID = array_merge($arrUserID, $siblingID);
			}

			$conditions = array_merge_recursive($conditions, array(
				'conditions' => array(
					'Payment.user_id' => $arrUserID, 
				)
			));
		}

		$record	= $this->Payment->getData('first', $conditions);
	
		if($isCompanyAdmin){
		//	check company config, jika belum ada redirect ke halaman membership
			$principleID	= $this->RmCommon->filterEmptyField($record, 'Payment', 'principle_id');
			$companyConfig	= $this->User->UserCompanyConfig->getData('first', array(
				'conditions' => array(
					'UserCompanyConfig.user_id' => $principleID, 
				)
			));

			if(empty($companyConfig)){
				$this->RmCommon->setCustomFlash(__('Perusahaan Anda belum dikonfigurasi, silakan hubungi Administrator untuk melakukan konfigurasi.'), 'error');
			//	$this->redirect(Configure::read('__Site.membership_request_url'));
			}

			$record = array_merge($record, $companyConfig);
		}

		if($record){
			$namedParams	= $this->params->named;
			$export			= $this->RmCommon->filterEmptyField($namedParams, 'export');

			$this->set(array(
				'module_title'		=> __('Detail Invoice'),
				'title_for_layout'	=> __('Detail Invoice'),
				'record'			=> $record, 
			));

			if($export == 'excel'){
				$this->layout = FALSE;
				$this->render('admin_view_excel');
			}
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function checkout($recordID = null, $invoiceNumber = null, $token = null){
		$this->loadModel('Voucher');

		$record		= null;
		$valid		= false;
		$redirect	= array(
			'controller'	=> 'memberships', 
			'action'		=> 'index',
			'admin'			=> false,
		);

		if($recordID && $invoiceNumber && $token){
			$record = $this->Payment->getData('first', array(
				'conditions' => array(
					'Payment.id'				=> $recordID,
					'Payment.invoice_number'	=> $invoiceNumber,
					'Payment.payment_status'	=> array('pending', 'process', 'failed'),
				), 
			));

			if($record){
				$userID	= $this->RmCommon->filterEmptyField($record, 'Payment', 'user_id');
				$valid	= md5($invoiceNumber . $recordID . $userID) === $token;
			}
		}

		if($valid){
		//	check expiry
			$expiredDate = $this->RmCommon->filterEmptyField($record, 'Payment', 'expired_date');

			if(strtotime($expiredDate) < strtotime(date('Y-m-d H:i:s'))){
				$message = __('Maaf, Anda tidak bisa melanjutkan transaksi karena Invoice <strong>%s</strong> sudah Kadaluarsa.', $invoiceNumber);

				$this->RmCommon->setCustomFlash($message, 'error');
				$this->redirect($redirect);
			}

		//	process
			$refererURL = sprintf('%s/%s', FULL_BASE_URL, $this->params->prefix);

			$record	= $this->Payment->setPaymentStatus($record, 'process', $refererURL);
			$record = $this->User->getMergeList($record, array(
				'contain' => array(
					'UserProfile', 
					'UserCompany', 
					'UserCompanyConfig', 
				), 
			));

			$postData = null;

			if($this->request->data){
				$this->Payment->validator()->add('agreement', 'required', array(
				    'rule' => 'notempty',
					'message' => 'Mohon centang kebijakan kami',
				));

				$userID	= $this->RmCommon->filterEmptyField($record, 'User', 'id');
				$data	= array_merge_recursive($this->request->data, array(
					'Payment' => array(
						'id'			=> $recordID, 
						'user_id'		=> $userID, 
						'mall_id'		=> $this->mallID, 
						'shared_key'	=> $this->sharedKey, 
					), 
				));

				$result = $this->Payment->doCheckout($data);
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if($status == 'success'){
					$record			= $this->RmCommon->filterEmptyField($result, 'data');
					$paymentStatus	= $this->RmCommon->filterEmptyField($record, 'Payment', 'payment_status');

					if($paymentStatus == 'paid'){
						$principleID	= $this->RmCommon->filterEmptyField($record, 'Payment', 'principle_id');
						$voucher_id	    = $this->RmCommon->filterEmptyField($record, 'Payment', 'voucher_code_id');
						$record			= $this->User->UserProfile->getMerge($record, $principleID);
						$record			= $this->User->UserCompany->getMerge($record, $principleID);
						$record			= $this->User->UserCompanyConfig->getMerge($record, $principleID);
						$record			= $this->Voucher->VoucherCode->getMerge($record, $voucher_id);

						// debug($record);exit;

					//	kirim email ke user makasih udah membayar ===============================================

						$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
						$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
						$principleEmail	= $this->RmCommon->filterEmptyField($record, 'Principle', 'email');
						$subject		= 'Informasi pembayaran transaksi';
						$template		= 'paid_invoice_notification';

						$financeEmail	= Configure::read('Global.Data.finance_email');
						$senderEmail	= Configure::read('__Site.send_email_from');
						$bcc			=  array(
							$email,
							$financeEmail, 
							$senderEmail,
						//	ga usah dinaikin
							'financeprimesystem@yopmail.com',
							Configure::read('__Site.prime_leads_email'),
						);

						if( !empty($principleEmail) ) {
							$bcc[] = $principleEmail;
						}

						$params = array_merge($record, array(
							'from'	=> $senderEmail,
							'bcc'	=> $bcc,
							// 'debug'	=> 'view', 
						));

						$emailSent = $this->RmCommon->sendEmail($fullName, $bcc, $template, __($subject), $params);

					//	=========================================================================================

					//	redirect to thanks page
						$message = __('Berhasil menyimpan data Invoice %s', $invoiceNumber);
						$message = $this->RmCommon->filterEmptyField($result, 'msg', null, $message);

					//	debug($recordID);
					//	debug($invoiceNumber);exit;

						$this->RmCommon->setCustomFlash($message, 'success');
						$this->redirect(array(
							'admin'		=> false, 
							'action'	=> 'complete', 
							$recordID, 
							$invoiceNumber, 
						));
					}
					else{
						$postData = $this->RmCommon->filterEmptyField($result, 'post_data');
					}
				} else {
					$record['Payment']['agreement'] = Common::hashEmptyField($this->request->data, 'Payment.agreement');
				}
			}

		//	$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));
			$this->request->data = $record;

			$themeID	= $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'theme_id');
			$record		= $this->User->UserCompanyConfig->Theme->getMerge($record, $themeID);
			$result		= array('data' => $record);

			$this->RmCommon->setProcessParams($result);

			$this->set(array(
				'record'			=> $record, 
				'collapseHeader'	=> true, 
				'bodyClass'			=> 'cart checkout', 
				'bodyClass'			=> 'cart', 
				'bubleType' 		=> 'payment',
				'bodyClass'			=> 'no-scroll',
			));

			if(empty($postData)){
				$this->render('/Elements/blocks/memberships/forms/frontend_checkout');
			}
			else{
			//	numpang punya admin
				$this->admin_post_payment($recordID, $invoiceNumber, $postData);
				$this->render('/Elements/blocks/memberships/forms/frontend_doku');
			}
		}
		else{
			$this->RmCommon->setCustomFlash(__('Invoice tidak ditemukan atau sudah pernah diproses sebelumnya.'), 'error');
			$this->redirect($redirect);
		}
	}

	public function complete($recordID = NULL, $invoiceNumber = NULL){
		$redirect = array(
			'controller'	=> 'memberships',
			'action'		=> 'index',
			'admin'			=> false,
		);

		$record = $this->Payment->getData('first', array(
			'conditions' => array(
				'Payment.id'				=> $recordID,
				'Payment.invoice_number'	=> $invoiceNumber,
				'Payment.payment_status'	=> array('paid', 'failed', 'waiting'),
			), 
		));

		if($record){
			// $this->autoLayout = false;
			// $this->autoRender = true;

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

	public function admin_checkout($recordID = NULL, $invoiceNumber = NULL){
		$authUserID		= $this->Auth->user('id');
		$authGroupID	= $this->Auth->user('group_id');
		$authParentID	= $this->Auth->user('parent_id');
		$isCompanyAdmin	= $this->RmCommon->_isCompanyAdmin();
		$options		= array(
			'conditions' => array(
				'Payment.id'				=> $recordID,
				'Payment.invoice_number'	=> $invoiceNumber,
				'Payment.payment_status'	=> array('pending', 'process', 'failed'),
			), 
		);

		$arrUserID = array($authUserID);

		if($isCompanyAdmin && $authGroupID != 4){
		//	admin perusahaan / principle bisa ngeliat semua invoice 1 perusahaan
			$principleID = $authGroupID == 3 ? $authUserID : $authParentID;
			$arrUserID[] = $principleID;

			if($principleID){
				$siblings = $this->User->getData('all', array(
					'conditions' => array(
						'User.parent_id' => $principleID, 
					)
				), array('status' => 'active'));

				$siblingID = Hash::extract($siblings, '{n}.User.id');
				$siblingID = array_unique(array_filter($siblingID));
				$arrUserID = array_merge($arrUserID, $siblingID);
			}
		}

		$arrUserID	= array_unique($arrUserID);
		$options	= array_merge_recursive($options, array(
			'conditions' => array(
				'OR' => array(
					'Payment.user_id'		=> $arrUserID, 
				//	'Payment.principle_id'	=> $arrUserID, 
				), 
			)
		));

		$record = $this->Payment->getData('first', $options);

		if($record){
			$userID			= $this->RmCommon->filterEmptyField($record, 'Payment', 'user_id');
			$principleID	= $this->RmCommon->filterEmptyField($record, 'Payment', 'principle_id');
			$expiredDate	= $this->RmCommon->filterEmptyField($record, 'Payment', 'expired_date');

			if(strtotime($expiredDate) < strtotime(date('Y-m-d H:i:s'))){
				$message = __('Maaf, Anda tidak bisa melanjutkan transaksi karena Invoice <strong>%s</strong> sudah Kadaluarsa.', $invoiceNumber);
				$this->RmCommon->setCustomFlash($message, 'error');
				$this->redirect(array('action' => 'index', 'admin' => TRUE));
			}

		//	update status jadi process
			$refererURL = sprintf('%s/%s', FULL_BASE_URL, $this->params->prefix);

			$record	= $this->Payment->setPaymentStatus($record, 'process', $refererURL);
			$data	= $this->request->data;
			$isAjax	= $this->RequestHandler->isAjax();
			$result	= array('data' => $record);

			if($data){
				$data['Payment']['id']			= $recordID;
				$data['Payment']['user_id']		= $userID;
				$data['Payment']['mall_id']		= $this->mallID;
				$data['Payment']['shared_key']	= $this->sharedKey;

				$result = $this->Payment->doCheckout($data);
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if($status == 'success'){
					$record			= $this->RmCommon->filterEmptyField($result, 'data');
					$paymentStatus	= $this->RmCommon->filterEmptyField($record, 'Payment', 'payment_status');

					if($paymentStatus == 'paid'){
						$record	= $this->User->UserProfile->getMerge($record, $principleID);
						$record	= $this->User->UserCompany->getMerge($record, $principleID);
						$record	= $this->User->UserCompanyConfig->getMerge($record, $principleID);

					//	kirim email ke user makasih udah membayar ===============================================

						$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
						$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
						$principleEmail	= $this->RmCommon->filterEmptyField($record, 'Principle', 'email');
						$subject		= 'Informasi pembayaran transaksi';
						$template		= 'paid_invoice_notification';

						$financeEmail	= Configure::read('Global.Data.finance_email');
						$senderEmail	= Configure::read('__Site.send_email_from');
						$bcc =  array(
							$financeEmail, 
						//	ga usah dinaikin
							'finance@primesystem.id', 
							'rikarumahku@gmail.com', 
							'andriani@rumahku.com', 
							Configure::read('__Site.prime_leads_email'),
						);

						if( !empty($principleEmail) ) {
							$bcc[] = $principleEmail;
						}
						
						$params			= array_merge($record, array(
							'from'	=> $senderEmail, 
							'bcc'	=> $bcc, 
						//	'debug'	=> 'view', 
						));

						$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

					//	=========================================================================================

						$message = $this->RmCommon->filterEmptyField($result, 'msg', null, __('Berhasil menyimpan data Invoice %s', $invoiceNumber));

						$this->RmCommon->setCustomFlash($message, 'success');
						$this->redirect(array('action' => 'index', 'admin' => TRUE));
					}
					else{
						$postData = $this->RmCommon->filterEmptyField($result, 'post_data');
						$this->admin_post_payment($recordID, $invoiceNumber, $postData);	
					}
				}
			}

		//	$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));
			$this->request->data = $record;

			$this->set(array(
				'module_title'		=> __('Checkout Invoice'),
				'title_for_layout'	=> __('Checkout Invoice'),
				'record'			=> $record,
				'mallID'			=> $this->mallID,
				'sharedKey'			=> $this->sharedKey,
				'record'			=> $record,
			));
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function admin_post_payment($recordID, $invoiceNumber, $postData = NULL){
		if($postData){
			$paymentChannel = $this->RmCommon->filterEmptyField($postData, 'PAYMENTCHANNEL');

			if($paymentChannel == '03'){
			//	bca beda cara post + url nya
				$dokuMIPURL	= Configure::read('__Site.doku_payment_mip_url');
				$curl		= curl_init();

				curl_setopt($curl, CURLOPT_URL, $dokuMIPURL);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_HEADER, FALSE);
				curl_setopt($curl, CURLOPT_SSLVERSION, 3);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));

				$xmlResult = curl_exec($curl);
				curl_close($curl);

				$this->set(compact('xmlResult'));
			}

			$this->set(compact('postData'));
			$this->render('doku_form');
		}
		else{
			$this->RmCommon->setCustomFlash(__('Gagal melakukan proses pembayaran.'), 'error');
			$this->redirect(array('action' => 'checkout', 'admin' => TRUE, $recordID, $invoiceNumber));
		}
	}

	public function admin_identify(){
	//	persiapan jika suatu saat dipake
		$this->layout		= FALSE;
		$this->autoRender	= FALSE;

		return TRUE;
	}

///////////////////////////////////////////////////////////////////////////////////////
/*
	name		: notify
	desc		: mandatory function that fired when transaction process is finished (payment status)
	direction	: doku -> rumahku
*/
///////////////////////////////////////////////////////////////////////////////////////

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
						$record = $this->Payment->getData('first', array(
							'recursive'		=> 1,
							'conditions'	=> array(
								'Payment.invoice_number'	=> $invoiceNumber,
								'Payment.payment_status'	=> array('process', 'failed', 'waiting')
							)
						));

						if($record){
							if($responseMessage == 'SUCCESS'){
								$invoiceData = array(
									'Payment' => array(
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
									'Payment' => array(
										'payment_status'	=> 'failed',
										'response_code'		=> $responseCode,
										'response_message'	=> $responseMessage
									)
								);
							}

							$this->Payment->id = $this->RmCommon->filterEmptyField($record, 'Payment', 'id');

							if($this->Payment->save($invoiceData)){
								if($responseMessage == 'SUCCESS'){
								//	update user company config, set paket aktif + tanggal tayang
									$result	= $this->Payment->processPaidInvoice($record);
									$status	= $this->RmCommon->filterEmptyField($result, 'status');

									if($status == 'success'){
										echo(__('CONTINUE'));
									}
									else{
										echo(__('FAILED'));
									}

								/*	backup jangan diapus dulu
									$userID			= $this->RmCommon->filterEmptyField($record, 'Payment', 'user_id');
									$companyConfig	= $this->User->UserCompanyConfig->getData('first', array(
										'conditions' => array(
											'UserCompanyConfig.user_id' => $userID
										)
									));

									if($companyConfig){
										$packageID		= $this->RmCommon->filterEmptyField($record, 'MembershipPackage', 'id');
										$monthDuration	= $this->RmCommon->filterEmptyField($record, 'MembershipPackage', 'month_duration', 0);

										$configID		= $this->RmCommon->filterEmptyField($companyConfig, 'UserCompanyConfig', 'id');
										$oldPackageID	= $this->RmCommon->filterEmptyField($companyConfig, 'UserCompanyConfig', 'membership_package_id');
										$liveDate		= $this->RmCommon->filterEmptyField($companyConfig, 'UserCompanyConfig', 'live_date');
										$endDate		= $this->RmCommon->filterEmptyField($companyConfig, 'UserCompanyConfig', 'end_date');

										if($oldPackageID != $packageID){
										//	kalo paket beda dengan sebelumnya, paket lama angus
											$newLiveDate	= date('Y-m-d');
											$newEndDate		= date('Y-m-d', strtotime(sprintf('%s +%s month', $newLiveDate, $monthDuration)));
										}
										else{
										//	kalo sama paket lama di extend
											$newLiveDate	= $liveDate;
											$newEndDate		= date('Y-m-d', strtotime(sprintf('%s +%s month', $endDate, $monthDuration)));
										}

										$configData = array(
											'membership_package_id'	=> $packageID,
											'live_date'				=> $newLiveDate,
											'end_date'				=> $newEndDate
										);

										$this->User->UserCompanyConfig->id = $configID;

										if($this->User->UserCompanyConfig->save($configData)){
											echo(__('CONTINUE'));
										}
										else{
											echo(__('FAILED'));
										}
									}
									else{
										echo(__('FAILED'));
									}
								*/
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

			// START INIT =========================================================================
			// buat ngetes aja, manual nembak ke url
			// example : devel.primeagent.com/admin/payments/finalize/metode:transfer/invoice:CW-INV201809280003/payment_code:8965000000001742
			$invoice_numb = Common::hashEmptyField($this->params->params, 'named.invoice');
			$paymeny_code = Common::hashEmptyField($this->params->params, 'named.payment_code');
			$metode       = Common::hashEmptyField($this->params->params, 'named.metode');

			$response_code = false;
			if ($invoice_numb) {
				$isPost = true;
				if (!empty($metode) && $metode == 'doku-wallet') {
					$response_code = '0000';
				} else {
					$response_code = '5511';
				}

			}
			// END INIT =========================================================================

			$invoiceNumber	= $this->RmCommon->filterEmptyField($data, 'TRANSIDMERCHANT', false, $invoice_numb);
			$record			= $this->Payment->getData('first', array(
				'conditions' => array(
					'Payment.invoice_number' => $invoiceNumber, 
				),
			));

			$record = $this->Payment->getMergeList($record, array(
				'contain' => array(
					'User' => array(
						'contain' => array(
							'Principle' => array(
								'uses'       => 'User',
								'primaryKey' => 'id',
								'foreignKey' => 'parent_id',
							),
						),
					),
				),
			));
			// debug($record);die();
			if($record){
				$isAdmin	= Configure::read('User.Admin');
				$recordID	= $this->RmCommon->filterEmptyField($record, 'Payment', 'id');
				$refererURL	= $this->RmCommon->filterEmptyField($record, 'Payment', 'referer_url', FULL_BASE_URL);
				$payment_channel = $this->RmCommon->filterEmptyField($record, 'Payment', 'payment_channel');

				$defaultMsg	= 'Pembayaran untuk Invoice';

			//	redirect ===========================================================================================

				$redirectURL = $refererURL;
				$redirectURL.= substr($redirectURL, -1) == '/' ? null : '/';

				if(strpos($refererURL, '/admin') !== false){
					$redirectURL.= sprintf('payments/?ref=%s', $invoiceNumber);
				}
				else{
					$redirectURL.= sprintf('payments/complete/%s/%s', $recordID, $invoiceNumber);
				}

			//	====================================================================================================

				if($isPost){
				//	HIT BY DOKU
					$paymentCode	= $this->RmCommon->filterEmptyField($data, 'PAYMENTCODE', false, $paymeny_code);
					$responseCode	= $this->RmCommon->filterEmptyField($data, 'STATUSCODE', false, $response_code);
					$paymentChannel	= $this->RmCommon->filterEmptyField($data, 'PAYMENTCHANNEL', false, $payment_channel);

					$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
					$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
					$principleEmail	= $this->RmCommon->filterEmptyField($record, 'Principle', 'email');

					// init global data email info, goes here
					$financeEmail	 = Configure::read('Global.Data.finance_email');
					$financeEmailDev = Configure::read('Global.Data.finance_email_dev');
					$supportPrime	 = Configure::read('__Site.send_email_from');

					$bcc =  array(
						$financeEmail,
					//  email dev (yopmail)
						$financeEmailDev,
						$supportPrime,
					//	ga usah dinaikin
						'finance@primesystem.id', 
						'rikarumahku@gmail.com', 
						'afuuzarumahku@gmail.com', 
						Configure::read('__Site.prime_leads_email'),
					);

					if($responseCode == '0000'){
					//	kirim email ke user makasih udah membayar ===============================================
						$subject		= 'Informasi pembayaran transaksi';
						$template		= 'paid_invoice_notification';

						if( !empty($principleEmail) ) {
							$bcc[] = $principleEmail;
						}

						$params			= array_merge($record, array(
							'from'	=> $supportPrime, 
							'bcc'	=> $bcc, 
						//	'debug'	=> 'view', 
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
						$this->Payment->save(array(
							'Payment' => array(
								'id'				=> $recordID, 
								'payment_code'		=> $paymentCode, 
								'payment_channel'	=> $paymentChannel, 
							), 
						));

					//	change invoice status to waiting
						$read_record = $this->Payment->read(null, $recordID);
						$read_record = $this->Payment->setPaymentStatus($read_record, 'waiting');

						$expDatetime = $this->RmCommon->filterEmptyField($read_record, 'Payment', 'transfer_expired_date');

					//	kirim email ke user untuk segera membayar ===============================================
						$subject	= 'Informasi pembayaran transaksi';
						$template	= 'transfer_invoice_notification';

						$params	    = array_merge($record, array(
							'with_greet'      => false,
							'new_read_record' => $read_record,
							'bcc'   => $bcc, 
							'from'  => $supportPrime, 
							// 'debug' => 'view', 
						));

						$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

					//	=========================================================================================

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
					$responseCode = $this->RmCommon->filterEmptyField($record, 'Payment', 'response_code');

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

	public function test(){
	//	default redirect url
		$redirectURL = Router::url(array(
			'action'	=> 'index', 
			'admin'		=> true, 
		));

		// Configure::write('debug', 2);
		// $isPost	= $this->request->is('post');
		// $data	= $isPost ? $this->request->data : $this->params->query;

		// $data = array(
		// 	'TRANSIDMERCHANT' => 'CW-INV201612270001',
		// 	'PAYMENTCODE' => '',
		// 	'STATUSCODE' => '0000',
		// 	'PAYMENTCHANNEL' => '15',
		// );

		// parameter for testing force paid
		$debug        = Common::hashEmptyField($this->params->params, 'named.debug', false);
		$metode       = Common::hashEmptyField($this->params->params, 'named.metode');
		$invoice_numb = Common::hashEmptyField($this->params->params, 'named.invoice');

		if (!empty($invoice_numb) && !empty($metode)) {

			// $invoiceNumber = $this->RmCommon->filterEmptyField($data, 'TRANSIDMERCHANT', false, 'CW-INV201612270001');
			$record = $this->Payment->getData('first', array(
				'conditions' => array(
					'Payment.invoice_number' => $invoice_numb, 
				),
			));

			$record = $this->Payment->getMergeList($record, array(
				'contain' => array(
					'User' => array(
						'contain' => array(
							'Principle' => array(
								'uses'       => 'User',
								'primaryKey' => 'id',
								'foreignKey' => 'parent_id',
							),
						),
					),
				),
			));

			if($record){
				$isAdmin	= Configure::read('User.Admin');

				$recordID       = Common::hashEmptyField($record, 'Payment.id');
				$refererURL     = Common::hashEmptyField($record, 'Payment.referer_url', FULL_BASE_URL);
				$paymentCode    = Common::hashEmptyField($record, 'Payment.payment_code');
				$paymentChannel = Common::hashEmptyField($record, 'Payment.payment_channel');

				$fullName       = Common::hashEmptyField($record, 'User.full_name');
				$email          = Common::hashEmptyField($record, 'User.email');

				$defaultMsg	= 'Pembayaran untuk Invoice';

			//	redirect ===========================================================================================

				$redirectURL = $refererURL;
				$redirectURL.= substr($redirectURL, -1) == '/' ? null : '/';

				if(strpos($refererURL, '/admin') !== false){
					$redirectURL.= sprintf('payments/?ref=%s', $invoice_numb);
				}
				else{
					$redirectURL.= sprintf('payments/complete/%s/%s', $recordID, $invoice_numb);
				}

				if ($metode == 'doku-wallet') {
					$responseCode	= '0000';
				} elseif ($metode == 'transfer') {
					$responseCode	= '5511';
				}

			//	====================================================================================================

			//	HIT BY DOKU
				// $paymentCode	= $this->RmCommon->filterEmptyField($data, 'PAYMENTCODE');
				// $responseCode	= $this->RmCommon->filterEmptyField($data, 'STATUSCODE');
				// $paymentChannel	= $this->RmCommon->filterEmptyField($data, 'PAYMENTCHANNEL');

				if($responseCode == '0000'){
				//	kirim email ke user makasih udah membayar ===============================================
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
					$this->Payment->save(array(
						'Payment' => array(
							'id'				=> $recordID, 
							'payment_code'		=> $paymentCode, 
							'payment_channel'	=> $paymentChannel, 
						), 
					));

				//	change invoice status to waiting
					$read_record = $this->Payment->read(null, $recordID);
					$read_record = $this->Payment->setPaymentStatus($read_record, 'waiting');

				//	kirim email ke user untuk segera membayar ===============================================
					$subject	= 'Informasi pembayaran transaksi';
					$template	= 'transfer_invoice_notification';

					$financeEmail	= Configure::read('Global.Data.finance_email');
					$senderEmail	= Configure::read('__Site.send_email_from');
					$params			= array_merge($record, array(
						// 'bcc'			=> array(
						// 	$financeEmail, 
						// ), 
						'from'			=> $senderEmail, 
						'with_greet'	=> false, 
						'payment_code'  => $paymentCode,
						'debug'			=> 'view', 
					));

					$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

				//	=========================================================================================

					$expDatetime	= $this->RmCommon->filterEmptyField($read_record, 'Payment', 'transfer_expired_date');
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
						'message'	=> __($message, $invoice_numb, $paymentPlace, $expDatetime), 
					);
				}
				else{
					$errorCode	= $responseCode ? sprintf('(Error Code : %s)', $responseCode) : '';
					$result		= array(
						'status'	=> 'error',
						'message'	=> __('%s : %s gagal. %s', $defaultMsg, $invoice_numb, $errorCode), 
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
		} else {
			$this->layout = false;
			$msg_empty = __('Pilih metodenya dulu, dan invoice yang ingin dipaksa paid.');
			$this->set('msg_empty', $msg_empty);
			$this->render('/Elements/empty_data');
		}
	}

	public function admin_cancel(){
		$data	= $this->request->data;
		$id		= $this->RmCommon->filterEmptyField($data, 'Payment', 'id');
    	$result = $this->Payment->doToggle($id);
    	$status	= $this->RmCommon->filterEmptyField($result, 'status', null, 'error');

    	if($status == 'success'){
			$records = $this->RmCommon->filterEmptyField($result, 'data');

			if($records){
				foreach($records as $record){
					$fullName	= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
					$email		= $this->RmCommon->filterEmptyField($record, 'User', 'email');
					$subject	= 'Informasi pembatalan transaksi';
					$template	= 'membership_process';

				//	di model ga diread ulang payment_status-nya (masih payment_status yang lama)
				//	di alter disini aja supaya ga usah read database lagi, karna udah jelas statusnya "cancelled"
					$record = array_replace_recursive($record, array(
					//	'debug'		=> 'view', 
						'Payment'	=> array(
							'payment_status' => 'cancelled', 
						), 
					));

					$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $record);
    			}
			}
		}

		$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
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

	public function admin_send_invitation($recordID = null, $invoiceNumber = null){
		$siteName	= Configure::read('__Site.site_name');
		$options	= array(
			'conditions' => array(
				'Payment.id'				=> $recordID,
				'Payment.invoice_number'	=> $invoiceNumber,
			), 
		);

		$record = $this->Payment->getData('first', $options);

		if($record){
			$isPrinciple = $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'is_principle');

			if($isPrinciple){
				$principleID = $this->RmCommon->filterEmptyField($record, 'User', 'id');
			}
			else{
				$principleID = $this->RmCommon->filterEmptyField($record, 'Principle', 'id');
			}

			$record	= $this->User->UserProfile->getMerge($record, $principleID); 
			$record	= $this->User->UserConfig->getMerge($record, $principleID);
			$record	= $this->User->UserCompany->getMerge($record, $principleID);
			$record	= $this->User->UserCompanyConfig->getMerge($record, $principleID);

		//	kirim email ke pemesan ======================================================================

			$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
			$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');

			$subject		= __('Undangan %s', $siteName);
			$template		= 'user_invitation';
			$senderEmail	= Configure::read('__Site.send_email_from');
			$params			= array_merge($record, array(
				'from'	=> $senderEmail, 
			//	'debug'	=> 'view', 
			));

			$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, $subject, $params);

		//	=============================================================================================

			if($emailSent){
				$result = array(
					'status'	=> 'success', 
					'msg'		=> __('Berhasil mengirimkan email undangan untuk No. Invoice %s.', $invoiceNumber), 
				);

				if(empty($isPrinciple)){

				//	kirim email ke pemesan ======================================================================

					$principleName	= $this->RmCommon->filterEmptyField($record, 'Principle', 'full_name');
					$principleEmail	= $this->RmCommon->filterEmptyField($record, 'Principle', 'email');

				//	$params = array_merge($record, array(
				//		'from'	=> $senderEmail, 
				//		'debug'	=> 'view', 
				//	));

					$emailSent	= $this->RmCommon->sendEmail($principleName, $principleEmail, $template, $subject, $params);
					$result		= array(
						'status'	=> $emailSent ? 'success' : 'error', 
						'msg'		=> __('%s mengirimkan email undangan untuk No. Invoice %s.', $emailSent ? 'Berhasil' : 'Gagal', $invoiceNumber), 
					);

				//	=============================================================================================

				}
			}
			else{
				$result = array(
					'status'	=> 'error', 
					'msg'		=> __('Gagal mengirimkan email undangan.'), 
				);
			}
		}
		else{
			$result = array(
				'status'	=> 'error', 
				'msg'		=> __('Data tidak ditemukan.'), 
			);
		}

		$this->RmCommon->setCustomFlash($result['msg'], $result['status']);
		$this->redirect(array(
			'controller'	=> $this->params->controller, 
			'action'		=> 'index', 
			'admin'			=> true, 
		));
	}
}
