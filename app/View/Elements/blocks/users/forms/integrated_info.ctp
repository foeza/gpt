<?php

    $currency   = Configure::read('__Site.config_currency_code');
    $currency   = $currency ? trim($currency) : NULL;
    $data_integrated = isset($data_integrated) ? $data_integrated : NULL;
    $places     = 2;

    if($data_integrated){
    //  invoice detail
        $r123_package = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'UserIntegratedAddonPackageR123');

        $recordID       = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'id');
        $userID         = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'user_id');
        $invoiceNumber  = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'invoice_number');

        $R123packageName = $this->Rumahku->filterEmptyField($r123_package, 'name', false, '-');
        $R123packagePrice = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'r123_base_price', 0);

        $OLXpackageName  = Common::hashEmptyField($data_integrated, 'UserIntegratedOrderAddon.UserIntegratedAddonPackageOLX.name');
        $OLXpackagePrice = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedAddonPackageOLX', 'price');
        $voucherCode    = $this->Rumahku->filterEmptyField($data_integrated, 'VoucherCode', 'code', 'N/A');
        $discountAmount = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'discount_price', 0);
        $totalAmount    = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'total_price', 0);
        $paymentStatus  = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'payment_status');
        $approvedDate   = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'created');
        $expiredDate    = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'expired_date');
        $paymentDate    = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'payment_datetime');
        $paymentCode    = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'payment_code');
        $transExpDate   = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrderAddon', 'transfer_expired_date');

    //  order detail
        $is_all_addon   = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'is_email_all_addon');
        $addon_r123     = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'addon_r123');
        $addon_olx      = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'addon_olx');

        $mail_all_addon = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'email_all_addon');
        $email_r123     = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'email_r123');
        $email_olx      = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'email_olx');

        $name           = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'name_applicant');
        $phone          = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'phone');
        $orderNumber    = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'order_number');
        $companyName    = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'company_name');
        $orderDate      = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'created');
        $status         = $this->Rumahku->filterEmptyField($data_integrated, 'UserIntegratedOrder', 'status');

        $dateFormat     = 'd M Y H:i';
        $approvedDate   = $this->Rumahku->formatDate($approvedDate, $dateFormat);
        $expiredDate    = $this->Rumahku->formatDate($expiredDate, $dateFormat);
        $paymentDate    = $this->Rumahku->formatDate($paymentDate, $dateFormat);
        $transExpDate   = $this->Rumahku->formatDate($transExpDate, $dateFormat);
        $orderDate      = $this->Rumahku->formatDate($orderDate, $dateFormat);

        ?>
        <div class="box box-success">
            <div class="box-body">
                <div id="document-detail" class="row">
                    <div class="col-xs-12 col-md-6">
                        <?php

                            echo($this->Html->tag('h3', __('Detail Order'), array('class' => 'custom-heading')));

                            $statuses = array(
                                'pending'   => $this->Html->tag('span', __('Pending'), array('class' => 'badge')), 
                                'request'   => $this->Html->tag('span', __('Request'), array('class' => 'badge badge-success')), 
                                'renewal'   => $this->Html->tag('span', __('Renewal'), array('class' => 'badge badge-info')),
                                'rejected'  => $this->Html->tag('span', __('Rejected'), array('class' => 'badge badge-danger')), 
                                'expired'   => $this->Html->tag('span', __('Expired'), array('class' => 'badge badge-inverse')), 
                                'waiting'   => $this->Html->tag('span', __('Waiting'), array('class' => 'badge badge-warning')), 
                            );

                            $template = $this->Html->tag('div', $this->Html->tag('b', __('Tgl. Daftar')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                            $template.= $this->Html->tag('div', $this->Html->tag('b', $orderDate), array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);

                            $template = $this->Html->tag('div', $this->Html->tag('b', __('Nomor Order')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                            $template.= $this->Html->tag('div', $this->Html->tag('b', $orderNumber), array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);
                            
                            $template = $this->Html->tag('div', $this->Html->tag('b', __('Nama')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                            $template.= $this->Html->tag('div', $name, array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);

                            if ($is_all_addon) {
                                $email = $this->Html->link($mail_all_addon, sprintf('mailto:%s', $mail_all_addon));

                                $template = $this->Html->tag('div', $this->Html->tag('b', __('Email All Addon')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                                $template.= $this->Html->tag('div', $email, array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                                $template = $this->Html->tag('div', $template, array('class' => 'row'));
                            } else {
                                if ($addon_r123) {
                                    $email = $this->Html->link($email_r123, sprintf('mailto:%s', $email_r123));

                                    $template = $this->Html->tag('div', $this->Html->tag('b', __('Email Addon R123')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                                    $template.= $this->Html->tag('div', $email, array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                                    $template = $this->Html->tag('div', $template, array('class' => 'row'));
                                }

                                if ($addon_olx) {
                                    $email = $this->Html->link($email_olx, sprintf('mailto:%s', $email_olx));

                                    $template = $this->Html->tag('div', $this->Html->tag('b', __('Email Addon OLX')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                                    $template.= $this->Html->tag('div', $email, array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                                    $template = $this->Html->tag('div', $template, array('class' => 'row'));
                                }
                            }

                            echo($template);

                            $template = $this->Html->tag('div', $this->Html->tag('b', __('No. Telepon')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                            $template.= $this->Html->tag('div', $phone, array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);

                            $template = $this->Html->tag('div', $this->Html->tag('b', __('Perusahaan')), array('class' => 'col-xs-12 col-sm-6 col-md-4'));
                            $template.= $this->Html->tag('div', $companyName, array('class' => 'col-xs-12 col-sm-6 col-md-8'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);

                        ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?php

                            echo($this->Html->tag('h3', __('Detail Invoice'), array('class' => 'custom-heading')));

                            $template = $this->Html->tag('div', $this->Html->tag('b', __('Nomor Invoice')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                            $template.= $this->Html->tag('div', $this->Html->tag('b', $invoiceNumber), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);
                            
                            if ($addon_r123) {
                                $template = $this->Html->tag('div', $this->Html->tag('b', __('Nama Membership Rumah 123')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                                $template.= $this->Html->tag('div', $R123packageName, array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                                $template = $this->Html->tag('div', $template, array('class' => 'row'));

                                echo($template);

                                $template = $this->Html->tag('div', $this->Html->tag('b', sprintf('%s (%s)', __('Harga Membership'), $currency)), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                                $template.= $this->Html->tag('div', 
                                    $this->Number->currency($R123packagePrice, '', array(
                                        'places' => $places, 
                                    )), 
                                    array(
                                        'class' => 'col-xs-12 col-sm-6 col-md-6', 
                                    )
                                );

                                $template = $this->Html->tag('div', $template, array('class' => 'row'));

                                echo($template);
                            }

                            if ($addon_olx) {
                                $template = $this->Html->tag('div', $this->Html->tag('b', __('Nama Membership OLX')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                                $template.= $this->Html->tag('div', $OLXpackageName, array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                                $template = $this->Html->tag('div', $template, array('class' => 'row'));

                                echo($template);
                            }

                            $template = $this->Html->tag('div', $this->Html->tag('b', __('Voucher')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                            $template.= $this->Html->tag('div', $voucherCode, array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            // echo($template);

                            $discountAmount = $this->Number->currency($discountAmount, '', array('places' => $places));
                            $discountAmount = sprintf('(%s)', $discountAmount);

                            $template = $this->Html->tag('div', $this->Html->tag('b', sprintf('%s (%s)', __('Potongan'), $currency)), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                            $template.= $this->Html->tag('div', $discountAmount, array('class' => 'col-xs-12 col-sm-6 col-md-6'));

                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            // echo($template);

                            echo($this->Html->tag('hr'));
                            $template = $this->Html->tag('div', $this->Html->tag('b', sprintf('%s (%s)', __('Total'), $currency)), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
                            $template.= $this->Html->tag('div', 
                                $this->Number->currency($totalAmount, '', array(
                                    'places' => $places, 
                                )), 
                                array(
                                    'class' => 'col-xs-12 col-sm-6 col-md-6', 
                                )
                            );

                            $template = $this->Html->tag('div', $template, array('class' => 'row'));

                            echo($template);

                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php

    }
    else{
        echo($this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border')));
    }

?>