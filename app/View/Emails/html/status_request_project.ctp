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
?>

<tr>
	<td align="center">
		<p style="margin: 0; padding-bottom: 30px; font-size: 20px; color: #24242f;">
		<?php echo __('Status Request Project'); ?></p>
	</td>
</tr>

<tr>
	<td>
		<p style="margin-top: 0;">Hallo <?php echo $company_name; ?></p>
		<p style="margin-top: 0; line-height:1.5;">Project <?php echo $project_name; ?> sudah dinon-aktif/sudah dihapus dari Prime System.</p><br/>
		<p style="margin-top: 0;">Berikut infromasi project yang dinon-aktif/sudah dihapus :</p>
		<?php echo $this->Html->image($logoProject); ?>
		<p style="margin-top: 0;"><strong><?php echo $property_type.$dev_name; ?></strong></p>
		<p style="margin-top: 0;"><strong><?php echo $project_name; ?></strong></p>
		<p style="margin-top: 0;">Project contact : <?php echo $contact; ?></p>
		<p style="margin-top: 0;"><?php echo $result_address; ?></p>
	</td>
</tr>