<?php 

	$params			= empty($params) ? array() : $params;
	$subject		= Common::hashEmptyField($params, 'subject');
	$ebrochureData	= Common::hashEmptyField($params, 'ebrochure', array());
	$propertyData	= Common::hashEmptyField($params, 'property', array());

//	ebrochure maker data
	$userFullName	= Common::hashEmptyField($ebrochureData, 'User.full_name');
	$userEmail		= Common::hashEmptyField($ebrochureData, 'User.email');
	$userHP			= Common::hashEmptyField($ebrochureData, 'UserProfile.no_hp');
	$userHP2		= Common::hashEmptyField($ebrochureData, 'UserProfile.no_hp_2');

//	$userPhones = array_filter(array($userHP, $userHP2));
	$userPhones = array();

	if($userEmail){
		$userEmail = $this->Html->link($userEmail, sprintf('mailto:%s', $userEmail), array(
			'escape'	=> false, 
			'target'	=> '_blank', 
		));
	}

	if($userHP){
		$userPhones[] = $this->Html->link($userHP, sprintf('tel:%s', $userHP), array(
			'escape'	=> false, 
			'target'	=> '_blank', 
		));
	}

	if($userHP2){
		$userPhones[] = $this->Html->link($userHP2, sprintf('tel:%s', $userHP2), array(
			'escape'	=> false, 
			'target'	=> '_blank', 
		));
	}

//	property owner data
	$ownerEmail		= Common::hashEmptyField($propertyData, 'User.email');
	$ownerFullName	= Common::hashEmptyField($propertyData, 'User.full_name');

	$mlsID	= Common::hashEmptyField($propertyData, 'Property.mls_id');
	$title	= Common::hashEmptyField($propertyData, 'Property.title');

	$even	= 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
	$odd	= $even.'background-color: transparent;';

	$propertyTitle = sprintf('%s - #%s', $title, $mlsID);

	$label 		= $this->Property->getNameCustom($propertyData);
	$slug	 	= $this->Rumahku->toSlug($label);
	$viewURL	= $this->Html->url(array(
		'admin'		 => false,
		'controller' => 'properties', 
		'action'	 => 'detail',
		'mlsid'		 => $mlsID,
		'slug'		 => $slug, 
	), true);

	echo($this->Html->tag('p', $subject, array(
		'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;', 
	)));

?>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<?php 

			$contentTr = $this->Html->tag('th', __('Nama Agen'), array(
				'style' => 'font-weight: bold;color:#303030;'.$even,
			));
			$contentTr .= $this->Html->tag('td', sprintf(': %s', $userFullName), array(
				'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
			));

			echo $this->Html->tag('tr', $contentTr);

			$contentTr = $this->Html->tag('th', __('Email'), array(
				'style' => 'font-weight: bold;color:#303030;'.$even,
			));
			$contentTr .= $this->Html->tag('td', sprintf(': %s', $userEmail), array(
				'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
			));

			echo $this->Html->tag('tr', $contentTr);

			$contentTr = $this->Html->tag('th', __('No. HP'), array(
				'style' => 'font-weight: bold;color:#303030;'.$even,
			));
			$contentTr .= $this->Html->tag('td', sprintf(': %s', implode(' / ', $userPhones)), array(
				'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
			));

			echo $this->Html->tag('tr', $contentTr);

			$contentTr = $this->Html->tag('th', __('Properti'), array(
				'style' => 'font-weight: bold;color:#303030;'.$even,
			));
			$contentTr .= $this->Html->tag('td', sprintf(': %s', $this->Html->link($propertyTitle, $viewURL)), array(
				'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
			));

			echo $this->Html->tag('tr', $contentTr);

		?>
	</tbody>
</table>