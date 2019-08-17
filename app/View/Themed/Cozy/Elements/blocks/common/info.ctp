<?php
        $_class = isset($_class)?$_class:'footer-contacts';
        $_class_li = !empty($_class_li)?$_class_li:false;

        $company = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany');
        $phone = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'phone', '', true, 'formatNumber');
        $phone2 = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'phone_2', '', true, 'formatNumber');
        $email = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'email');
        $email = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'contact_email', $email);
        $fax = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'fax', '', true, 'formatNumber');

        $address = $this->Rumahku->getFullAddress($company);

        // additional address
        $additional_address = $this->Rumahku->filterEmptyField($company, 'additional_address');
        $additional_address = nl2br($additional_address);

?>
<ul class="<?php echo $_class; ?>">
    <?php
            
            if(!empty($address)){
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-map-marker').$address, array(
                    'class' => $_class_li,
                    'title' => __('Alamat'),
                ));
            }

            if(!empty($additional_address)){
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-map-marker').$additional_address, array(
                    'class' => $_class_li,
                    'title' => __('Alamat'),
                ));
            }

            if(!empty($phone)){
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$this->Html->link($phone, 'tel:'.$phone), array(
                    'class' => $_class_li,
                    'title' => __('Telepon'),
                ));
            }

            if(!empty($phone2)){
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$this->Html->link($phone2, 'tel:'.$phone2), array(
                    'class' => $_class_li,
                    'title' => __('Telepon'),
                ));
            }

            if( !empty($_split) ) {
                echo $this->Rumahku->clearfix();
            }

            if(!empty($email)){
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-envelope').$this->Html->link($email, sprintf('mailto:%s', $email)), array(
                    'class' => $_class_li,
                    'title' => __('Email'),
                ));
            }
            if( !empty($fax) ) {
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-fax').$fax, array(
                    'class' => $_class_li,
                    'title' => __('Fax'),
                ));
            }
    ?>
</ul>