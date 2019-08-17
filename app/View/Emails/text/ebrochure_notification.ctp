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
	$userPhones		= array_filter(array($userHP, $userHP2));
	$userPhones		= implode(' / ', $userPhones);

//	property owner data
	$ownerEmail		= Common::hashEmptyField($propertyData, 'User.email');
	$ownerFullName	= Common::hashEmptyField($propertyData, 'User.full_name');

	$mlsID	= Common::hashEmptyField($propertyData, 'Property.mls_id');
	$title	= Common::hashEmptyField($propertyData, 'Property.title');

	$propertyTitle = sprintf('%s - #%s', $title, $mlsID);

	$label	= $this->Property->getNameCustom($propertyData);
	$slug	= $this->Rumahku->toSlug($label);

	echo(__($subject)."\n\n");
	echo(__('Nama Agen : %s', $userFullName)."\n");
	echo(__('Email : %s', $userEmail)."\n");
	echo(__('No. HP : %s', $userPhones)."\n");
	echo(__('Properti : %s', $propertyTitle)."\n");

?>