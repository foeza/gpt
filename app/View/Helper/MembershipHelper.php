<?php
class MembershipHelper extends AppHelper {
	var $helpers = array(
        'Rumahku', 'Html', 'Form',
    );

    function _callStatus($document_status = false, $value = false, $options = array()){
    	$result = false;
        $is_request = Common::hashEmptyField($value, 'MembershipOrder.is_request');

        $badge_cancelled = !empty($is_request) ? 'red' : 'black';

    	if(!empty($document_status)){
    		switch ($document_status) {
                case 'request':
                    $text = 'Request';
                    $class = 'badge-blue'; 
                    break;
                case 'follow-up':
                    $text = 'Follow Up';
                    $class = 'badge-yellow'; 
                    break;
                case 'trial':
                    $text = 'Demo';
                    $class = 'badge-green'; 
                    break;
    			case 'waiting':
    				$text = 'Menunggu Pembayaran';
    				$class = 'badge-brown'; 
    				break;
    			case 'cancelled':
    				$text = 'Dibatalkan';
    				$class = sprintf('badge-%s', $badge_cancelled); 
    				break;
    			case 'expired':
    				$text = 'Kadaluarsa';
    				$class = 'badge-red'; 
    				break;
    			case 'paid':
    				$text = 'Dibayarkan';
    				$class = 'badge-green'; 
    				break;
                case 'renewal':
                    $text = 'Renewal';
                    $class = 'badge-blue'; 
                    break;
                case 'new':
                    $text = 'Baru';
                    $class = 'badge-blue'; 
                    break;
    			default:
    				$text = 'Pending';
    				$class = 'badge-gray2';
    				break;
    		}

    		return $this->Rumahku->badge($class, array(
                'text' => $text,
                'class' => 'float-none',
            ), $options);
    	}
    }

    function dataRow($label, $value, $options = array()){
        $col_label = Common::hashEmptyField($options, 'col_label', 'col-sm-5');
        $col_value = Common::hashEmptyField($options, 'col_value', 'col-sm-7');

        $tag_label = Common::hashEmptyField($options, 'tag_label', 'span');
        $tag_value = Common::hashEmptyField($options, 'tag_value', 'strong');

        $labelView = $this->Html->tag('div', $this->Html->tag( $tag_label, $label), array(
            'class' => $col_label,
        ));
        $valueView = $this->Html->tag('div', $this->Html->tag( $tag_value, $value), array(
            'class' => $col_value,
        ));

        return $this->Html->tag('div', $labelView.$valueView, array(
            'class' => 'row margin-bottom-2',
        ));
    }

    function colOrderView($document_status = false, $orderNotes = false){
        if(in_array($document_status, array('renewal', 'request', 'follow-up'))){
            $colContent = 'col-lg-8 padding-right-lg-1 padding-side-xs-1';
            $colSide = 'col-lg-4 padding-left-lg-1 padding-side-xs-1 margin-top-xs-6 margin-top-sm-6 margin-top-lg-0';
        } else {
            $colContent = 'col-lg-12';
            $colSide = false;
        }
        return array(
            'colContent' => $colContent,
            'colSide' => $colSide,
        );
    }

    function paymentMethod($payment_channel = false){
        if(!empty($payment_channel)){
            $result = false;
            switch ($payment_channel) {
                case '04':
                    $result = __('Doku Wallet');
                    break;
                case '15':
                    $result = __('Credit Card');
                    break;
                case '99':
                    $result = __('Bank Transfer');
                    break;
                case '98':
                    $result = __('Cash');
                    break;
            }
            return $result;
        }
        return false;
    }

    function _callStatusOrder ( $status, $options = array() ) {
        $status = !empty($status)?$status:0;
        
        $isRequest = Common::hashEmptyField($options, 'is_request');
        $isRaw = Common::hashEmptyField($options, 'is_raw');

        $options = Hash::remove($options, 'is_request');

        $options = array_replace(array(
            0 => array(
                'text' => 'Non-Aktif', 
                'class' => 'badge-red', 
            ), 
            1 => array(
                'text' => 'Aktif', 
                'class' => 'badge-green', 
            ),
            'rejected' => array(
                'text' => 'Rejected', 
                'class' => 'badge-red', 
            ),
            'approved' => array(
                'text' => 'Approved', 
                'class' => 'badge-green', 
            ),
            'pending' => array(
                'text' => 'Pending', 
                'class' => 'badge-yellow', 
            ),
            'waiting' => array(
                'text' => 'Menunggu Pembayaran', 
                'class' => 'badge-brown', 
            ), 
            'cancelled' => array(
                'text' => 'Dibatalkan', 
                'class' => sprintf('badge-%s', $isRequest ? 'red' : 'black'), 
            ), 
            'cancelled_third_party' => array(
                'text' => 'Menunggu Pembayaran', 
                'class' => 'badge-brown', 
            ),
            'expired' => array(
                'text' => 'Kadaluarsa', 
                'class' => 'badge-red', 
            ), 
            'rejected' => array(
                'text' => 'Ditolak', 
                'class' => 'badge-red', 
            ), 
            'refund' => array(
                'text' => 'Refund', 
                'class' => 'badge-red', 
            ), 
            'completed' => array(
                'text' => 'Completed', 
                'class' => 'badge-blue', 
            ),
            'pending_confirmation' => array(
                'text' => 'Menunggu Konfirmasi', 
                'class' => 'badge-blue', 
            ),
            'paid' => array(
                'text' => 'Lunas', 
                'class' => 'badge-green', 
            ), 
            'renewal' => array(
                'text' => 'Renewal', 
                'class' => 'badge-blue', 
            ), 
            'new' => array(
                'text' => 'Baru', 
                'class' => 'badge-blue', 
            ), 
        ), $options);

        $text = Common::hashEmptyField($options, sprintf('%s.text', $status));
        $class = Common::hashEmptyField($options, sprintf('%s.class', $status));
        
        $options = Common::hashEmptyField($options, (int)$status);
        $options = Common::_callUnset($options, array(
            'text',
            'class',
        ));

        if($isRaw){
            return array(
                'class' => $class,
                'text' => $text
            );
        }else{
            return $this->badge($class, $text, $options);   
        }
    }

    public function badge($class = 'badge-gray2', $text = null, $options = array()){
        // $availableClasses = array(
        //  'badge-gray2', 
        //  'badge-blue', 
        //  'badge-red', 
        //  'badge-yellow', 
        //  'badge-green', 
        // );

        $badge = false;
        $options = is_array($options) ? $options : array();

        $link = Common::hashEmptyField($options, 'link');
        $div = Common::hashEmptyField($options, 'div', true, array(
            'isset' => true,
        ));
        $options = Common::_callUnset($options, array(
            'link',
            'div',
        ));
        
        // if($class && in_array($class, $availableClasses)){
            $badge = $this->Html->tag('div', '', array(
                'class' => sprintf('data-badge %s', $class), 
            ));

            if( !empty($text) ){
                if( is_array($text) ) {
                    $text_class = Common::hashEmptyField($text, 'class');
                    $text = Common::hashEmptyField($text, 'text');
                } else {
                    $text_class = null;
                }

                $badge.= $this->Html->tag('span', $text, array(
                    'class' => $text_class,
                ));
            }

            if( !empty($link) ) {
                $url = Common::hashEmptyField($link, 'url');
                $class = Common::hashEmptyField($link, 'class');

                $badge = $this->Html->link($badge, $url, array(
                    'escape' => false,
                    'class' => $class,
                ));
            }

            if($div){
                $class = Common::hashEmptyField($div, 'class', 'data-status');

                $badge = $this->Html->tag('div', $badge, array_replace(array(
                    'class' => $class, 
                ), $options));
            }
        // }

        return $badge;
    }

    function getPaymentStatus($data, $tag = true, $model = 'Booking'){
        $tipe_status = array(
            'pending'   => __('Abandoned'),
            'paid'      => __('Lunas'),
            'cancelled' => __('Dibatalkan'),
            'waiting'   => __('Menunggu pembayaran'),
            'completed' => __('Selesai'),
            'expired'   => __('Kadaluarsa')
        );

        $raw_status_payment     = Common::hashEmptyField($data, $model.'.status_payment', '');
        $raw_is_from_cart       = Common::hashEmptyField($data, $model.'.is_from_cart', '');
        $is_transfer_rumahku    = Common::hashEmptyField($data, 'OrderPayment.is_transfer_rumahku');
        $is_confirm_transfer    = Common::hashEmptyField($data, 'OrderPayment.is_confirm_transfer');
        $response_code          = Common::hashEmptyField($data, 'OrderPayment.response_code');
        $payment_status         = Common::hashEmptyField($data, 'OrderPayment.payment_status');
        $api_request_developer_id   = Common::hashEmptyField($data, 'OrderPayment.api_request_developer_id');

        if((!empty($is_transfer_rumahku) || empty($raw_is_from_cart)) && $raw_status_payment == 'paid'){
            $raw_status_payment = 'completed';
        }

        if(!empty($api_request_developer_id) && $payment_status == 'paid' && empty($is_confirm_transfer)){

            if(!empty($payment_status) && $payment_status == 'cancelled_third_party' && $response_code == '5510'){
                $raw_status_payment = $payment_status;
            }else{
                $raw_status_payment = 'pending_confirmation';
            }
        }

        $result = $this->_callStatusOrder($raw_status_payment);

        return $result;
    }
}
?>