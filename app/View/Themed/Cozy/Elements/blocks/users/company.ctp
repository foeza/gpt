<?php
        $domain = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'domain');
        $name = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name');
        $phone = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'phone');
        $phone_2 = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'phone_2');
        $fax = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'fax');

        $dataCompany = $this->Rumahku->filterEmptyField($value, 'UserCompany');
        $location = $this->Rumahku->getFullAddress($dataCompany, '<br>');

        if( !empty($domain) ) {
            $domain_url = $this->Rumahku->wrapWithHttpLink($domain, false);
            $domain = $this->Html->link(__('Visit Our Website'), $domain_url, array(
                'escape' => false,
                'class' => 'goto-website',
                'target' => '_blank',
            ));
        }
?>
<div class="info">
    <?php
            echo $this->Html->tag('header', $this->Html->tag('h2', $name));
    ?>
    
    <ul class="contact-us">
        <?php
                if( !empty($domain) ) {
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-globe').$domain);
                }
                
                if(!empty($location)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-map-marker').$location);
                }

                if(!empty($phone)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$phone);
                }

                if(!empty($phone_2)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$phone_2);
                }

                if(!empty($fax)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-fax').$fax);
                }
        ?>
    </ul>
</div>