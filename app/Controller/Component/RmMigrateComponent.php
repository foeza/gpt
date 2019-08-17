<?php
class RmMigrateComponent extends Component {
	var $components = array(
		'Email', 'Session', 'RequestHandler',
		'RmCommon', 'Auth',
	);

	/**
	*	@param object $controller - inisialisasi class controller
	*/
	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callMigrateEaston ( $value ) {
		$tipeid = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'tipepropertiid');
		$arahhadap = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'arahhadap');
		$tipelistingid = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'tipelistingid');
		$lokasiid = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'lokasiid');
		$lokasisubid = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'lokasisubid');

		$kondisi = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'kondisi');
		$kondisiproperti = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'kondisiproperti');

		$view = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'view');
		$sertifikat = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'sertifikat');

		$tipe = $this->controller->LotpropertyListing->LotpropertyTipeproperti->find('first', array(
			'conditions' => array(
				'LotpropertyTipeproperti.tipepropertiid' => $tipeid,
			),
		));
		$tipename = $this->RmCommon->filterEmptyField($tipe, 'LotpropertyTipeproperti', 'tipeproperti');

		switch ($kondisi) {
			case 'Full Furnished':
				$value['LotpropertyListing']['kondisi'] = 3;
				break;
			case 'Semi furnished':
				$value['LotpropertyListing']['kondisi'] = 2;
				break;
			default:
				$value['LotpropertyListing']['kondisi'] = 1;
				break;
		}

		$value = $this->controller->LotpropertyListing->LotpropertyLokasi->getMerge($value, $lokasiid);
		$value = $this->controller->LotpropertyListing->LotpropertyLokasisub->getMerge($value, $lokasisubid);
		$value = $this->controller->User->Property->PropertyType->getMerge($value, $tipename, 'PropertyType.name', array(
			'cache' => array(
				'name' => __('PropertyType.%s', $tipename),
			),
		));
		$value = $this->controller->User->Property->PropertyAction->getMerge($value, $tipelistingid, 'PropertyAction.id');
		$value = $this->controller->User->Property->PropertyAsset->PropertyDirection->getMerge($value, $arahhadap, false, 'PropertyDirection.name');
		$value = $this->controller->User->Property->PropertyAsset->PropertyCondition->getMerge($value, $kondisiproperti, false, 'PropertyCondition.name');
		$value = $this->controller->User->Property->PropertyAsset->ViewSite->getMerge($value, $view, 3, false, 'ViewSite.name');
		$value = $this->controller->User->Property->Certificate->getMerge($value, $sertifikat, false, 'Certificate.name', array(
			'cache' => array(
				'name' => __('Certificate.%s', $sertifikat)
			),
		));
		
		$kota = $this->RmCommon->filterEmptyField($value, 'LotpropertyLokasi', 'lokasi');
		$subarea = $this->RmCommon->filterEmptyField($value, 'LotpropertyLokasisub', 'lokasisub');

		$value = $this->controller->User->Property->PropertyAddress->City->getMerge($value, $kota, 'City', 'City.name');
		$city_id = $this->RmCommon->filterEmptyField($value, 'City', 'id');
		
		$value = $this->controller->User->Property->PropertyAddress->Subarea->getMerge($value, $subarea, 'Subarea', 'Subarea.name', array(
			'conditions' => array(
				'Subarea.city_id' => $city_id,
			),
		));
		
		$region_id = $this->RmCommon->filterEmptyField($value, 'City', 'region_id');
		$value = $this->controller->User->Property->PropertyAddress->Region->getMerge($value, $region_id, 'Region', array(
			'cache' => array(
				'name' => __('Region.%s', $region_id),
			),
		));
		
		return $value;
	}

	function _callMigrateKuta ( $value ) {
		$tipe = $this->RmCommon->filterEmptyField($value, 'KutaPropertyCategoryText', 'name');
		$region_id = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'state');
		$city_id = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'city_id');

		$value = $this->controller->KutaProperty->KutaState->getMerge($value, $region_id);
		$value = $this->controller->KutaProperty->KutaCity->getMerge($value, $city_id);

		$value = $this->controller->User->Property->PropertyType->getMerge($value, $tipe, 'PropertyType.name', array(
			'cache' => array(
				'name' => __('PropertyType.%s', $tipe),
			),
		));
		
		$region = $this->RmCommon->filterEmptyField($value, 'KutaState', 'name');
		$city = $this->RmCommon->filterEmptyField($value, 'KutaCity', 'name');

		$value = $this->controller->User->Property->PropertyAddress->Region->getMerge($value, $region, 'Region', array(
			'cache' => array(
				'name' => __('Region.%s', $region),
			),
		));
		$value = $this->controller->User->Property->PropertyAddress->City->getMerge($value, $city, 'City', 'City.name');
		
		return $value;
	}
}
?>