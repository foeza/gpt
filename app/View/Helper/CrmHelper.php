<?php
class CrmHelper extends AppHelper {
    var $helpers = array(
        'Rumahku', 'Html', 'Form',
    );

    function getStatus ( $data, $tag = 'div' ) {
        $lblName = $this->Rumahku->filterEmptyField($data, 'AttributeSet', 'name');
        $bg_color = $this->Rumahku->filterEmptyField($data, 'AttributeSet', 'bg_color', '#FFF');

        $lblName = ucwords($lblName);
        $lblStyle = sprintf('background: %s;', $bg_color);

        if( !empty($tag) ) {
            return $this->Html->tag($tag, $lblName, array(
                'class' => 'label for-project mb10',
                'style' => $lblStyle,
            ));
        } else {
            return $lblName;
        }
    }

    function _callAttributeSetOptions ( $values ) {
        $options = false;

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $key = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'id');
                $labelOption = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'name');
                $labelType = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'type');

                switch ($labelType) {
                    case 'option':
                        $options[$key] = $labelOption;
                        break;
                }
            }
        }

        return $options;
    }

    function _callAttributeOptions ( $value, $label = true, $class = false, $attributes = array() ) {
        $result = '';
        $options = array();
        $optionsInput = array(
            'formGroupClass' => false,
            'wrapperClass' => false,
            'frameClass' => false,
            'labelClass' => false,
            'class' => false,
        );

        $activity_id = $this->Rumahku->filterEmptyField($attributes, 'activity_id');
        $crm_project_id = $this->Rumahku->filterEmptyField($attributes, 'crm_project_id');
        $session_id = $this->Rumahku->filterEmptyField($attributes, 'session_id');
        $crmProject = $this->Rumahku->filterEmptyField($attributes, 'crmProject');

        $wrapperAttribute = $this->Rumahku->filterEmptyField($attributes, 'wrapperAttribute', false, 'wrapper-attribute'.$activity_id);
        $dataParams = $this->Rumahku->filterEmptyField($attributes, 'dataParams');

        $id = $this->Rumahku->filterEmptyField($value, 'Attribute', 'id');
        $labelName = $this->Rumahku->filterEmptyField($value, 'Attribute', 'name');
        $fieldType = $this->Rumahku->filterEmptyField($value, 'Attribute', 'type');
        $dataPayment = $this->Rumahku->filterEmptyField($crmProject, 'CrmProjectPayment');

        $editPayment = Set::extract('/CrmProjectActivityAttributeOption/AttributeOptionChild/is_payment', $crmProject);
        $editPayment = array_values($editPayment);

        $slug = $this->Rumahku->toSlug($labelName, '_');

        $dataOptions = $this->Rumahku->filterEmptyField($value, 'Child');
        $dataOptions = $this->Rumahku->filterEmptyField($value, 'AttributeOption', false, $dataOptions);

        if( !empty($dataOptions) ) {
            foreach ($dataOptions as $key => $option) {
                $attribute_id = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'attribute_id');
                $attribute_slug = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'slug');
                $parent_id = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'parent_id');
                $key = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'id');
                $labelOption = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'name');
                $labelType = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'type');
                $labelSlug = $this->Rumahku->toSlug($labelOption, '_');

                $is_payment = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'is_payment');
                $childAttr = $this->Rumahku->filterEmptyField($option, 'Child');
                $fieldNameRoot = 'CrmProjectActivityAttributeOption.attribute_option_id.'.$attribute_id;
                $fieldName = 'CrmProjectActivityAttributeOption.attribute_option_child_id.'.$attribute_id.'.'.$key;

                if( in_array($labelType, array( 'phone_number', 'email' )) ) {                    
                    $labelType = 'text';
                }

                switch ($attribute_slug) {
                    case 'email-klien':
                        $attribute_val = Common::hashEmptyField($crmProject, 'CurrentClient.email');
                        break;
                    case 'no-telp':
                        $attribute_val = Common::hashEmptyField($crmProject, 'CurrentClient.phone');
                        $attribute_val = Common::hashEmptyField($crmProject, 'CurrentClient.no_hp_2', $attribute_val);
                        $attribute_val = Common::hashEmptyField($crmProject, 'CurrentClient.no_hp', $attribute_val);
                        break;
                    default:
                        $attribute_val = null;
                        break;
                }

                switch ($labelType) {
                    case 'option':
                        if( ( !empty($is_payment) && empty($dataPayment) ) || empty($is_payment) || !empty($editPayment) ) {
                            $options[$key] = $labelOption;
                        }
                        break;
                    case 'text':
                        $result = $this->Html->tag('div', $this->Form->label($fieldName, $labelOption).$this->Html->tag('div', $this->Form->input($fieldName, array(
                            'type' => 'text',
                            'label' => false,
                            'required' => false,
                            'div' => false,
                            'error' => false,
                            'value' => $attribute_val,
                        )), array(
                            'class' => 'input-group side',
                        )), array(
                            'class' => 'col-sm-12 mt15',
                        ));
                        break;
                    case 'select':
                        $optionsValue = $this->_callAttributeSetOptions($childAttr);
                        $result = $this->Html->tag('div', $this->Form->label($fieldName, $labelOption).$this->Html->tag('div', $this->Html->tag('div', $this->Form->input($fieldName, array(
                            'label' => false,
                            'required' => false,
                            'div' => false,
                            'error' => false,
                            'empty' => sprintf(__('- Pilih %s -'), $labelOption),
                            'options' => $optionsValue,
                        )).$this->Rumahku->icon('rv4-angle-down', false, 'span'), array(
                            'class' => 'select',
                        )), array(
                            'class' => 'input-group side',
                        )), array(
                            'class' => 'col-sm-12 mt15',
                        ));
                        break;
                    case 'price':
                        $currency = Configure::read('__Site.config_currency_symbol');

                        if( empty($activity_id) ) {
                            $uploadContent = $this->Html->tag('div', $this->Rumahku->buildInputForm('CrmProjectActivity.booking_fee', array(
                                'type' => 'file',
                                'formGroupClass' => false,
                                'frameClass' => 'col-sm-12',
                                'labelClass' => false,
                                'class' => false,
                                'label' => __('Bukti Pembayaran'),
                            )), array(
                                'class' => 'col-sm-4 mt30',
                            ));
                        } else {
                            $uploadContent = false;
                        }

                        $result = $this->Rumahku->buildInputForm($fieldName, array(
                            'type' => 'text',
                            'formGroupClass' => false,
                            'frameClass' => 'col-sm-12 crm-payment-form ',
                            'labelClass' => false,
                            'class' => false,
                            'label' => $labelOption,
                            'inputClass' => 'input_price',
                            'textGroup' => $currency,
                            'positionGroup' => 'left',
                            'fieldError' => sprintf('CrmProjectActivityAttributeOption.%s', $labelSlug),
                        ));
                        $result = $this->Html->tag('div', $result, array(
                            'class' => 'col-sm-8 mt15',
                        )).$uploadContent.$this->Form->hidden($fieldNameRoot, array(
                            'value' => $parent_id,
                        ));
                        break;
                    case 'payment':
                        $result = $this->_View->element('blocks/crm/forms/payment').$this->Form->hidden($fieldName, array(
                            'value' => $key,
                        )).$this->Form->hidden($fieldNameRoot, array(
                            'value' => $parent_id,
                        ));
                        break;
                    case 'addkpr':
                         $result = $this->_View->element('blocks/crm/forms/add_kpr').$this->Form->hidden($fieldName, array(
                            'value' => $key,
                        )).$this->Form->hidden($fieldNameRoot, array(
                            'value' => $parent_id,
                        ));
                        break;
                }

                $result = $this->Html->tag('div', $result, array(
                    'class' => 'row',
                ));
            }
        }

        switch ($fieldType) {
            case 'select':
                $result = $this->Form->label($fieldNameRoot, $labelName).$this->Html->tag('div', $this->Html->tag('div', $this->Rumahku->buildInputForm($fieldNameRoot, array(
                    'type' => 'select',
                    'formGroupClass' => false,
                    'wrapperClass' => false,
                    'frameClass' => false,
                    'labelClass' => false,
                    'class' => false,
                    'error' => false,
                    'inputClass' => 'ajax-attribute',
                    'label' => false,
                    'options' => $options,
                    'empty' => sprintf(__('- Pilih %s -'), $labelName),
                    'attributes' => array(
                        'data-wrapper-write' => '#'.$wrapperAttribute,
                        'data-params' => $dataParams,
                        'data-form' => '#form-crm-activity',
                        'data-href' => $this->Html->url(array(
                            'controller' => 'crm',
                            'action' => 'attributes',
                            $crm_project_id,
                            'session_id' => $session_id,
                            'activity_id' => $activity_id,
                            'admin' => true,
                        )),
                    ),
                )).$this->Rumahku->icon('rv4-angle-down', false, 'span'), array(
                    'class' => 'select',
                )), array(
                    'class' => 'input-group',
                ));
                break;
            case 'price':
                $result = $this->Form->label($fieldNameRoot, $labelName).$this->Html->tag('div', $this->Rumahku->buildInputForm($fieldNameRoot, array(
                    'formGroupClass' => false,
                    'wrapperClass' => false,
                    'frameClass' => false,
                    'labelClass' => false,
                    'class' => false,
                    'error' => false,
                    'inputClass' => 'input_price',
                    'label' => false,
                    'placeholder' => $labelName,
                )), array(
                    'class' => 'input-group',
                ));
                break;
        }

        if( !empty($result) ) {
            $result .= $this->Form->error('CrmProjectActivityAttributeOption.'.$slug);
            $result = $this->Html->tag('div', $result, array(
                'class' => $class,
            ));
        }

        return $result;
    }

    function _callProjectClosing ( $value = false, $self = true ) {
        $closing_id = $this->Rumahku->filterEmptyField($value, 'CrmClosing', 'id');
        $crm_project_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
        $attribute_set_slug = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'slug');

        if( !empty($self) ) {
            $flagSelf = ($closing_id == $crm_project_id);
        } else {
            $flagSelf = false;
        }

        if( empty($closing_id) || ( $flagSelf && $attribute_set_slug != 'complete' ) ) {
            return false;
        } else {
            return true;
        }
    }

    // function _callStatusBankRequest ( $data, $tagHtml = false,$complete = false ) {
    //     $id = $this->Rumahku->filterEmptyField($data, 'KprApplicationRequest', 'id');

    //     $result = $this->_getStatusBankRequest($data, array(
    //         'type' => $tagHtml,
    //     ));
    //     $status = $this->_getColorRequest($result, true, $id, $complete);

    //     return $status;
    // }

    function _getStatusBankRequest ( $data, $options = array() ) {
        $color = $flag = false;
        $label = $status = false;

        $snyc = $this->Rumahku->filterEmptyField($data, 'snyc');
        $document_status = $this->Rumahku->filterEmptyField($data, 'document_status');
        $is_hold = Common::hashEmptyField($data, 'value.KprBank.is_hold');

        $type = $this->Rumahku->filterEmptyField($options, 'type', false, 'html');
        $slug = $this->Rumahku->filterEmptyField($options, 'slug');

        if( !empty($is_hold) ) {
            $label = 'hold';
            $status = __('HOLD');
            $color = 'color-black label black';
        } else {
            if($slug == 'kpr'){
                $flag_complete = in_array($document_status, array('completed'));
                $flag_PK = in_array($document_status, array('approved_credit'));
                $flag_appraisal = in_array($document_status, array('approved_bank'));
                $flag_process = in_array($document_status, array('process', 'approved_proposal', 'approved_bi_checking', 'approved_verification'));
                $flag_rejected = in_array($document_status, array('rejected_admin', 'rejected_proposal', 'rejected_bi_checking', 'rejected_verification', 'rejected_bank', 'rejected_credit'));

                if($flag_complete){
                    $document_status = 'completed';
                
                } else  if ($flag_PK){
                    $document_status = 'approved_credit';

                } else if ($flag_appraisal){
                    $document_status = 'approved_bank';
                
                } else if ($flag_process){
                    $document_status = 'process';
                
                } else if($flag_rejected){
                    $document_status = 'rejected';
                }
            }

            if( in_array($document_status, array( 'approved_proposal', 'approved_verification', 'process_appraisal' )) ) {
                $document_status = 'process';
            }

            switch($document_status){
                case 'rejected' :
                    $label = 'rejected';
                    $status = __('Ditolak');
                    $color = 'label red label red';
                    break;
                case 'pending' :
                    $label = 'pending';
                    $status = __('Pending');
                    $color = 'label grey';
                    break;
                case 'cancel' :
                    $label = 'cancel';
                    $status = __('Cancel');
                    $color = 'label red';
                    break;
                case 'approved_admin' :
                    $label = 'approved_admin';
                    $status = __('Terkirim');
                    $color = 'color-green label blue';
                    break;
                case 'rejected_admin' :
                    $label = 'rejected_admin';
                    $status = __('Ditolak');
                    $color = 'color-red label red';
                    break;
                case 'process' :
                    $label = 'process';
                    $status = __('Proses');
                    $color = 'color-red label orange';
                    break;
                // case 'proposal_without_comiission' :
                //     $label = 'proposal_without_comiission';
                //     $status = __('Referral Disetujui & Provisi Ditolak');
                //     $color = 'color-red label orange-dark';
                //     break;
                case 'approved_proposal' :
                    $label = 'approved_proposal';
                    $status = __('KPR Diterima');
                    $color = 'color-red label orange';
                    break;
                case 'rejected_proposal' :
                    $label = 'rejected_proposal';
                    $status = __('KPR Ditolak');
                    $color = 'color-red label red';
                    break;
                case 'approved_bank' :
                    $label = 'approved_bank';
                    $status = __('Appraisal');
                    $color = 'color-green label blue-dark';
                    break;
                case 'rejected_bank' :
                    $label = 'rejected_bank';
                    $status = __('Ditolak Bank');
                    $color = 'color-red label red';
                    break;
                case 'credit_process' :
                    $label = 'credit_process';
                    $status = __('Proses Akad');
                    $color = 'color-red label blue-dark';
                    break;
                case 'reschedule_pk' :
                    $label = 'reschedule_pk';
                    $status = __('Reschedule Akad');
                    $color = 'color-red label blue-light';
                    break;
                case 'rejected_credit' :
                    $label = 'rejected_credit';
                    $status = __('Akad Ditolak');
                    $color = 'color-red label red';
                    break;
                case 'approved_credit' :
                    $label = 'approved_credit';
                    $status = __('Akad Disetujui');
                    $color = 'color-red label purple';
                    break;
                case 'completed' :
                    $label = 'completed';
                    $status = __('Completed');
                    $color = 'color-red label green';
                    break;
                case 'approved_verification' :
                    $label = 'process';
                    $status = __('Lulus Verifikasi Dokumen');
                    $color = 'color-red label orange';
                    break;
                case 'rejected_verification' :
                    $label = 'rejected_verification';
                    $status = __('Tidak Lulus Verifikasi');
                    $color = 'color-red label red';
                    break;
                case 'hold' :
                    $label = 'hold';
                    $status = __('HOLD');
                    $color = 'color-black label black';
                    break;
                case 'rejected_bi_checking' :
                    $label = 'process';
                    $status = __('Tidak Lulus BI Checking');
                    $color = 'color-red label red';
                    break;
                case 'approved_bi_checking' :
                    $label = 'process';
                    $status = __('BI Checking Disetujui');
                    $color = 'color-red label orange';
                    break;
            }
        }

        if(in_array($label, array('cancel', 'rejected_admin', 'rejected_proposal', 'rejected_bank', 'rejected_credit'))){
            $flag = true;
        }

        if( $type == 'html' ) {
            return $this->Html->tag('span', $status, array_merge_recursive($options, array(
                'class' => $color.' for-project',
            )));
        } else if( $type == 'arr' ) {
            return array(
                'status' => $status,
                'color' => $color,
                'label' => $label,
                'flag' => $flag,
            );
        } else {
            return $label;
        }
    }

    // function _callStatusSnycRequest($data,$tagHtml = false,$complete = false){

    //     $snyc = $this->Rumahku->filterEmptyField($data, 'KprApplicationRequest', 'snyc');
    //     $result = $this->_getStatusSnycRequest( $snyc, $tagHtml );
    //     $status = $this->_getColorRequest($result,$tagHtml,false,$complete);
    //     return $status;

    // }

    function _getStatusSnycRequest($snyc,$tagHtml = false){
        $color = false;
        if(!empty($snyc)){
            $status = 'Sent';
            $color = 'color-green label send';
        }else{
            $status = 'Pending';
            $color = 'label grey';
        }

        return array(
            'status' => $status,
            'color' => $color,
        );

    }

    function _getColorRequest($result, $tagHtml = false, $id = null, $complete = false){
        $status_approved = $this->Rumahku->filterEmptyField($result,'status');
        
        if( !empty($tagHtml) ) {
            $status = !empty($result['status'])?$result['status']:false;
            $color = !empty($result['color'])?$result['color']:false;

            $status =  $this->Html->tag('span', false, array(
                'class' => $color.' for-project mr5',
                'title' => $status,
            ));
        } else {
            $status = !empty($result['status'])?$result['status']:false;
        }    

        return $status;
    }

    function unShowType () {
        $attributeSetOption = $this->Rumahku->filterEmptyField($this->request->data, 'AttributeSetOption');

        if( !empty($attributeSetOption) ) {
            foreach ($attributeSetOption as $key => $child) {
                $childArr = $this->Rumahku->filterEmptyField($child, 'Child');

                if( !empty($childArr) ) {
                    foreach ($childArr as $key => $option) {
                        $attributeOption = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'show');

                        if( empty($attributeOption) ) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                } else {
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    function unShowStatus ( $attribute_set_id = false ) {
        if( in_array($attribute_set_id, array( 1,2 )) ) {
            $attributeSetOption = $this->Rumahku->filterEmptyField($this->request->data, 'AttributeSetOption');

            if( !empty($attributeSetOption) ) {
                return $this->unShowType();
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    function getDocumentStatus($documentCategories, $options = array()){
        $divClass = $this->Rumahku->filterEmptyField( $options, 'divClass', false, null);
        $hideFile = $this->Rumahku->filterEmptyField( $options, 'hideFile', false, true);
        
        $divRow = $this->Rumahku->filterEmptyField( $options, 'divRow');
        $divide = !empty($divide)?$divide:false;
        if(!empty($divRow)){
            $divide = count($documentCategories)/2;
            $divide = round($divide);
        }

        $documentContent = '';
        $i = 0;
        $j = 0;
        
        if( !empty($documentCategories) ) {
            foreach ($documentCategories as $key => $category) {
                if(empty($documentContent[$i])){
                    $documentContent[$i] = '';
                }

                $category_id = $this->Rumahku->filterEmptyField($category, 'DocumentCategory', 'id');

                if( !empty($category_id) ) {
                    $categoryName = $this->Rumahku->filterEmptyField($category, 'DocumentCategory', 'name');
                    $crmProjectDocument = $this->Rumahku->filterEmptyField($category, 'CrmProjectDocument');
                    $fileId = $this->Rumahku->filterEmptyField($crmProjectDocument, 'id');
                    // $fileName = $this->Rumahku->filterEmptyField($crmProjectDocument, 'name', false, 'noname.jpg');
                    $fileName = __('Download');
                    $fileTitle = $this->Rumahku->filterEmptyField($crmProjectDocument, 'title');
                    
                    if( !empty($crmProjectDocument) ) {
                        $documentContent[$i] .= $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $categoryName), array(
                            'class' => 'label-file',
                        )).$this->Html->tag('div', $this->Html->link($fileName, array(
                            'controller' => 'settings',
                            'action' => 'download',
                            'crm_document',
                            $fileId,
                            'admin' => true,
                        ), array(
                            'title' => $fileTitle,
                        )), array(
                            'class' => 'input-file',
                        )), array(
                            'class' => 'active',
                        ));
                    } else {
                        if($hideFile == 'hideFile'){
                            $documentContent[$i] .= $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $categoryName), array(
                                'class' => 'label-file empty'
                            )));
                        }else{
                            $documentContent[$i] .= $this->Html->tag('li', $this->Rumahku->buildInputForm('CrmProjectDocument.file.'.$category_id, array(
                                'type' => 'file',
                                'wrapperClass' => false,
                                'formGroupClass' => false,
                                'frameClass' => false,
                                'rowFormClass' => false,
                                'labelClass' => 'label-file',
                                'class' => 'input-file',
                                'label' => $categoryName,
                            ))); 
                        }
                    }

                    if($divide > $j){
                        $i++;
                        $j=0;
                    }
                }
                $j++;
            }
                
            if( !empty($documentContent) ) {
                $document = '';
                foreach($documentContent AS $key => $content){

                    if(!empty($divRow)){
                        $document .= $this->Html->tag('div', $this->Html->tag('ul', $content, array(
                            'class' => 'list-documents '.$divClass,
                        )), array(
                            'class' => 'col-sm-6'
                        ));        
                    }else{
                        $document = $this->Html->tag('ul', $content, array(
                            'class' => 'list-documents '.$divClass,
                        ));
                    }
                } 
                $textdocument = $this->Html->tag('div', $document, array(
                    'class' => 'row',
                ));
                
            }
        }

        return $textdocument;
    }

    function _callLastActivity ( $value, $options = array() ) {
        $empty = Common::hashEmptyField($options, 'empty', '-');
        $use_link = Common::hashEmptyField($options, 'use_link', true);
        $crm_project_id = Common::hashEmptyField($value, 'LastActivity.crm_project_id');

        if( !empty($crm_project_id) ) {
            $last_activity = Common::hashEmptyField($value, 'LastActivity.note', NULL, array(
                'urldecode' => false,
            ));
            $last_activity = !empty($last_activity)?implode('<br>', $last_activity):false;

            if( !empty($use_link) ) {
                $last_activity = $this->Html->link($last_activity, array(
                    'controller' => 'crm',
                    'action' => 'project_detail',
                    $crm_project_id,
                    'admin' => true,
                ), array(
                    'target' => '_blank',
                    'escape' => false,
                ));
            }
        } else {
            $last_activity = $empty;
        }

        return $last_activity;
    }
}