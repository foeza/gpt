<?php 
        $defaultHideAddress = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_hidden_address_property');

        $dataAddress = $this->Rumahku->filterEmptyField($value, 'PropertyAddress');
        $hide_address = $this->Rumahku->filterEmptyField($value, 'PropertyAddress', 'hide_address');

        $address = $this->Property->getAddress($dataAddress);

        if( empty($defaultHideAddress) && empty($hide_address) ){
            echo $this->Html->tag('h1', __('Lokasi'), array(
                'class' => 'section-title',
            ));

            echo $this->Html->tag('p', $address);
        }
?>