<?php 
        $name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
        $longitude = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'longitude');
        $latitude = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'latitude');

        $company = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany');
        $address = $this->Rumahku->getFullAddress($company);

        $logo_path = Configure::read('__Site.logo_photo_folder');
        $logo = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');
        $customLogo = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $logo_path, 
            'src'=> $logo, 
            'size' => 'xxsm',
            'url' => true,
        ));

        echo $this->Form->hidden('latitude', array(
            'id'=>'company-map-latitude', 
            'value' => $latitude,
        ));
        echo $this->Form->hidden('longitude', array(
            'id'=>'company-map-longitude', 
            'value' => $longitude,
        ));
        echo $this->Form->hidden('name', array(
            'id'=>'company-map-name', 
            'value' => $name,
        ));
        echo $this->Form->hidden('address', array(
            'id'=>'company-map-address', 
            'value' => $address,
        ));
        echo $this->Form->hidden('logo', array(
            'id'=>'company-map-logo', 
            'value' => $customLogo,
        ));
?>