<?php 
        $index = !empty($index)?$index:0;
        $value = !empty($value)?$value:false;
        $item_class = !empty($item_class)?$item_class:'col-sm-4';
        $property_id = $this->Rumahku->filterEmptyField($value, 'Property', 'id');
        $price_measure = $this->Rumahku->filterEmptyField($value, 'Property', 'price_measure');

?>
<div id="myCarousel<?php echo $index; ?>" class="carousel slide prime-mortgage type-1">
    <div class="bank-control">
        <a class="prev control carousel-control" href="#myCarousel<?php echo $index; ?>" data-slide="prev"></a>
        <a class="next control carousel-control" href="#myCarousel<?php echo $index; ?>" data-slide="next"></a>
    </div>
    <ul class="bank-list carousel-inner row">
        <?php
                foreach ($list_banks as $key => $banks) {
                    $active = ($key == 0) ? 'active' : null;
                    $li = null;

                    foreach($banks AS $i => $bank){
                        $bank_id = $this->Rumahku->filterEmptyField($bank, 'Bank', 'id');
                        $logo = $this->Rumahku->filterEmptyField($bank, 'Bank', 'logo');
                        $bank_name = $this->Rumahku->filterEmptyField($bank, 'Bank', 'name');
                        $dp = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'dp');
                        $periode_installment = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'periode_installment');
                        $setting_id = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'id');
                        $no_product = $this->Rumahku->filterEmptyField($bank, 'BankProduct', 'no_product');
                        $text_promo = $this->Rumahku->filterEmptyField($bank, 'BankProduct', 'text_promo');
                        $cnt_product = $this->Rumahku->filterEmptyField($bank, 'BankProduct', 'cnt_product');
                        $first_credit = $this->Rumahku->filterEmptyField($bank, 'BankProduct', 'first_credit');
                        $date_to = Common::hashEmptyField($bank, 'BankProduct.date_to');

                        $interest_rate_fix = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'interest_rate_fix');
                        $interest_rate_cabs = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'interest_rate_cabs');
                        $periode_fix = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'periode_fix');
                        $periode_cab = $this->Rumahku->filterEmptyField($bank, 'BankSetting', 'periode_cab');
                        $down_payment = $this->Kpr->_callCalcDp($price_measure, $dp);
                        
                        $dateTo = $this->Rumahku->customDate($date_to, 'd M Y');
                        $text_promo = $this->Rumahku->limitCharMore($text_promo, '64', '...', true, false, false);

                        // $text = __('%s%% Fix %s tahun', $interest_rate_fix, $periode_fix);

                        // $textCustom = $this->Html->tag('p', $text, array(
                        //     'class' => 'promo-text',
                        // ));

                        // if(!empty($interest_rate_cabs)){
                        //     $text = __(' %s%% Cap %s tahun', $interest_rate_cabs, $periode_cab);
                        //     $textCustom .= $this->Html->tag('p', $text, array(
                        //         'class' => 'promo-text',
                        //     ));
                        // }

                        $textCustom = $this->Html->tag('p', sprintf('Berakhir pada : %s', $this->Html->tag('strong', $dateTo)), array(
                            'class' => 'promo-text',
                        ));

                        $desc_promo = $this->Html->tag('div', $text_promo, array(
                            'class' => 'promo-desc',
                            'title' => $text_promo,
                        ));

                        $firstCreditCustom = $this->Rumahku->getConvertStringDecimal($first_credit, array(
                            'more' => true,
                        ));

                        $photo_logo = $this->Rumahku->photo_thumbnail(array(
                            'save_path' => Configure::read('__Site.logo_photo_folder'),
                            'src' => $logo, 
                            'size' => 'xsm',
                        ), array(
                            'alt' => $bank_name,
                            'title' => $bank_name,
                        ));

                        $photo =  $this->Html->tag('span', $photo_logo, array(
                            'class' => 'logo-bank',
                        ));

                        $installment = $this->Html->tag('p', sprintf('%s/bulan', $firstCreditCustom), array(
                            'class' => 'installment',
                        ));

                        $promo = $this->Html->tag('p', sprintf('%s Pilihan Promo', $cnt_product), array(
                            'class' => 'promo',
                        ));

                        $btn_apps = $this->Html->tag('p', __('Lihat Promo'));

                        $promo_apps =  $this->Html->tag('span', sprintf('%s %s %s', $installment, $promo, $btn_apps), array(
                            'class' => 'total-promo'
                        ));

                        $linkView = $this->Html->url(array(
                            'controller' => 'kpr',
                            'action' => 'detail_banks',
                            'property_id' => $property_id,
                            'bank_id' => $bank_id,
                            'down_payment'=> $down_payment,
                            'periode_installment' => $periode_installment,
                        ));

                        $result = $this->Html->link(sprintf('%s %s %s %s', $photo, $textCustom, $desc_promo, $promo_apps), sprintf('%s#result-calculator|20', $linkView) , array(
                            'escape' => false,
                        ));

                        $li .= $this->Html->tag('div', $this->Html->tag('div', $result, array(
                            'class' => 'bankSlides',
                        )), array(
                            'class' => $item_class
                        ));
                    }

                    echo $this->Html->tag('li', 
                        $this->Html->tag('div', $li, array(
                            'class' => 'row',
                        )), array(
                        'class' => sprintf('item col-md-12 %s', $active),
                    ));
                }
        ?>
    </ul>
</div>
<?php
        $classButton = $this->Kpr->widgetClass('button', $dataCompany);
        $next_button = $this->Html->link(__('Lihat Semua'), array(
            'controller' => 'kpr',
            'action' => 'select_product',
            'property_id' => $property_id,
        ), array(
            'type' => 'submit',
            'class' => sprintf('%s', $classButton),
        ));

        echo $this->Html->tag('div', $next_button, array(
            'class' => 'button-group tacenter additional-button',
        ));
?>
