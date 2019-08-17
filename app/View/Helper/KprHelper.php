<?php
class KprHelper extends AppHelper {
    var $helpers = array(
        'Rumahku', 'Html', 'Crm'
    );

    function _getBungaKPRPersen ( $bunga_kpr = false ) {
        $bunga_kpr = !empty($bunga_kpr)?$bunga_kpr:Configure::read('__Site.bunga_kpr');
        return ( 100 - $bunga_kpr ) / 100;
    }

    function calcLoan ( $price, $bunga_kpr = false, $down_payment = 0 ) {
        // if(!empty($down_payment)){
            return ($price - $down_payment);
        // }
        // else{
        //    $bunga_kpr_persen = $this->_getBungaKPRPersen( $bunga_kpr );
        //     return $price * $bunga_kpr_persen; 
        // }
        
    }

    function setLoan($price,$down_payment){
        return $price - $down_payment ;
    }

    function _setPercentDp($price, $down_payment){
        return @(round(($down_payment / $price) * 100,2));
    }

    function creditFix($amount, $rate, $year=20){
        if( empty($rate) ){
            return 0;
        } else {

            if( $rate != 0 ) {
                $rate = ($rate/100)/12;
            }
            $rateYear = pow((1+$rate), ($year*12));
            $rateMin = (pow((1+$rate), ($year*12))-1);

            if( $rateMin != 0 ) {
                $rateYear = $rateYear / $rateMin;
            }

            $mortgage = $rateYear * $amount * $rate; // rumus angsuran fix baru 
            return $mortgage;
        }
    }

    function _callCalcDp ( $price, $rate, $down_payment = false) {
        if(!empty($down_payment)){
            return ($down_payment);
        }else{
            return $price * ($rate / 100 );        
        }
    }

    function getNotif ( $data, $class = 'error-full alert' ) {
        $approved = $this->Rumahku->filterEmptyField($data, 'KprBank', 'approved');
        if( !empty($approved) ) {
            $msg = __('Aplikasi Anda telah disetujui bank, klik tombol "Proses" untuk menandakan bahwa Klien telah setuju untuk diproses Akad Kredit');
        }else{
            if(!empty($data[0])){
                    $name = Set::classicExtract($data, '{n}.Bank.name');
                    /*debug($name);die();*/
                    // $result = implode()

            }
        }

        if( !empty($msg) ) {
            return $this->Html->tag('div', $this->Html->tag('p', $msg), array(
                'class' => $class,
            ));
        } else {
            return false;
        }
    }

    function _getStatus ( $status ) {
        $color = false;

         // label-name list after for-project
        switch ($status) {
            case 'completed':
                $label = __('Completed');
                $color = 'for-project label green mr5 mb0';
                $color_code = 'rgba(8, 101, 56, 1)';
                break;
            case 'reschedule_pk':
                $label = __('Reschedule Akad');
                $color = 'for-project label blue-light mr5 mb0';
                $color_code = 'rgba(13, 245, 65, 1)';
                break;
            case 'approved_credit':
                $label = __('Akad Disetujui');
                $color = 'for-project label purple mr5 mb0';
                $color_code = 'rgba(13, 245, 65, 1)';
                break;
            case 'credit_process':
                $label = __('Proses Akad');
                $color = 'for-project label blue-dark mr5 mb0';
                $color_code = 'rgba(13, 245, 65, 1)';
                break;
            case 'approved':
                $label = __('Appraisal');
                $color = 'for-project label blue-dark mr5 mb0';
                $color_code = 'rgba(13, 245, 65, 1)';
                break;
            case 'rejected':
                $label = __('Ditolak');
                $color = 'for-project label red mr5 mb0';
                $color_code = 'rgba(195, 15, 15, 1)';
                break;
            // case 'proposal_without_comiission':
            //     $label = __('Refferal disetujui & Provisi ditolak');
            //     $color = 'for-project label orange-dark mr5 mb0';
            //     $color_code = 'rgba(247, 134, 13, 1)';
            //     break;
            // case 'approved_proposal':
            //     $label = __('Referral Disetujui');
            //     $color = 'for-project label blue mr5 mb0';
            //     $color_code = 'rgba(0, 0, 255, 0.5)';
            //     break;

            case 'process':
                $label = __('Proses');
                $color = 'for-project label orange mr5 mb0';
                $color_code = 'rgba(247, 134, 13, 1)';
                break;

            case 'cancel':
                $label = __('Cancel');
                $color = 'for-project label red mr5 mb0';
                $color_code = 'rgba(27, 30, 32, 1)';
                break;

            default:
                $label = __('Pending');
                $color = 'for-project label grey mr5 mb0';
                $color_code = 'rgba(102, 102, 102, 1)';
                break;
        }

        return array(
            'label' => $label,
            'color' => $color,
            'color_code' => $color_code,
        );
    }

    function getStatusKomisi($data,$tagHtml = false){

        $paid_fee_approved  = $this->Rumahku->filterEmptyField($data,'paid_fee_approved');
        $paid_fee_rejected  = $this->Rumahku->filterEmptyField($data,'paid_fee_rejected');

        if(!empty($paid_fee_approved)){
            $label = __('dibayarkan');
            $color = 'label green for-project';

        }else if(!empty($paid_fee_rejected)){
            $label = __('dibatalkan');
            $color = 'label red for-project';

        }else{
            $label = __('pending');
            $color = 'label grey for-project';
        }

        if($tagHtml){
            $label = !empty($label)?$label:false;
            $color = !empty($color)?$color:false;

            return $this->Html->tag('span', $label, array(
                'class' => $color,
            ));
        }else{
            $status = !empty($label)?$label:false;
            return $status;
        }

    }

    function _callStatus ( $data, $tagHtml = false ) {
        $status = $this->Rumahku->filterEmptyField($data, 'Kpr', 'document_status');
        $result = $this->_getStatus( $status );

        if( !empty($tagHtml) ) {
            $label = !empty($result['label'])?$result['label']:false;
            $color = !empty($result['color'])?$result['color']:false;

            return $this->Html->tag('span', $label, array(
                'class' => $color,
            ));
        } else {
            return $result;
        }
    }

    function getPercent($value = 0, $optionPrices = array(), $params = array()){
        $price = $this->Rumahku->filterEmptyField($optionPrices, 'price',false, 0);
        $loan_price = $this->Rumahku->filterEmptyField($optionPrices, 'loan_price', false, 0);
        $category = $this->Rumahku->filterEmptyField($params, 'category');
        $param = $this->Rumahku->filterEmptyField($params, 'params');
        $return_value = $this->Rumahku->filterEmptyField($params, 'return_value');
        $parameter = ($param == 'price')?$price:$loan_price;

       if($category == 'percent'){
            $result = @(round(($value/100)*$parameter));

            if(!empty($return_value)){

                if($return_value == 'nominal'){
                    return $result;
                }else if($return_value == 'percent'){
                    return $value;

                }

            }else{
                return array(
                    'nominal' => $result,
                    'percent' => $value,
                );    
            }
        }else{
            $result = @(round(($value/$parameter)*100, 2));

            if(!empty($return_value)){
                if($return_value == 'nominal'){
                    return $value;
                }else{
                    return $result;
                }    
            }else{
                return array(
                    'nominal' => $value,
                    'percent' => $result,
                );    
            }
        } 
        
    }

	public function getNotary($data, $params = array(), $options = array()){
		$options	= (array) $options;
		$model		= Common::hashEmptyField($options, 'model', 'BankSetting');
		$returnType	= Common::hashEmptyField($options, 'return_type', 'total');
		$returnType	= in_array($returnType, array('detail', 'total')) ? $returnType : 'total';

		$price			= Common::hashEmptyField($params, 'price', 0);
		$loanPrice		= Common::hashEmptyField($params, 'loan_price', 0);
		$totalAmount	= 0;
		$fieldNames		= array(
			'sale_purchase_certificate'	=> 'Akte jual beli', 
			'transfer_title_charge'		=> 'Bea balik nama', 
			'credit_agreement'			=> 'Akta Perjanjian Kredit', 
			'letter_mortgage'			=> 'Akta SKMHT', 
			'imposition_act_mortgage'	=> 'Akta APHT', 
			'mortgage'					=> 'Perjanjian HT', 
			'other_certificate'			=> 'Cek Sertifikat, ZNT, PNPB HT', 
		);

		foreach($fieldNames as $fieldName => $fieldLabel){
			$category	= Common::hashEmptyField($data, sprintf('%s.category_%s', $model, $fieldName));
			$param		= Common::hashEmptyField($data, sprintf('%s.param_%s', $model, $fieldName));
			$amount		= Common::hashEmptyField($data, sprintf('%s.%s', $model, $fieldName), 0);

			$chargeAmount	= $param == 'price' ? $price : $loanPrice;
			$chargeAmount	= $this->callGenerateNominal($chargeAmount, $amount, $category);
			$totalAmount	= $totalAmount + $chargeAmount;

			$fieldNames[$fieldName] = array(
				'label'	=> $fieldLabel, 
				'value'	=> floatval($chargeAmount), 
			);
		}

		if($returnType == 'total'){
			return floatval($totalAmount) > 0 ? $totalAmount : null;
		}
		else{
			return $fieldNames;
		}
	}

    function _setCalcNotary($value, $prices = array(), $model){
        if(!empty($model)){
            $total_notary = null;
            foreach($model AS $key => $modelName){
                $total = KprCommon::_callCalcNotary($value, $prices, $modelName);
                $total_notary = isset($total)?$total:$total_notary;
            }
        }
        return $total_notary;
    }

    function callGenerateNominal($price = false, $value = false, $category = false){
        if(!empty($value) && !empty($category)){
            if($category == 'percent'){
                $value = floatval(( $value / 100 ) * $price);
            }
        }
        return $value;
    }

    function _callGenerateNominal($value = array(), $prices = array(), $options = array()){
        $price = $this->Rumahku->filterEmptyField($prices, 'price');
        $loan_price = $this->Rumahku->filterEmptyField($prices, 'loan_price',false, 0, true);
        $model = $this->Rumahku->filterEmptyField($options, 'modelName');
        $fieldName = $this->Rumahku->filterEmptyField($options, 'fieldName');
        $val = 0;
        if(!empty($model)){
            foreach($model AS $key => $modelName){

                $category = sprintf('category_%s', $fieldName);
                $params = sprintf('param_%s', $fieldName);
                $val_category = $this->Rumahku->filterEmptyField($value, $modelName, $category);
                $val_params = $this->Rumahku->filterEmptyField($value, $modelName, $params);
                $val = $this->Rumahku->filterIssetField($value, $modelName, $fieldName, $val);

                // if($fieldName == 'appraisal' && $modelName == 'BankSetting'){debug($val);die();}
                if($val_params == 'price'){
                    // if($fieldName == 'appraisal'){debug($val);die();}
                    $val = $this->callGenerateNominal($price, $val, $val_category); 
                }else{
                    $val = $this->callGenerateNominal($loan_price, $val, $val_category); 
                }
            }
        }

        return $val;
    }

    function getReason( $data, $val){
        $result = array();

        $dp = $this->Rumahku->filterEmptyField( $data, 'Kpr', 'dp');
        $credit_total = $this->Rumahku->filterEmptyField( $data, 'Kpr', 'credit_total');

        $periode_installment = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'periode_installment', 0);
        $dp_bank = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'dp'); 
        $i = 0;
        if( $dp_bank > $dp){
            $result[] = __('Uang muka yang diajukan tidak sesuai dengan kriteria dan persyaratan. %s yang dapat diajukan adalah %s%', $this->Html->tag('strong',  __('Minimum Uang Muka')), $this->Html->tag('strong',  $dp_bank));
            // $i++;
        }
        if( $periode_installment < $credit_total){
            $result[] = __('Lama pinjaman yang diajukan tidak sesuai dengan kriteria dan persyaratan. %s yang dapat diajukan adalah %s tahun', $this->Html->tag('strong',  __('Maksimal lama pinjaman')), $this->Html->tag('strong',  $periode_installment));
        }

        return $result;
    }

    function getNotice1($value){
        $result = array();
        
        $id = Common::hashEmptyField($value, 'KprBank.id');
        $from_kpr = Common::hashEmptyField($value, 'KprBank.from_kpr');
        $bank_name = Common::hashEmptyField($value, 'Bank.name');
        
        $overlay = $url = $action_bottom = $color = false;
        $classRemove = '.overlay-grey,.kpr-notice-skip';
        $urlDefault = array(
            'controller' => 'kpr',
            'action' => 'notice_toggle',
            $id,
            'admin' => true,
        );

        if($from_kpr){
            $result = $this->dataNotice($value);
            $notice_type = $this->Rumahku->filterEmptyField($result, 'notice_type');
            $url_arr = $this->Rumahku->filterEmptyField($result, 'url_arr');
            $color = $this->Rumahku->filterEmptyField($result, 'color');
            $wrapper = $this->Rumahku->filterEmptyField($result, 'wrapper', false, false, false);

            $cookie_name = sprintf('Kpr.Notice.%s.%s', $notice_type, $id);
            $cookie = Configure::read('Cookie.Helper');
            $read = $cookie->read($cookie_name);

            $kpr_bank_installments = $this->Rumahku->filterEmptyField($value, 'KprBank', 'KprBankInstallment');
            $kpr_bank_installment = array_pop($kpr_bank_installments);

            if($url_arr){
                $kpr_id = $this->Rumahku->filterEmptyField($value, 'Kpr', 'id');

                foreach($url_arr AS  $key => $slug_url){
                    switch ($slug_url) {
                        case 'application_form':
                            if($key > 0){
                                $classes = 'default';
                            }else{
                                $classes = 'blue';
                            }
                            $url .= $this->Html->link(__('Edit Aplikasi'), array(
                                'controller' => 'kpr',
                                'action' => 'application',
                                $kpr_id,
                                $id,
                                'admin' => true,
                            ), array(
                                'class' => sprintf('btn %s', $classes),
                                'title' => __('Edit Aplikasi'),
                            ));
                            break;

                        case 'forward':
                            $url .= $this->Html->link(__('Lanjutkan'), array(
                                'controller' => 'kpr',
                                'action' => 'foward_application',
                                $id,
                                'admin' => true,
                            ), array(
                                'class' => 'btn blue ajaxModal',
                                'data-size' => 'mid-size',
                                'title' => __('Anda bisa memberikan keterangan tambahan'),
                            ));
                            break;

                        case 'view_bank':
                            // $url .= $this->Html->link(__('Lihat Bank'), '#list-bank-kpr', array(
                            //     'class' => 'btn blue scrollto ajax-link',
                            // ));
                            break;
                        case 'view_application':
                            $url .= $this->Html->link(__('Lihat Aplikasi'), '#application-detail', array(
                                'class' => 'btn default scrollto ajax-link',
                                'data-url' => $this->Html->url(array_merge($urlDefault, array(
                                    $notice_type,
                                ))),
                                'data-remove' => $classRemove,
                            ));
                            break;
                        case 'resend':
                            $url .= $this->Html->link(__('Setujui'), array(
                                'controller' => 'kpr',
                                'action' => 'resend_application',
                                $id,
                                'admin' => true,
                            ),array(
                                'class' => 'btn blue',
                            ), __('Anda yakin ingin melanjutkan proses KPR ini, sudah dijelaskan untuk proses KPR ini anda tidak mendapatkan provisi ?'));
                            break;
                        case 'delete':
                            $url .= $this->Html->link(__('Batalkan'), array(
                                'controller' => 'kpr',
                                'action' => 'delete_kpr',
                                $id,
                                'admin' => true,
                            ), array(
                                'class' => 'btn red ajaxModal',
                                'title' => __('Batalkan Pengajuan KPR'),
                            ));
                            break;
                        case 'rejected':
                            $url .= $this->Html->link(__('Batalkan'), array(
                                'controller' => 'kpr',
                                'action' => 'delete_kpr',
                                $id,
                                'admin' => true,
                            ), array(
                                'class' => 'btn red ajaxModal',
                                'title' => __('Batalkan Pengajuan KPR'),
                            ));
                            break;
                        case 'approved_verification':
                            $title_akad = $button_akad = __('Proses Appraisal');

                            $url .= $this->Html->link( $button_akad, array(
                                'controller' => 'kpr',
                                'action' => 'process_appraisal',
                                $id,
                                $slug_url,
                                'admin' => true,
                            ),array(
                                'class' => 'btn green ajaxModal',
                                'title' => $title_akad,
                                'alert' => __('Dengan menyetujui proses appraisal, maka semua pengajuan atas properti ini akan kami HOLD, Anda yakin ingin melanjutkan proses menggunakan Bank ini?'),
                            ));
                            break;

                        case 'process_kredit':
                            // $label_akad = __('Bank yang telah dipilih tidak dapat dibatalkan, dan semua pengajuan atas properti ini akan kami batalkan/cancel, Anda yakin ingin melanjutkan proses menggunakan %s ini?', $bank_name);
                            $title_akad = __('Informasi Pembayaran Provisi');
                            $button_akad = __('Setting Appointment');
                            $commission = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'commission');

                            if(!empty($commission) && is_numeric($commission)){
                                $url .= $this->Html->link( $button_akad, array(
                                    'controller' => 'kpr',
                                    'action' => 'update_kpr',
                                    $id,
                                    'status_confirm' => TRUE,
                                    'admin' => true,
                                ),array(
                                    'class' => 'btn green ajaxModal',
                                    'title' => $title_akad,
                                ));
                            }else{
                                $url .= $this->Html->link( $button_akad, array(
                                    'controller' => 'kpr',
                                    'action' => 'update_kpr_non_komisi',
                                    $id,
                                    'status_confirm' => TRUE,
                                    'admin' => true,
                                ),array(
                                    'class' => 'btn green',
                                    'title' => $title_akad,
                                ));
                            }
                            break;
                        
                        case 'back':
                            $url .= $this->Html->link(__('Kembali'), array(
                                'controller' => 'kpr',
                                'action' => 'index',
                                'admin' => true,
                            ), array(
                                'class' => 'btn default',
                                'escape' => false,
                            ));
                            break;
                        case 'completed':
                            $url .= $this->Html->link( 'Setujui Akad', array(
                                'controller' => 'kpr',
                                'action' => 'completed',
                                $id,
                                'admin' => true,
                            ),array(
                                'class' => 'btn green ajaxModal',
                                'title' => __('Confirm akad / setujui akad'),
                            ));
                            break;
                    }
                }
            }

            if( empty($read) ) {
                $overlay = $this->Html->tag('div', '', array(
                    'class' => 'overlay-grey',
                ));

                $url .= $this->Html->link(__('Lewati'), array_merge($urlDefault, array(
                    $notice_type,
                )), array(
                    'class' => 'btn ajax-link kpr-notice-skip',
                    'data-remove' => $classRemove,
                ));
            }

            if( !empty($url) ) {
                $action_bottom = $this->Html->tag('div', $url, array(
                    'class' => 'action-button',
                ));
            }

            $result = $this->Html->tag('div', $wrapper, array(
                'class' => 'wrapper-alert',
            )).$action_bottom;
              
            return $this->Html->tag('div', $result, array(
                'class' => sprintf('crm-tips kpr-alert %s', $color),
            )).$overlay;
        }
    }

    function dataNotice($value){
        $url_arr = array();
        $is_admin = Configure::read('User.Admin.Rumahku');
        $site_name = Configure::read('__Site.site_name');
        $site_name = ucwords(strtolower($site_name));

        $wrapper = $notice_type = $overlay = $color = $exlude = $additionalNote = false;
        $kpr_bank_id = $this->Rumahku->filterEmptyField($value, 'KprBank', 'id');
        $bank_name = $this->Rumahku->filterEmptyField($value, 'Bank', 'name');
        $document_status = $this->Rumahku->filterEmptyField($value, 'KprBank', 'document_status');
        $from_kpr = $this->Rumahku->filterEmptyField($value, 'KprBank', 'from_kpr');
        $forward_app = $this->Rumahku->filterEmptyField($value, 'KprBank', 'forward_app');
        $application_snyc = $this->Rumahku->filterEmptyField($value, 'KprBank', 'application_snyc');
        $is_hold = $this->Rumahku->filterEmptyField($value, 'KprBank', 'is_hold');
        $noted = $this->Rumahku->filterEmptyField($value, 'KprBank', 'noted');
        $request_pk = $this->Rumahku->filterEmptyField($value, 'KprBank', 'request_pk');
        $reschedule_pk = $this->Rumahku->filterEmptyField($value, 'KprBank', 'reschedule_pk');
        $confirm_pk = $this->Rumahku->filterEmptyField($value, 'KprBank', 'confirm_pk');
        $from_web = $this->Rumahku->filterEmptyField($value, 'Kpr', 'from_web');
        $unpaid_agent = $this->Rumahku->filterEmptyField($value, 'Kpr', 'unpaid_agent');
        $application_status = $this->Rumahku->filterEmptyField($value, 'KprBank', 'application_status');
        $domain = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'domain');
        $notice_type = $document_status;

        if( !empty($is_hold) ) {
            $wrapper = $this->Html->tag('label', __('HOLD Pengajuan KPR'));
            $wrapper .= $this->Html->tag('p', __('Pengajuan KPR di HOLD sementara hingga proses Akad Kredit selesai diproses oleh bank lain. '));
            $notice_type = 'hold';

            $url_arr[] = 'back';
            $color = 'red';
        } else if($document_status){
            $textTips = $this->Html->tag('label', __('Tips Pengajuan KPR'));
            $textInfo = $this->Html->tag('label', __('Pemberitahuan'));

            switch ($document_status) {
                case 'process':
                    $infoBank = $this->Html->tag('span', __('mohon menunggu konfirmasi dari pihak %s', $bank_name));

                    switch ($from_kpr) {
                        case 'frontend':
                            if($forward_app){
                                $wrapper = $textTips;
                                $wrapper .= $this->Html->tag('p', sprintf(__('Pengajuan KPR Anda telah Kami kirimkan ke %s, %s. '), $bank_name, $infoBank));
                                $url_arr[] = 'view_bank'; 
                            }else{
                                $url_arr[] = $notice_type = 'forward';
                                $url_arr[] = 'delete';
                                $client_name = $this->Rumahku->filterEmptyField($value, 'Kpr', 'client_name');
                                $wrapper = $textTips;
                                // $wrapper .= $this->Html->tag('p', __('Pengajuan KPR oleh klien %s.', $this->Html->tag('strong', $client_name)));
                                $wrapper .= $this->Html->tag('p', __('Anda mendapatkan pengajuan KPR oleh %s melalui detail properti %s', $this->Html->tag('strong', $client_name), $domain));
                                $wrapper .= $this->Html->tag('p', __('Klik lanjutkan untuk memproses pengajuan ini.'));
                            }
                            break;
                        case 'backend':
                            $url_arr[] = $notice_type = 'application_form';
                            $additionalNote = true;
                            $wrapper = $textTips;
                            $wrapper .= $this->Html->tag('p', sprintf(__('Pengajuan KPR Anda telah Kami terima, %s. '), $infoBank));
                            
                            break;
                    }
                    break;
                case 'approved_proposal':
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Aplikasi KPR Anda telah diterima Bank, harap menunggu hingga proses KPR selesai'));
                    $url_arr[] = 'view_bank';
                    break;
                case 'rejected_proposal':
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Mohon maaf Referral KPR anda telah ditolak oleh %s.', $bank_name));
                    $url_arr[] = 'back';
                    $color = 'red';
                    break;
                
                case 'proposal_without_comiission':
                    if(!empty($application_snyc)){
                        $notice_type = 'document_resend_true';
                        $url_arr[] = 'view_bank';
                        $wrapper .= $this->Html->tag('p',__('Terimakasih, anda sudah melakukan pengiriman ulang aplikasi KPR, harap menunggu pihak %s untuk proses KPR.', $bank_name));
                    }else{
                        $notice_type = 'document_resend';
                        $url_arr[] = 'resend';
                        $url_arr[] = 'delete';

                        $wrapper = $this->Html->tag('label',__('Referral diterima & Provisi ditolak'));
                        $wrapper .= $this->Html->tag('p', __('Harap lakukan pengiriman ulang dengan klik tombol dibawah ini, dan anda bisa membatalkan aplikasi KPR ini.'));
                    }
                    break;

                case 'cancel':
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Anda telah membatalkan pengajuan KPR'));
                    $color = 'red';
                    $url_arr[] = 'back';
                    break;

                case 'approved_bank':
                    $label = $this->Html->tag('b', __('Setting Appointment'));
                    $notice_type = 'process_akad_credit';
                    $kpr_bank_date = $this->Rumahku->filterEmptyField($value, 'KprBank', $document_status);

                    $action_date = $this->Rumahku->filterEmptyField($kpr_bank_date, 'KprBankDate', 'action_date');

                    $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                        'time' => 'H:i',
                        'zone' => false,
                    ));

                    $wrapper = $this->Html->tag('label', __('Tips Proses Akad Kredit'));
                    $wrapper .= $this->Html->tag('p', __('Selamat Aplikasi KPR Anda %s oleh %s pada tanggal %s.', $this->Html->tag('strong', 'telah disetujui'), $this->Html->tag('strong', $bank_name), $this->Html->tag('strong', $actionDateCustom)));
                    $wrapper .= $this->Html->tag('p', __('Anda dapat melanjutkan Proses Akad Kredit, dengan menekan tombol %s', $this->Html->tag('strong', $label)));
                    $url_arr[] = 'process_kredit';
                    $url_arr[] = 'delete';
                    $url_arr[] = 'view_bank';
                    $exlude = array(
                        'date' => true,
                    );
                    break;

                case 'rejected_bank':
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Mohon maaf %s telah menolak aplikasi KPR anda.', $bank_name));
                    $color = 'red';
                    $url_arr[] = 'back';
                    break;

                case 'rejected_bi_checking':
                    $wrapper = $this->Html->tag('label', __('Tidak Lulus BI Checking'));
                    $wrapper .= $this->Html->tag('p', __('Mohon maaf aplikasi KPR anda tidak lulus BI checking.'));
                    $color = 'red';
                    $url_arr[] = 'back';
                    break;

                case 'rejected_verification':
                    $wrapper = $this->Html->tag('label', __('Tidak Lulus Verifikasi Dokumen'));
                    $wrapper .= $this->Html->tag('p', __('Mohon maaf aplikasi KPR anda tidak lulus Verifikasi Dokumen.'));
                    $color = 'red';
                    $url_arr[] = 'back';
                    break;

                case 'credit_process':
                    $label       = $this->Html->tag('strong', $bank_name);
                    $notice_type = 'pending_akad_credit';
                    $url_arr[]   = 'view_bank';

                    $wrapper = $this->Html->tag('label', __('Menunggu Akad Kredit'));
                    $wrapper .= $this->Html->tag('p', __('Anda telah memilih %s untuk proses akad kredit.', $bank_name));
                    $wrapper .= $this->Html->tag('p', __('Kami akan mengirimkan %s apabila %s telah menentukan jadwal dan lokasi Akad Kredit', $this->Html->tag('strong', __('notifikasi via Email')), $bank_name));
                    
                    if(!empty($value['KprBankTransfer'])){
                        $ulContent = false;
                        $name_account = $this->Rumahku->filterEmptyField($value, 'KprBankTransfer', 'name_account');
                        $no_account = $this->Rumahku->filterEmptyField($value, 'KprBankTransfer', 'no_account');
                        $bank_account = $this->Rumahku->filterEmptyField($value, 'KprBankTransfer', 'bank_name');
                        $no_npwp = $this->Rumahku->filterEmptyField($value, 'KprBankTransfer', 'no_npwp');

                        $transfer = $this->Html->tag('p', __('Informasi klaim pembayaran provisi:'));

                        if($bank_account){
                            $ulContent .= $this->Html->tag('li', sprintf(__('Nama bank : %s'), $this->Html->tag('strong', $bank_account)));
                        }

                        if($name_account){
                            $ulContent .= $this->Html->tag('li', sprintf(__('Nama Pemilik Rekening : %s'), $this->Html->tag('strong', $name_account)));
                        }

                        if($no_account){
                            $ulContent .= $this->Html->tag('li', sprintf(__('No. Rekening : %s'), $this->Html->tag('strong', $no_account)));
                        }

                        if($no_npwp){
                            $ulContent .= $this->Html->tag('li', sprintf(__('NPWP : %s'), $this->Html->tag('strong', $no_npwp)));
                        }

                        if($ulContent){
                            $wrapper .= $this->Html->tag('div', $transfer.$this->Html->tag('ul', $ulContent), array(
                                'class' => 'mt15 mb15',
                            ));
                        }

                    }

                    // request jadwal info box
                    $request_pk  = Common::hashEmptyField($value, 'KprBank.request_pk');
                    $note_req_pk = Common::hashEmptyField($value, 'KprBank.request_pk.KprBankDate.note');
                    $action_date = Common::hashEmptyField($value, 'KprBank.request_pk.KprBankDate.action_date');

                    $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                        'time' => 'H:i',
                        'zone' => false,
                    ));

                    if (!empty($request_pk)) {
                        $liContent = false;
                        $info_request_pk = __('Berhasil, sistem kami telah mencatat informasi request jadwal Akad Kredit.');

                        $content_request = $this->Html->tag('p', $info_request_pk);
                        $content_request .= $this->Html->tag('p', __('Berikut adalah informasi jadwal yang Anda request:'));

                        if($action_date){
                            $liContent .= $this->Html->tag('li', sprintf(__('Tanggal Akad : %s'), $this->Html->tag('strong', $actionDateCustom)));
                        }

                        if($note_req_pk){
                            $liContent .= $this->Html->tag('li', sprintf(__('Keterangan : %s'), $this->Html->tag('strong', $note_req_pk)));
                        }

                        if($liContent){
                            $content_request .= $this->Html->tag('div', $this->Html->tag('ul', $liContent), array(
                                'class' => 'mt15 mb15',
                            ));
                        }

                    } else {
                        $url_button = $this->Html->link('Atur Jadwal Akad', array(
                            'controller' => 'kpr',
                            'action'     => 'reschedule_pk',
                            $kpr_bank_id,
                            'type'       => 'request_pk',
                            'admin'      => true,
                        ), array(
                            'class' => 'btn orange button-reschedule ajaxModal',
                            'title' => __('Atur Jadwal Akad'),
                        ));

                        $content_request = __('Jika anda ingin melakukan request jadwal Akad Kredit silahkan klik tombol berikut %s', $url_button);

                    }

                    $wrapper .= $this->Html->tag('div', $content_request, array(
                        'class' => 'wrap-reschedule-pk'
                    ));

                    break;

                case 'rejected_credit':
                    $label = $this->Html->tag('strong', $bank_name);
                    $url_arr[] = 'back';
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Mohon maaf, proses akad kredit anda ditolak oleh %s.', $label));
                    $wrapper .= $this->Html->tag('p', __('silakan hubungi bank terkait jika anda ingin mengetahui lebih jauh, dan kami sudah mengirimkan notifikasi via Email'));
                    $color = 'red';
                    break;

                case 'approved_bi_checking':
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Aplikasi KPR Anda telah %s, harap menunggu hingga proses KPR selesai', $this->Html->tag('strong', __('Lulus BI Checking'))));
                    $url_arr[] = 'view_bank';
                    break;

                case 'approved_verification':
                    $label = $this->Html->tag('b', __('Proses Appraisal'));
                    $kpr_bank_date = $this->Rumahku->filterEmptyField($value, 'KprBank', $document_status);

                    $action_date = $this->Rumahku->filterEmptyField($kpr_bank_date, 'KprBankDate', 'action_date');

                    $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                        'time' => 'H:i',
                        'zone' => false,
                    ));

                    $wrapper = $this->Html->tag('label', __('Tips Proses Appraisal'));
                    $wrapper .= $this->Html->tag('p', __('Bank telah menyatakan %s, untuk selanjutnya dilakukan proses appraisal.', $this->Html->tag('strong', __('Lulus Verifikasi Dokumen'))));
                    $wrapper .= $this->Html->tag('p', __('Anda dapat melanjutkan proses ini, dengan menekan tombol %s', $this->Html->tag('strong', '"Proses Appraisal"')));
                    $url_arr[] = 'approved_verification';
                    $url_arr[] = 'delete';
                    $url_arr[] = 'view_bank';
                    break;

                case 'process_appraisal':
                    $wrapper = $textInfo;
                    $wrapper .= $this->Html->tag('p', __('Aplikasi KPR dalam tahap appraisal, harap menunggu konfirmasi dari Bank'));
                    $url_arr[] = 'view_bank';
                    break;
                
                case 'approved_credit':
                case 'completed': {
                        $credit_date = Common::hashEmptyField($value, 'Kpr.cnt_data.set_akad.credit_date');
                        $contact_bank = Common::hashEmptyField($value, 'Kpr.cnt_data.set_akad.contact_bank');
                        $contact_name = Common::hashEmptyField($value, 'Kpr.cnt_data.set_akad.contact_name');
                        $contact_email = Common::hashEmptyField($value, 'Kpr.cnt_data.set_akad.contact_email');
                        $note = Common::hashEmptyField($value, 'Kpr.cnt_data.set_akad.note');

                        $notice_type = 'akad_credit';
                        $ulContent = null;

                        $note = $this->Rumahku->_callGetDescription($note, ', ');

                        $customCreditDate = $this->Html->tag('strong', $this->Rumahku->getIndoDateCutom( $credit_date, array(
                            'time' => 'H:i',
                            'zone' => false,
                        )));
                        $customLocation = $this->Html->tag('strong', $note);

                        if($document_status == 'approved_credit'){
                            $wrapper = $this->Html->tag('label', __('Selamat, akad kredit anda disetujui'));
                        }else{
                            $wrapper = $this->Html->tag('label', __('Completed'));
                        }

                        $wrapper .= $this->Html->tag('p', sprintf(__('Terima kasih telah menggunakan sistem KPR Kami, berikut jadwal dan lokasi Akad Kredit yang telah ditentukan oleh %s :'), $bank_name));

                        if(!empty($credit_date)){
                            $ulContent = $this->Html->tag('li', sprintf(__('Tanggal Akad : %s'), $customCreditDate));
                        }

                        if(!empty($note)){
                            $ulContent .= $this->Html->tag('li', sprintf(__('Lokasi : %s'), $customLocation));
                        }

                        if(!empty($contact_name)){
                            $customContactName = $this->Html->tag('strong', ucwords($contact_name));
                            $ulContent .= $this->Html->tag('li', sprintf(__('Bertemu dengan :  %s'), $customContactName));
                        }

                        if(!empty($contact_bank)){
                            $customContactBank = $this->Html->tag('strong', $contact_bank);
                            $ulContent .= $this->Html->tag('li', sprintf(__('Kontak : %s'), $customContactBank));
                        }

                        if(!empty($contact_email)){
                            $customContactEmail = $this->Html->tag('strong', ucwords($contact_email));
                            $ulContent .= $this->Html->tag('li', sprintf(__('Email :  %s'), $customContactEmail));
                        }
                        
                        if($ulContent){
                            $wrapper .= $this->Html->tag('ul', $ulContent);
                        }
                       
                        if((!empty($document_status) && $document_status <> 'completed')){

                            if (empty($reschedule_pk) && empty($confirm_pk)) {
                                // optional, reschedule PK info, hanya sekali mengajukan
                                $url_reschedule = $this->Html->link('Reschedule', array(
                                    'controller' => 'kpr',
                                    'action'     => 'reschedule_pk',
                                    $kpr_bank_id,
                                    'admin'      => true,
                                ), array(
                                    'class' => 'btn orange button-reschedule ajaxModal',
                                    'title' => __('Reschedule Akad'),
                                ));

                                $content_reschedule = __('%s, Jika anda ingin mengajukan reschedule silahkan klik tombol berikut %s', $this->Html->tag('strong', __('Optional')), $url_reschedule);

                                $wrapper .= $this->Html->tag('div', $content_reschedule, array(
                                    'class' => 'wrap-reschedule-pk'
                                ));

                            }

                            $text = __('klik tombol %s dibawah ini :', $this->Html->tag('strong', __('Setujui Akad')));

                            if (!empty($confirm_pk)) {
                                $wrapper .= $this->Html->tag('p', sprintf(__('Anda telah menyetujui Akad Kredit. Selanjutnya menunggu dari pihak bank untuk menyelesaikan proses Akad.')), array(
                                    'class' => 'mt5'
                                ));

                            } else if($unpaid_agent == 'pending'){
                                $wrapper .= $this->Html->tag('p', sprintf(__('Jika anda sudah setuju dengan jadwal tersebut, silakan anda %s'), $text), array(
                                    'class' => 'mt5'
                                ));

                            } else {
                                $wrapper .= $this->Html->tag('p', sprintf(__('Anda dapat %s'), $text), array(
                                    'class' => 'mt5'
                                ));
                            }

                            if (empty($confirm_pk)) {
                                $url_arr[] = 'completed';
                            } else {
                                $url_arr[] = 'confirm_pk';                                
                            }

                        }else{
                            $unpaid_agent = $this->Rumahku->filterEmptyField($value, 'Kpr', 'unpaid_agent');
                            $unpaid_rumahku = $this->Rumahku->filterEmptyField($value, 'Kpr', 'unpaid_rumahku');
                            $kpr_bank_installments = $this->Rumahku->filterEmptyField($value, 'KprBank', 'KprBankInstallment');
                            $kprBankInstallment = array_pop($kpr_bank_installments);
                            $status_confirm = $this->Rumahku->filterEmptyField($kprBankInstallment, 'KprBankInstallment', 'status_confirm');

                            $flag_admin = ($is_admin && in_array($unpaid_rumahku, array('pending', 'approved')));
                            $flag_agent = in_array($unpaid_agent, array('pending', 'approved'));
                            
                            if( ($flag_agent || $flag_admin) && !empty($status_confirm)){
                                $ulContent = false;
                            
                                $address = $this->Rumahku->filterEmptyField($value, 'Bank', 'address');
                                $phone_center = $this->Rumahku->filterEmptyField($value, 'Bank', 'phone_center');
                                $bankContacts = $this->Rumahku->filterEmptyField($value, 'BankContact');

                                $wrapper .= $this->Html->tag('p', __('Berikut informasi %s:', $bank_name), array(
                                    'class' => 'mt15'
                                ));

                                if($address){
                                    $ulContent .= $this->Html->tag('li', sprintf(__('Alamat :  %s'), $this->Html->tag('strong', $address)));
                                }

                                if($phone_center){
                                    $ulContent .= $this->Html->tag('li', sprintf(__('Telpon Pusat :  %s'), $this->Html->tag('strong', $phone_center)));
                                }

                                if($bankContacts){
                                    foreach ($bankContacts as $key => $bankContact) {
                                        $phone = $this->Rumahku->filterEmptyField($bankContact, 'BankContact', 'phone');

                                        if($phone){
                                            $ulContent .= $this->Html->tag('li', sprintf(__('Telpon %s :  %s'), (1+$key), $this->Html->tag('strong', $phone)));
                                        }
                                    }
                                }

                                if($ulContent){
                                    $wrapper .= $this->Html->tag('ul', $ulContent);
                                }

                                if($flag_agent){
                                    $commission = $this->Rumahku->filterEmptyField($kprBankInstallment, 'KprBankInstallment', 'commission');
                                    $commission = $this->Rumahku->getCurrencyPrice($commission);
                                    $provisi_status = ($unpaid_agent == 'pending') ? __('Belum dibayarkan') : __('Sudah dibayarkan');


                                    $wrapper .= $this->Html->tag('p', __('Provisi : %s (%s)', $this->Html->tag('strong', $commission), $provisi_status), array(
                                        'class' => 'mt15'
                                    ));

                                    if($unpaid_agent == 'approved'){
                                        $ulContent = false;
                                        $action_date = $this->Rumahku->filterEmptyField($kprBankInstallment, 'agentCommission', 'action_date');
                                        $note_reason = $this->Rumahku->filterEmptyField($kprBankInstallment, 'agentCommission', 'note_reason');

                                        if($action_date){
                                            $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                                                'time' => 'H:i',
                                                'zone' => false,
                                            ));

                                            $ulContent .= $this->Html->tag('li', sprintf(__('Tanggal bayar :  %s', $this->Html->tag('strong', $actionDateCustom))));
                                        }

                                        if($note_reason){
                                            $ulContent .= $this->Html->tag('li', sprintf(__('Keterangan :  %s', $this->Html->tag('strong', $note_reason))));
                                        }

                                        if($ulContent){
                                            $wrapper .= $this->Html->tag('ul', $ulContent);
                                        }
                                    }   
                                }

                                if($flag_admin){
                                    $commission = $this->Rumahku->filterEmptyField($kprBankInstallment, 'KprBankInstallment', 'commission_rumahku');
                                    $commission = $this->Rumahku->getCurrencyPrice($commission);
                                    $provisi_status = ($unpaid_rumahku == 'pending') ? __('Belum dibayarkan') : __('Sudah dibayarkan');

                                    $wrapper .= $this->Html->tag('p', __('Provisi %s : %s (%s)', $site_name, $this->Html->tag('strong', $commission), $provisi_status));

                                    if($unpaid_rumahku == 'approved'){
                                        $ulContent = false;
                                        $action_date = $this->Rumahku->filterEmptyField($kprBankInstallment, 'agentCommission', 'action_date');
                                        $note_reason = $this->Rumahku->filterEmptyField($kprBankInstallment, 'agentCommission', 'note_reason');

                                        if($action_date){
                                            $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                                                'time' => 'H:i',
                                                'zone' => false,
                                            ));

                                            $ulContent .= $this->Html->tag('li', sprintf(__('Tanggal bayar :  %s', $this->Html->tag('strong', $actionDateCustom))));
                                        }

                                        if($note_reason){
                                            $ulContent .= $this->Html->tag('li', sprintf(__('Keterangan :  %s', $this->Html->tag('strong', $note_reason))));
                                        }

                                        if($ulContent){
                                            $wrapper .= $this->Html->tag('ul', $ulContent);
                                        }
                                    }   
                                }
                            }
                        }
                        $url_arr[] = 'view_bank';
                        $no_kpr_date = true;
                    break;
                    }

                case 'reschedule_pk':
                    $info_reschedule = $info_request = false;
                    $label       = $this->Html->tag('strong', $bank_name);
                    $notice_type = 'pending_akad_credit';
                    $url_arr[]   = 'view_bank';
                    
                    if (!empty($request_pk)) {
                        $li_content_request = false;
                        $date_request = $this->Rumahku->filterEmptyField($request_pk, 'KprBankDate', 'action_date');
                        $note_request = $this->Rumahku->filterEmptyField($request_pk, 'KprBankDate', 'note');
                        $requestDateCustom = $this->Rumahku->getIndoDateCutom($date_request, array(
                            'time' => 'H:i',
                            'zone' => false,
                        ));

                        $intro_req_pk = $this->Html->tag('p', __('Sebelumnya Anda telah mengajukan/request jadwal Akad. Berikut adalah data atau informasi request jadwal Akad yang anda ajukan:'));

                        $li_content_request .= $this->Html->tag('li', sprintf(__('Tanggal Akad : %s'), $this->Html->tag('strong', $requestDateCustom)));

                        if (!empty($note_request)) {
                            $li_content_request .= $this->Html->tag('li', sprintf(__('Keterangan : %s'), $this->Html->tag('strong', $note_request)));
                        }

                        $ul_content_request = $this->Html->tag('ul', $li_content_request, array(
                            'class' => 'ul-reschedule',
                        ));

                        $info_request = $this->Html->tag('div', $intro_req_pk.$ul_content_request,array(
                            'class' => 'mt15 mb15 box-info-request',
                        ));

                    }

                    if (!empty($reschedule_pk)) {
                        $li_content_reschedule = false;
                        $action_date = $this->Rumahku->filterEmptyField($reschedule_pk, 'KprBankDate', 'action_date');
                        $note        = $this->Rumahku->filterEmptyField($reschedule_pk, 'KprBankDate', 'note');
                        $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                            'time' => 'H:i',
                            'zone' => false,
                        ));

                        $intro = $this->Html->tag('p', __('Informasi berikut adalah data reschedule yang anda ajukan:'));

                        $li_content_reschedule .= $this->Html->tag('li', sprintf(__('Tanggal Akad : %s'), $this->Html->tag('strong', $actionDateCustom)));

                        if (!empty($note)) {
                            $li_content_reschedule .= $this->Html->tag('li', sprintf(__('Keterangan : %s'), $this->Html->tag('strong', $note)));
                        }

                        $ul_content_reschedule = $this->Html->tag('ul', $li_content_reschedule, array(
                            'class' => 'ul-reschedule',
                        ));

                        $info_reschedule = $this->Html->tag('div', $intro.$ul_content_reschedule,array(
                            'class' => 'mt15 mb15 box-info-reschedule',
                        ));

                    }

                    $wrapper = $this->Html->tag('label', __('Reschedule Akad'));

                    // info box reschedule
                    $wrapper .= $this->Html->tag('span', __('Sistem kami telah berhasil mencatat informasi reschedule, selanjutnya mohon menunggu konfirmasi dari pihak %s.', $bank_name));
                    $wrapper .= $info_request;
                    $wrapper .= $info_reschedule;
                    $no_kpr_date = true;
                    break;
            }

            if(empty($no_kpr_date)){
                $wrapper .= $this->getInformation($value, array(
                    'exlude' => $exlude,
                ));
            }

            $flag = (in_array($document_status, array('process', 'pending', 'approved_admin')));

            if($application_status == 'pending' && !empty($flag)){
                    $wrapper .= $this->Html->tag('p', sprintf(__('Anda juga dapat melengkapi %s dengan mengklik button "Edit Aplikasi" dibawah ini: '), $this->Html->tag('strong', __('FORM APLIKASI KLIEN')))); 
                if(!in_array('application_form', $url_arr)){
                    $url_arr[] = 'application_form';
                }
            }else if( $application_status == 'completed' && !in_array($document_status, array( 'completed', 'cancel' )) ){
                if(!in_array('view_application', $url_arr)){
                    $url_arr[] = 'view_application';
                }
            }
        }

        return array(
            'wrapper' => $wrapper,
            'notice_type' => $notice_type,
            'url_arr' => $url_arr,
            'color' => $color,
        );
    }

    function getInformation($value, $options = array()){
        $result = false;
        $exlude_date = $this->Rumahku->filterEmptyField($options, 'exlude', 'date');
        $exlude_noted = $this->Rumahku->filterEmptyField($options, 'exlude', 'note');

        $document_status = $this->Rumahku->filterEmptyField($value, 'KprBank', 'document_status');
        $kpr_bank_date = $this->Rumahku->filterEmptyField($value, 'KprBank', $document_status);

        // get label
        switch ($document_status) {
            case 'approved_proposal':
                $label = __('Tgl Disetujui');
                break;

            case 'approved_bi_checking':
                $label = __('Tgl Disetujui');
                break;

            case 'approved_verification':
                $label = __('Tgl Verifikasi');
                break;

            case 'process_appraisal':
                $label = __('Tgl Diproses');
                break;

            case 'reschedule_pk':
                $label = __('Tgl Reschedule');
                break;
            
            default:
                $label = __('Tgl Pengajuan');
                break;
        }

        if($kpr_bank_date){
            $action_date = $this->Rumahku->filterEmptyField($kpr_bank_date, 'KprBankDate', 'action_date');
            $note = Common::hashEmptyField($kpr_bank_date, 'KprBankDate.note', null, array(
                'type' => 'EOL',
            ));

            $actionDateCustom = $this->Rumahku->getIndoDateCutom($action_date, array(
                'time' => 'H:i',
                'zone' => false,
            ));

            if( $document_status == 'approved_proposal' ){
                $sales_id = $this->Rumahku->filterEmptyField($value, 'BankUser', 'id');
                $sales_name = $this->Rumahku->filterEmptyField($value, 'BankUser', 'full_name');
                $result .= $this->Html->tag('p', __('%s: %s', $this->Html->tag('Strong', 'Marketing Bank'), $this->Html->link($sales_name, array(
                    'controller' => 'kpr',
                    'action' => 'sales',
                    $sales_id,
                ), array(
                    'target' => '_blank',
                ))));
            }
            if(empty($exlude_date)){
                $result .= $this->Html->tag('p', __('%s: %s', $this->Html->tag('Strong', $label), $actionDateCustom));
            }
            if( !empty($note) && empty($exlude_noted) ){
                $result .= $this->Html->tag('p', __('%s: %s', $this->Html->tag('Strong', 'Keterangan'), $note));
            }

            if( $document_status == 'approved_verification' ){
                $nomor_rekening = Common::hashEmptyField($value, 'KprBank.nomor_rekening');
                $name_nomor_rekening = Common::hashEmptyField($value, 'KprBank.name_nomor_rekening');

                $result .= $this->Html->tag('h3', $this->Html->tag('h4', __('INFORMASI KJPP'), array(
                    'class' => 'mt20'
                )));
                $result .= $this->Html->tag('p', sprintf('%s %s', $this->Html->tag('strong', __('No. Rekening : ')), $nomor_rekening));
                $result .= $this->Html->tag('p', sprintf('%s %s', $this->Html->tag('strong', __('Nama Rekening : ')), $name_nomor_rekening));
            }

        }

        if(!empty($result)){
            $result = $this->Html->tag('div', $result, array(
                'class' => 'alert-information mt10',
            ));
        }
        return $result;
    }

    function getNotice($value){
        ## COUNT STATUS  ##
        $kpr = $this->Rumahku->filterEmptyField($value, 'Kpr');
        $id = $this->Rumahku->filterEmptyField($kpr, 'id');
        $kprBank = $this->Rumahku->filterEmptyField($value, 'KprBank');

        $noticeArr = Set::classicExtract($kprBank, '{n}.KprBank.application_status');  
        $log_application_snyc = Set::classicExtract($kprBank, '{n}.KprBank.application_snyc');
        $status = $this->Rumahku->filterEmptyField($kpr, 'document_status');
        $status_frontend = $this->Rumahku->filterEmptyField($kpr, 'status_frontend');
        $from_web = $this->Rumahku->filterEmptyField($kpr, 'from_web');
        $from_web = ($from_web == 'primesystem') ? 'Prime System' : 'Rumahku.com';

        $cnt = $this->Rumahku->filterEmptyField( $value, 'Kpr','cnt_data'); 
        $cnt_data = $this->Rumahku->filterEmptyField( $cnt, 'cnt_data', false, 0);
        $cnt_pending = $this->Rumahku->filterEmptyField( $cnt, 'cnt_pending', false, 0);
        $cnt_process = $this->Rumahku->filterEmptyField( $cnt, 'cnt_process', false, 0);
        $cnt_cancel = $this->Rumahku->filterEmptyField( $cnt, 'cnt_cancel', false, 0);
        $cnt_approved_admin = $this->Rumahku->filterEmptyField( $cnt, 'cnt_approved_admin', false, 0);
        $cnt_rejected_admin = $this->Rumahku->filterEmptyField( $cnt, 'cnt_rejected_admin', false, 0);
        $cnt_approved_propposal = $this->Rumahku->filterEmptyField( $cnt, 'cnt_approved_propposal', false, 0);
        $cnt_rejected_propposal = $this->Rumahku->filterEmptyField( $cnt, 'cnt_rejected_propposal', false, 0);
        $cnt_proposal_without_comiission = $this->Rumahku->filterEmptyField( $cnt, 'cnt_proposal_without_comiission', false, 0);
        $cnt_approved = $this->Rumahku->filterEmptyField( $cnt, 'cnt_approved', false, 0);
        $cnt_rejected = $this->Rumahku->filterEmptyField( $cnt, 'cnt_rejected', false, 0);
        $cnt_credit_process = $this->Rumahku->filterEmptyField( $cnt, 'cnt_credit_process', false, 0);
        $cnt_rejected_credit = $this->Rumahku->filterEmptyField( $cnt, 'cnt_rejected_credit', false, 0);
        $cnt_approved_credit = $this->Rumahku->filterEmptyField( $cnt, 'cnt_approved_credit', false, 0);
        $cnt_completed = $this->Rumahku->filterEmptyField( $cnt, 'cnt_completed', false, 0);

        $bank_name = $this->Rumahku->filterEmptyField( $cnt, 'bank_name');
        $kpr_bank_code = $this->Rumahku->filterEmptyField( $cnt, 'kpr_bank_code');
        $set_akad = $this->Rumahku->filterEmptyField( $cnt, 'set_akad');

        $count_rejected = $cnt_cancel + $cnt_rejected_propposal + $cnt_rejected + $cnt_rejected_admin + $cnt_rejected_credit;
        ###################
        ## CRM PROJECT 
        $crm_project_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
        $attribute_set_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
        $completed_date = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'completed_date');
        $wrapper = false;
        $action_bottom = false;
        $notice_type = false;
        $overlay = false;
        $url = false;
        $classRemove = '.overlay-grey,.kpr-notice-skip';
        $urlDefault = array(
            'controller' => 'kpr',
            'action' => 'notice_toggle',
            $id,
            'admin' => true,
        );

        if($cnt_data <= $count_rejected && $cnt_data > 0){

            if($cnt_data == $cnt_cancel){
                $wrapper = $this->Html->tag('label', __('Pemberitahuan'));
                $wrapper .= $this->Html->tag('p', __('Mohon maaf, Anda telah membatalkan pengajuan KPR, silakan buat Pengajuan KPR Baru'));
            }else{
                $wrapper = $this->Html->tag('label', __('Pemberitahuan'));
                $wrapper .= $this->Html->tag('p', __('Mohon maaf, Pengajuan KPR Anda ditolak, Anda dapat membuat Pengajuan KPR baru'));
            }
            
            $notice_type = 'kpr_add';
            $url = $this->Html->link(__('Pengajuan KPR'), array(
                'controller' => 'kpr',
                'action' => 'add',
                'kpr_application_id' => $id,
                'admin' => true,
            ), array(
                'class' => 'btn blue',
                'title' => __('PERHATIAN'),
            ));
        }else{ 
           if(empty($kprBank)){
                $notice_type = 'kpr_filing';
                $url = $this->Html->link(__('Pilih Bank KPR'), array(
                    'controller' => 'kpr',
                    'action' => 'filing',
                    $id,
                    'admin' => true,
                ), array(
                    'class' => 'btn blue',
                    'title' => __('PERHATIAN'),
                ));

                $wrapper = $this->Html->tag('label', __('Tips Pengajuan KPR'));
                $wrapper .= $this->Html->tag('p', __('Untuk dapat melanjutkan Proses KPR Anda, mohon pilih bank KPR yang ingin Anda ajukan.'));
            }else if(!empty($set_akad)){
                $notice_type = 'akad_credit';
                $ulContent = null;
                $credit_date = $this->Rumahku->filterEmptyField( $set_akad, 'credit_date');
                $contact_bank = $this->Rumahku->filterEmptyField( $set_akad, 'contact_bank');
                $contact_name = $this->Rumahku->filterEmptyField( $set_akad, 'contact_name');
                $contact_email = $this->Rumahku->filterEmptyField( $set_akad, 'contact_email');
                $note = $this->Rumahku->filterEmptyField( $set_akad, 'note', false, null, true, 'EOL');

                $customCreditDate = $this->Html->tag('strong', $this->Rumahku->getIndoDateCutom($credit_date));
                $customLocation = $this->Html->tag('strong', $note);

                $label = $this->Html->tag('strong', $bank_name);
                $link = $this->Html->url(array_merge($urlDefault, array(
                    $notice_type,
                )));
                
                $result = sprintf( __('%s menyetujui akad kredit, tanggal akad %s, dan lokasi %s'), $label, $customCreditDate, $customLocation);
                $wrapper = $this->Html->tag('label', __('Akad Kredit'));
                $wrapper .= $this->Html->tag('p', sprintf(__('Terima kasih telah menggunakan sistem KPR Kami, berikut jadwal dan lokasi Akad Kredit yang telah ditentukan oleh %s:'), $label));

                if(!empty($credit_date)){
                    $ulContent = $this->Html->tag('li', sprintf(__('Tanggal Akad : %s'), $customCreditDate));
                }

                if(!empty($note)){
                    $ulContent .= $this->Html->tag('li', sprintf(__('Lokasi : %s'), $customLocation));
                }

                if(!empty($contact_name)){
                    $customContactName = $this->Html->tag('strong', ucwords($contact_name));
                    $ulContent .= $this->Html->tag('li', sprintf(__('Bertemu dengan :  %s'), $customContactName));
                }

                if(!empty($contact_bank)){
                    $customContactBank = $this->Html->tag('strong', $contact_bank);
                    $ulContent .= $this->Html->tag('li', sprintf(__('Kontak : %s'), $customContactBank));
                }

                if(!empty($contact_email)){
                    $customContactEmail = $this->Html->tag('strong', ucwords($contact_email));
                    $ulContent .= $this->Html->tag('li', sprintf(__('Email :  %s'), $customContactEmail));
                }
                
                $wrapper .= $this->Html->tag('ul', $ulContent);

                if((!empty($status) && $status <> 'completed')){
                    $contentAlert = $this->Html->tag('p', sprintf(__('Anda dapat meningkatkan status menjadi %s dengan klik tombol dibawah ini:'), $this->Html->tag('strong', __('Complete'))), array(
                        'class' => 'mt5'
                    ));

                    $btn = __('Setujui Akad');
                    $url = $this->Html->link($btn, array(
                        'controller' => 'kpr',
                        'action' => 'completed',
                        $id,
                        'complete',
                        'admin' => true,
                    ), array(
                        'class' => 'btn green',
                        'data-size' => 'modal-md',
                        'title' => __('PERHATIAN'),
                    ), __('Confirm akad / setujui akad'));

                }

                $url .= $this->Html->link(__('Lihat Detil'), '#bank-info', array(
                    'class' => 'btn blue scrollto ajax-link',
                    'data-url' => $link,
                    'data-remove' => $classRemove,
                ));
            }else if(!empty($cnt_credit_process) && $cnt_credit_process > 0){
                $label = $this->Html->tag('strong', $bank_name);
                $label_code = $this->Html->tag('strong', $kpr_bank_code);
                $notice_type = 'pending_akad_credit';
                $link = $this->Html->url(array_merge($urlDefault, array(
                    $notice_type,
                )));

                $wrapper = $this->Html->tag('label', __('Menunggu Proses Akad Kredit'));
                $wrapper .= $this->Html->tag('p', sprintf(__('Anda telah memilih %s dengan kode KPR #%s, Mohon menunggu konfirmasi dari pihak Bank'), $label, $label_code));
                $wrapper .= $this->Html->tag('p', sprintf(__('Kami akan mengirimkan %s apabila Bank telah menentukan jadwal dan lokasi Akad Kredit'), $this->Html->tag('strong', __('notifikasi via Email'))));
                $url = $this->Html->link(__('Lihat Bank'), sprintf('#bank-%s', $kpr_bank_code), array(
                    'class' => 'btn blue scrollto ajax-link',
                    'data-url' => $link,
                    'data-remove' => $classRemove,
                ));
            }else if(!empty($cnt_approved) && $cnt_approved > 0){
                $label = $this->Html->tag('b', __('Proses Akad'));
                $result = sprintf( __('Salah satu Aplikasi klien sudah disetujui oleh bank, silakan klik %s di menu Bank KPR sesuai aplikasi yang disetujui'), $label);
                $notice_type = 'process_akad_credit';
                $link = $this->Html->url(array_merge($urlDefault, array(
                    $notice_type,
                )));

                $wrapper = $this->Html->tag('label', __('Tips Proses Akad Kredit'));
                $wrapper .= $this->Html->tag('p', __('Selamat Aplikasi KPR Anda telah disetujui oleh Bank.'));
                $wrapper .= $this->Html->tag('p', __('Anda dapat melanjutkan Proses Akad Kredit, dengan meng-klik tombol "Proses Akad" pada salah satu Bank yang menyetujui KPR Anda'));
                $url = $this->Html->link(__('Lihat Bank'), '#list-bank-kpr', array(
                    'class' => 'btn blue scrollto ajax-link',
                    'data-url' => $link,
                    'data-remove' => $classRemove,
                ));
            }else if($status == 'proposal_without_comiission' && in_array('completed', $noticeArr)  && !in_array(TRUE, $log_application_snyc)){
                $notice_type = 'document_resend';
                $wrapper = $this->Html->tag('label',__('Provisi KPR ditolak & pengiriman ulang aplikasi KPR'));
                $wrapper .= $this->Html->tag('p', __('Salah satu provisi telah ditolak oleh Bank'));
                $wrapper .= $this->Html->tag('p', __('lakukan pengiriman ulang dengan klik button dibawah ini, dan anda bisa membatalkan aplikasi KPR ini'));

                $link = $this->Html->url(array_merge($urlDefault, array(
                    $notice_type,
                )));

                $url = $this->Html->link(__('Lihat Bank'), '#list-bank-kpr', array(
                    'class' => 'btn blue scrollto ajax-link',
                    'data-url' => $link,
                    'data-remove' => $classRemove,
                ));
            }else if($status == 'proposal_without_comiission'){
                $wrapper = $this->Html->tag('label',__('Anda sudah melakukan pengiriman ulang aplikasi KPR'));

                if(in_array('completed', $noticeArr)){
                    $notice_type = 'document_completed';
                    $wrapper .= $this->Html->tag('p', sprintf(__('Terima kasih telah melengkapi %s. Pengajuan Anda sedang dalam %s, mohon %s dari pihak Bank.'), $this->Html->tag('strong', __('FORM APLIKASI KPR')), $this->Html->tag('strong', __('Proses')), $this->Html->tag('strong', __('menunggu approval/persetujuan'))));
                    $wrapper .= $this->Html->tag('p', sprintf(__('Kami akan mengirimkan %s apabila Pengajuan Anda telah disetujui oleh Bank'), $this->Html->tag('strong', __('notifikasi via Email'))));

                    $link = $this->Html->url(array_merge($urlDefault, array(
                        $notice_type,
                    )));

                    $url = $this->Html->link(__('Lihat Aplikasi'), '#buyer-info', array(
                        'class' => 'btn blue scrollto ajax-link',
                        'data-url' => $link,
                        'data-remove' => $classRemove,
                    ));

                }else{
                    $url = $this->Html->link(__('Edit Aplikasi'), array(
                        'controller' => 'kpr',
                        'action' => 'application',
                        $id,
                        'admin' => true,
                    ), array(
                        'class' => 'btn blue',
                        'title' => __('PERHATIAN'),
                    ));
                    
                    $wrapper .= $this->Html->tag('p', sprintf(__('Anda juga dapat melengkapi %s dengan mengklik tombol dibawah ini:'), $this->Html->tag('strong', __('FORM APLIKASI KLIEN'))));
                }
            }
            // else if($status == 'proposal_without_comiission' && in_array('completed', $noticeArr)){
            //     $notice_type = 'document_completed';
            //     $wrapper = $this->Html->tag('label',__('Anda sudah melakukan pengiriman ulang aplikasi KPR'));
            //     $wrapper .= $this->Html->tag('p', sprintf(__('Terima kasih telah melengkapi %s. Pengajuan Anda sedang dalam %s, mohon %s dari pihak Bank.'), $this->Html->tag('strong', __('FORM APLIKASI KPR')), $this->Html->tag('strong', __('Proses')), $this->Html->tag('strong', __('menunggu approval/persetujuan'))));
            //     $wrapper .= $this->Html->tag('p', sprintf(__('Kami akan mengirimkan %s apabila Pengajuan Anda telah disetujui oleh Bank'), $this->Html->tag('strong', __('notifikasi via Email'))));

            //     $link = $this->Html->url(array_merge($urlDefault, array(
            //         $notice_type,
            //     )));

            //     $url = $this->Html->link(__('Lihat Aplikasi'), '#buyer-info', array(
            //         'class' => 'btn blue scrollto ajax-link',
            //         'data-url' => $link,
            //         'data-remove' => $classRemove,
            //     ));
            // }else if($status == 'proposal_without_comiission' && in_array('resend', $noticeArr)){

            // }
            else if(in_array('completed', $noticeArr) || in_array('sent', $noticeArr)){
                $forward_apps = Set::classicExtract($kprBank, '{n}.KprBank.forward_app');
                $notice_type = 'document_completed';

                $link = $this->Html->url(array_merge($urlDefault, array(
                    $notice_type,
                )));

                $url_bank = $this->Html->link(__('Lihat Bank'), '#kpr-filing', array(
                    'class' => 'btn default scrollto ajax-link',
                    'data-url' => $link,
                    'data-remove' => $classRemove,
                ));
                
                if( $status_frontend == 'active' ) {

                    $url .= $this->Html->link(__('Lanjutkan'), array(
                        'controller' => 'kpr',
                        'action' => 'all_forward',
                        $id,
                        'admin' => true,
                    ), array(
                        'class' => 'btn blue',
                    ), __('Anda yakin ingin meneruskan seluruh pengajuan KPR ini ?'));

                    $url .= $url_bank;
                    $client_name = $this->Rumahku->filterEmptyField($kpr, 'KprApplication', 'full_name');
                    $wrapper = $this->Html->tag('label', __('Pengajuan KPR oleh klien %s', $client_name));

                    $warpper_text = __('Anda mendapatkan Pengajuan KPR melalui detail properti %s', $this->Html->tag('strong', $from_web));

                    $wrapper .= $this->Html->tag('p', __('%s , silakan klik button %s agar pengajuan KPR dikirm ke bank', $warpper_text, $this->Html->tag('strong', __('lanjutkan'))));
                }else{
                    $url = $this->Html->link(__('Lihat Aplikasi'), '#buyer-info', array(
                        'class' => 'btn blue scrollto ajax-link',
                        'data-url' => $this->Html->url(array_merge($urlDefault, array(
                            $notice_type,
                        ))),
                        'data-remove' => $classRemove,
                    ));
                    $url .= $url_bank;
                    $wrapper = $this->Html->tag('label', __('Menunggu Konfirmasi Bank'));
                    $wrapper .= $this->Html->tag('p', sprintf(__(' %s sudah lengkap jika anda ingin memperbaiki klik %s.'), $this->Html->tag('strong', __('FORM APLIKASI KPR')), $this->Html->tag('strong', __('Lihat Aplikasi'))));
                    $wrapper .= $this->Html->tag('p', sprintf(__('Pengajuan Anda sedang dalam %s, mohon %s dari pihak Bank.'), $this->Html->tag('strong', __('Proses')), $this->Html->tag('strong', __('menunggu approval/persetujuan')))); 
                    $wrapper .= $this->Html->tag('p', sprintf(__('Kami akan mengirimkan %s apabila Pengajuan Anda telah disetujui oleh Bank'), $this->Html->tag('strong', __('notifikasi via Email'))));
                }
            }else if(in_array('pending', $noticeArr)){
                $notice_type = 'application_form';
                $url = $this->Html->link(__('Edit Aplikasi'), array(
                    'controller' => 'kpr',
                    'action' => 'application',
                    $id,
                    'admin' => true,
                ), array(
                    'class' => 'btn blue',
                    'title' => __('PERHATIAN'),
                ));

                $wrapper = $this->Html->tag('label', __('Tips Pengajuan KPR'));

                if( $status == 'approved_proposal' ) {
                    $wrapper .= $this->Html->tag('p', sprintf(__('Referral KPR Anda telah disetujui Bank, silakan masukkan %s.'), $this->Html->tag('strong', __('FORM APLIKASI KLIEN'))));
                } else {
                    $wrapper .= $this->Html->tag('p', sprintf(__('Pengajuan KPR Anda telah Kami terima, %s.'), $this->Html->tag('strong', __('mohon menunggu konfirmasi pihak Bank'))));
                    $wrapper .= $this->Html->tag('p', sprintf(__('Anda juga dapat melengkapi %s dengan mengklik tombol dibawah ini:'), $this->Html->tag('strong', __('FORM APLIKASI KLIEN'))));
                }
            } 
        }

        $cookie_name = sprintf('Kpr.Notice.%s.%s', $notice_type, $id);
        $cookie = Configure::read('Cookie.Helper');
        $read = $cookie->read($cookie_name);

        if( empty($read) ) {
            $overlay = $this->Html->tag('div', '', array(
                'class' => 'overlay-grey',
            ));
            $url .= $this->Html->link(__('Lewati'), array_merge($urlDefault, array(
                $notice_type,
            )), array(
                'class' => 'btn ajax-link kpr-notice-skip',
                'data-remove' => $classRemove,
            ));
        }
        if( !empty($url) ) {
            $action_bottom = $this->Html->tag('div', $url, array(
                'class' => 'action-button',
            ));
        }

        $result = $this->Html->tag('div', $wrapper, array(
            'class' => 'wrapper-alert',
        )).$action_bottom;
        
            
        return $this->Html->tag('div', $result, array(
            'class' => 'crm-tips kpr-alert',
        )).$overlay;
    }

    function viewChartKpr($icon, $cnt_data = 0 , $options){

        $frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');
        $divIcon = $this->Rumahku->filterEmptyField($options, 'divIcon');
        $divValue = $this->Rumahku->filterEmptyField($options, 'divValue');
        $label = $this->Rumahku->filterEmptyField($options, 'label');
        $url = $this->Rumahku->filterEmptyField($options, 'url');

        $icon = $this->Html->div($divIcon, $this->Html->div('icon floleft', $this->Html->tag('i', ' ', array(
            'class' => $icon,
        ))));

        $tagKpr = $this->Html->tag('h5', $cnt_data);
        $label = !empty($label)?sprintf('%s &raquo;', $label):false;
        $spanLabel = $this->Html->tag('span', $label);

        if(!empty($url)){
            $tagKpr .= $this->Html->link($spanLabel, $url, array(
                'escape' => false,
            ));
        }else{
            $tagKpr .= $spanLabel;
        }

        $value = $this->Html->div($divValue, $tagKpr);

        return $this->Html->div($frameClass, $this->Html->tag('div', sprintf('%s %s', $icon, $value), array(
            'class' => 'row' 
        )));
    }

    function getTheadKpr($checkAll = false, $filter = true){

        $dataColumns = !empty($dataColumns)?$dataColumns:array();
        $fieldColumn = null;
        if(!empty($checkAll)){
            $dataColumns = array(
                'checkall' => array(
                    'name' => $checkAll,
                    'class' => 'tacenter',
                    'style' => 'width: 2%;',
                ),
            );
        }
        if($filter){
            $dataColumns = array_merge( $dataColumns, array(
                'bank_name' => array(
                    'name' => __('NAMA BANK'),
                    // 'style' => 'width: 15%;',
                ),
                'loan_price' => array(
                    'name' => __('TOTAL PINJAMAN'),
                    // 'style' => 'width: 15%;',
                ),
                'dp' => array(
                    'name' => __('UANG MUKA'),
                    // 'style' => 'width: 15%;',
                ),
                'first_credit' => array(
                    'name' => __('ANGSURAN'),
                    // 'style' => 'width: 15%;',
                ),
                'interest_rate' => array(
                    'name' => __('SUKU BUNGA'),
                    // 'style' => 'width: 15%;',
                ),
                'provisi' => array(
                    'name' => __('PROVISI'),
                    // 'style' => 'width: 15%;',
                ),
            ));
            $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
        }
        return $fieldColumn;
    }

    function continueApplication($kprBank){
        $kpr_bank_id = $this->Rumahku->filterEmptyField($kprBank, 'id');
        $from_kpr = $this->Rumahku->filterEmptyField($kprBank, 'from_kpr');
        $forward_app = $this->Rumahku->filterEmptyField($kprBank, 'forward_app');
        $document_status = $this->Rumahku->filterEmptyField($kprBank, 'document_status');
        $btn = $flag =  false;

        if((!empty($from_kpr) && $from_kpr == 'frontend') && empty($forward_app) && $document_status == 'process'){
            $btn = $this->Html->link(__('Lanjutkan'), array(
                'controller' => 'kpr',
                'action' => 'foward_application',
                $kpr_bank_id,
                'admin' => true,
            ),array(
                'class' => 'color-blue mr5',
            ), __('Anda yakin ingin meneruskan aplikasi ini kebank ?'));
            $flag = true;
        }

        return array(
            'btnContinue' => $btn,
            'flag' => $flag,
        );
    }

    function resendApplication( $kpr_bank_id, $options = array()){
        $application_status = $this->Rumahku->filterEmptyField($options, 'application_status');
        $application_snyc = $this->Rumahku->filterEmptyField($options, 'application_snyc');
        $document_status = $this->Rumahku->filterEmptyField($options, 'document_status');
        
        if(!empty($kpr_bank_id) && $document_status == 'proposal_without_comiission' && empty($application_snyc)){
            $btn = $this->Html->link(__('Lanjutkan'), array(
                'controller' => 'kpr',
                'action' => 'resend_application',
                $kpr_bank_id,
                'admin' => true,
            ),array(
                'class' => 'color-green mr5',
            ), __('Anda yakin ingin melanjutkan proses KPR ini, sudah dijelaskan untuk proses KPR ini anda tidak mendapatkan provisi ?'));

            return array(
                'btn' => $btn,
                'flag' => TRUE,
            );
        }else{
            return FALSE;
        }
       
    }

    function setInstallmentArr($kprBank){
        if(!empty($kprBank)){
            $default_dp = Configure::read('__Site.bunga_kpr');
            $kpr_bank_installments = $this->Rumahku->filterEmptyField($kprBank, 'KprBankInstallment');
            $kpr_bank_installment_filing = !empty($kpr_bank_installments[0]['KprBankInstallment'])?$kpr_bank_installments[0]['KprBankInstallment']:array();
            $kpr_bank_installment_approved = !empty($kpr_bank_installments[1]['KprBankInstallment'])?$kpr_bank_installments[1]['KprBankInstallment']:array();

            $periode_installment = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'credit_total');
            $periode_installment = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'credit_total', false, $periode_installment);

            $rate = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'interest_rate_fix');
            $rate = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'interest_rate_fix', false, $rate);

            $rate_cabs = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'interest_rate_cabs');
            $rate_cabs = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'interest_rate_cabs', false, $rate_cabs);

            $down_payment = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'down_payment', false, $default_dp);
            $down_payment = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'down_payment', false, $down_payment);

            $interest_rate_float = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'interest_rate_float');
            $interest_rate_float = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'interest_rate_float', false, $interest_rate_float);

            $credit_fix = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'periode_fix');
            $credit_fix = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'periode_fix', false, $credit_fix);

            $credit_cab = $this->Rumahku->filterEmptyField($kpr_bank_installment_filing, 'periode_cab');
            $credit_cab = $this->Rumahku->filterIssetField($kpr_bank_installment_approved, 'periode_cab', false, $credit_cab);

            $work_day = $this->Rumahku->filterEmptyField($kprBank, 'work_day');

            return array(
                'periode_installment' => $periode_installment,
                'rate' => $rate,
                'rate_cabs' => $rate_cabs,
                'down_payment' => $down_payment,
                'interest_rate_float' => $interest_rate_float,
                'credit_fix' => $credit_fix,
                'credit_cab' => $credit_cab,
                'work_day' => $work_day,
            );
        }
    }

    function getFilterCommissionAgent($val){
        $kpr_bank = $this->Rumahku->filterEmptyField($val, 'KprBank');

        if(!empty($kpr_bank)){
            $agentCommission = !empty($kpr_bank['agentCommission'])?$kpr_bank['agentCommission']:array();
            $commission = $this->Rumahku->filterEmptyField($agentCommission, 'value');
            $percent = $this->Rumahku->filterEmptyField($agentCommission, 'percent', false, 0);
            $termsAddOn = $this->Rumahku->filterEmptyField($agentCommission, 'note', false, false, false, 'EOL');
            $region_name = $this->Rumahku->filterEmptyField($agentCommission, 'region_name');
            $city_name = $this->Rumahku->filterEmptyField($agentCommission, 'city_name');
        }else{
            $bankCommissionSetting = $this->Rumahku->filterEmptyField($val, 'BankCommissionSetting');
            $commission = $this->Rumahku->filterEmptyField($bankCommissionSetting, 'commission');
            $percent = $this->Rumahku->filterEmptyField($bankCommissionSetting, 'percent');
            $termsAddOn = $this->Rumahku->filterEmptyField($bankCommissionSetting, 'description');
            $region_name = $this->Rumahku->filterEmptyField($bankCommissionSetting, 'Region', 'name');
            $city_name = $this->Rumahku->filterEmptyField($bankCommissionSetting, 'City', 'name');
        }

        return array(
            'commission' => $commission,
            'percent' => $percent,
            'termsAddOn' => $termsAddOn,
            'region_name' => $region_name,
            'city_name' => $city_name,
        );
        
    }

    function setKprCommission($installment){
        $agentCommission = $this->Rumahku->filterEmptyField($installment, 'agentCommission');
        $rumahkuCommission = $this->Rumahku->filterEmptyField($installment, 'rumahkuCommission');
        $data = array();
        if(!empty($agentCommission)){
            $data[] = $agentCommission;
        }

        if(!empty($rumahkuCommission)){
            $data[] = $rumahkuCommission;
        }
        return $data;
    }

    function printKPR($val, $params = array()){
        $li = null; 
        $btns = $this->Rumahku->filterEmptyField($params, 'btn');
        $kpr_bank_id = $this->Rumahku->filterEmptyField($val, 'KprBank', 'id');
        $kpr_id = $this->Rumahku->filterEmptyField($val, 'KprBank', 'kpr_id');

        if(!empty($btns)){
            foreach($btns AS $key => $btn){
                if(!empty($btn)){
                    $active = true;
                    $li .= $this->Html->tag('li', $btn);
                }
            }
        }

        $li .= $this->Html->tag('li', $this->Html->link(__('Print'), array(
            'action' => 'application_detail_excel',
            'controller' => 'kpr',
            $kpr_id,
            $kpr_bank_id,
            'print' => true,
        ), array(
            'escape' => false,
            'title' => __('surat pengantar(.pdf)'), 
            'class' => 'popup-window',  
            'data-height' => '768',        
            'data-width' => '1024',        
        )));

        $li .= $this->Html->tag('li', $this->Html->link(__('Unduh Excel'), array(
            'controller' => 'kpr',
            'action' => 'application_detail_excel',
            $kpr_id,
            $kpr_bank_id,
            'export' => true,
            'admin' => true,
        ), array(
            'escape' => false,
            'title' => __('surat pengantar(.xls)'),
        )));

        $btn = $this->Html->tag('div', $this->Rumahku->icon('rv4-burger'), array(
            'data-toggle' => 'dropdown'
        ));

        $print = $this->Html->tag('div', sprintf('%s%s', $btn, $this->Html->tag('ul', $li, array(
            'class' => 'dropdown-menu right',
            'role' => 'menu',
        ))), array(
            'class' => 'dropdown action-list',
        ));

        if(!empty($active)){
            $print .= $this->Html->tag('span', '!', array(
                'class' => 'label total kpr'
            )); 
        }

        return $print;
    }

    function statusCheck($checkbox = false, $statusCheck = false){
        if(!empty($statusCheck)){
            $colspan = 8;
            $content = array(
                array(
                    $checkbox,
                    array(
                        'class' => 'tacenter',
                    ),
                )
            );
        }else{
            $colspan = 7;
            $content = array();
        }

        return array(
            'colspan' => $colspan,
            'content' => $content,
        );

    }

    function iconInfo($bank_id, $document_status, $reason, $disabled){
        $data_display = array();
        $icon_info = $flag = $flag_filling = false;

        if(!empty($document_status)){
            $flag = !in_array($document_status, array( 'cancel', 'rejected_admin', 'rejected_proposal', 'rejected_bank', 'rejected_credit'));
        }else{
            $flag_filling = !(empty($document_status) && !empty($reason));
        }

        if(!$disabled){
            $data_display = array(
                'data-display' => sprintf("#kpr-info-detail[rel='%s']", $bank_id),
            );
        }

        if( !empty($flag_filling) || !empty($flag)){
            $icon_info = $this->Html->link($this->Html->image('icons/info.png'), '#', array_merge(array(
                'escape' => false,
                'class' => 'toggle-display',
                'data-type' => 'slide',
                'data-arrow' => 'true',
            ), $data_display));
        }
        return $icon_info;
    }

    function loopInstallment($start = 1, $periode = 30){
        if(!empty($periode)){
            for($i=$start ; $i <= $periode; $i++){
                $arr[$i] = sprintf('%s Tahun', $i);
            }
            return $arr;
        }else{
            return false;
        }
    }

    function getDataField($data, $options = array()){
        $model = $this->Rumahku->filterEmptyField($options, 'model');
        $field = $this->Rumahku->filterEmptyField($options, 'field');
        $optionPrice = $this->Rumahku->filterEmptyField($options, 'optionPrice');
        $view = $this->Rumahku->filterEmptyField($options, 'view', false, 'all');


        $category = $this->Rumahku->filterEmptyField($data, $model, sprintf('category_%s', $field));
        $param = $this->Rumahku->filterEmptyField($data, $model, sprintf('param_%s', $field));
        $value = $this->Rumahku->filterEmptyField($data, $model, $field);

        $value_arr = $this->getPercent($value, $optionPrice, array(
            'category' => $category,
            'params' => $param,
        ));

        if($view == 'nominal'){
            return !empty($value_arr['nominal'])?$value_arr['nominal']:false;
        }else if($view == 'percent'){
            return !empty($value_arr['percent'])?$value_arr['percent']:false;
        }elseif($view == 'with_param'){

            if($param == 'price'){
                $source = 'harga properti';
            }else{
                $source = 'jumlah pinjaman';
            }
            return array_merge($value_arr, array('source' => $source));
        }else{
            return $value_arr;
        }
    }

    function dataFieldList($data, $model, $options = array()){
        $optionsPrice = $this->Rumahku->filterEmptyField($options, 'optionsPrice');
        $fields = $this->Rumahku->filterEmptyField($options, 'fields');
        $view = $this->Rumahku->filterEmptyField($options, 'view');
        $data_arr = array();

        if(!empty($fields)){
            foreach($fields AS $key => $field){
                $data_arr[$field] = $this->getDataField($data, array(
                    'model' => $model,
                    'field' => $field,
                    'optionPrice' => $optionsPrice,
                    'view' => $view,
                ));
            }
        }
        return $data_arr;
    }

    function slideListKpr($label, $value, $options = array()){
        $divLabel = $this->Rumahku->filterEmptyField($options, 'divLabel', false, 'col-md-8 col-sm-5 col-xs-6');
        $divValue = $this->Rumahku->filterEmptyField($options, 'divValue', false, 'col-md-4 col-sm-7 col-xs-6');
        $extraLabel = $this->Rumahku->filterEmptyField($options, 'extraLabel');
        $extraValue = $this->Rumahku->filterEmptyField($options, 'extraValue');
        $tagLabel = $this->Rumahku->filterEmptyField($options, 'tagLabel');
        $formatPrice = $this->Rumahku->filterIssetField($options, 'formatPrice', false, true);

        if(!empty($tagLabel)){
            $label = $this->Html->tag($tagLabel, $label);
        }

        if(!empty($extraLabel)){
            $label = sprintf('%s %s', $label, $this->Html->tag('span', $extraLabel, array('class' => 'desc')));
        }

        $label = $this->Html->tag('div', $this->Html->tag('label', $label, array(
            'for' => '',
            'id' => 'propertyPrice',
        )), array(
            'class' => $divLabel,
        ));


        if($formatPrice && isset($value)){
            if(is_numeric($value)){
                $value = $this->Rumahku->getCurrencyPrice($value, sprintf('%s 0', Configure::read('__Site.config_currency_symbol')));
            }
        }

        if(!empty($extraValue)){
            $value = sprintf('%s %s', $value, $this->Html->tag('span', $extraValue, array('class' => 'desc')));
        }

        $value = $this->Html->tag('div', $this->Html->tag('span', $value, array(
            'for' => '',
            'id' => 'propertyPrice',
            'class' => 'floright',
        )), array(
            'class' => $divValue,
        ));

        $result = $this->Html->tag('div', sprintf('%s %s', $label, $value), array(
            'class' => 'row',
        ));

        return $result;
    }

    function __getSpecification($data, $modelName = 'display'){
        $result = NULL;

        if($data){
            $interestRateCustom = $interestRateCabs = false;
            $globalData     = Configure::read('Global.Data');
            // get data virtialModel display 
            $bank_id = $this->Rumahku->filterEmptyField($data, 'Bank', 'id');
            $default_promo = $this->Rumahku->filterEmptyField($data, 'Bank', 'promo_text');
            $property_id = $this->Rumahku->filterEmptyField($data, $modelName, 'property_id');

            $interest_rate_fix = $this->Rumahku->filterEmptyField($data, $modelName, 'interest_rate');
            $interest_rate_cabs = $this->Rumahku->filterEmptyField($data, $modelName, 'interest_rate_cabs');
            $periode_fix = $this->Rumahku->filterEmptyField($data, $modelName, 'periode_fix');
            $periode_cab = $this->Rumahku->filterEmptyField($data, $modelName, 'periode_cab');

            $loan_price = $this->Rumahku->filterEmptyField($data, $modelName, 'loan_price');
            $product_name = $this->Rumahku->filterEmptyField($data, $modelName, 'product_name', $default_promo);
            $total_first_credit = $this->Rumahku->filterEmptyField($data, $modelName, 'first_credit');
            $total_cost_bank = $this->Rumahku->filterEmptyField($data, $modelName, 'total_cost_bank');
            $total_notaris = $this->Rumahku->filterEmptyField($data, $modelName, 'total_notaris');
            $grand_total = $this->Rumahku->filterEmptyField($data, $modelName, 'grand_total');
            $text_promo = $this->Rumahku->filterEmptyField($data, $modelName, 'text_promo');
            $desc_promo = $this->Rumahku->filterEmptyField($data, $modelName, 'desc_promo');

            $down_payment = $this->Rumahku->filterEmptyField($data, $modelName, 'down_payment');
            $periode_installment = $this->Rumahku->filterEmptyField($data, $modelName, 'periode_installment');

            $loan_price = $this->Rumahku->getCurrencyPrice($loan_price);
            $total_first_credit = $this->Rumahku->getCurrencyPrice($total_first_credit);
            $total_cost_bank = $this->Rumahku->getCurrencyPrice($total_cost_bank);
            $total_notaris = $this->Rumahku->getCurrencyPrice($total_notaris);
            $grand_total = $this->Rumahku->getCurrencyPrice($grand_total);

            if(!empty($interest_rate_fix) && !empty($periode_fix)){
                $interestRateCustom = sprintf('%s%% (%s Tahun)', $interest_rate_fix, $periode_fix);
            }

             if(!empty($interest_rate_cabs) && !empty($periode_cab)){
                $interestRateCabs = sprintf('%s%% (%s Tahun)', $interest_rate_cabs, $periode_cab);
            }

            $linkView = $this->Html->url(array(
                'controller' => 'kpr',
                'action' => 'detail_banks',
                'property_id' => $property_id,
                'bank_id' => $bank_id,
                'down_payment' => $down_payment,
                'periode_installment' => $periode_installment,
            ));

            $termView = $this->Html->link(__('Detail'), sprintf('%s#term-conditions|250', $linkView), array(
                'target' => '_blank',
                'class' => 'scrolling'
            ));

            $specifications = array(
                array(
                    'name'  => __('Jumlah Pinjaman'),
                    'value' => $loan_price, 
                    'model' => $modelName,
                    'field' => 'loan_price', 
                    'key'   => 'loan_price', 
                ),
                array(
                    'name'  => __('Nama Promo'), 
                    'value' => $product_name, 
                    'model' => 'BankProduct',
                    'field' => 'name',
                    'key'   => 'product_name', 
                ),
                array(
                    'name'  => __('Angsuran per bulan'), 
                    'value' => $total_first_credit, 
                    'model' => $modelName,
                    'field' => 'total_first_credit', 
                    'key'   => 'total_first_credit', 
                ),
                array(
                    'name'  => __('Bunga Fix'), 
                    'value' => $interestRateCustom, 
                    'model' => $modelName,
                    'field' => 'interest_rate_fix,periode_fix', 
                    'key'   => 'interest_rate_fix', 
                ),
                array(
                    'name'  => __('Bunga Cap'),
                    'value' => $interestRateCabs, 
                    'model' => $modelName,
                    'field' => 'interest_rate_cabs,periode_cab', 
                    'key'   => 'interest_rate_cabs', 
                ),
                array(
                    'name'  => __('Biaya Bank'),
                    'value' => $total_cost_bank, 
                    'model' => $modelName,
                    'field' => 'total_cost_bank', 
                    'key'   => 'total_cost_bank', 
                ),
                array(
                    'name'  => __('Biaya Notaris'),
                    'value' => $total_notaris, 
                    'model' => $modelName,
                    'field' => 'total_notaris', 
                    'key'   => 'total_notaris', 
                ),
                array(
                    'name'  => __('Pembayaran Pertama'),
                    'value' => $grand_total, 
                    'model' => $modelName,
                    'field' => 'grand_total', 
                    'key'   => 'grand_total', 
                ),
                array(
                    'name'  => __('Promo'),
                    'value' => $text_promo, 
                    'model' => $modelName,
                    'field' => 'text_promo', 
                    'key'   => 'text_promo', 
                ),
                array(
                    'name'  => __('Deskripsi Promo'),
                    'value' => $desc_promo, 
                    'model' => $modelName,
                    'field' => 'desc_promo', 
                    'key'   => 'desc_promo', 
                ),
                array(
                    'name'  => __('Syarat dan Ketentuan'),
                    'value' => $termView, 
                    'model' => $modelName,
                    'field' => 'term_conditions', 
                    'key'   => 'term_conditions', 
                ),
            );

            return $specifications;
        }
    }

    function setrowEmail($label, $value, $options){
        $labelStyle  = $this->Rumahku->filterEmptyField($options, 'labelStyle');
        $valueStyle  = $this->Rumahku->filterEmptyField($options, 'valueStyle');

        $labelView = $this->Html->tag('td', $label, $labelStyle);

        $valueView = $this->Html->tag('td', sprintf(': %s', $value), $valueStyle);

        return $this->Html->tag('tr', sprintf('%s %s', $labelView, $valueView));
    }

    function legend($status, $colClass = 'col-sm-4 mb5'){
        $statusArr = $this->Crm->_getStatusBankRequest(array(
            'document_status' => $status
        ), array(
            'type' => 'arr',
        ));
        $status = $this->Rumahku->filterEmptyField($statusArr, 'status');
        $status_html = $this->Crm->_getColorRequest($statusArr, true);

        return $this->Html->tag('div', sprintf('%s %s', $status_html, $status), array(
            'class' => $colClass
        ));
    }

    function detailKpr($datas, $label, $fieldName, $options = array(), $concat = false, $combaine = array()){
        $labelClass = $this->Rumahku->filterEmptyField($options, 'labelClass', false, 'col-sm-4 mb5');
        $valueClass = $this->Rumahku->filterEmptyField($options, 'valueClass', false, 'col-sm-4 mb5');
        $labelFloat = $this->Rumahku->filterEmptyField($options, 'labelFloat', false, 'text-amount title');
        $valueFloat = $this->Rumahku->filterEmptyField($options, 'valueFloat', false, 'text-amount value');
        $extraText = $this->Rumahku->filterEmptyField($options, 'extraText');
        $modelName = $this->Rumahku->filterEmptyField($options, 'modelName');
        $flag = Common::hashEmptyField($options, 'flag');

        $view = $this->Html->tag('div', $this->Html->tag('div', $label, array(
            'class' => sprintf('%s', $labelFloat),
        )), array(
            'class' => $labelClass,
        ));

        if(!empty($datas)){
            $color_class = false;

            foreach ($datas as $key => $data) {

                switch ($fieldName) {
                    case 'notary':
                        $property_price = $this->Rumahku->filterEmptyField($data, $modelName, 'property_price');
                        $loan_price = $this->Rumahku->filterEmptyField($data, $modelName, 'loan_price');
                        $is_notary = $this->Rumahku->filterEmptyField($data, $modelName, 'is_notary');

                        $value = 0;
                        if($is_notary){
                            $value = KprCommon::_callCalcNotary($data, array(
                                 'price' => $property_price,
                                 'loan_price' => $loan_price,
                            ), $modelName);
                        }

                        break;
                    case 'grandtotal':
                        $property_price = $this->Rumahku->filterEmptyField($data, $modelName, 'property_price');
                        $loan_price = $this->Rumahku->filterEmptyField($data, $modelName, 'loan_price');
                        $is_notary = $this->Rumahku->filterEmptyField($data, $modelName, 'is_notary');

                        $notary = 0;
                        if($is_notary){
                            $notary = KprCommon::_callCalcNotary($data, array(
                                 'price' => $property_price,
                                 'loan_price' => $loan_price,
                            ), $modelName);
                        }

                        $down_payment = $this->Rumahku->filterEmptyField($data, $modelName, 'down_payment');
                        $appraisal = $this->Rumahku->filterEmptyField($data, $modelName, 'appraisal');
                        $administration = $this->Rumahku->filterEmptyField($data, $modelName, 'administration');
                        $insurance = $this->Rumahku->filterEmptyField($data, $modelName, 'insurance');
                        $commission = $this->Rumahku->filterEmptyField($data, $modelName, 'commission');
                        $total_first_credit = $this->Rumahku->filterEmptyField($data, $modelName, 'total_first_credit');
                        $value = round($down_payment + $appraisal + $commission + $administration + $insurance + $notary + $total_first_credit, 0);
                        break;
                    
                    default:
                        $value = $this->Rumahku->filterEmptyField($data, $modelName, $fieldName, 0);
                        break;
                }

                if(!empty($value) && is_numeric($value) && empty($concat)){
                    $value = $this->Rumahku->getCurrencyPrice($value, 'N/A');
                }else{
                    $value = !empty($value) ? sprintf('%s%s', $value, $concat) : 'N/A';
                }

                if($combaine){
                    $combineFieldName = $this->Rumahku->filterEmptyField($combaine, 'fieldName');
                    $combineConcat = $this->Rumahku->filterEmptyField($combaine, 'concat');
                    $combaineValue = $this->Rumahku->filterEmptyField($data, $modelName, $combineFieldName, 0);


                    if(!empty($combaineValue) && is_numeric($combaineValue) && empty($combineConcat)){
                        $combaineValue = $this->Rumahku->getCurrencyPrice($combaineValue, 'N/A');
                    }else{
                        $value .= (!empty($combaineValue) && $combaineValue <> 'N/A') ? sprintf(', %s %s', $combaineValue, $combineConcat) : false;
                    }
                }

                if($key == 1){
                    $color_class = 'color-blue';
                }

                $view .= $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('strong', $value, array(
                    'class' => $color_class,
                )), array(
                    'class' => sprintf('%s %s', 'mr5', $valueFloat),
                )), array(
                    'class' => $valueClass,
                ));
            }

            if($extraText ){
                $text = $this->Rumahku->filterEmptyField($extraText, 'text');
                $class = $this->Rumahku->filterEmptyField($extraText, 'class');
                $frameClass = $this->Rumahku->filterEmptyField($extraText, 'frameClass', false, 'col-sm-12');

                if($flag){
                    $field = $this->Rumahku->filterEmptyField($data, $modelName, $flag);

                    if(empty($field)){
                        $flag = false;
                    }
                }

                if($text && empty($flag)){
                    $view .= $this->Html->tag('div', $this->Html->tag('span', $text, array(
                        'class' => $class,
                    )), array(
                        'class' => $frameClass
                    ));
                }
            }
        }

        return $this->Html->tag('div', $this->Html->tag('div', $view, array(
            'class' => 'row'
        )));
    }

    function label_info($label, $value, $extraText = null){
        $result = $this->Html->tag('div', $this->Html->tag('div', $label, array(
                'class' => 'text-amount title',
            )), array(
            'class' => 'col-sm-6 mb5',
        )).
        $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('strong', $value), array(
                'class' => 'mr5 text-amount value',
            )), array(
            'class' => 'col-sm-6 mb5',
        ));
        
        if( !empty($extraText) ){
            $text = Common::hashEmptyField($extraText, 'text');
            $class = Common::hashEmptyField($extraText, 'class');
            $frameClass = Common::hashEmptyField($extraText, 'frameClass', 'col-sm-12');

            if( !empty($text) ){
                $result .= $this->Html->tag('div', $this->Html->tag('span', $text, array(
                    'class' => $class,
                )), array(
                    'class' => $frameClass
                ));
            }
        }

        return $this->Html->tag('div', $result, array(
            'class' => 'row',
        ));
    }

    function labelCompany($val){
        if($val){
            switch ($val) {
                case '1':
                    $label = __('Perusahaan/Usaha');
                    break;
                case '2':
                    $label = __('Departemen');
                    break;
                case '3':
                    $label = __('Perusahaan');
                    break;
                case '4':
                    $label = __('Bidang Usaha');
                    break;
            }
        }else{
            $label = __('Perusahaan');
        }
        return $label;
    }

    function ChangeLabel($case, $data, $modelName, $fieldName, $mandatory = true){
        $label = false;
        if(!empty($case)){
            $val = $this->Rumahku->filterEmptyField($data, $modelName, $fieldName);

            switch ($case) {
                case 'label_company':
                    $label = $this->labelCompany($val);
                    break;
                
                default:
                    # code...
                    break;
            }

            if($mandatory && ($val <> '4')){
                $label .= $this->Html->tag('span', ' *', array(
                    'class' => 'color-red',
                ));
            }
        }
        return $label;
    }

    function widgetClass($slug, $data){
        $class = false;

        if($slug && !empty($data)){
            $slugTheme = $this->Rumahku->filterEmptyField($data, 'Theme', 'slug');

            switch ($slug) {
                case 'button':
                    if($slugTheme){
                        switch ($slugTheme) {
                        case 'EasyLiving':
                                $class = 'buttonColor';
                                break;

                            case 'RealsiteMaterial':
                                $class = 'btn';
                                break;
                            case 'Cozy':
                                $class = 'btn btn-default-color';
                                break;
                            
                            case 'Estato':
                                $class = 'btn btn-warning text-capitalize';
                                break;

                            case 'RealSpaces':
                                $class = 'btn btn-primary text-capitalize';
                                break;

                            case 'Suburb':
                                $class = 'btn btn-large btn-primary';
                                break;

                            case 'Villareal':
                                $class = 'btn btn-primary';
                                break;

                            case 'Apartement':
                                $class = 'form__submit btn btn-primary';
                                break;
                                
                            default:
                                $class = 'form__submit';
                                break;
                        }
                    }else{
                        $class = 'form__submit';
                    }
                    break;
                case 'tab' :
                    if($slugTheme){
                        switch ($slugTheme) {
                            case 'RealSpaces':
                                $class = 'nav-tab';
                                break;
                            
                            default:
                                $class = 'nav-tabs';
                                break;
                        }
                    }else{
                        $class = 'nav-tabs';
                    }
                    break;
                case 'section':
                    if($slugTheme){
                        switch ($slugTheme) {
                            case 'Apartement':
                                $class = 'section-light no-bottom-padding detail-property';
                                break;
                            
                            default:
                                $class = false;
                                break;
                        }
                    }else{
                       $class = false;
                    }
                    break;
            }
        }
        return $class;
    }

    function headerTitle($slug, $headerClass,  $options = array()){
        $label = $this->Rumahku->filterEmptyField($options, 'label', false, __('Aplikasi KPR'));
        $tag = $this->Rumahku->filterEmptyField($options, 'tag');

        $header =  $this->Html->tag($tag, $label, array(
            'class' => $headerClass,
        ));

        if($slug){
            switch ($slug) {
                case 'Cozy':
                    $header = $this->Html->tag('h1', $label, array(
                        'class' => 'section-title print-align-left',
                    ));
                    break;
                case 'Apartement':
                    $header = $this->Html->tag( $tag, $label, array(
                        'class' => $headerClass,
                    ));
                    $header .= $this->Html->tag('div', false, array('class' => 'title-separator-primary'));
                    break;
            
            }
        }

        return $header;
    }

    function addCrumb($slugTheme = false, $options = array()){

        if(!empty($slugTheme)){
            $label = $this->Rumahku->filterEmptyField($options, 'label');
            $urlProperty = $this->Rumahku->filterEmptyField($options, 'urlProperty');
            $bankName = $this->Rumahku->filterEmptyField($options, 'bankName');

            $this->Html->addCrumb(__('Properti'), array(
                'controller' => 'properties',
                'action' => 'find',
                'admin' => false,
            ));

            if(!in_array($slugTheme, array('Villareal'))){
                $this->Html->addCrumb($label, $urlProperty);
            }
            
            $this->Html->addCrumb($bankName);

        }
    }

    function _callGetProgress ( $values, $value ) {
        $kpr_status = Set::extract('/KprBank/KprBankDate/KprBankDate/slug', $value);

        if( !empty($values) ) {
            foreach ($values as $status => $val) {
                $num = Common::hashEmptyField($val, 'num');
                $text = Common::hashEmptyField($val, 'text');

                $find = array_search($status, $kpr_status);
                $kprBankDate = 'N/A';

                if( is_numeric($find) ) {
                    $disabled = '';
                    $kprBankDate = Common::hashEmptyField($value, 'KprBank.KprBankDate.'.$find.'.KprBankDate.action_date', $kprBankDate, array(
                        'date' => 'd M Y - H:i',
                    ));
                } else {
                    $disabled = 'disabled';
                }

                echo $this->Html->div('margin-vert-2 top-agent-list-item '.$disabled, 
                    $this->Html->div('padding-side-2 padding-bottom-3', 
                        $this->Html->div('row', 
                            $this->Html->div('col-md-1', 
                                $this->Html->tag('span', $this->Html->tag('label', $num), array(
                                    'class' => 'numbering',
                                ))
                            ).
                            $this->Html->div('col-md-11', 
                                $this->Html->tag('label', $text, array(
                                    'class' => 'disblock fbold',
                                )).
                                $this->Html->tag('label', $kprBankDate, array(
                                    'class' => 'disblock',
                                ))
                            )
                        )
                    )
                );
            }
        }
    }

    function _callKprProperty ($value) {
        $document_type = Common::hashEmptyField($value, 'Kpr.document_type');

        switch ($document_type) {
            case 'developer':
                return $this->_View->element('blocks/kpr/list/unit', array(
                    'value' => $value,
                ));
                break;
            
            default:
                return $this->_View->element('blocks/kpr/list/property', array(
                    'value' => $value,
                ));
                break;
        }
    }
}