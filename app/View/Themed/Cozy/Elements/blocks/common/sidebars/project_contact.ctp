<?php
    $detail_project = !empty($detail_project)?$detail_project:false;

    if (!empty($detail_project)) {
        $_class = isset($_class)?$_class:'footer-contacts';
        $_class_li = !empty($_class_li)?$_class_li:false;

        // $project_contact = $this->Rumahku->filterEmptyField($detail_project, 'ApiAdvanceDeveloper', 'ProjectContact');
        // $ContactInfo = $this->Project->_callProjectContact( $project_contact );
        // $phone = $this->Rumahku->filterEmptyField($ContactInfo, 'ProjectContact', 'phone', '');
        // $fax = $this->Rumahku->filterEmptyField($ContactInfo, 'ProjectContact', 'fax', '');
        // $email = $this->Rumahku->filterEmptyField($ContactInfo, 'ProjectContact', 'email');

        $phone = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'phone', '', true, 'formatNumber');
        $phone2 = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'phone_2', '', true, 'formatNumber');
        $email = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'email');
        $email = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'contact_email', $email);
        $fax = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'fax', '', true, 'formatNumber');


        echo $this->Html->tag('h2', __('Project Contact'), array(
            'class' => 'section-title'
        ));

        ?>
        <ul class="<?php echo $_class; ?>">
            <?php
                if(!empty($email)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-envelope').$this->Html->link($email, sprintf('mailto:%s', $email)), array(
                        'class' => $_class_li,
                        'title' => __('Email'),
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

                if( !empty($fax) ) {
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-fax').$fax, array(
                        'class' => $_class_li,
                        'title' => __('Fax'),
                    ));
                }
            ?>
        </ul>
<?php
    
    }

?>