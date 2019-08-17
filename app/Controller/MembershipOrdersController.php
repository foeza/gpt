<?php
App::uses('AppController', 'Controller');
class MembershipOrdersController extends AppController {
	public $uses = array('MembershipOrder', 'MembershipPackage');

	public function beforeFilter(){
		parent::beforeFilter();
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
		$options = array(
			'recursive'		=> 1, 
			'conditions'	=> array(
				'MembershipOrder.is_deleted' => 0
			), 
			'order'			=> array(
				'MembershipOrder.created' => 'DESC'
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

			$options = array_merge_recursive($options, array(
				'conditions' => array(
					'MembershipOrder.user_id' => $arrUserID, 
				)
			));
		}

		$options = $this->MembershipOrder->_callRefineParams($this->params, $options);

		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate	= $this->MembershipOrder->getData('paginate', $options);
		$records		= $this->paginate('MembershipOrder');
		$packages = $this->MembershipPackage->getData('list', array(
			'conditions' => array(
				// 'MembershipPackage.status' => 1, 
				'MembershipPackage.is_deleted' => 0,
			),
		));

		$this->set(array(
			'module_title'		=> __('Kontak PRIME'), 
			'title_for_layout'	=> __('Kontak PRIME'),
			'active_menu'	=> 'contact_list',
			'packages'	=> $packages,
		));

		$this->set(compact('records'));
	}

	public function admin_add($principleID = NULL){
		$isAdmin		= $this->RmCommon->_isAdmin();
		$isCompanyAdmin	= $this->RmCommon->_isCompanyAdmin();
		$authUserID		= $this->Auth->user('id');
		$authGroupID	= $this->Auth->user('group_id');
		$authParentID	= $this->Auth->user('parent_id');
		$record			= NULL;
		$redirect		= array(
			'action'	=> 'index', 
			'admin'		=> true, 
		);

		$isPrinciple = false;

		// sudah menggunakan acl tidak usah cek manual setting di aclnya saja 
		// if($isAdmin || ($isCompanyAdmin)){
		//	setting global dari appController
			$companyPrincipleID	= $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'user_id');
			$isPrinciple		= ($authGroupID == 3) || $isAdmin;

			if(empty($principleID)){
				if($isAdmin){
					$principleID = Configure::read('Principle.id');
				}
				else{
					$principleID = ($authGroupID == 3) ? $authUserID : $authParentID;
				}
			}

			if($principleID == $companyPrincipleID){
				$record = $this->data_company;
			}
			else if($isAdmin && $principleID){
				$principle = $this->User->getData('first', array(
					'conditions' => array(
						'User.id'		=> $principleID, 
						'User.group_id'	=> 3, 
					), 
				), array(
					'status' => 'all', 
				));

				if($principle){
					$record = $this->User->getMergeList($principle, array(
						'contain' => array(
							'UserProfile',
							'UserCompany', 
							'UserCompanyConfig', 
						), 
					));

					$configID = $this->RmCommon->filterEmptyField($record, 'UserCompanyConfig', 'id');

					if(empty($configID)){
						$principalName	= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
						$principalEmail	= $this->RmCommon->filterEmptyField($record, 'User', 'email');

						$message = 'Konfigurasi website principal <strong>%s</strong> ( <strong>%s</strong> ) tidak ditemukan.';

						$this->RmCommon->setCustomFlash(__($message, $principalName, $principalEmail), 'error');
						$this->redirect($redirect);
					}
				}
				else{
					$this->RmCommon->setCustomFlash(__('Data Principal tidak ditemukan.'), 'error');
					$this->redirect($redirect);
				}
			}
		// }
		// else{
		// 	$this->RmCommon->setCustomFlash(__('Anda tidak memiliki hak untuk mengakses halaman tersebut.'), 'error');
		// 	$this->redirect($redirect);
		// }

		if($record){
		//	get active package
			$packageID	= $this->RmCommon->filterEmptyField($record, 'UserCompanyConfig', 'membership_package_id');
			$package	= $this->MembershipPackage->getData('first', array(
				'conditions' => array(
					'MembershipPackage.id' => $packageID, 
				), 
			));

		//	merge all data
			$record	= array_merge($record, $package);
			$result	= array('data' => $record);

			if($this->request->data){
				$data			= $this->request->data;
				$userID			= $this->RmCommon->filterEmptyField($record, 'User', 'id');
				$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
				$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
				$profileID		= $this->RmCommon->filterEmptyField($record, 'UserProfile', 'id');
				$phone			= $this->RmCommon->filterEmptyField($record, 'UserProfile', 'phone');
				$phone			= $this->RmCommon->filterEmptyField($record, 'UserProfile', 'no_hp', $phone);
				$companyID		= $this->RmCommon->filterEmptyField($record, 'UserCompany', 'id');
				$companyName	= $this->RmCommon->filterEmptyField($record, 'UserCompany', 'name');

				$packageID	= $this->RmCommon->filterEmptyField($data, 'MembershipOrder', 'membership_package_id');
				$status		= 'renewal'; 

				$referer	= sprintf('%s/%s', FULL_BASE_URL, $this->params->prefix);
				$data		= array(
					'MembershipOrder' => array(
						'membership_package_id'	=> $packageID, 
						'company_name'			=> $companyName, 
						'status'				=> $status, 
						'is_principle'			=> $isPrinciple, 
					), 
					'Payment' => array(
						'referer_url' => $referer, 
					), 
				);

				if($isPrinciple){
					$data = array_merge_recursive($data, array(
						'MembershipOrder' => array(
							'user_id'		=> $userID, 
							'principle_id'	=> $userID, 
							'name'			=> $fullName, 
							'email'			=> $email, 
							'phone'			=> $phone, 

						//	additional
							'profile_id'	=> $profileID, 
							'company_id'	=> $companyID, 
						), 
					));
				}
				else{
				//	yang pesen bukan principal langsung (diwakilin jadi harus isi data pemesan / yang login)
					$authData		= Configure::read('User.data');
					$authFullName	= $this->RmCommon->filterEmptyField($authData, 'full_name');
					$authEmail		= $this->RmCommon->filterEmptyField($authData, 'email');
					$authProfileID	= $this->RmCommon->filterEmptyField($authData, 'UserProfile', 'id');
					$authPhone		= $this->RmCommon->filterEmptyField($authData, 'UserProfile', 'phone', $phone);
					$authHP1		= $this->RmCommon->filterEmptyField($authData, 'UserProfile', 'no_hp');
					$authHP2		= $this->RmCommon->filterEmptyField($authData, 'UserProfile', 'no_hp_2');
					$authCompanyID	= $this->RmCommon->filterEmptyField($authData, 'UserCompany', 'id');

					$authPhone = empty($authPhone) ? $authHP1 : $authPhone;
					$authPhone = empty($authPhone) ? $authHP2 : $authPhone;

					$data = array_merge_recursive($data, array(
						'MembershipOrder' => array(
							'user_id'			=> $authUserID, 
							'principle_id'		=> $userID, 
							'name'				=> $authFullName, 
							'email'				=> $authEmail, 
							'phone'				=> $authPhone, 
							'principle_name'	=> $fullName, 
							'principle_email'	=> $email, 
							'principle_phone'	=> $phone, 

						//	additional
							'profile_id'			=> $authProfileID, 
							'company_id'			=> $authCompanyID, 
							'principle_profile_id'	=> $profileID, 
							'principle_company_id'	=> $companyID, 
						), 
					));
				}

			//	debug($data);exit;

				$packageData = array(
					'MembershipPackage' => array(
						'id' => $packageID, 
					), 
				);

				$result			= $this->MembershipOrder->doSave($data, $packageData);
				$paymentData	= $this->RmCommon->filterEmptyField($result, 'data', 'Payment');

				if($paymentData){
					$invoiceID		= $this->RmCommon->filterEmptyField($paymentData, 'id');
					$invoiceNumber	= $this->RmCommon->filterEmptyField($paymentData, 'invoice_number');
					
					$url_payment = array(
						'controller'	=> 'payments', 
						'action'		=> 'view', 
						'admin'			=> true, 
						$invoiceID, 
						$invoiceNumber, 
					);
					$url_membership = array(
						'controller'	=> 'membership_orders', 
						'action'		=> 'view', 
						'admin'			=> true, 
						$invoiceID,
					);

					$check = $this->RmCommon->_callCheckAcl($url_payment, false);

					if($check){
						$redirect = $url_payment;
					} else {
						$redirect = $url_membership;
					}
				}
			}

			$this->RmCommon->setProcessParams($result, $redirect);

			$packages = $this->MembershipPackage->getData('list', array(
				'conditions' => array(
					'MembershipPackage.status'		=> 1, 
					'MembershipPackage.is_deleted'	=> 0, 
				), 
			));

			$this->set(array(
				'module_title'		=> __('Membership Renewal'), 
				'title_for_layout'	=> __('Membership Renewal'), 
				'active_menu'		=> 'renewal', 
				'record'			=> $record, 
				'packages'			=> $packages, 
			));

			$this->render('admin_form');
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect($redirect);
		}
	}

	public function admin_billing(){
		$isAdmin = $this->RmCommon->_isAdmin();
		if($isAdmin){
			if($this->request->data){
				$userCode	= $this->RmUser->_generateCode('user_code');
				$domain		= $this->RmCommon->filterEmptyField($this->request->data, 'MembershipOrder', 'domain');

				if($domain){
					$scheme	= parse_url($domain, PHP_URL_SCHEME);
					$domain	= str_replace(array('http://', 'https://'), '', $domain);
					$domain = sprintf('%s://%s', ($scheme ? $scheme : 'http'), strtolower($domain));
				}

				$data = array_replace_recursive($this->request->data, array(
					'User' => array(
						'code' => $userCode, 
					), 
					'MembershipOrder' => array(
						'status'		=> 'approved',
						'is_principle' 	=> 1, 
						'domain'		=> $domain, 
					), 
					'Payment' => array(
						'referer_url' => FULL_BASE_URL, 
					), 
				));

				$packageID	= $this->RmCommon->filterEmptyField($data, 'MembershipOrder', 'membership_package_id');
				$package	= array(
					'MembershipPackage' => array(
						'id' => $packageID, 
					), 
				);

			//	debug($data);exit;

				$result	= array('data' => $data);
				$result	= array_replace_recursive($result, $this->MembershipOrder->doSave($data, $package));

				$paymentData	= $this->RmCommon->filterEmptyField($result, 'data', 'Payment');
				$invoiceID		= $this->RmCommon->filterEmptyField($paymentData, 'id');
				$invoiceNumber	= $this->RmCommon->filterEmptyField($paymentData, 'invoice_number');

				$this->RmCommon->setProcessParams($result, array(
					'controller'	=> 'payments', 
					'action'		=> 'view', 
					'admin'			=> true, 
					$invoiceID, 
					$invoiceNumber, 
				));
			}

			$title		= __('Membership Billing');
			$packages	= $this->MembershipPackage->getData('list', array(
				'conditions' => array(
					'MembershipPackage.status' => 1, 
				), 
				'order' => array(
					'MembershipPackage.name' => 'ASC', 
				), 
			));

			$themes = $this->User->UserCompanyConfig->Theme->getData('list', array(
				'order' => array(
					'Theme.name' => 'ASC', 
				)
			));

			$templates = $this->User->UserCompanyConfig->Template->getData('list', array(
				'order' => array(
					'Template.name' => 'ASC', 
				)
			));

			$this->set(array(
				'module_title'		=> $title, 
				'title_for_layout'	=> $title, 
				'packages'			=> $packages, 
				'themes'			=> $themes, 
				'templates'			=> $templates,
				'active_menu' 		=> 'billing',
			));
		}
		else{
			$this->RmCommon->setCustomFlash(__('Anda tidak memiliki hak untuk mengakses halaman tersebut.'), 'error');
			$this->redirect(array(
				'action'	=> 'index', 
				'admin'		=> true, 
			));
		}
	}

	public function admin_view($recordID = NULL){
		$authUserID		= $this->Auth->user('id');
		$authGroupID	= $this->Auth->user('group_id');
		$authParentID	= $this->Auth->user('parent_id');
		$isCompanyAdmin	= $this->RmCommon->_isCompanyAdmin();
		$options		= array(
			'conditions' => array(
				'MembershipOrder.id'			=> $recordID,
				'MembershipOrder.is_deleted'	=> 0,
			)
		);

		if($isCompanyAdmin && $authGroupID != 4){
		//	admin perusahaan / principle bisa ngeliat semua invoice 1 perusahaan
			$principleID	= $authGroupID == 3 ? $authUserID : $authParentID;
			$arrUserID		= array($principleID, $authUserID);

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

			$arrUserID	= array_unique($arrUserID);
			$options	= array_merge_recursive($options, array(
				'conditions' => array(
					'OR' => array(
						'MembershipOrder.user_id'		=> $arrUserID, 
					//	'MembershipOrder.principle_id'	=> $arrUserID, 
					), 
				), 
			));
		}

		$record = $this->MembershipOrder->getData('first', $options);

		if($isCompanyAdmin){
		//	check company config, jika belum ada redirect ke halaman membership
			$principleID	= $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'principle_id');
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
			$record = $this->MembershipOrder->getMerge($record);

			$this->set(array(
				'module_title'		=> __('Detail Kontak PRIME'), 
				'title_for_layout'	=> __('Detail Kontak PRIME'), 
				'active_menu'		=> 'contact_list',
				'record'			=> $record
			));
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function admin_process($recordID = NULL){
		$record = $this->MembershipOrder->getData('first', array(
			'conditions' => array(
				'MembershipOrder.id'			=> $recordID,
				'MembershipOrder.status'		=> array('pending'),
				'MembershipOrder.is_deleted'	=> 0,
			)
		));

		if($record){
			$record	= $this->MembershipOrder->getMerge($record);
			$data	= $this->request->data;
			$result	= array('data' => $record);

			if($data){
			//	generate user code for new user registration in case user not exists when invoice function being called.
				$userCode	= $this->RmUser->_generateCode('user_code');
				$status		= $this->RmCommon->filterEmptyField($data, 'MembershipOrder', 'status');
				$prefix		= $status == 'approved' ? null : $this->params->prefix;

				$refererURL	= sprintf('%s/%s', FULL_BASE_URL, $prefix);
				$data		= array_replace_recursive($data, array(
					'MembershipOrder' => array(
						'id'			=> $recordID, 
						'is_principle'	=> 1, 
					), 
					'User' => array(
						'code' => $userCode, 
					), 
					'Payment' => array(
						'referer_url' => $refererURL, 
					), 
				));

				$packageID	= $this->RmCommon->filterEmptyField($data, 'MembershipPackage', 'id');
				$package	= $this->MembershipPackage->getData('first', array(
					'conditions' => array(
						'MembershipPackage.id' => $packageID, 
					), 
				));

				$domain	= $this->RmCommon->filterEmptyField($data, 'MembershipOrder', 'domain');
				if($domain){
					$scheme	= parse_url($domain, PHP_URL_SCHEME);
					$domain	= str_replace(array('http://', 'https://'), '', $domain);
					$domain = sprintf('%s://%s', ($scheme ? $scheme : 'http'), strtolower($domain));

					$data = array_replace_recursive($data, array(
						'MembershipOrder' => array(
							'domain' => $domain, 
						), 
					));
				}

				$result = $this->MembershipOrder->doSave($data, $package);
			}

			$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));

			$packages = $this->MembershipPackage->getData('list', array(
				'conditions' => array(
					'MembershipPackage.status'		=> 1, 
					'MembershipPackage.is_deleted'	=> 0
				)
			));

		//	status nya cuma bisa maju, ga statis, apalagi mundur
			$statuses = array(
				'approved'	=> __('Approved'), 
				'cancelled'	=> __('Cancelled'), 
				'rejected'	=> __('Rejected'), 
			);

			$this->loadModel('Theme');
			$themes = $this->Theme->getData('list', array(
				'fields'	=> array('Theme.id', 'Theme.name'), 
				'order'		=> array(
					'Theme.name' => 'ASC', 
				)
			));

			$this->set(array(
				'module_title'		=> __('Proses Kontak PRIME'), 
				'title_for_layout'	=> __('Proses Kontak PRIME'), 
				'packages'			=> $packages, 
				'statuses'			=> $statuses, 
				'themes'			=> $themes, 
			));
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan / Order sudah pernah diproses.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function admin_cancel($recordID = NULL){
		$userID		= Configure::read('User.id');
		$options	= array(
			'conditions' => array(
				'MembershipOrder.id'			=> $recordID,
				'MembershipOrder.status'		=> array('pending'),
				'MembershipOrder.is_deleted'	=> 0,
			)
		);

		if(!$this->RmCommon->_isAdmin()){
			$options = array_merge_recursive($options, array(
				'conditions' => array(
					'MembershipOrder.user_id' => $userID, 
				), 
			));
		}

		$record = $this->MembershipOrder->getData('first', $options);
		if($record){
			$newStatus = 'cancelled';
			$record['MembershipOrder']['status'] = $newStatus;

			$result		= $this->MembershipOrder->doSave($record);
			$status		= $this->RmCommon->filterEmptyField($result, 'status');
			$message	= $this->RmCommon->filterEmptyField($result, 'msg');

			$this->RmCommon->setCustomFlash($message, $status);
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan / Order sudah pernah diproses.'), 'error');
		}

		$this->redirect(array('action' => 'index', 'admin' => TRUE));
	}

	public function admin_resend_rejection_email($recordID = NULL){
		$userID		= Configure::read('User.id');
		$options	= array(
			'conditions' => array(
				'MembershipOrder.id'			=> $recordID,
				'MembershipOrder.status'		=> array('cancelled', 'rejected'),
				'MembershipOrder.is_deleted'	=> 0,
			)
		);

		if(!$this->RmCommon->_isAdmin()){
			$options = array_merge_recursive($options, array(
				'conditions' => array(
					'MembershipOrder.user_id' => $userID, 
				), 
			));
		}

		$record = $this->MembershipOrder->getData('first', $options);
		if($record){
			$userID	= $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'user_id');
			$record	= $this->User->getMerge($record, $userID);

			$orderStatus	= $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'status');
			$fullName		= $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'name');
			$email			= $this->RmCommon->filterEmptyField($record, 'MembershipOrder', 'email');

			$fullName	= $this->RmCommon->filterEmptyField($record, 'User', 'full_name', $fullName);
			$email		= $this->RmCommon->filterEmptyField($record, 'User', 'email', $email);

			$subject	= __('Informasi Pemesanan Paket Membership');
			$template	= 'membership_process';

			$params = array_merge($record, array(
			//	'debug'	=> 'view', 
			));

			$emailSent	= $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);
			$emailType	= $orderStatus == 'cancelled' ? 'pembatalan' : 'penolakan';
			$status		= $emailSent ? 'success' : 'error';
			$message	= __('%s mengirim ulang email %s kepada <strong>%s</strong>.', ($emailSent ? 'Berhasil' : 'Gagal'), $emailType, $fullName);

			$this->RmCommon->setCustomFlash($message, $status);
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan / Order sudah pernah diproses.'), 'error');
		}

		$this->redirect(array('action' => 'index', 'admin' => TRUE));
	}

	public function admin_delete(){
		$data	= $this->request->data;
		$id		= $this->RmCommon->filterEmptyField($data, 'MembershipOrder', 'id');
    	$result = $this->MembershipOrder->doToggle($id);

		$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
	}
}
?>