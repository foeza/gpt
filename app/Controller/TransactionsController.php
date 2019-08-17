<?php
App::uses('AppController', 'Controller');
/**
 * ProductUnits Controller
 *
 * @property ProductUnit $ProductUnit
 * @property PaginatorComponent $Paginator
 */
class TransactionsController extends AppController {

	public $components = array(
		'RmBooking'
	);

	public $helpers = array(
		'Kpr', 'Membership'
	);

	public $uses = array(
		'ApiProductUnitRelation'
	);

	function beforeFilter() {
		parent::beforeFilter();
 		
 		$this->Auth->allow(array(
 			'confirmation', 'uncart', 'booking_term_and_conditions', 'invoice',
 			'notify', 'test_email'
 		));
	}

	function admin_search ( $action = 'index', $_admin = true, $addParam = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => $_admin,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function confirmation($cart_id){
		$cart_data = $this->Session->read($this->RmBooking->session_cart_name);

		if(!empty($cart_data)){
			$original_project_id	= Common::hashEmptyField($cart_data, 'AdditionalData.project_id');
			$product_unit_stock_id 	= Common::hashEmptyField($cart_data, 'BookingDetail.product_unit_stock_id');

			$original_product_unit_id 	= Common::hashEmptyField($cart_data, 'BookingDetail.product_unit_id');
			$original_product_id 		= Common::hashEmptyField($cart_data, 'BookingDetail.product_id');

			$link_cart 				= sprintf('transactions/get_cart/%s/%s', $cart_id, $product_unit_stock_id);
			$link_payment_scheme 	= sprintf('transactions/payment_scheme/%s', $product_unit_stock_id);

			$cart 			= $this->RmCommon->getAPI($link_cart, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $original_project_id,
                    ),
                ),
            ));

            $cart_data_temp = Common::hashEmptyField($cart, 'data');

			if(!empty($cart) && !empty($cart_data_temp)){
				$is_expired = Common::hashEmptyField($cart, 'Booking.is_expired');

				if(!empty($is_expired)){
					$result = array(
						'status' => 'error',
						'msg' => __('Waktu pemesanan Anda telah habis, silahkan pilih kembali unit yang Anda inginkan.')
					);

					$url_back = $this->RmBooking->urlBackUnit();
					$this->RmBooking->destroySessionCartID();

					$this->RmCommon->setProcessParams($result, $url_back);
				}
			}else{
				$result = array(
					'status' => 'error',
					'msg' => __('Data tidak ditemukan atau waktu pemesanan Anda telah habis.')
				);

				$url_back = $this->RmBooking->urlBackUnit();
				$this->RmBooking->destroySessionCartID();

				$this->RmCommon->redirectReferer($result['msg'], $result['status'], $url_back);
			}

			$payment_scheme = $this->RmCommon->getAPI($link_payment_scheme, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $original_project_id,
                    ),
                ),
            ));

			$project_id 		= $this->RmBooking->getOriginalProjectID($original_project_id, true);
			$product_unit_id 	= $this->RmBooking->getOriginalProductUnitID($original_product_unit_id, true);
			$product_id 		= $this->RmBooking->getOriginalProductID($original_product_id, true);

			$this->RmBooking->saveBooking();

			$url_back = $this->RmBooking->urlBackUnit();

			$this->set(array(
				'session_data'		=> $cart_data,
				'project_id'		=> $project_id,
				'product_unit_id'	=> $product_unit_id,
				'product_id'		=> $product_id,
				'cart' 				=> $cart,
				'payment_scheme' 	=> $payment_scheme,
				'active_menu'		=> 'developers',
				'title'				=> 'Checkout',
				'module_title'		=> 'Checkout',
				'url_back'			=> $url_back,
				'layout_css'		=> array(
					'booking/booking'
				)
			));
		}else{
			$this->RmCommon->redirectReferer(__('Silahkan pilih unit yang Anda inginkan'));
		}
	}

	function uncart($cart_id){
		$cart_data = $this->Session->read($this->RmBooking->session_cart_name);

		if(!empty($cart_data)){
			$project_id = Common::hashEmptyField($cart_data, 'AdditionalData.project_id');
			
			$url_back = $this->RmBooking->urlBackUnit();
			
			$link_cart 	= sprintf('transactions/uncart/%s', $cart_id);

			$uncart = $this->RmCommon->getAPI($link_cart, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));

			if(!empty($uncart)){
				$status = Common::hashEmptyField($uncart, 'status');
				$msg 	= Common::hashEmptyField($uncart, 'msg');

				if($status == 1){
					$status = 'success';
				}else{
					$status = 'error';
				}

				$result = array(
					'status' => $status,
					'msg' => $msg,
				);
			}else{
				$result = array(
					'status' => 'error',
					'msg' => '',
				);
			}

			$this->RmBooking->destroySessionCartID();

			$this->RmCommon->setProcessParams($result, $url_back);
		}else{
			$this->RmCommon->redirectReferer(__('Data cart tidak ditemukan'));
		}
	}

	function booking_term_and_conditions(){
		$cart_data = $this->Session->read($this->RmBooking->session_cart_name);

		$result = array();
		if(!empty($cart_data)){
			$project_id = Common::hashEmptyField($cart_data, 'AdditionalData.project_id');
			$link 	= 'transactions/booking_term_and_conditions';

			$result = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));
		}else{
			$result = array(
				'status' => 0,
				'msg' => 'tidak ada data syarat dan ketentuan berlaku'
			);
		}

		$this->set('data', $result);

		$this->layout = 'ajax';
	}

	function invoice($invoice){
		$this->loadModel('InvoiceCollector');

		$invoice_data = $this->InvoiceCollector->getData('first', array(
			'conditions' => array(
				'InvoiceCollector.invoice_number' => $invoice
			)
		));

		if(!empty($invoice_data)){
			$project_id 	= Common::hashEmptyField($invoice_data, 'InvoiceCollector.project_id');
			$invoice_number = Common::hashEmptyField($invoice_data, 'InvoiceCollector.invoice_number');
			$link 	= 'transactions/invoice/'.$invoice_number;

			$record = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));

			$title = __('Invoice');

			$data_record = Common::hashEmptyField($record, 'data');
			if(empty($data_record)){
				$this->RmCommon->redirectReferer(__('Invoice tidak ditemukan'));
			}

			$this->set(array(
				'record'			=> $record,
				'active_menu'		=> 'developers',
				'title'				=> $title,
				'module_title'		=> $title,
				'_breadcrumb'		=> false,
				'layout_css'		=> array(
					'booking/booking'
				)
			));
		}else{
			$this->RmCommon->redirectReferer(__('Invoice tidak ditemukan'));
		}
	}

	function backprocess_ajax_fastbooking_list_units($product_id){
		$params = $this->params->params;

		$project_id = Common::hashEmptyField($params, 'named.project_id');

		$this->RmBooking->getListProductUnit($project_id, $product_id, array(
			'not_sold' => true
		));

		$this->set(compact('product_units', 'product_id', 'project_id'));

 		$this->render('/Elements/blocks/transactions/backends/list_unit');
	}

	function backprocess_ajax_fastbooking_list_unit_stocks($product_unit_id, $blok = false){
		$data = $this->request->data;

		$product_id = Common::hashEmptyField($this->params->params, 'named.product_id');
		$project_id = Common::hashEmptyField($this->params->params, 'named.project_id');

		$product_unit = $this->RmBooking->getDetailProductUnit($project_id, $product_unit_id);

		$this->request->data['BookingDetail']['booking_fee'] = Common::hashEmptyField($product_unit, 'ProductUnit.booking_fee');

		$this->RmBooking->getStock($project_id, $product_unit_id, $product_id, $blok, array(
			'not_sold' => true
		));

		$this->set(compact('product_id', 'product_unit_id', 'project_id', 'blok'));

		$this->render('/Elements/blocks/transactions/backends/price_unit');
	}

	function backprocess_ajax_bloks($product_unit_id){
		$product_id = Common::hashEmptyField($this->params->params, 'named.product_id');
		$project_id = Common::hashEmptyField($this->params->params, 'named.project_id');

		$product_unit = $this->RmBooking->getDetailProductUnit($project_id, $product_unit_id);

		$this->request->data['BookingDetail']['booking_fee'] = Common::hashEmptyField($product_unit, 'ProductUnit.booking_fee');

		$link_api = __('transactions/bloks');
		if(!empty($product_unit_id)){
			$link_api .= '/product_unit_id:'.$product_unit_id;
		}
		if(!empty($product_id)){
			$link_api .= '/product_id:'.$product_id;
		}

        $link_api .= '/not_sold:1';

		$bloks = $this->RmCommon->getAPI($link_api, array(
	        'header' => array(
	            'slug' => 'primedev-api',
	            'data' => array(
	                'project' => $project_id,
	            ),
	        ),
	    ));
		$bloks = Common::hashEmptyField($bloks,'data');

		$stock_bloks = array();

		$this->set(array(
			'bloks' 			=> $bloks,
			'stock_bloks' 		=> $stock_bloks,
			'product_id' 		=> $product_id,
			'product_unit_id' 	=> $product_unit_id,
			'project_id'		=> $project_id
		));

		$this->render('/Elements/blocks/transactions/backends/list_bloks');
	}

	function backprocess_ajax_payment_scheme($project_id, $product_unit_stock_id){
		$link_payment_scheme = sprintf('transactions/payment_scheme/%s', $product_unit_stock_id);
		$payment_scheme = $this->RmCommon->getAPI($link_payment_scheme, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

		$payment_scheme = Common::hashEmptyField($payment_scheme, 'data');

		$payment_scheme = $this->RmBooking->convertArrayPayment($payment_scheme);

		$this->Session->write('BookingDetail.product_unit_stock_id', $product_unit_stock_id);

		$this->set('payment_scheme', $payment_scheme);
		$this->set('project_id', $project_id);

		$this->render('/Elements/blocks/transactions/backends/list_payment_scheme');
	}

	function backprocess_ajax_detail_payment($project_id, $product_payment_method_id){
		$data = $this->request->data;
		$link_payment_scheme = sprintf('transactions/product_payment_method/%s', $product_payment_method_id);
		$product_payment = $this->RmCommon->getAPI($link_payment_scheme, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
            'post' => $data,
        ));

		$product_payment = Common::hashEmptyField($product_payment, 'data');

		$this->set('product_payment', $product_payment);
		$this->set('project_id', $project_id);

		$this->render('/Elements/blocks/transactions/backends/payment_method_detail');
	}

	function backprocess_ajax_fastbooking_list_products($project_id){
		$this->RmBooking->getListProduct($project_id, array(
			'not_sold' => true
		));

		$this->set('project_id', $project_id);

		$this->render('/Elements/blocks/transactions/backends/list_product');
	}

	function admin_invoice_booking(){
		$this->RmCommon->_callRefineParams($this->params);

		if(empty($isAdmin)){
		//	https://basecamp.com/1789306/projects/10415456/todos/368530755
		//	view dibatas by sales yang ngajuin
			$this->request->params['named']['api_requester'] = Configure::read('User.data.email');
		}

    	$link 	= 'transactions/invoices';
    	$link 	= $this->RmCommon->generateParamsApi($link);

		$records = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
            ),
        ));

		$this->set(array(
			'records' 		=> Common::hashEmptyField($records, 'data'),
			'paging' 		=> Common::hashEmptyField($records, 'paging'),
			'active_menu'	=> 'invoice_booking',
			'module_title'	=> __('Invoice Booking'),
		));
	}

	public function backprocess_validateVoucher(){
		$this->layout		= FALSE;
		$this->autoRender	= FALSE;

		$data	= $this->request->data;
		$isAjax	= $this->RequestHandler->isAjax();

		// if($isAjax && $data){
			$voucherCode		= $this->RmCommon->filterEmptyField($data, 'code');
			$documentCode		= $this->RmCommon->filterEmptyField($data, 'document_code');
			$documentType		= $this->RmCommon->filterEmptyField($data, 'document_type');
			$membershipPackage	= $this->RmCommon->filterEmptyField($data, 'membership_package');
			$refer_prefix		= $this->RmCommon->filterEmptyField($data, 'refer_prefix');
			// $price	            = $this->RmCommon->filterEmptyField($data, 'price');

			$result = $this->Voucher->redeemVoucher($voucherCode, array(
				'document_type'			=> $documentType, 
				'document_code'			=> $documentCode, 
				'membership_package'	=> $membershipPackage, 
				'refer_prefix'			=> $refer_prefix
				// 'price'					=> $price, 
			));
		// }
		// else{
		// 	$result	= array(
		// 		'status'	=> 'error', 
		// 		'msg'		=> __('Invalid method.'), 
		// 	);
		// }

		return json_encode($result);
	}

	/*
		syarat: kasih post data : 

		RESPONSECODE 	=> kode transaksi, 200 sukses dan 500 error
		WORDS 			=> MD5 dari invoice_number
		INVOICENUMBER 	=> nomor invoice
	*/
	function notify(){
		$data = $this->request->data;

		if($this->request->is('post') && !empty($data)){
			/*
				ini nanti kalo mau live di matiin
			*/
			// $this->RmCommon->_saveLog('PRIMEDEV Notify', $data);
			/*
				END
			*/

			$responseCode		= Common::hashEmptyField($data, 'RESPONSECODE');
			$secretWord			= Common::hashEmptyField($data, 'WORDS');
			$invoiceNumber		= Common::hashEmptyField($data, 'INVOICENUMBER');

			// debug(md5($invoiceNumber));die();

			if(!empty($responseCode) && !empty($secretWord) && !empty($invoiceNumber) && $responseCode == 200 && md5($invoiceNumber) == $secretWord){

				$link 	= 'transactions/invoice/'.$invoiceNumber;

				$record = $this->RmCommon->getAPI($link, array(
	                'header' => array(
	                    'slug' => 'primedev-api',
	                ),
	            ));

	            $data_record = Common::hashEmptyField($record, 'data');
				if(!empty($data_record)){
					$principle_id 	= Configure::read('Principle.id');
					$paidSubject	= __('Invoice %s telah dibayar', $invoiceNumber);

                    $this->loadModel('InvoiceCollector');
                    $this->InvoiceCollector->_callInvoicePaid($invoiceNumber);

					$result['SendEmail'] = array(
		                'subject' => $paidSubject,
		                'template' => 'invoice_paid_booking',
		                'data' => $data_record,
		                'include_role' => array(
		                    'role' => array('principle', 'admin'),
		                	'from_parent_id' => $principle_id
		                ),
					);

					$result['Notification'] = array(
		                'name' => $paidSubject,
		                'link' => array(
		                    'admin'			=> true, 
							'controller'	=> 'projects',
							'action'		=> 'invoice',
							$invoiceNumber,
		                ),
		                'include_role' => array(
		                    'role' => array('principle', 'admin'),
		                    'from_parent_id' => $principle_id
		                )
		            );

		            $this->RmCommon->setProcessParams($result, false, array(
		            	'noRedirect' => true
		            ));

		            $result = array(
						'status' => 1,
						'msg' => __('Berhasil melakukan notifikasi untuk invoice %s', $invoiceNumber)
					);
				}else{
					$result = array(
						'status' => 0,
						'msg' => __('Invoice tidak ditemukan')
					);
				}
			}else{
				$result = array(
					'status' => 0,
					'msg' => __('Post data tidak lengkap, periksa kembali kelengkapan datanya')
				);
			}
		}else{
			$result = array(
				'status' => 0,
				'msg' => __('Post data tidak tersedia')
			);
		}

		$this->render(false);
		$this->layout = false;

		echo json_encode($result);
		die();
	}
}