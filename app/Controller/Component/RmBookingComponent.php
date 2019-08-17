<?php
class RmBookingComponent extends Component {
	var $components = array(
		'RmCommon', 'Session', 'RmUser'
	); 

	var $session_cart_name = 'cart_id';

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callBeforeSaveIdentity($data, $product_id = false, $product_unit_id = false, $product_unit_stock_id = false, $project_id = false){

		$this->BookingProfile = ClassRegistry::init('BookingProfile');
		$sesion_name = 'Booking.unit';

		if(!empty($data)){
            // Utk handle maria DB, tipe int harus isinya 0 atau null tdk bole false
            $product_id = !empty($product_id)?$product_id:0;
            $product_unit_id = !empty($product_unit_id)?$product_unit_id:0;
            $product_unit_stock_id = !empty($product_unit_stock_id)?$product_unit_stock_id:0;
            $project_id = !empty($project_id)?$project_id:0;

			$data = hash::insert($data, 'BookingDetail.product_id', $product_id);
			$data = hash::insert($data, 'BookingDetail.product_unit_id', $product_unit_id);
			$data = hash::insert($data, 'BookingDetail.product_unit_stock_id', $product_unit_stock_id);

            $cart_data = $this->Session->read($this->session_cart_name);

            $booking_code = Common::hashEmptyField($cart_data, 'Booking.booking_code');
            if(!empty($booking_code)){
                $data = hash::insert($data, 'Booking.booking_code', $booking_code);
            }

			$flag = $this->BookingProfile->saveAll($data, array(
                'validate' => 'only',
                'deep' => true
            ));

            if(!empty($flag)){
            	$product_unit_stock_id = Common::hashEmptyField($data, 'BookingDetail.product_unit_stock_id');

            	if(!empty($product_unit_stock_id)){
                    $link = sprintf('transactions/set_cart/%s', $product_unit_stock_id);

                    $result = $this->RmCommon->getAPI($link, array(
                        'header' => array(
                            'slug' => 'primedev-api',
                            'data' => array(
                                'project' => $project_id,
                            ),
                        ),
                        'post' => $data,
                    ));

                    $status     = Common::hashEmptyField($result, 'status');
                    $msg        = Common::hashEmptyField($result, 'msg');
                    $cart_id    = Common::hashEmptyField($result, 'cart_id');

                    if($status == 1){
                        $data = hash::insert($data, 'Booking.booking_code', $cart_id);
                        $data = hash::insert($data, 'AdditionalData.project_id', $project_id);
                        $data = hash::insert($data, 'AdditionalData.url_cart', array(
                            'controller' => 'transactions',
                            'action' => 'confirmation',
                            $cart_id,
                            'admin' => false
                        ));

                		$this->Session->write($this->session_cart_name, $data);

                		$result = array(
    	        			'status' => 'success',
    	        			'msg' => __('Berhasil memasukkan unit ke dalam cart.'),
    	        		);
                    }else{
                        $result = array(
                            'status' => 'error',
                            'msg' => $msg
                        );
                    }
            	}else{
            		$result = array(
            			'status' => 'error',
            			'msg' => __('Terjadi kesalahan, silakan coba kembali beberapa saat lagi.')
            		);
            	}
            }else{
            	$result = array(
        			'status' => 'error',
        			'msg' => __('Gagal memasukkan unit ke dalam cart.')
        		);
            }

            $this->RmCommon->setProcessParams($result, false, array(
            	'ajaxFlash' => true,
            ));
		}
	}

    function destroySessionCartID($just_cart_id = false){
        if(!empty($just_cart_id)){
            $cart_data = $this->Session->read($this->session_cart_name);

            if(isset($cart_data['Booking']['booking_code'])){
                unset($cart_data['Booking']['booking_code']);
            }

            if(isset($cart_data['BookingDetail'])){
                unset($cart_data['BookingDetail']);
            }

            if(isset($cart_data['AdditionalData'])){
                unset($cart_data['AdditionalData']);
            }

            $this->Session->write($this->session_cart_name, $cart_data);
        }else{
            $this->Session->delete($this->session_cart_name);
        }
    }

    function saveBooking(){
        $data       = $this->controller->request->data;
        $cart_data  = $this->Session->read($this->session_cart_name);

        if(!empty($data)){
            $this->Booking = ClassRegistry::init('Booking');
            $this->InvoiceCollector = ClassRegistry::init('InvoiceCollector');

            $BookingProfile = Common::hashEmptyField($cart_data, 'BookingProfile');
            $booking_code = Common::hashEmptyField($cart_data, 'Booking.booking_code');

            $full_name = Common::hashEmptyField($BookingProfile, 'full_name');

            $field_booking = array(
                'product_payment_method_id',
                'payment_channel',
                'tenor',
                'agree'
            );

            $data = hash::insert($data, 'BookingProfile', $BookingProfile);

            foreach ($field_booking as $key => $value) {
                $val = Common::hashEmptyField($data, 'Booking.'.$value);
                
                $data = hash::insert($data, 'Booking.'.$value, $val);
            }

            $flag = $this->Booking->saveAll($data, array(
                'validate' => 'only',
                'deep' => true
            ));

            $url = false;
            
            if($flag){
                $product_payment_method_id = Common::hashEmptyField($data, 'Booking.product_payment_method_id');

                $data = hash::insert($data, 'BookingDetail.product_payment_method_id', $product_payment_method_id);
                $data = hash::insert($data, 'Booking.booking_code', $booking_code);

                $project_id = Common::hashEmptyField($cart_data, 'AdditionalData.project_id', 0);
                $link   = 'transactions/checkout';

                $result = $this->RmCommon->getAPI($link, array(
                    'header' => array(
                        'slug' => 'primedev-api',
                        'data' => array(
                            'project' => $project_id,
                        ),
                    ),
                    'post' => $data,
                ));

                $status             = Common::hashEmptyField($result, 'status');
                $msg                = Common::hashEmptyField($result, 'msg');
                $invoice_number     = Common::hashEmptyField($result, 'invoice');
                $is_kpr             = Common::hashEmptyField($result, 'is_kpr', 0);

                if(!empty($result)){
                    if($status == 1){
                        $principle = Configure::read('Config.Company.data.User.id');

                        $result = array(
                            'status' => 'success',
                            'msg' => 'Anda berhasil memesan unit',
                            'Notification' => array(
                                'name' => __('Informasi Pemesanan invoice %s dari %s', $invoice_number, $full_name),
                                'link' => array(
                                    'controller' => 'projects',
                                    'action' => 'invoice',
                                    $invoice_number,
                                    'admin' => true
                                ),
                                'include_role' => array(
                                    'role' => array('principle', 'admin'),
                                    'from_parent_id' => $principle
                                )
                            )
                        );

                        $url = array(
                            'controller' => 'transactions',
                            'action' => 'invoice',
                            $invoice_number,
                            'admin' => false
                        );

                        $this->InvoiceCollector->doSave(array(
                            'project_id' => $project_id,
                            'invoice_number' => $invoice_number,
                            'booking_code' => $booking_code,
                            'is_kpr' => $is_kpr,
                        ));

                        $this->RmUser->createClientOnfly($data, 'BookingProfile');

                        $this->destroySessionCartID();
                    }else{
                        $result = array(
                            'status' => 'error',
                            'msg' => $msg
                        );
                    }
                }else{
                    $result = array(
                        'status' => 'error',
                        'msg' => 'Sedang terjadi kendala dalam melakukan checkout, tunggu bebeapa saat lagi.'
                    );
                }
            }else{
                $result = array(
                    'status' => 'error',
                    'msg' => 'Gagal melakukan booking unit, pastikan semua data terisi dengan benar.'
                );
            }

            $this->RmCommon->setProcessParams($result, $url);
        }
    }

    function saveFastBooking($project_id = false){
        $data = $this->controller->request->data;

        if(!empty($data)){
            $this->Booking = ClassRegistry::init('Booking');
            $this->InvoiceCollector = ClassRegistry::init('InvoiceCollector');

            $payment_channel = Common::hashEmptyField($data, 'Booking.payment_channel');
            $this->BuildValidation($data);

            $field_booking_api = array(
                'Booking' => array(
                    'payment_channel',
                ),
                'BookingDetail' => array(
                    'product_id',
                    'product_unit_id',
                    'product_unit_stock_id',
                    'product_payment_method_id',
                    'booking_fee'
                ),
                'BookingProfile' => array(
                    'full_name',
                    'no_hp',
                    'email'
                ),
                'OrderPayment' => array(
                    'payment_datetime'
                ),
                'OrderPaymentTransfer' => array(
                    'bank_id',
                    'total_transfer',
                    'bank_name',
                    'no_account',
                    'name_account'
                )
            );

            $project_id = Common::hashEmptyField($data, 'BookingDetail.project_id');
            $email = Common::hashEmptyField($data, 'BookingProfile.email');
            $email = $this->RmCommon->getEmailConverter($email);

            $data = Hash::insert($data, 'BookingProfile.email', $email);

            $data = $this->setDataOrderPaymentTransfer($data);

            $data = Common::dataConverter($data, array(
                'price' => array(
                    'BookingDetail' => array(
                        'booking_fee'
                    )
                )
            ), false, '-');

            $flag = $this->Booking->saveAll($data, array(
                'validate' => 'only',
                'deep' => true
            ));

            $data = $this->RmUser->_callDataRegister($data);

            $url = false;

            if($flag && !empty($project_id)){
				$isAdmin = Configure::read('User.admin');

				if(empty($isAdmin)){
				//  https://basecamp.com/1789306/projects/10415456/todos/368530755
					$authEmail	= Configure::read('User.data.email');
					$data		= Hash::insert($data, 'Booking.api_requester', $authEmail);
				}

                $link   = 'transactions/checkout_fast_booking';
                $result = $this->RmCommon->getAPI($link, array(
                    'header' => array(
                        'slug' => 'primedev-api',
                        'data' => array(
                            'project' => $project_id,
                        ),
                    ),
                    'post' => $data,
                ));

                $status = Common::hashEmptyField($result, 'status');
                $msg = Common::hashEmptyField($result, 'msg');
                $invoice_number = Common::hashEmptyField($result, 'invoice');
                $is_kpr = Common::hashEmptyField($result, 'is_kpr', 0);
                $booking_code = Common::hashEmptyField($result, 'booking_code');

                if(!empty($result)){
                    if($status == 1){
                        $this->RmUser->createClientOnfly($data, 'BookingProfile');
                        $payment_cash = Configure::read('Config.PaymentConfig.payment_cash');
                        
                        if( $payment_channel == $payment_cash ) {
                            $is_payment_confirm = true;
                        } else {
                            $is_payment_confirm = false;
                        }

                        $result = array(
                            'status' => 'success',
                            'msg' => 'Anda berhasil memesan unit'
                        );

                        $url = array(
                            'controller' => 'projects',
                            'action' => 'invoice',
                            $invoice_number,
                            'admin' => true
                        );

                        $this->InvoiceCollector->doSave(array(
                            'project_id' => $project_id,
                            'invoice_number' => $invoice_number,
                            'booking_code' => $booking_code,
                            'is_payment_confirm' => $is_payment_confirm,
                            'is_kpr' => $is_kpr,
                        ));

                        $this->Session->delete('BookingDetail.product_unit_stock_id');
                    }else{
                        $result = array(
                            'status' => 'error',
                            'msg' => $msg
                        );
                    }
                }else{
                    $result = array(
                        'status' => 'error',
                        'msg' => 'Sedang terjadi kendala dalam melakukan checkout, tunggu beberapa saat lagi.'
                    );
                }
            }else{
                $result = array(
                    'status' => 'error',
                    'msg' => 'Gagal melakukan booking unit, pastikan semua data terisi dengan benar.'
                );
            }

            $this->RmCommon->setProcessParams($result, $url);
        }else{
            $this->Session->delete('BookingDetail.product_unit_stock_id');
            
            $this->controller->request->data['BookingDetail']['project_id'] = $project_id;
            $this->controller->request->data['Booking']['payment_datetime'] = date('d/m/Y H:i');
            
            $data['BookingDetail']['project_id'] = $project_id;
        }

        $project_id                 = Common::hashEmptyField($data, 'BookingDetail.project_id'); 
        $product_id                 = Common::hashEmptyField($data, 'BookingDetail.product_id'); 
        $product_unit_id            = Common::hashEmptyField($data, 'BookingDetail.product_unit_id'); 
        $product_unit_stock_id      = Common::hashEmptyField($data, 'BookingDetail.product_unit_stock_id'); 
        $product_payment_method_id  = Common::hashEmptyField($data, 'BookingDetail.product_payment_method_id');
        $blok                       = Common::hashEmptyField($data, 'BookingDetail.bloks');

        $this->supportingDataFastBooking($project_id, $product_id, $product_unit_id, $blok, $product_unit_stock_id, $product_payment_method_id);
    }

    function supportingDataFastBooking($project_id, $product_id = false, $product_unit_id = false, $blok = false, $product_unit_stock_id = false, $product_payment_method_id = false, $is_payment_schema = true){

        $this->ApiProductUnitRelation = ClassRegistry::init('ApiProductUnitRelation');

        $this->getListProject('approved');

        if(!empty($project_id)){
            $this->getListProduct($project_id, array(
                'not_sold' => true
            ));
        }

        if(!empty($project_id) && !empty($product_id)){
            $this->getListProductUnit($project_id, $product_id);
        }

        if(!empty($product_unit_id)){
            $this->getStock($project_id, $product_unit_id, $product_id, $blok);
        }

        if(!empty($product_unit_id)){
            $this->getBloks($project_id, $product_unit_id, $product_id, array(
                'not_sold' => true
            ));
        }

        if(!empty($product_unit_stock_id)){
            $this->getPaymentScheme($project_id, $product_unit_stock_id);
        }

        if(!empty($product_payment_method_id)){
            $this->getDetailPayment($project_id, $product_payment_method_id);
        }

        $this->controller->set(array(
            'product_unit_id' => $product_unit_id,
            'product_id' => $product_id,
        ));
    }

    function getListProject($status = false){
        $link = 'transactions/list_projects';

        if(!empty($status)){
            $link .= '/'.$status;
        }

        $result = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
            ),
        ));

        $projects = Common::hashEmptyField($result, 'data');

        $this->controller->set(array(
            'projects' => $projects
        ));

        return $projects;
    }

    function getListProduct($project_id, $options = array()){
        $link = 'transactions/list_products';

        $not_sold = Common::hashEmptyField($options, 'not_sold');

        if(!empty($not_sold)){
            $link .= '/not_sold:1';
        }

        $result = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        $products = Common::hashEmptyField($result, 'data');

        $this->controller->set(array(
            'products' => $products
        ));

        return $products;
    }

    function getListProductUnit($project_id, $product_id = false, $options = array()){
        $link = 'transactions/list_product_units';

        if(!empty($product_id)){
            $link .= '/'.$product_id;
        }

        $not_sold = Common::hashEmptyField($options, 'not_sold');

        if(!empty($not_sold)){
            $link .= '/not_sold:1';
        }

        $result = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        $product_units = Common::hashEmptyField($result, 'data');

        $this->controller->set(array(
            'product_units' => $product_units
        ));

        return $product_units;
    }

    function getDetailProductUnit($project_id, $product_unit_id){
        $link = 'transactions/detail_product_unit/'.$product_unit_id;

        $result = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        $product_units = Common::hashEmptyField($result, 'data');

        return $product_units;
    }

    function getStock($project_id, $product_unit_id, $product_id, $blok = false, $options = array()){
        $link = 'transactions/booking_stocks';

        if(!empty($product_unit_id)){
            $link .= '/product_unit_id:'.$product_unit_id;
        }

        if(!empty($product_id)){
            $link .= '/product_id:'.$product_id;
        }

        if(!empty($blok)){
            $link .= '/blok:'.$blok;
        }

        $not_sold = Common::hashEmptyField($options, 'not_sold');

        if(!empty($not_sold)){
            $link .= '/not_sold:1';
        }

        $result = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        $stock_bloks = Common::hashEmptyField($result, 'data');

        $this->controller->set(array(
            'stock_bloks' => $stock_bloks
        ));

        return $stock_bloks;
    }

    function getPaymentScheme($project_id, $product_unit_stock_id){
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
        $payment_scheme = $this->convertArrayPayment($payment_scheme);

        $this->controller->set(array(
            'payment_scheme' => $payment_scheme
        ));

        return $payment_scheme;
    }

    function getBloks($project_id, $product_unit_id = false, $product_id = false, $options = array()){
        $link_api = __('transactions/bloks');
        if(!empty($product_unit_id)){
            $link_api .= '/product_unit_id:'.$product_unit_id;
        }
        if(!empty($product_id)){
            $link_api .= '/product_id:'.$product_id;
        }

        $not_sold = Common::hashEmptyField($options, 'not_sold');

        if(!empty($not_sold)){
            $link_api .= '/not_sold:1';
        }

        $bloks = $this->RmCommon->getAPI($link_api, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));
        $bloks = Common::hashEmptyField($bloks,'data');

        $this->controller->set(array(
            'bloks' => $bloks
        ));

        return $bloks;
    }

    function getOriginalProjectID($project_id, $reverse = false){
        $this->ApiAdvanceDeveloper = ClassRegistry::init('ApiAdvanceDeveloper');

        if(!empty($reverse)){
            $conditions['ApiAdvanceDeveloper.original_id'] = $project_id;
            $field = 'id';
        }else{
            $conditions['ApiAdvanceDeveloper.id'] = $project_id;
            $field = 'original_id';
        }
        
        $project = $this->ApiAdvanceDeveloper->getData('first', array(
            'conditions' => $conditions
        ));

        return Common::hashEmptyField($project, 'ApiAdvanceDeveloper.'.$field);
    }

    function getOriginalProductID($product_id, $reverse = false){
        $this->ApiAdvanceDeveloperProduct = ClassRegistry::init('ApiAdvanceDeveloperProduct');

        if(!empty($reverse)){
            $conditions['ApiAdvanceDeveloperProduct.original_id'] = $product_id;
            $field = 'id';
        }else{
            $conditions['ApiAdvanceDeveloperProduct.id'] = $product_id;
            $field = 'original_id';
        }

        $data = $this->ApiAdvanceDeveloperProduct->getData('first', array(
            'conditions' => $conditions
        ));

        return Common::hashEmptyField($data, 'ApiAdvanceDeveloperProduct.'.$field);
    }

    function getOriginalProductUnitID($product_unit_id, $reverse = false){
        $this->ApiAdvanceDeveloperProductUnit = ClassRegistry::init('ApiAdvanceDeveloperProductUnit');

        if(!empty($reverse)){
            $conditions['ApiAdvanceDeveloperProductUnit.original_id'] = $product_unit_id;
            $field = 'id';
        }else{
            $conditions['ApiAdvanceDeveloperProductUnit.id'] = $product_unit_id;
            $field = 'original_id';
        }

        $data = $this->ApiAdvanceDeveloperProductUnit->getData('first', array(
            'conditions' => $conditions
        ));

        return Common::hashEmptyField($data, 'ApiAdvanceDeveloperProductUnit.'.$field);
    }

    function getBanks($project_id){
        $link = 'transactions/banks';

        $result = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        $banks = Common::hashEmptyField($result, 'data');

        $this->controller->set(array(
            'banks' => $banks
        ));

        return $banks;
    }

    function getDetailPayment($project_id, $product_payment_method_id){
        $link_payment_scheme = sprintf('transactions/product_payment_method/%s', $product_payment_method_id);
        $product_payment = $this->RmCommon->getAPI($link_payment_scheme, array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        $product_payment = Common::hashEmptyField($product_payment, 'data');

        $this->controller->set(array(
            'product_payment' => $product_payment
        ));

        return $product_payment;
    }

    function convertArrayPayment($data){
        $result = array();

        if(!empty($data) && is_array($data)){
            foreach ($data as $key => $methods) {
                foreach ($methods as $key => $value) {
                    $id = Common::hashEmptyField($value, 'ProductPaymentMethod.id');
                    $title = Common::hashEmptyField($value, 'ProductPaymentMethod.title');
                    $name_method = Common::hashEmptyField($value, 'MasterPaymentMethod.name');
                    
                    $result[$id] = __('%s (%s)', $title, $name_method);
                }
            }
        }

        return $result;
    }

    function setVoucherCode(){
        $data = $this->controller->request->data;

        $temp_data_VoucherCode = Common::hashEmptyField($data, 'VoucherCode');

        $code = Common::hashEmptyField($temp_data_VoucherCode, 'code');

        if(!empty($code)){
            $this->Voucher = ClassRegistry::init('Voucher');

            $data_voucher = $this->Voucher->redeemVoucher($code, $temp_data_VoucherCode);

            Configure::write('__Site.data_voucher', $data_voucher);
        }
    }

    function setDataOrderPaymentTransfer($data, $model = 'BookingNup'){
        /*Metode pembayaran*/
        $payment_channel = Common::hashEmptyField($data, $model.'.payment_channel');

        if($payment_channel == 99 || $payment_channel == '99'){
            $bank_id = Common::hashEmptyField($data, 'Booking.bank_id');

            $detail_unset = true;
            /*transfer sudah pasti full paid*/
            if(!empty($data['Booking'])){
                $data = Hash::insert($data, 'Booking.is_paid', 1);
            }
        }else if($payment_channel == 98 || $payment_channel == '98'){
            $total_transfer = Common::hashEmptyField($data, $model.'.booking_fee');
            if($model == 'Booking'){
                $total_transfer = Common::hashEmptyField($data, 'BookingDetail.booking_fee');
            }
            
            $data = Hash::insert($data, 'Booking', array(
                'name_account'      => Common::hashEmptyField($data, $model.'Profile.full_name'),
                'total_transfer'    => $total_transfer,
                'date_transfer'     => Common::hashEmptyField($data, 'Booking.payment_datetime'),
            ));
        }
        /*END - Metode Pembayaran*/

        $data = Common::dataConverter($data, array(
            'datetime' => array(
                'Booking' => array(
                    'date_transfer'
                )
            ),
            'price' => array(
                'Booking' => array(
                    'total_transfer'
                )
            )
        ), false, '-');

        return $data;
    }

    private function _transferValidation(){
        $this->Booking->validator()
            ->add('name_account', 'required', array(
                'rule' => 'notempty',
                'message' => 'Mohon masukkan nama akun'
            ))
            ->add('no_account', 'required', array(
                'rule' => 'notempty',
                'message' => 'Mohon masukkan nomor rekening'
            ))
            ->add('bank_name', 'required', array(
                'rule' => 'notempty',
                'message' => 'Mohon masukkan nama bank'
            ))
            ->add('payment_datetime', 'required', array(
                'rule' => 'notempty',
                'message' => 'Mohon masukkan tanggal pembayaran'
            ));
    }

    function BuildValidation($data, $model = 'Booking'){
        $payment_channel = Common::hashEmptyField($data, $model.'.payment_channel');
        $bank_id = Common::hashEmptyField($data, $model.'.bank_id');

        $this->Booking = ClassRegistry::init('Booking');

        if($payment_channel == 98 || $payment_channel == '98'){
            $this->Booking->validator()->add('payment_datetime', 'required', array(
                'rule' => 'notempty',
                'message' => 'Mohon masukkan tanggal pembayaran'
            ));
        }

        $this->Booking->BookingDetail->validator()->add('project_id', 'required', array(
            'rule' => 'notempty',
            'message' => 'Mohon pilih projek'
        ));

        $this->Booking->BookingDetail->validator()->add('bloks', 'required', array(
            'rule' => 'notempty',
            'message' => 'Mohon pilih blok'
        ));

        $this->Booking->BookingDetail->validator()->add('product_payment_method_id', 'required', array(
            'rule' => 'notempty',
            'message' => 'Mohon pilih cara pembayaran'
        ));
    }

    function confirmTransfer($project_id, $invoice_number){
        $this->Booking = ClassRegistry::init('Booking');

        $data = $this->controller->request->data;
        if(!empty($data['Booking'])){
            $this->_transferValidation();

            $this->Booking->validator()
                ->add('bank_id', 'required', array(
                    'rule' => 'notempty',
                    'message' => 'Mohon pilih bank tujuan transfer'
                ))
                ->add('date_transfer', 'required', array(
                    'rule' => 'notempty',
                    'message' => 'Mohon masukkan tanggal transfer'
                ));

            // $time_transfer = Common::hashEmptyField($data, 'Booking.time_transfer', '00:00');
            $date_transfer = Common::hashEmptyField($data, 'Booking.date_transfer');

            // $date_transfer .= ' '.$time_transfer;
            $data = Hash::insert($data, 'Booking.date_transfer', $date_transfer);

            $data = Common::dataConverter($data, array(
                'datetime' => array(
                    'Booking' => array(
                        'date_transfer'
                    )
                ),
                'price' => array(
                    'Booking' => array(
                        'total_transfer'
                    )
                )
            ), false, '-');

            $flag = $this->Booking->saveAll($data, array(
                'validate' => 'only',
                'deep' => true
            ));
            
            if($flag){
                $data_temp['OrderPaymentConfirmation'] = Common::hashEmptyField($data, 'Booking');

                $link = sprintf('transactions/confirm_transfer/%s', $invoice_number);

                $result = $this->RmCommon->getAPI($link, array(
                    'header' => array(
                        'slug' => 'primedev-api',
                        'data' => array(
                            'project' => $project_id,
                        ),
                    ),
                    'post' => $data_temp,
                ));

                $status             = Common::hashEmptyField($result, 'status');
                $msg                = Common::hashEmptyField($result, 'msg');

                if(!empty($result)){
                    if($status == 1){
                        $this->InvoiceCollector = ClassRegistry::init('InvoiceCollector');
                        $this->InvoiceCollector->_callInvoicePaid($invoice_number);

                        $result = array(
                            'status' => 'success',
                            'msg' => 'Anda berhasil melakukan konfirmasi transfer'
                        );
                    }else{
                        $result = array(
                            'status' => 'error',
                            'msg' => $msg
                        );
                    }
                }else{
                    $result = array(
                        'status' => 'error',
                        'msg' => 'Sedang terjadi kendala dalam melakukan checkout, tunggu bebeapa saat lagi.'
                    );
                }
            }else{
                $result = array(
                    'status' => 'error',
                    'msg' => __('Gagal melakukan konfirmasi transfer')
                );
            }

            $status = Common::hashEmptyField($result, 'status');

            $this->controller->set('status', $status);

            if($status == 'success'){
                $this->controller->set('_flash', false);
            }

            $this->RmCommon->setProcessParams($result, false, array(
                'ajaxFlash' => true,
                'flash' => true
            ));
        }
    }

    function cartValidationData(){
        $cart_data = $this->Session->read($this->session_cart_name);

        $booking_code = Common::hashEmptyField($cart_data, 'Booking.booking_code');

        if(!empty($booking_code)){
            $product_unit_stock_id  = Common::hashEmptyField($cart_data, 'BookingDetail.product_unit_stock_id');
            $original_project_id    = Common::hashEmptyField($cart_data, 'AdditionalData.project_id');

            if(!empty($booking_code) && !empty($product_unit_stock_id) && !empty($original_project_id)){
                $link_cart      = sprintf('transactions/get_cart/%s/%s', $booking_code, $product_unit_stock_id);

                $cart           = $this->RmCommon->getAPI($link_cart, array(
                    'header' => array(
                        'slug' => 'primedev-api',
                        'data' => array(
                            'project' => $original_project_id,
                        ),
                    ),
                ));

                $cart_data = Common::hashEmptyField($cart, 'data');

                if(!empty($cart) && !empty($cart_data)){
                    $is_expired = Common::hashEmptyField($cart, 'Booking.is_expired');

                    if(!empty($is_expired)){
                        $this->destroySessionCartID(true);
                    }
                }else{
                    $this->destroySessionCartID(true);
                }
            }
        }
    }

    function urlBackUnit(){
        $cart_data = $this->Session->read($this->session_cart_name);

        if(!empty($cart_data)){
            $product_id        = Common::hashEmptyField($cart_data, 'BookingDetail.product_id');
            $product_unit_id   = Common::hashEmptyField($cart_data, 'BookingDetail.product_unit_id');

            // $product_id         = $this->getOriginalProductID($original_product_id, true);
            // $product_unit_id    = $this->getOriginalProductUnitID($original_product_unit_id, true);

            $url = array(
                'controller' => 'pages',
                'action' => 'detail_unit',
                $product_unit_id,
                'product' => $product_id,
                'admin' => false
            );
        }else{
            $url = array(
                'controller' => 'pages',
                'action' => 'developers',
                'admin' => false
            );
        }

        return $url;
    }

    function checkAllowSelling($project_id){
        $allow_selling = $this->RmCommon->getAPI('transactions/check_allow_selling', array(
            'header' => array(
                'slug' => 'primedev-api',
                'data' => array(
                    'project' => $project_id,
                ),
            ),
        ));

        return Common::hashEmptyField($allow_selling, 'status', 0);
    }
}
?>