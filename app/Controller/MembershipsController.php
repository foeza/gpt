<?php
App::uses('AppController', 'Controller');
class MembershipsController extends AppController {
	public $components	= array('Captcha');
	public $uses		= array(
		'MembershipPackage', 
		'MembershipPackageFeature', 
		'MembershipPackageFeatureDetail', 
		'MembershipOrder', 
		'Voucher'
	);

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow(array(
			'dashboard',
			'index', 
			'order', 
			'about_us',
			'feature',
			'price',
			'terms_and_conditions', 
			'get_package', 
		));

		$this->set('active_menu', 'membership');
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

	public function order($packageID = NULL, $packageSlug = NULL){
		$isAjax	= $this->RequestHandler->isAjax();
		$result	= array();
		$data = $this->request->data;
		$pick_package = array();

	//	get package list
		$package = $this->MembershipPackage->getData('first', array(
			'conditions' => array(
				'MembershipPackage.id' => $packageID, 
			), 
		));

		if(empty($package) && empty($isAjax)){
			$default_options = array(
				'conditions' => array(
					'MembershipPackage.status' => TRUE,
				),
			);

			$count = $this->MembershipPackage->getData('count', $default_options);

			if($count > 1){
				$package = $this->MembershipPackage->getData('list', $default_options);

				$membership_package_id = Common::hashEmptyField($data, 'MembershipOrder.membership_package_id');

				$pick_package =  $this->MembershipPackage->getData('first', array(
					'conditions' => array(
						'MembershipPackage.id' => $membership_package_id,
					),
				));
			} else {
				$package = $pick_package = $this->MembershipPackage->getData('first', $default_options);
				$packageID = Common::hashEmptyField($package, 'MembershipPackage.id');
			}
		}

		$captchaCode = $this->Captcha->generateEquation();
		$redirectURL = false;

		if($data){

			if($package){
				$data = array_merge_recursive($data, array(
					'MembershipOrder' => array('status' => 'approved'), 
				));
			}

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

			$refererURL	= sprintf('%s/%s', FULL_BASE_URL, $this->params->prefix);
			$data		= array_replace_recursive($data, array(
				'Payment' => array(
					'referer_url' => $refererURL, 
				), 
			));

			$result	= array('data' => $data);
			$save	= $this->MembershipOrder->doSave($data, $package);
			$result = array_replace_recursive($result, $save);

			$status		= $this->RmCommon->filterEmptyField($result, 'status');
			$message	= $this->RmCommon->filterEmptyField($result, 'msg');

			if($isAjax){
				if($status == 'success'){
					$this->set('result', $result);
				}

				$redirectOpts = array(
					'ajaxFlash'		=> true, 
					'ajaxRedirect'	=> false,
				);
			}
			else{
				$this->RmCommon->setCustomFlash(__($message), $status);

				if($status == 'success'){
					$paymentData	= $this->RmCommon->filterEmptyField($result, 'data', 'Payment');
					$invoiceID		= $this->RmCommon->filterEmptyField($paymentData, 'id');
					$invoiceNumber	= $this->RmCommon->filterEmptyField($paymentData, 'invoice_number');
					$userID			= $this->RmCommon->filterEmptyField($paymentData, 'user_id');
					$invoiceToken	= md5($invoiceNumber . $invoiceID . $userID);

					$redirectURL = array(
						'controller'	=> 'payments', 
						'action'		=> 'checkout', 
						$invoiceID, 
						$invoiceNumber, 
						$invoiceToken, 
					);
				}

				$redirectOpts = array('redirectError' => false);
			}

			if( !empty($result['data']) && $status == 'error' ){
				$this->loadModel('Theme');
				$theme_id = Common::hashEmptyField($result, 'data.MembershipOrder.theme_id');

				$result['data'] = $this->Theme->getMerge($result['data'], $theme_id);
			}

		//	re-use old captcha code
			$captchaCode = $this->RmCommon->filterEmptyField($data, 'MembershipOrder', 'security_code', $captchaCode);
			$this->RmCommon->setProcessParams($result, $redirectURL, $redirectOpts);
		}
		else{
		//	user code pemesan + principle
			$this->request->data = array_replace_recursive($this->request->data, array(
				'MembershipOrder' => array(
					'code'						=> $this->RmUser->_generateCode('user_code'), 
					'activation_code'			=> $this->RmUser->_generateCode(), 
					'principle_code'			=> $this->RmUser->_generateCode('user_code'), 
					'principle_activation_code'	=> $this->RmUser->_generateCode(), 
				), 
			));
		}

		$this->loadModel('Template');
		$templates = $this->Template->getData('list', array(
			'conditions' => array(
				'Template.status' => 1, 
			), 
			'order' => array(
				'Template.name' => 'ASC', 
			), 
		));

		$themes = array();
		if($packageID || !empty($package)){
			$this->loadModel('Theme');
			$themes = $this->Theme->getData('all', array(
				'order' => array(
					'Theme.name' => 'ASC', 
				)
			));
		}

	//	$this->layout = FALSE;
		$this->set(array(
			'templates'			=> $templates, 
			'package'			=> $package, 
			'pick_package' 		=> $pick_package,
			'themes'			=> $themes, 
			'result'			=> $result, 
			'isAjax'			=> $isAjax,
			'captchaCode'		=> $captchaCode,
			'collapseHeader'	=> true, 
			'bodyClass'			=> 'cart', 
			'bubleType' 		=> 'register',
		));

		$renderView = ($packageID || !empty($package)) ? 'frontend_order' : 'contact';

		if($packageID || !empty($package)){
			$this->render('/Elements/blocks/memberships/forms/frontend_order');
		} else {
			$this->render('/Elements/blocks/membershipV2/extra/modal');
		}

	}

	public function dashboard(){
		$this->set(array(
			'active_menu'		=> 'membership',
			'module_title'		=> __('Prime System Indonesia'), 
			'title_for_layout'	=> __('PRIME SYSTEM INDONESIA'), 
		));
	}

	public function index(){
		$this->RmUser->QuickStat();
		
		$membershipPackages	= $this->MembershipPackage->getData('all', array('conditions' => array('MembershipPackage.status' => 1)));
		$membershipPackages = $this->MembershipPackage->getMergeFeature($membershipPackages);
		$packageFeatures	= $this->MembershipPackageFeature->getData('all', array('conditions' => array('MembershipPackageFeature.status' => 1)));

		$agentAll = $this->User->getData('count', array(
			'conditions' => array(
				'User.group_id' => '2',
			),
		));

		$this->RmCommon->_layout_file('membership_video');

		$this->set(array(
			'active_menu'		=> 'membership',
			'module_title'		=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
			'title_for_layout'	=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
		));

		$this->set('captchaCode', $this->Captcha->generateEquation());
		$this->set(compact('membershipPackages', 'packageFeatures', $agentAll));
	}

	public function about_us(){

		$this->RmUser->QuickStat();

		$this->RmCommon->_layout_file('animate-counter');

		$this->set(array(
			'active_menu'		=> 'about',
			'bubleType' 		=> 'about',
			'module_title'		=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
			'title_for_layout'	=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
		));
	} 

	public function feature(){
		$this->RmUser->QuickStat();

		$this->set(array(
			'active_menu'		=> 'feature',
			'bubleType' 		=> 'feature',
			'module_title'		=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
			'title_for_layout'	=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
		));
	}

	public function price(){

		$membershipPackages	= $this->MembershipPackage->getData('all', array('conditions' => array('MembershipPackage.status' => 1)));
		$membershipPackages = $this->MembershipPackage->getMergeFeature($membershipPackages);
		$packageFeatures	= $this->MembershipPackageFeature->getData('all', array('conditions' => array('MembershipPackageFeature.status' => 1)));

		$this->set(array(
			'membershipPackages'	=> $membershipPackages,
			'packageFeatures'		=> $packageFeatures,
			'active_menu'			=> 'price',
			'bubleType' 			=> 'price',
			'module_title'			=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
			'title_for_layout'		=> __('PRIME - PROPERTY MANAGEMENT SYSTEM'), 
			'bodyClass'	=> 'no-scroll',
		));
	}

	public function terms_and_conditions(){
		$title = __('Syarat dan Ketentuan');

		$this->set(array(
			'active_menu'		=> 'membership', 
			'module_title'		=> $title, 
			'title_for_layout'	=> $title, 
			'collapseHeader'	=> TRUE, 
			'bubleType' 		=> 'home',
		));
	}

	public function admin_index(){
		$groupID = Configure::read('User.group_id');

		if(in_array($groupID, array(19, 20)) === FALSE){
			$errorMsg = __d('cake', 'You are not authorized to access that location.');
			$this->RmCommon->redirectReferer($errorMsg, 'error', Configure::read('User.dashboard_url'));
		}

		$options = $this->MembershipPackage->_callRefineParams($this->params, array(
			'conditions' => array(
				'MembershipPackage.is_deleted' => 0, 
			),
			'order' => array(
				'MembershipPackage.created' => 'DESC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate		= $this->MembershipPackage->getData('paginate', $options);
		$membershipPackages	= $this->paginate('MembershipPackage');

		$this->set(array(
			'module_title'		=> __('Paket Membership'), 
			'title_for_layout'	=> __('Paket Membership')
		));

		$this->set(compact('membershipPackages'));
	}

	public function admin_add(){
		if($this->request->data){
			$data	= $this->request->data;
			$price	= $this->RmCommon->filterEmptyField($data, 'MembershipPackage', 'price');
			$price	= str_replace(',', '', $price);
			$name	= $this->RmCommon->filterEmptyField($data, 'MembershipPackage', 'name');

			$data['MembershipPackage']['slug']	= $this->RmCommon->toSlug($name);
			$data['MembershipPackage']['price']	= $price;
			$result	= $this->MembershipPackage->doSave($data);
			$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));
		}

		$packageFeatures = $this->MembershipPackageFeature->getData('all', array(
			'conditions' => array(
				'MembershipPackageFeature.status'		=> 1, 
				'MembershipPackageFeature.is_deleted'	=> 0, 
			)
		));

		$this->set(array(
			'module_title'		=> __('Tambah Paket Membership'), 
			'title_for_layout'	=> __('Tambah Paket Membership'), 
			'packageFeatures'	=> $packageFeatures
		));

		$this->render('admin_form');
	}

	public function admin_edit($packageID = NULL){
		$record = $this->MembershipPackage->getData('first', array(
			'conditions' => array(
				'MembershipPackage.id'			=> $packageID,
				'MembershipPackage.is_deleted'	=> 0,
			)
		));

		if($record){
			$record		= $this->MembershipPackage->getMergeFeature($record);
			$features	= $this->RmCommon->filterEmptyField($record, 'MembershipPackage', 'features', NULL);

			if($features){
				$record['MembershipPackageFeature']['id'] = array();
				foreach($features as $key => $feature){
					$featureID		= $this->RmCommon->filterEmptyField($feature, 'MembershipPackageFeature', 'id');
					$featureType	= $this->RmCommon->filterEmptyField($feature, 'MembershipPackageFeature', 'field_type');
					$value			= $this->RmCommon->filterEmptyField($feature, 'MembershipPackageFeatureDetail', 'value');

					if($featureID){
						if($featureType == 'freetext'){
						//	untuk trigger toggler-state nya
							$record['MembershipPackageFeature']['toggler'][$featureID] = $value ? 1 : 0;
						}

						$record['MembershipPackageFeature']['id'][$featureID] = $value;
					}
				}

				unset($record['MembershipPackage']['features'], $features);
			}

			$data	= $this->request->data;
			$result	= array('data' => $record);

			if($data){
				$price	= $this->RmCommon->filterEmptyField($data, 'MembershipPackage', 'price');
				$price	= str_replace(',', '', $price);
				$name	= $this->RmCommon->filterEmptyField($data, 'MembershipPackage', 'name');

				$data['MembershipPackage']['id']	= $packageID;
				$data['MembershipPackage']['slug']	= $this->RmCommon->toSlug($name);
				$data['MembershipPackage']['price']	= $price;

				$result = $this->MembershipPackage->doSave($data);
			}

			$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));

			$packageFeatures = $this->MembershipPackageFeature->getData('all', array(
				'conditions' => array(
					'MembershipPackageFeature.status'		=> 1, 
					'MembershipPackageFeature.is_deleted'	=> 0, 
				)
			));

			$this->set(array(
				'module_title'		=> __('Edit Paket Membership'), 
				'title_for_layout'	=> __('Edit Paket Membership'), 
				'packageFeatures'	=> $packageFeatures
			));

			$this->render('admin_form');
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function admin_delete(){
		$data	= $this->request->data;
		$id		= $this->RmCommon->filterEmptyField($data, 'MembershipPackage', 'id');
    	$result = $this->MembershipPackage->doToggle($id);

		$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
	}

	function get_package(){
		$data = $this->request->data;

		if(!empty($data['MembershipOrder'])){
			$this->loadModel('MembershipPackage');

			$membership_package_id = Common::hashEmptyField($data, 'MembershipOrder.membership_package_id');

			$package = $this->MembershipPackage->getData('first', array(
				'conditions' => array(
					'MembershipPackage.id' => $membership_package_id,
				),
			));

			$this->set(array(
				'package' => $package,
			));

			$this->render('/Elements/blocks/membershipV2/content/sign_up/order_package');
		}
	}
}
?>