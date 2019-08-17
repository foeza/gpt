<?php
    
    $value = empty($value) ? array() : $value;

    if($value){
        $tag = !empty($tag) ? $tag : 'h4';
        $slugTheme = $this->Rumahku->filterEmptyField($dataCompany, 'Theme', 'slug');
        $list_banks = !empty($list_banks) ? $list_banks : false;
        $headerClass = !empty($headerClass) ? $headerClass : 'section-title';
        $_action = $this->Rumahku->filterEmptyField($value, 'Property', 'property_action_id');
        $on_progress_kpr = $this->Rumahku->filterEmptyField($value, 'Property', 'on_progress_kpr');
        $property_type_id = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'id');
        $is_building = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'is_building');
        $sold = $this->Rumahku->filterEmptyField($value, 'Property', 'sold');

        if( $_action == 1 && !empty($is_building) && in_array($property_type_id, array(1, 3, 7)) && $list_banks && empty($on_progress_kpr) && empty($sold) ) {
            echo $this->Kpr->headerTitle($slugTheme, $headerClass, array(
                'tag' => $tag,
            ));

            if(in_array($slugTheme, array('Estato'))){
                $number = 2;
                $item_class = 'col-sm-6';
            }else{
                $number = 3;
                $item_class = 'col-sm-4';
            }

            $desktop_list_banks = $this->Rumahku->_callSplitContentArr($list_banks, $number);
            $ipad_list_banks = $this->Rumahku->_callSplitContentArr($list_banks, 2);
            $m_list_banks = $this->Rumahku->_callSplitContentArr($list_banks, 1);

            if(!empty($list_banks)){
                echo $this->Html->tag('div', $this->element('widgets/kpr/banks/products', array(
                    'value' => $value, 
                    'list_banks' => $desktop_list_banks,
                    'item_class' => $item_class,
                )), array(
                    'class'=> 'desktop-787-only notranslate',
                ));

                echo $this->Html->tag('div', $this->element('widgets/kpr/banks/products', array(
                    'value' => $value, 
                    'list_banks' => $ipad_list_banks,
                    'item_class' => 'col-sm-6',
                    'index' => 'ipad',
                )), array(
                    'class'=> 'ipad-only notranslate',
                ));
                echo $this->Html->tag('div', $this->element('widgets/kpr/banks/products', array(
                    'value' => $value, 
                    'list_banks' => $m_list_banks,
                    'item_class' => 'col-sm-12',
                    'index' => 'mobile',
                )), array(
                    'class'=> 'mobile-only notranslate',
                ));

            }

        }
    }



?>

