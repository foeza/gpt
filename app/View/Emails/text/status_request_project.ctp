<?php
		$company_name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
		$project_name = $this->Rumahku->filterEmptyField($params, 'ApiAdvanceDeveloper', 'name');
		$logo = $this->Rumahku->filterEmptyField($params, 'ApiAdvanceDeveloper', 'logo');

		$infoDev = $this->Project->_callInfoDeveloper($params);
        $property_type = $this->Rumahku->filterEmptyField($infoDev, 'Result', 'property_type');
		$dev_name = $this->Rumahku->filterEmptyField($infoDev, 'Result', 'dev_name');

		$infoProject = $this->Project->_callInfoProject($params);
		$result_address = $this->Rumahku->filterEmptyField($infoProject, 'result', 'result_address');

		$project_contact = $this->Rumahku->filterEmptyField($params, 'ApiAdvanceDeveloper', 'ProjectContact');
        $ContactInfo = $this->Project->_callProjectContact( $project_contact );
        $phone = $this->Rumahku->filterEmptyField($ContactInfo, 'ProjectContact', 'phone', '');
        $telephone = $this->Html->link($phone, sprintf('tel:%s', $phone));
        $email = $this->Rumahku->filterEmptyField($ContactInfo, 'ProjectContact', 'email');
		$email = $this->Html->link($email, sprintf('mailto:%s', $email));
        $contact = $email;
        if (!empty($phone)) {
        	$contact .= __(' or ').$telephone;
        }

		$logoProject = $this->Rumahku->photo_thumbnail(array(
			'url' => true,
			'save_path' => Configure::read('__Site.general_folder'), 
			'src'=> $logo, 
			'size' => 'sqm',
		));

		echo __('Hallo ');
		echo $company_name;
		echo "\n";
		echo __('Project ').$project_name;
		echo __(' sudah dinon-aktif/sudah dihapus dari Prime System.');
		echo __('Berikut infromasi project yang dinon-aktif/sudah dihapus : ');
		echo "\n";
		echo $this->Html->image($logoProject);
		echo "\n";
		echo $property_type.$dev_name;
		echo "\n";
		echo $project_name;
		echo "\n";
		echo __('Project contact : ').$contact;
		echo "\n";
		echo $result_address;

?>

