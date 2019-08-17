<?php
        // Sementara
        // $property_path = Configure::read('__Site.property_photo_folder');
        // $defaultHiddenMap = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_hidden_map');
        // $dataAddress = $this->Rumahku->filterEmptyField($value, 'PropertyAddress');
        // $hide_map = $this->Rumahku->filterEmptyField($dataAddress, 'hide_map');

        // $defaultHideAddress = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_hidden_address_property');
        // $hide_address = $this->Rumahku->filterEmptyField($dataAddress, 'hide_address');

        // if( empty($defaultHiddenMap) && empty($hide_map) ){
        //     $photo = $this->Rumahku->filterEmptyField($value, 'Property', 'photo');
        //     $title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
        //     $longitude = $this->Rumahku->filterEmptyField($dataAddress, 'longitude');
        //     $latitude = $this->Rumahku->filterEmptyField($dataAddress, 'latitude');
        //     $address = $this->Property->getAddress($dataAddress);

        //     $mainPhoto = $this->Rumahku->photo_thumbnail(array(
        //         'save_path' => $property_path, 
        //         'src'=> $photo, 
        //         'size' => 'm',
        //         'url' => true,
        //     ));

        //     echo $this->Html->tag('h1', __('Lokasi Properti'), array(
        //         'class' => 'section-title hidden-print',
        //     ));
        //     echo $this->Html->tag('div', '', array(
        //         'id' => 'property_location',
        //         'class' => 'map col-sm-12',
        //     ));

        //     echo $this->Form->hidden('latitude', array(
        //         'id'=>'property-map-latitude', 
        //         'value' => $latitude,
        //     ));
        //     echo $this->Form->hidden('longitude', array(
        //         'id'=>'property-map-longitude', 
        //         'value' => $longitude,
        //     ));
        //     echo $this->Form->hidden('name', array(
        //         'id'=>'property-map-name', 
        //         'value' => $title,
        //     ));
        //     echo $this->Form->hidden('photo', array(
        //         'id'=>'property-map-photo', 
        //         'value' => $mainPhoto,
        //     ));

        //     if( empty($defaultHideAddress) && empty($hide_address) ){
        //         echo $this->Form->hidden('address', array(
        //             'id'=>'property-map-address', 
        //             'value' => $address,
        //         ));
        //     }
        // }
?>