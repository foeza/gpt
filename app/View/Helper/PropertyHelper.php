<?php
class PropertyHelper extends AppHelper {
	var $helpers = array(
		'Rumahku', 'Html', 'Number',
		'Session', 'AclLink.AclLink'
	);

	function getPropertyStatusListing( $data ) {
		$category_name = $this->Rumahku->filterEmptyField($data, 'PropertyStatusListing', 'name');
		$badge_color = $this->Rumahku->filterEmptyField($data, 'PropertyStatusListing', 'badge_color');

		if( !empty($category_name) ) {
			return $this->Html->tag('span', $category_name, array(
				'class' => 'btn label-premium',
				'style' => 'color:white;background-color:'.$badge_color.';',
			));
		} else {
			return false;
		}
	}

	function getStatus ( $data, $tag = false, $_action = true ) {
		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$active = $this->Rumahku->filterEmptyField($data, 'Property', 'active', 0);
		$status = $this->Rumahku->filterEmptyField($data, 'Property', 'status', 0);
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold', 0);
		$published = $this->Rumahku->filterEmptyField($data, 'Property', 'published', 0);
		$deleted = $this->Rumahku->filterEmptyField($data, 'Property', 'deleted', 0);
		$in_update = $this->Rumahku->filterEmptyField($data, 'Property', 'in_update', 0);
		$property_action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id', 0);
		$action_name = $this->Rumahku->filterEmptyField($data, 'PropertyAction', 'inactive_name', 0);
		$addClass = false;

		if( $in_update && $status && !$sold && $published && !$deleted ) {
			$labelStatus = __('Update');
			$addClass = 'update';
		} else if( $active && $status && !$sold && $published && !$deleted ) {
			$labelStatus = __('Aktif');
			$addClass = 'active';
		} else if( !$active && $status && !$sold && $published && !$deleted ) {
			$labelStatus = __('Pratinjau');
			$addClass = 'process';
		} else if( $sold ) {
			if( $property_action_id == 2 ) {
				$labelStatus = __('Tersewa');
			} else {
				$labelStatus = __('Terjual');
			}
			$addClass = 'sold';
			$prefix = Configure::read('App.prefix');

			if( $prefix == 'admin' && !empty($tag) && !empty($_action) ) {
				$labelStatus .= $this->Html->link(__('Lihat detil..'), array(
					'controller' => 'properties',
					'action' => 'sold_preview',
					$id,
					'admin' => true,
				), array(
					'class' => 'ajaxModal',
					'title' => sprintf(__('Keterangan %s'), $action_name),
				));
			}
		} else if( !$status && $published && !$deleted ) {
			$labelStatus = __('Non-Aktif/Rejected');
			$addClass = 'non-active';
		} else if( !$published && !$deleted ) {
			$labelStatus = __('Unpublish');
			$addClass = 'unpublish';
		} else {
			$labelStatus = false;
		}

		if( !empty($tag) && !empty($labelStatus) ) {
			return $this->Html->tag($tag, $labelStatus, array(
				'class' => $addClass.' fbold',
			));
		} else {
			return $labelStatus;
		}
	}

	function getShortStatus ( $data, $tag = false, $emptyField = false, $is_featured = true ) {
		$action = $this->Rumahku->filterEmptyField($data, 'PropertyAction', 'name');
		$featured = $this->Rumahku->filterEmptyField($data, 'Property', 'featured', 0);
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold', 0);
		$property_action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id', 0);
		$addClass = false;

		if( empty($is_featured) ) {
			$featured = false;
		}

		if( $sold ) {
			if( $property_action_id == 2 ) {
				$labelStatus = __('Tersewa');
			} else {
				$labelStatus = __('Terjual');
			}
			$addClass = 'label label-danger';
		} else {
			if( !empty($featured) ) {
				$labelStatus = __('Premium');
				$addClass = 'label label-premium';
			} else {
				$labelStatus = $action;
				$addClass = '';

				if ($labelStatus == 'Dijual') {
					$addClass = 'label-sell';
				} elseif ($labelStatus == 'Disewakan') {
					$addClass = 'label-rent';
				}

			}
		}

		if( !empty($tag) && !empty($labelStatus) ) {
			if( !empty($emptyField) && empty($addClass) ) {
				return false;
			} else {
				return $this->Html->tag($tag, $labelStatus, array(
					'class' => $addClass,
				));
			}
		} else {
			return $labelStatus;
		}
	}

	function getNameCustom( $data, $only_location = false ) {
		$dataAddress = !empty($data['PropertyAddress'])?$data['PropertyAddress']:false;
		$subarea = $this->Rumahku->filterEmptyField($dataAddress, 'Subarea', 'name');
		$city = $this->Rumahku->filterEmptyField($dataAddress, 'City', 'name');
		$zip = $this->Rumahku->filterEmptyField($dataAddress, 'zip');

		if( !empty($subarea) && !empty($city) ) {
			$location = sprintf('%s, %s %s', $subarea, $city, $zip);
			$location = $this->Html->tag('span', $location, array('class' => 'notranslate'));
		} else {
			$location = '';
		}

		if( !empty($only_location) ) {
			$result = $location;
		} else {
			$type = strtolower($this->Rumahku->filterEmptyField($data, 'PropertyType', 'name'));
			$action = strtolower($this->Rumahku->filterEmptyField($data, 'PropertyAction', 'name'));
			
			$result = trim(sprintf(__('%s %s %s'), $type, $action, $location));
		}	
		$result = ucwords($result);

		// debug($result);die();
		// return $this->Rumahku->safeTagPrint($result);
		return $result;
	}

	function getLotUnit ( $size = 'm2', $action_type = false, $position = false ) {
		$lblUnit = array(
			1 => __('m2'),
			2 => __('m2'),
			3 => __('hektar'),
			4 => __('are'),
		);
		$size = !empty($lblUnit[$size])?$lblUnit[$size]:$size;

		if( !empty($size) ) {
			switch ($action_type) {
				case 'format':
					$lotUnit = str_split($size);
					$unit_name = end($lotUnit);

					if( !is_numeric($unit_name) ) {
						$unit_name = false;
					} else {
						array_pop($lotUnit);

						$size = implode('', $lotUnit);
					}

					switch ($position) {
						case 'top':

							return sprintf('%s%s', $size, $this->Html->tag('sup', $unit_name));
							break;
						
						default:
							# code...
							break;
					}
					break;
				
				default:
					return ucwords($size);
					break;
			}
		} else {
			return false;
		}
	}

	public function getTypeLot($data) {
		$dataAsset = $this->Rumahku->filterEmptyField($data, 'PropertyAsset');
		$is_lot = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_lot');
		$is_building = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_building');
		$is_space = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_space');
		$action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id');

		$measure = $this->Rumahku->filterEmptyField($data, 'LotUnit', 'measure');
		$measure = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'measure', $measure);

		if( !empty($measure) ) {
			if( $is_lot && !$is_building && $action_id == 1 ) {
				return true;
			} else if( $is_space ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function getPrice( $data, $empty = false, $just_data_array = false, $display_price_sold = true ){
		$dataAsset = $this->Rumahku->filterEmptyField($data, 'PropertyAsset');

		$property_action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id');
		$price = $this->Rumahku->filterEmptyField($data, 'Property', 'price');
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		$dataSold = $this->Rumahku->filterEmptyField($data, 'PropertySold');

		$show_period = isset($data['show_period']) ? $data['show_period'] : true;

		if( empty($display_price_sold) && !empty($sold) ) {
			$display = false;
		} else {
			$display = true;
		}

		if( !empty($display) ) {
			$period = $this->Rumahku->filterEmptyField($data, 'Period', 'name');

			$period_id = $this->Rumahku->filterEmptyField($data, 'Period', 'id');
			$currency = $this->Rumahku->filterEmptyField($data, 'Currency', 'symbol');

			$lot_unit = $this->Rumahku->filterEmptyField($data, 'LotUnit', 'slug');
			$lot_unit_id = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id');
			$lot_unit = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'slug', $lot_unit);
			$lot_unit = ucwords($lot_unit);

			$lot_type = $this->getTypeLot($data);

			if( !empty($sold) && !empty($dataSold) ) {
				$price = $this->Rumahku->filterEmptyField($data, 'PropertySold', 'price_sold');
				$period = $this->Rumahku->filterEmptyField($dataSold, 'Period', 'name');
				$currency = $this->Rumahku->filterEmptyField($dataSold, 'Currency', 'symbol', $currency);

				if( !$lot_type ) {
					$lot_unit = false;
				}
			} else if( !$lot_type ) {
				$lot_unit = false;
			}

			$for_data_array = array(
				'currency'	=> $currency, 
				'price'		=> $price, 
			);

			if( !empty($price) ) {
				$price = $this->Number->currency($price, $currency.' ', array('places' => 0));

				if( !empty($lot_unit) ) {
					if(empty($sold)){
						$price = sprintf('%s / %s', $price, $lot_unit);
					}

					$for_data_array = array_merge($for_data_array, array(
						'lot_unit_id'		=> $lot_unit_id, 
						'lot_unit_label'	=> $lot_unit, 
					));
				}

				if( $property_action_id == 2 && !empty($period) ) {
					if($show_period){
						$price = sprintf('%s %s', $price, $period);
					}

					$for_data_array = array_merge($for_data_array, array(
						'period_id'		=> $period_id, 
						'period_label'	=> $period, 
					));
				}
			} else if( !empty($empty) ) {
				$price = $this->Html->tag('span', $empty, array(
					'class' => 'disabled',
				));
			}

			if($just_data_array == true){
				return $for_data_array;
			}else{
				return $price;
			}
		} else {
			return $empty;
		}
	}

	function getCertificate ( $data ) {
		$certificate_id = $this->Rumahku->filterEmptyField($data, 'Property', 'certificate_id');
		$others_certificate = $this->Rumahku->filterEmptyField($data, 'Property', 'others_certificate');
		$certificate = $this->Rumahku->filterEmptyField($data, 'Certificate', 'name');
		$certificate_name = false;

		if( $certificate_id == -1 && !empty($others_certificate) ) {
			$certificate_name = $others_certificate;
		} else if( !empty($certificate) ) {
			$certificate_name = $certificate;
		}

		return $certificate_name;
	}

	function _callUnset( $fieldArr, $data ) {
		if( !empty($fieldArr) ) {
			foreach ($fieldArr as $key => $value) {
				if( is_array($value) ) {
					foreach ($value as $idx => $fieldName) {
						if( !empty($data[$key][$fieldName]) ) {
							unset($data[$key][$fieldName]);
						}
					}
				} else {
					unset($data[$value]);
				}
			}
		}

		return $data;
	}

	function getSpec ( $data, $showParams = array(), $options = false, $wrapper = true ) {
		$result = false;
		$spec = array();
		$display = $this->Rumahku->filterEmptyField($options, 'display');

		$options = $this->_callUnset(array(
			'display',
		), $options);

		$is_lot = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_lot');
		$is_building = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_building');
		$is_residence = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_residence');
		$is_space = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_space');

		$dataAsset = $this->Rumahku->filterEmptyField($data, 'PropertyAsset');
		$level = $this->Rumahku->filterEmptyField($dataAsset, 'level');
		$building_size = $this->Rumahku->filterEmptyField($dataAsset, 'building_size');
		$lot_width = $this->Rumahku->filterEmptyField($dataAsset, 'lot_width');
		$lot_length = $this->Rumahku->filterEmptyField($dataAsset, 'lot_length');
		$lot_size = $this->Rumahku->filterEmptyField($dataAsset, 'lot_size');
		$lot_unit = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'slug');

		$lot_unit = ucwords($lot_unit);

		if( ( $is_space && $is_building ) || ( $is_building && !$is_residence ) ) {
			if( !empty($level) ) {
				$spec[] = array(
					'alias' => __('Lt'),
					'name' => __('Lantai'),
					'value' => $level,
				);
			}
			if( !empty($building_size) ) {
				$spec[] = array(
					'name' => __('L. Bangunan'),
					'alias' => __('LB'),
					'value' => sprintf('%s %s', $building_size, $lot_unit),
				);
			}
			if( count($spec) < 2 && !empty($lot_width) ) {
				$lot_dimension = $this->_callGetLotDimension($lot_width, $lot_length);

				$spec[] = array(
					'alias' => __('Dim'),
					'name' => __('Dimensi'),
					'value' => $lot_dimension,
				);
			}
		} else if( ( $is_space && $is_lot ) || ( $is_lot && !$is_building ) ) {
			if( !empty($lot_size) ) {
				$spec[] = array(
					'name' => __('L. Tanah'),
					'alias' => __('LT'),
					'value' => sprintf('%s %s', $lot_size, $lot_unit),
				);
			}

			$certificate_name = $this->getCertificate($data);
			
			if( !empty($certificate_name) ) {
				$spec[] = array(
					'alias' => __('Strfkt'),
					'name' => __('Sertifikat'),
					'value' => $certificate_name,
				);
			}
		} else if( ( $is_building && $is_residence && $is_lot ) || !empty($is_residence) ) {
			$beds = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'beds');
			$beds_maid = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'beds_maid');

			if( !empty($building_size) ) {
				$spec[] = array(
					'name' => __('L. Bangunan'),
					'alias' => __('LB'),
					'value' => sprintf('%s %s', $building_size, $lot_unit),
				);
			}
			if( $display != 'frontend' ) {
				if( !empty($lot_size) ) {
					$spec[] = array(
						'name' => __('L. Tanah'),
						'alias' => __('LT'),
						'value' => sprintf('%s %s', $lot_size, $lot_unit),
					);
				}
			}
			if( !empty($beds) ) {
				if( !empty($beds_maid) ) {
					$beds = sprintf('%s + %s', $beds, $beds_maid);
				}

				$spec[] = array(
					'name' => __('K. Tidur'),
					'alias' => __('KT'),
					'value' => $beds,
				);
			}
		}

		if( !empty($showParams) ) {
			$addText = false;
			
			foreach ($showParams as $modelName => $params) {
				foreach ($params as $key => $fieldName) {
					if( is_array($fieldName) ) {
						$field = $this->Rumahku->filterEmptyField($fieldName, 'name');
						$label = $this->Rumahku->filterEmptyField($fieldName, 'label');
						$alias = $this->Rumahku->filterEmptyField($fieldName, 'alias');
						$addText = $this->Rumahku->filterEmptyField($fieldName, 'addText');
						$newline = $this->Rumahku->filterEmptyField($fieldName, 'newline');
						$format = $this->Rumahku->filterEmptyField($fieldName, 'format');
						$display = isset($fieldName['display'])?$fieldName['display']:true;

						if( !empty($display) ) {
							$value = $this->Rumahku->filterEmptyField($data, $modelName, $field);
						}

						if( $format == 'date' ) {
							$value = $this->Rumahku->formatDate($value, 'd/m/Y');
						}
					} else {
						$value = $this->Rumahku->filterEmptyField($data, $modelName, $fieldName);
						$label= $alias = ucwords($fieldName);
					}

					if( !empty($value) ) {
						$spec[] = array(
							'name' => $label,
							'alias' => $alias,
							'value' => $value.$addText,
							'newline' => $newline,
						);
					}
				}
			}
		}

		if( !empty($spec) ) {
			$contentLi = '';

			if( empty($wrapper) ) {
				$wrapperLabel = 'span';
				$wrapperValue = 'strong';
			} else {
				$wrapperLabel = $wrapper['wrapperLabel'];
				$wrapperValue = $wrapper['wrapperValue'];
			}

			if( !empty($wrapper) ) {
				foreach ($spec as $key => $value) {
					$newline = $this->Rumahku->filterEmptyField($value, 'newline');
					$alias = $this->Rumahku->filterEmptyField($value, 'alias');

					$lblSpec = $this->Html->tag($wrapperLabel, $value['name']);
					$lblSpec .= '&nbsp;'.$this->Html->tag($wrapperValue, $value['value']);

					if( !empty($newline) ) {
						$contentLi .= '<br>';
					}
					$contentLi .= $this->Html->tag('li', $lblSpec, array(
						'title' => $alias,
					));
				}

				$result = $this->Html->tag('ul', $contentLi, $options);
			} else {
				$result = $spec;
			}
		}

		return $result;
	}

	function _callLabelSold ( $action_id ) {
		if( $action_id == 2 ) {
			return __('Terjual');
		} else {
			return __('Tersewa');
		}
	}

	function refreshButton ( $data, $options = array() ) {
		$btnClass = $this->Rumahku->filterIssetField($options, 'btnClass', false, 'btn blue');
		$frame = $this->Rumahku->filterIssetField($options, 'frame', false, 'li');
		$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');
		$url = $this->Rumahku->filterEmptyField($options, 'url');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$refreshDate = $this->Rumahku->filterEmptyField($data, 'Property', 'refresh_date');
		$status = $this->getStatus($data);

		$nowDate = date('Y-m-d');
		$customRefreshDate = $this->Rumahku->formatDate($refreshDate, 'Y-m-d');

		if( $customRefreshDate == $nowDate ) {
			return false;
		} else if( in_array($status, array( 'Aktif', 'Update', 'Pratinjau' )) ) {
			if( empty($url) ) {
				$url = array(
					'controller'=> 'properties', 
					'action'=> 'refresh', 
					$id, 
					'admin' => true,
				);
			}

			$content = $this->AclLink->link(__('Refresh'), $url, array(
				'class' => $btnClass,
			), __('Anda yakin ingin melakukan refresh terhadap properti ini?'));

			if( !empty($frame) ) {
				$content = $this->Html->tag($frame, $content, array(
					'class' => $frameClass,
				));
			}

			return $content;
		} else {
			return false;
		}
	}

    function _callAllowPremium ($options) {
		$limit_premium_property = Common::hashEmptyField($options, 'packages.MembershipPackage.limit_premium_property');
		$premium_property_mine  = Common::hashEmptyField($options, 'premium_property_mine');
		$data_package 			= Common::hashEmptyField($options, 'packages.MembershipPackage');
		$btn_action  			= Common::hashEmptyField($options, 'btn_action');

		$total_premium_property = (int)$premium_property_mine;

		// check property limit
        if( $total_premium_property < $limit_premium_property ) {
            return true;
        } elseif ( $total_premium_property >= $limit_premium_property && $btn_action && !empty($data_package)) {
        	return true;
        } else {
            return false;
        }

    }

	function premiumButton ( $data, $options = array() ) {
		if( $this->_callAllowPremium($options) ) {
			$btnClass = $this->Rumahku->filterIssetField($options, 'btnClass', false, 'btn green');
			$frame = $this->Rumahku->filterIssetField($options, 'frame', false, 'li');
			$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');
			$url = $this->Rumahku->filterEmptyField($options, 'url');

			$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
			$featured = $this->Rumahku->filterEmptyField($data, 'Property', 'featured');
			$status = $this->getStatus($data);

			if( !empty($featured) ) {
				return false;
			} else if( $status == 'Aktif' ) {
				if( empty($url) ) {
					$url = array(
						'controller'=> 'properties', 
						'action'=> 'premium', 
						$id, 
						'admin' => true,
					);
				}

				$content = $this->AclLink->link(__('Premium'), $url, array(
					'class' => $btnClass,
				), __('Jadikan premium pada Properti ini?'));

				if( !empty($frame) ) {
					$content = $this->Html->tag($frame, $content, array(
						'class' => $frameClass,
					));
				}

				return $content;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function unPremiumButton($data, $options = array()){
		// if( $this->_callAllowPremium($options) ) {
			$btnClass	= $this->Rumahku->filterIssetField($options, 'btnClass', FALSE, 'btn orange');
			$frame		= $this->Rumahku->filterIssetField($options, 'frame', FALSE, 'li');
			$frameClass	= $this->Rumahku->filterEmptyField($options, 'frameClass');
			$url		= $this->Rumahku->filterEmptyField($options, 'url');

			$id			= $this->Rumahku->filterEmptyField($data, 'Property', 'id');
			$featured	= $this->Rumahku->filterEmptyField($data, 'Property', 'featured');
			$status		= $this->getStatus($data);

			if($featured == 1 && in_array($status, array('Aktif', 'Terjual', 'Tersewa'))){
				if(empty($url)){
					$url = array('controller' => 'properties', 'action' => 'unpremium', $id, 'admin' => TRUE);
				}

				$content = $this->AclLink->link(__('Hilangkan Premium'), $url, array(
					'class' => $btnClass), 
					__('Hilangkan Status Premium pada Properti ini?')
				);

				if(!empty($frame)){
					$content = $this->Html->tag($frame, $content, array('class' => $frameClass));
				}

				return $content;
			}
			else{
				return FALSE;
			}
		// } else{
		// 	return FALSE;
		// }
	}

	function soldButton ( $data, $options = array() ) {
		$btnClass = $this->Rumahku->filterEmptyField($options, 'btnClass', false, 'btn default');
		$frame = $this->Rumahku->filterEmptyField($options, 'frame', false, 'li');
		$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		$active = $this->Rumahku->filterEmptyField($data, 'Property', 'active');
		$status = $this->Rumahku->filterEmptyField($data, 'Property', 'status');
		$customLabelSold = $this->Rumahku->filterEmptyField($data, 'PropertyAction', 'inactive_name', __('Terjual'));

		$url = array(
			'controller' => 'properties',
			'action' => 'sold',
			$id,
			'admin' => true,
		);

		if( !empty($sold) ) {
			$url['action'] = 'unsold';
			$msgAlert = __('Anda yakin ingin menghilangkan status terjual pada properti ini?');
			$customLabelSold = sprintf(__('Hilangkan %s'), $customLabelSold);
			$customTitleSold = $customLabelSold;
		} else {
			$url['action'] = 'sold';
			$btnClass .= ' ajaxModal';
			$msgAlert = false;
			$customTitleSold = sprintf(__('Tandai menjadi %s'), $customLabelSold);
		}

		$content = '';

		if($active || $status){

			$checkContent = $this->AclLink->aclCheck($url);

			$content = $this->AclLink->link($customLabelSold, $url, array(
				'class' => $btnClass,
				'title' => $customTitleSold,
			), $msgAlert);

			if( !empty($frame) && $checkContent ) {
				$content = $this->Html->tag($frame, $content, array(
					'class' => $frameClass,
				));
			}
		}

		return $content;
	}

	function statusListingButton ( $data, $options = array() ) {
		$btnClass = $this->Rumahku->filterEmptyField($options, 'btnClass', false, 'btn default');
		$frame = $this->Rumahku->filterEmptyField($options, 'frame', false, 'li');
		$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$property_status_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_status_id');
		$active = $this->Rumahku->filterEmptyField($data, 'Property', 'active');
		$status = $this->Rumahku->filterEmptyField($data, 'Property', 'status');
		$customLabelStatus = $this->Rumahku->filterEmptyField($data, 'PropertyStatusListing','name',__('Kategori Properti'));

		$url = array(
			'controller' => 'properties',
			'action' => 'status_listing',
			$id,
			'admin' => true,
		);

		if( !empty($property_status_id) ) {
			$btnClass .= ' ajaxModal';
			$customLabelStatus = __('Kategori : %s', $customLabelStatus);
			$customTitleStatus = $customLabelStatus;
		} else {
			$btnClass .= ' ajaxModal';
			$customTitleStatus = __('Kategori Properti');
		}

		$content = '';

		if($active || $status){
			$check = $this->AclLink->aclCheck($url);

			$content = $this->AclLink->link($customLabelStatus, $url, array(
				'class' => $btnClass,
				'title' => $customTitleStatus,
			));

			if( !empty($frame) && $check ) {
				$content = $this->Html->tag($frame, $content, array(
					'class' => $frameClass,
				));
			}
		}

		return $content;
	}

	function _callCategoryMedias ( $session_id = false, $category_id = false, $title = false, $lainnya = 'Lainnya' ) {
		$categoryMedias = Configure::read('__Site.CategoryMedias.Data');

		if( !empty($title) ) {
			$categoryMedias[$session_id] = $title;
		}

		if( !empty($lainnya) ) {
			$categoryMedias[0] = $lainnya;
		}

		if( empty($category_id) ) {
			$category_id = $session_id;
		}

		return array(
			'categoryMedias' => $categoryMedias,
			'category_id' => $category_id,
		);
	}

	function _callMediaTitle ( $value ) {
		$title = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'title');
		$title = $this->Rumahku->filterEmptyField($value, 'CategoryMedias', 'name', $title);

		return $title;
	}

	function _callRentPrice ( $value, $default = false, $empty = false, $display_price_sold = true ) {
		$sold = $this->Rumahku->filterEmptyField($value, 'Property', 'sold');
		$prices = $this->Rumahku->filterEmptyField($value, 'PropertyPrice');
		$arrTemp = array();
		$content = $default;

		if( empty($display_price_sold) && !empty($sold) ) {
			$display = false;
		} else {
			$display = true;
		}

		if( !empty($display) ) {
			if(!empty($prices['format_arr'])){
				unset($prices['format_arr']);
			}

			if( !empty($prices) ) {
				foreach ($prices as $key => $val) {
					$price = $this->Rumahku->filterEmptyField($val, 'PropertyPrice', 'price');
					$alias = $this->Rumahku->filterEmptyField($val, 'Currency', 'alias');
					$period = $this->Rumahku->filterEmptyField($val, 'Period', 'name');

					$customPrice = $this->Number->currency($price, $alias.' ', array('places' => 0));
					$arrTemp[] = $this->Html->tag('li', sprintf('%s %s', $customPrice, $period));
				}

				if( !empty($arrTemp) ) {
					$content = implode('', $arrTemp);
					$content = $this->Html->tag('ul', $content);
				}
			}
			
			return $content;
		} else {
			return $empty;
		}
	}

	function _callGetLotDimension ( $lot_width, $lot_length ) {
		$lot_dimension = $lot_width;

		if( !empty($lot_length) ) {
			$lot_dimension = sprintf('%s X %s', $lot_dimension, $lot_length);
		}

		return $lot_dimension;
	}

	function _callGetSpecification ( $data, $options = false, $wrapper = true, $data_revision = array() ) {
		$result = '';
		$is_lot = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_lot');
		$is_building = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_building');
		$is_residence = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_residence');
		$is_space = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_space');
		$_type = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'name');

		$dataAsset = $this->Rumahku->filterEmptyField($data, 'PropertyAsset');
		$lot_unit_id = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id');

		$lot_name = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'slug');
		$lot_name = $this->Rumahku->filterEmptyField($data, 'LotUnit', 'slug', $lot_name);

		$level = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'level');
		$building_size = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'building_size');
		$lot_width = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_width');
		$lot_length = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_length');
		$lot_size = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_size');
		$beds = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'beds');
		$beds_maid = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'beds_maid');
		$baths = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'baths');
		$baths_maid = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'baths_maid');
		$cars = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'cars');
		$carports = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'carports');
		$phoneline = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'phoneline');
		$electricity = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'electricity');
		$furnished = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'furnished', 'none');
	//	$year_built = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'year_built', '-', true, 'year');
		$year_built = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'year_built', 0, true, 'year');
		$direction = $this->Rumahku->filterEmptyField($dataAsset, 'PropertyDirection', 'name');
		$condition = $this->Rumahku->filterEmptyField($dataAsset, 'PropertyCondition', 'name');
		$view = $this->Rumahku->filterEmptyField($dataAsset, 'ViewSite', 'name');
		$spec = $this->Rumahku->filterEmptyField($options, 'specs', false, array());

		$list_options = $this->Rumahku->filterEmptyField($options, 'list_options', false, array(
			'class' => 'clearafter'
		));

		if(isset($options['list_options'])){
			unset($options['list_options']);
		}

		if( !empty($spec) ) {
			unset($options['specs']);
		}

		$certificate_name = $this->getCertificate($data);
		$furnishedOptions = Configure::read('Global.Data');

		// if( !empty($certificate_name) ) {
			$spec[] = array(
				'alias' => __('Srtfkt'),
				'name' => __('Sertifikat'),
				'value' => $certificate_name,
				'model' => 'Property',
				'field' => 'certificate_id,others_certificate'
			);
		// }

		if( !empty($is_residence) ) {
			// if( !empty($beds) ) {
				if( !empty($beds_maid) ) {
					$beds = sprintf('%s + %s', $beds, $beds_maid);
				}

				$spec[] = array(
					'alias' => __('KT'),
					'name' => __('Kamar Tidur'),
					'value' => $beds,
					'model' => 'PropertyAsset',
					'field' => 'beds,beds_maid'
				);
			// }
			// if( !empty($baths) ) {
				if( !empty($baths_maid) ) {
					$baths = sprintf('%s + %s', $baths, $baths_maid);
				}

				$spec[] = array(
					'alias' => __('KM'),
					'name' => __('Kamar Mandi'),
					'value' => $baths,
					'model' => 'PropertyAsset',
					'field' => 'baths,baths_maid'
				);
			// }
		}

		if( !empty($data_revision) ) {
			// if( !empty($lot_unit_id) && !empty($lot_name) ){
			if( !empty($lot_unit_id) ){
				if( !empty($is_space) ) {
					$lblLotName = __('Harga Satuan');
					$lot_name = sprintf(__('Per %s'), $lot_name);
				} else {
					$lblLotName = __('Satuan Luas');
				}

				$spec[] = array(
					'name' => $lblLotName,
					'value' => $lot_name,
					'model' => 'PropertyAsset',
					'field' => 'lot_unit_id'
				);
			}
		}

		// if( !empty($lot_width) ) {
			$lot_dimension = $this->_callGetLotDimension($lot_width, $lot_length);
			$spec[] = array(
				'alias' => __('Dimensi'),
				'name' => __('Dimensi'),
				'value' => $lot_dimension,
				'model' => 'PropertyAsset',
				'field' => 'lot_width,lot_length'
			);
		// }

		if( !empty($is_lot) && !empty($lot_size) ) {
			// if( !empty($lot_size) ) {
				$spec[] = array(
					'alias' => __('LT'),
					'name' => __('Luas Tanah'),
					'value' => trim(sprintf('%s %s', $lot_size, $lot_name)),
					'model' => 'PropertyAsset',
					'field' => 'lot_size,lot_unit_id'
				);
			// }
		}

		if( !empty($is_building) ) {
			if( !empty($building_size) ) {
				$spec[] = array(
					'alias' => __('LB'),
					'name' => __('L. Bangunan'),
					'value' => trim(sprintf('%s %s', $building_size, $lot_name)),
					'model' => 'PropertyAsset',
					'field' => 'building_size,lot_unit_id'
				);
			}
			// if( !empty($level) ) {
				$spec[] = array(
					'alias' => __('Lantai'),
					'name' => __('Lantai'),
					'value' => $level,
					'model' => 'PropertyAsset',
					'field' => 'level'
				);
			// }
			// if( !empty($cars) ) {
				$spec[] = array(
					'alias' => __('Garasi'),
					'name' => __('Garasi'),
					'value' => $cars,
					'model' => 'PropertyAsset',
					'field' => 'cars'
				);
			// }
			// if( !empty($carports) ) {
				$spec[] = array(
					'alias' => __('Carport'),
					'name' => __('Carport'),
					'value' => $carports,
					'model' => 'PropertyAsset',
					'field' => 'carports'
				);
			// }
			// if( !empty($phoneline) ) {
				$spec[] = array(
					'alias' => __('Line Tlp'),
					'name' => __('Jml Line Telepon'),
					'value' => $phoneline,
					'model' => 'PropertyAsset',
					'field' => 'phoneline'
				);
			// }
			// if( !empty($electricity) ) {
				$spec[] = array(
					'alias' => __('Listrik'),
					'name' => __('Daya Listrik'),
					'value' => $electricity,
					'model' => 'PropertyAsset',
					'field' => 'electricity'
				);
			// }
			// if( !empty($furnished) ) {
				$customFurnished = $this->Rumahku->filterEmptyField($furnishedOptions, 'furnished', $furnished);
				$spec[] = array(
					'name' => __('Interior'),
					'value' => $customFurnished,
					'model' => 'PropertyAsset',
					'field' => 'furnished'
				);
			// }
			// if( !empty($direction) ) {
				$spec[] = array(
					'alias' => __('Arah'),
					'name' => __('Arah Bangunan'),
					'value' => $direction,
					'model' => 'PropertyAsset',
					'field' => 'property_direction_id'
				);
			// }
			// if( !empty($year_built) && $year_built != '0000' ) {
			if(intval($year_built) > 0){
				$spec[] = array(
					'alias' => __('Tahun'),
					'name' => __('Tahun dibangun'),
					'value' => $year_built,
					'model' => 'PropertyAsset',
					'field' => 'year_built'
				);
			}
			// }
			// if( !empty($condition) ) {
				$spec[] = array(
					'alias' => __('Kondisi'),
					'name' => __('Kondisi Bangunan'),
					'value' => $condition,
					'model' => 'PropertyAsset',
					'field' => 'property_condition_id'
				);
			// }
			// if( !empty($view) ) {
				$spec[] = array(
					'alias' => __('View'),
					'name' => sprintf(__('View %s'), $_type),
					'value' => $view,
					'model' => 'PropertyAsset',
					'field' => 'view_site_id'
				);
			// }
		}

		if( !empty($spec) ) {
			if( !empty($wrapper) ) {
				$contentLi = '';

				if( is_array($wrapper) ) {
					$wrapperLabel = $wrapper['wrapperLabel'];
					$wrapperValue = $wrapper['wrapperValue'];
				} else {
					$wrapperLabel = 'span';
					$wrapperValue = 'strong';
				}

				foreach ($spec as $key => $value) {
					if( empty($data_revision) ) {
						if( !empty($value['value']) ) {
							$flag = true;
						} else {
							$flag = false;
						}
					} else {
						$flag = true;
					}

					if( !empty($flag) ) {

						$lblSpec = $this->Html->tag($wrapperLabel, sprintf('%s: ', $value['name']));
						$lblSpec .= $this->Html->tag($wrapperValue, $value['value']);
						
						$lblSpec = $this->Html->tag('span', sprintf('%s: ', $value['name']));
						$lblSpec .= $this->Html->tag('strong', $value['value']);

						$contentLi .= $this->Html->tag('li', $this->Rumahku->getCheckRevision($value['model'], $value['field'], $data_revision, $lblSpec), $list_options);
					}


				}

				$result = $this->Html->tag('ul', $contentLi, $options);
			} else {
				$result = $spec;
			}
		}

		return $result;
	}

	function approveButton ( $data, $options = array() ) {
		$btnClass = $this->Rumahku->filterEmptyField($options, 'btnClass', false, 'btn darkblue');
		$frame = $this->Rumahku->filterEmptyField($options, 'frame', false, 'li');
		$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');
		$url = $this->Rumahku->filterEmptyField($options, 'url');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$status = $this->Rumahku->filterEmptyField($data, 'Property', 'status');
		$active = $this->Rumahku->filterEmptyField($data, 'Property', 'active');
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		$deleted = $this->Rumahku->filterEmptyField($data, 'Property', 'deleted');
		$published = $this->Rumahku->filterEmptyField($data, 'Property', 'published');

		if( !empty($status) && !empty($published) && empty($active) && empty($sold) && empty($deleted) ) {
			########## APROVE
			if( empty($url) ) {
				$url = array(
					'controller'=> 'properties', 
					'action'=> 'approval', 
					$id, 
					'admin' => true,
				);
			}

			$check = $this->AclLink->aclCheck($url);

			$content = $this->AclLink->link(__('Setujui'), $url, array(
				'class' => $btnClass,
			), __('Apakah Anda yakin untuk menyetujui properti ini?'));

			if( !empty($frame) && $check ) {
				$content = $this->Html->tag($frame, $content, array(
					'class' => $frameClass,
				));
			}
			########## REJECTED
			$url = array(
				'controller'=> 'properties', 
				'action'=> 'rejected', 
				$id, 
				'admin' => true,
			);

			$check = $this->AclLink->aclCheck($url);

			$contentReject = $this->AclLink->link(__('Tolak'), $url, array(
				'class' => 'btn red ajaxModal',
				'title' => __('Tolak Properti'),
			));

			if( !empty($frame) && $check ) {
				$contentReject = $this->Html->tag($frame, $contentReject, array(
					'class' => $frameClass,
				));
			}

			$content .= $contentReject;

			return $content;			
		} else {
			return false;
		}
	}

	function _callPointPlus($data, $default = false){
		$point_plus = $this->Rumahku->filterEmptyField($data, 'PropertyPointPlus', 'name');
		
		$arrTemp = array();
		$content = $default;

		if(!empty($point_plus['format_arr'])){
			unset($point_plus['format_arr']);
		}

		if( !empty($point_plus) ) {
			foreach ($point_plus as $key => $val) {
				if(empty($val)){
					$val = ' - ';
				}
				$arrTemp[] = $this->Html->tag('li', $val);
			}

			if( !empty($arrTemp) ) {
				$content = implode('', $arrTemp);
				$content = $this->Html->tag('ul', $content);
			}
		}
		
		return $content;
	}

	function _callFacility($data, $facilities, $default = false){
		$data = $this->Rumahku->filterEmptyField($data, 'PropertyFacility');
		$facility = $this->Rumahku->filterEmptyField($data, 'facility_id');
		
		$arrTemp = array();
		$content = $default;

		if(!empty($data['PropertyFacility']['format_arr'])){
			unset($data['PropertyFacility']['format_arr']);
		}

		if( !empty($facilities) ) {
			foreach ($facilities as $key => $val) {

				$icon = $this->Rumahku->icon('rv4-bold-cross');
				if(!empty($facility[$key])){
					$icon = $this->Rumahku->icon('rv4-bold-check');
				}

				$arrTemp[] = $this->Html->tag('li', sprintf('%s %s', $val, $icon));
			}

			if(!empty($data['other_id']) && !empty($data['other_text'])){
				$data['other_text'] = explode(',', $data['other_text']);

				foreach ($data['other_text'] as $key => $val) {
					$arrTemp[] = $this->Html->tag('li', sprintf('%s %s', $val, $this->Rumahku->icon('rv4-bold-check')));
				}
			}

			if( !empty($arrTemp) ) {
				$content = implode('', $arrTemp);
				$content = $this->Html->tag('ul', $content);
			}
		}
		
		return $content;
	}

	function checkPratinjau ( $data ) {
		$in_update = $this->Rumahku->filterEmptyField($data, 'Property', 'in_update');
		$active = $this->Rumahku->filterEmptyField($data, 'Property', 'active');
		$status = $this->Rumahku->filterEmptyField($data, 'Property', 'status');
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold');

		if( ( empty($active) || !empty($in_update) ) && !empty($status) ) {
			if(!empty($sold)){
				return false;
			}else{
				return true;
			}
		} else {
			return false;
		}
	}

	function previewButton($data){
		$status = '';
		$group_id = Configure::read('User.group_id');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$in_update = $this->Rumahku->filterEmptyField($data, 'Property', 'in_update');

		$url = array(
			'controller' => 'properties',
			'action' => 'preview',
			$id,
			'admin' => true,
		);
		$check = $this->AclLink->aclCheck($url);

		if( in_array($group_id, array( 5 )) || $check ) {

			if(!empty($in_update)){
				$status = '<span class="badge">!</span>';
			}

			if( $this->checkPratinjau($data) ) {
				return $this->Html->tag('li', $this->AclLink->link(__('Pratinjau').' '.$status, $url, array(
					'class' => 'btn darkblue',
					'escape' => false,
					'title' => !empty($status) ? __('Terdapat perubahan pada listing properti ini') : '',
				)));
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function getNotifRejected ( $data, $class = 'error-full alert' ) {
		$message = $this->Rumahku->filterEmptyField($data, 'PropertyNotification', 'message');
		$created = $this->Rumahku->filterEmptyField($data, 'PropertyNotification', 'created');
		$in_updated = $this->Rumahku->filterEmptyField($data, 'PropertyNotification', 'in_updated');

		if( !empty($in_updated) ) {
			$title = __('Perubahan ditolak,');
		} else {
			$title = __('Properti ditolak,');
		}

		$customDate = $this->Html->tag('span', $this->Rumahku->formatDate($created, 'd/m/Y H:i:s'));

		if( !empty($message) ) {
			$message = str_replace(PHP_EOL, ', ', $message);
			return $this->Html->tag('div', $this->Html->tag('p', sprintf(__('%s - %s dengan alasan: %s'), $customDate, $this->Html->tag('strong', $title), $message)), array(
				'class' => $class,
			));
		} else {
			return false;
		}
	}

	function getAddress( $data, $separator = ', ', $location_display = false, $break_address = true ) {
		$address = $this->Rumahku->filterEmptyField($data, 'address');
		$region = $this->Rumahku->filterEmptyField($data, 'Region', 'name');
		$city = $this->Rumahku->filterEmptyField($data, 'City', 'name');
		$subarea = $this->Rumahku->filterEmptyField($data, 'Subarea', 'name');
		$zip = $this->Rumahku->filterEmptyField($data, 'zip');
		$no = $this->Rumahku->filterEmptyField($data, 'no');
		$rt = $this->Rumahku->filterEmptyField($data, 'rt');
		$rw = $this->Rumahku->filterEmptyField($data, 'rw');
		$fulladdress = '';

		if( (empty($location_display) || $location_display == 'address') && !empty($address) ) {
			$detail_address = array();
			
			if ( !empty($no) ) {
				$detail_address[] = sprintf('No.%s', $no);
			}
			if ( !empty($rt) ) {
				$detail_address[] = sprintf('RT.%s', $rt);
			}
			if ( !empty($rt) ) {
				$detail_address[] = sprintf('RW.%s', $rw);
			}

			if( !empty($detail_address) ) {
				$address .= '<br>'.implode(', ', $detail_address);
			}

			if( !empty($break_address) ) {
				$fulladdress = $address . '<br>';
			} else {
				$fulladdress = $address . ' ';
			}
			
		}

		if( $location_display != 'address' ) {
			if( !empty($subarea) ) {
				$fulladdress .= $subarea;
			}
			if( !empty($city) ) {
				$fulladdress .= $separator. $city;
			}
			if( !empty($region) ) {
				$fulladdress .= $separator . $region . ' ' . $zip;
			}
		}

		return $fulladdress;
	}

	function _callGetCustom ( $data, $tag = 'div', $data_revision = false, $contract = true ) {
		$_config = Configure::read('Config.Company.data');
		$authUserID = Configure::read('User.id');
		$isAdminCompany = Configure::read('User.admin');

		$is_co_broke = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_co_broke');
		$is_bt = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_bt_commission');
		$is_kolisting_koselling = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_kolisting_koselling');
		$co_broke_type = Common::hashEmptyField($data, 'Property.co_broke_type');
		$property_cobroke = Common::hashEmptyField($data, 'Property.is_cobroke');
		$property_user_id = Common::hashEmptyField($data, 'Property.user_id');

		$content = array(
			array(
				'name' => 'commission',
				'label' => __('Komisi'),
				'addText' => ' %',
				'display' => true,
			),
		);

		if( !empty($is_bt) ) {
			$text = 'BT';
			if(empty($is_co_broke)){
				$text = 'Komisi Perantara';
			}

			$content[] = array(
				'name' => 'bt',
				'label' => __($text),
				'addText' => ' %',
				'display' => $is_bt,
			);
		}

		if(!empty($is_co_broke) && !empty($property_cobroke)){
			$text = 'Tipe Co-Broke';
			$content[] = array(
				'name' => 'co_broke_type',
				'label' => __($text),
				'display' => true,
			);
		}

		if( !empty($is_kolisting_koselling) ) {
			$content[] = array(
				'name' => 'kolisting_koselling',
				'label' => __('Kolisting Koseling'),
				'display' => $is_kolisting_koselling,
			);
		}

		if( !empty($contract) ) {
			$content[] = array(
				'name' => 'contract_date',
				'label' => __('Tgl Kontrak'),
				'title' => __('Tanggal kontrak/kesepakatan dengan Vendor'),
				'format' => 'date',
				'display' => true,
			);
		}

		$result = '';

		if( $authUserID === $property_user_id || !empty($isAdminCompany) ) {
			$resultArr = array(
				'Property' => $content,
				'ClientProfile' => array(
					array(
						'name' => 'full_name',
						'label' => __('Vendor'),
						'newline' => true,
					),
					array(
						'name' => 'no_hp',
						'label' => __('No. HP Vendor'),
					),
				),
			);
		}

		if( !empty($resultArr) ) {
			if( !empty($tag) ) {
				foreach ($resultArr as $modelName => $values) {
					if( !empty($values) ) {
						foreach ($values as $key => $value) {
							$fieldName = $this->Rumahku->filterEmptyField($value, 'name');
							$label = $this->Rumahku->filterEmptyField($value, 'label');
							$display = $this->Rumahku->filterEmptyField($value, 'display');
							$addText = $this->Rumahku->filterEmptyField($value, 'addText');
							$newline = $this->Rumahku->filterEmptyField($value, 'newline');
							$title = $this->Rumahku->filterEmptyField($value, 'title');
							$format = $this->Rumahku->filterEmptyField($value, 'format');

							$val = $this->Rumahku->filterEmptyField($data, $modelName, $fieldName);

							if( empty($data_revision) ) {
								if( !empty($val) ) {
									$flag = true;
								} else {
									$flag = false;
								}
							} else {
								$flag = true;
							}

							if( !empty($display) && $flag ) {
								if( !empty($newline) ) {
									$result .= '<br>';
								}

								if( $format == 'date' ) {
									$val = $this->Rumahku->formatDate($val, 'd/m/Y');
								}

								$result .= $this->Html->tag($tag, $this->Rumahku->getCheckRevision($modelName, $fieldName, $data_revision, sprintf('%s : %s', $label, $this->Html->tag('strong', $val.$addText, array(
									'title' => $title,
								)))));
							}
						}
					}
				}
			} else {
				$result = $resultArr;
			}
		}

		return $result;
	}

	function reportButton( $data ) {

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');

		return $this->Html->tag('li', $this->Html->link(__('Lihat Laporan'), array(
			'controller' => 'properties',
			'action' => 'report_visitor',
			$id,
			'client' => true,
			'admin' => false,
		), array(
			'class' => 'btn default',
			'escape' => false,
			'title' => __('Lihat Laporan'),
		)));
	}

	function _callCertificates ( $certificates = false, $lainnya = 'Lainnya' ) {
		if( !empty($lainnya) ) {
			$certificates[-1] = $lainnya;
		}

		return $certificates;
	}

	function getShortPropertyType( $property = false ) {
		$customPropertyType = false;
		if( !empty($property) ) {
			$property_type = $this->Rumahku->filterEmptyField($property, 'PropertyType', 'name');
			$status = $this->Rumahku->filterEmptyField($property, 'PropertyAction', 'name', '-');

			$addresses = $this->Rumahku->filterEmptyField($property, 'PropertyAddress');
			$location = $this->Rumahku->filterEmptyField($addresses, 'Subarea', 'name');

			$customPropertyType = sprintf('%s %s', $property_type, $status);
			if( !empty($location) ) {
				$customPropertyType = sprintf('%s,%s', $customPropertyType, $location);
			}
		}

		return $customPropertyType;
	}

	

	function ActivateButton ( $data, $options = array() ) {
		$btnClass = $this->Rumahku->filterIssetField($options, 'btnClass', false, 'btn green');
		$frame = $this->Rumahku->filterIssetField($options, 'frame', false, 'li');
		$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');
		$url = $this->Rumahku->filterEmptyField($options, 'url');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$status = $this->getStatus($data);

		if( $status == 'Non-Aktif/Rejected' ) {

			$check = $this->AclLink->aclCheck(array(
				'controller'=> 'properties', 
				'action'=> 'activate', 
				$id, 
				'admin' => true,
			));

			$content = $this->AclLink->link(__('Aktifkan Properti'), array(
				'controller'=> 'properties', 
				'action'=> 'activate', 
				$id, 
				'admin' => true,
			), array(
				'class' => $btnClass,
			), __('Anda yakin ingin meng-aktifkan properti ini?'));

			if( !empty($frame) && $check ) {
				$content = $this->Html->tag($frame, $content, array(
					'class' => $frameClass,
				));
			}

			return $content;
		} else {
			return false;
		}
	}

	function deActivateButton( $data, $options = array() ) {
		$btnClass = $this->Rumahku->filterIssetField($options, 'btnClass', false, 'btn red');
		$frame = $this->Rumahku->filterIssetField($options, 'frame', false, 'li');
		$frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass');
		$url = $this->Rumahku->filterEmptyField($options, 'url');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		$status = $this->getStatus($data);

		if( $status != 'Non-Aktif/Rejected' && empty($sold) ) {
			$check = $this->AclLink->aclCheck(array(
				'controller'=> 'properties', 
				'action'=> 'deactivate', 
				$id, 
				'admin' => true,
			));

			$content = $this->AclLink->link(__('Non Aktifkan'), array(
				'controller'=> 'properties', 
				'action'=> 'deactivate', 
				$id, 
				'admin' => true,
			), array(
				'class' => $btnClass,
			), __('Anda yakin ingin meng-nonaktifkan properti ini?'));

			if( !empty($frame) && $check ) {
				$content = $this->Html->tag($frame, $content, array(
					'class' => $frameClass,
				));
			}

			return $content;
		} else {
			return false;
		}
	}

	function coBrokeButton($data, $pure_array = false){
		$status = $this->getStatus($data);
		$is_cobroke = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'id');
		$approve = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'approve');
		$decline = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'decline');
		$active = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'active');

		$id = $this->Rumahku->filterEmptyField($data, 'Property', 'id');
		$bt = $this->Rumahku->filterEmptyField($data, 'Property', 'bt');
		$sold = $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		$mls_id = $this->Rumahku->filterEmptyField($data, 'Property', 'mls_id');
		
		$content = '';

		$dataCompany = Configure::read('Config.Company.data');
		$config_is_cobroke = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'is_co_broke');
		$is_bt_commission = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'is_bt_commission');

		$alert = $url = $label = '';

		if(!empty($config_is_cobroke)){
			if(empty($sold)){
				if(in_array($status, array('Update', 'Aktif')) && empty($is_cobroke)){
					
					$label = __('Co-Broke');
					$url = array(
						'controller' => 'co_brokes',
						'action' => 'make_cobroke',
						$id,
						'backprocess' => true
					);

					if(!$pure_array){
						$content = $this->AclLink->link($label, $url, array(
							'class' => 'btn yellow ajaxModal',
							'title' => __('Jadikan Co-Broke'),
						));
					}
				}else if(!empty($is_cobroke) && !empty($approve)){
					if( !empty($active) ){
						$text = __('Hentikan Co-Broke');
						$alert = __('menghentikan');
					}else{
						$text = __('Lanjutkan Co-Broke');
						$alert = __('melanjutkan');
					}

					$label = $text;
					$url = array(
						'controller' => 'co_brokes',
						'action' => 'stop_toggle',
						$id,
						'backprocess' => true
					);
					$alert = sprintf(__('Apakah Anda yakin ingin %s properti ini sebagai listing Co-Broke?'), $alert);

					if(!$pure_array){
						$content = $this->AclLink->link($text, $url, array(
							'class' => 'btn orange',
							'title' => __('Hentikan Co-Broke'),
						), $alert);
					}
				}
			}
		}

		if(!empty($content) && !$pure_array){
			return $this->Html->tag('li', $content);
		}else{

			if($pure_array){
				$content = array(
					'label' => $label,
					'url' => $url,
					'alert' => $alert,
				);
			}

			return $content;
		}
	}

	function kprButton($data, $options = array()){
		$btnClass 	= $this->Rumahku->filterIssetField($options, 'btnClass', false, 'btn yellow');
		$frame 		= $this->Rumahku->filterIssetField($options, 'frame', false, 'li');
		$frameClass = Common::hashEmptyField($options, 'frameClass');

		$id 				= Common::hashEmptyField($data, 'Property.id');
		$sold 				= Common::hashEmptyField($data, 'Property.sold');
		$on_progress_kpr 	= Common::hashEmptyField($data, 'Property.on_progress_kpr');

		$status = $this->getStatus($data);

		if(  in_array($status, array('Aktif', 'Update')) && empty($sold) && empty($on_progress_kpr) ) {
			$url = array(
				'controller'=> 'kpr', 
				'action'=> 'add', 
				'property' => $id, 
				'admin' => true,
			);

			$check = $this->AclLink->aclCheck($url);

			$content = $this->AclLink->link(__('Ajukan KPR'), $url, array(
				'class' => $btnClass,
			));

			if(!empty($check)){
				if( !empty($frame) ) {
					$content = $this->Html->tag($frame, $content, array(
						'class' => $frameClass,
					));
				}
			}else{
				$content = false;
			}	

			return $content;
		} else {
			return false;
		}
	}

	public function _getSpecification($data = array(), $dataRevision = array(), $options = array()){
		$data			= (array) $data;
		$options		= (array) $options;
		$dataRevision	= (array) $dataRevision;

		$autoRender	= Common::hashEmptyField($options, 'auto_render', true, array('isset' => true));
		$showEmpty	= Common::hashEmptyField($options, 'show_empty', false, array('isset' => true));
		$emptyText	= Common::hashEmptyField($options, 'empty_text');
		$result		= $autoRender ? '' : array();

		if($data){
			$globalData		= Configure::read('Global.Data');
			$specifications	= array();

			if($data){
				$is_lot = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_lot');
				$is_building = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_building');
				$is_residence = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_residence');
				$is_space = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_space');
				$_type = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'name');

				$dataAsset = $this->Rumahku->filterEmptyField($data, 'PropertyAsset');
				$lot_unit_id = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id');

				$lot_name = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'slug');
				$lot_name = $this->Rumahku->filterEmptyField($data, 'LotUnit', 'slug', $lot_name);

				$level = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'level');
				$building_size = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'building_size');
				$lot_width = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_width');
				$lot_length = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_length');
				$lot_size = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'lot_size');
				$beds = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'beds');
				$beds_maid = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'beds_maid');
				$baths = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'baths');
				$baths_maid = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'baths_maid');
				$cars = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'cars');
				$carports = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'carports');
				$phoneline = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'phoneline');
				$electricity = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'electricity');
				$furnished = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'furnished');
				$year_built = $this->Rumahku->filterEmptyField($data, 'PropertyAsset', 'year_built', false, true, 'year');

				$typeName		= Common::hashEmptyField($data, 'PropertyType.name');
				$actionName		= Common::hashEmptyField($data, 'PropertyAction.name');
				$directionID	= Common::hashEmptyField($data, 'PropertyAsset.PropertyDirection.id');
				$directionName	= Common::hashEmptyField($data, 'PropertyAsset.PropertyDirection.name');
				$conditionID	= Common::hashEmptyField($data, 'PropertyAsset.PropertyCondition.id');
				$conditionName	= Common::hashEmptyField($data, 'PropertyAsset.PropertyCondition.name');
				$viewSiteID		= Common::hashEmptyField($data, 'PropertyAsset.ViewSite.id');
				$viewSiteName	= Common::hashEmptyField($data, 'PropertyAsset.ViewSite.name');

				$baseApiURL = array(
					'plugin'		=> false, 
					'api'			=> true, 
					'controller'	=> 'api_properties', 
					'action'		=> 'master_data', 
				);

				$certificateName	= $this->getCertificate($data);
				$certificateID		= Common::hashEmptyField($data, 'Property.certificate_id');
			//	$certificateName	= Common::hashEmptyField($data, 'Certificate.name');
			//	$othersCertificate	= Common::hashEmptyField($data, 'Properties.others_certificate');

				$placeholder		= __('Pilih Sertifikat');
				$specifications[]	= array(
					'alias'	=> __('Sert.'),
					'name'	=> __('Sertifikat'),
				//	'value'	=> $othersCertificate ?: $certificateName,
					'value'	=> $certificateName,
					'model'	=> 'Property',
					'field'	=> 'certificate_id,others_certificate',
					'input'	=> array(
						array(
							'type'			=> 'select', 
							'name'			=> 'Property.certificate_id', 
						//	'placeholder'	=> $placeholder, 
							'text'			=> $certificateName, 
							'value'			=> $certificateID, 
							'source'		=> array_merge($baseApiURL, array(
								'ext' => 'json', 
								'certificate', 
								'list', 
								'?' => array(
									'empty' => $placeholder, 
								)
							)), 
						), 
				//		array(
				//			'type'		=> 'text', 
				//			'name'		=> 'Property.others_certificate', 
				//			'value'		=> $othersCertificate, 
				//			'source'	=> false, 
				//		), 
					),
				//	'delimiter' => ',&nbsp;', 
				);

				if($is_space){
					$lblLotName = __('Harga Satuan');
				}
				else{
					$lblLotName = __('Satuan Luas');
				}

				$placeholder		= __('Pilih Satuan Luas');
				$specifications[]	= array(
					'name'	=> $lblLotName,
					'value'	=> $lot_name,
					'model'	=> 'PropertyAsset',
					'field'	=> 'lot_unit_id',
					'input'	=> array(
						'type'			=> 'select', 
						'name'			=> 'PropertyAsset.lot_unit_id',
					//	'placeholder'	=> $placeholder, 
						'text'			=> $lot_name, 
						'value'			=> $lot_unit_id, 
						'source'		=> array_merge($baseApiURL, array(
							'ext' => 'json', 
							'lot_unit', 
							'list',
							'?' => array(
								'empty' => $placeholder, 
							)
						)), 
					),
				);

				if($is_building && ($showEmpty || $building_size)){
					$specifications[] = array(
						'alias'	=> __('L.B.'),
						'name'	=> __('Luas Bangunan'),
						'value'	=> trim(sprintf('%s %s', $building_size, $lot_name)),
						'model'	=> 'PropertyAsset',
						'field'	=> 'building_size,lot_unit_id', 
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.building_size', 
								'value'		=> $building_size, 
							), 
						),
					);
				}

				if($is_lot){
					$specifications[] = array(
						'alias'	=> __('L.T.'),
						'name'	=> __('Luas Tanah'),
						'value'	=> trim(sprintf('%s %s', $lot_size, $lot_name)),
						'model'	=> 'PropertyAsset',
						'field'	=> 'lot_size,lot_unit_id',
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.lot_size', 
								'value'		=> $lot_size, 
								'source'	=> false, 
							), 
						),
					);
				}

				$specifications[]	= array(
					'alias'	=> __('Panjang'),
					'name'	=> __('Panjang Tanah'),
					'value'	=> $lot_length,
					'model'	=> 'PropertyAsset',
					'field'	=> 'lot_length',
					'input'	=> array(
						array(
							'type'	=> 'number',
							'name'	=> 'PropertyAsset.lot_length', 
							'value'	=> $lot_length, 
						), 
					),
				);

				$specifications[]	= array(
					'alias'	=> __('Lebar'),
					'name'	=> __('Lebar Tanah'),
					'value'	=> $lot_width,
					'model'	=> 'PropertyAsset',
					'field'	=> 'lot_width',
					'input'	=> array(
						array(
							'type'	=> 'number',
							'name'	=> 'PropertyAsset.lot_width', 
							'value'	=> $lot_width, 
						), 
					),
				);

				if($is_residence){
					$delimiter = '&nbsp;&plus;&nbsp;';

					$specificationText	= array_filter(array($beds, $beds_maid));
					$specificationText	= trim(implode($delimiter, $specificationText));
					$specifications[]	= array(
						'alias'	=> __('K.T.'),
						'name'	=> __('Kamar Tidur'),
						'value'	=> $beds,
						'model'	=> 'PropertyAsset',
						'field'	=> 'beds',
						'input'	=> array(
							array(
								'type'	=> 'number',
								'name'	=> 'PropertyAsset.beds', 
								'value'	=> $beds, 
							), 
						),
					);

					$specifications[] = array(
						'alias'	=> __('K.T.E.'),
						'name'	=> __('Kamar Tidur Ekstra'),
						'value'	=> $beds_maid,
						'model'	=> 'PropertyAsset',
						'field'	=> 'beds_maid',
						'input'	=> array(
							array(
								'type'	=> 'number',
								'name'	=> 'PropertyAsset.beds_maid', 
								'value'	=> $beds_maid, 
							), 
						),
					);

					$specificationText	= array_filter(array($baths, $baths_maid));
					$specificationText	= trim(implode($delimiter, $specificationText));
					$specifications[]	= array(
						'alias'		=> __('K.M.'),
						'name'		=> __('Kamar Mandi'),
						'value'		=> $baths,
						'model'		=> 'PropertyAsset',
						'field'		=> 'baths',
						'input'	=> array(
							array(
								'type'	=> 'number',
								'name'	=> 'PropertyAsset.baths', 
								'value'	=> $baths, 
							), 
						),
					);

					$specifications[]	= array(
						'alias'		=> __('K.M.E'),
						'name'		=> __('Kamar Mandi Ekstra'),
						'value'		=> $baths_maid,
						'model'		=> 'PropertyAsset',
						'field'		=> 'baths_maid',
						'input'	=> array(
							array(
								'type'	=> 'number',
								'name'	=> 'PropertyAsset.baths_maid', 
								'value'	=> $baths_maid, 
							), 
						),
					);
				}

				if($is_building){
				//	if($showEmpty || $building_size){
				//		$specifications[] = array(
				//			'alias'	=> __('L.B.'),
				//			'name'	=> __('Luas Bangunan'),
				//			'value'	=> trim(sprintf('%s %s', $building_size, $lot_name)),
				//			'model'	=> 'PropertyAsset',
				//			'field'	=> 'building_size,lot_unit_id', 
				//			'input'	=> array(
				//				array(
				//					'type'		=> 'number', 
				//					'name'		=> 'PropertyAsset.building_size', 
				//					'value'		=> $building_size, 
				//				), 
				//			),
				//		);
				//	}

					$specifications[] = array(
						'alias'	=> __('Lantai'),
						'name'	=> __('Jumlah Lantai'),
						'value'	=> $level,
						'model'	=> 'PropertyAsset',
						'field'	=> 'level', 
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.level', 
								'value'		=> $level, 
							), 
						),
					);

					$specifications[] = array(
						'alias'	=> __('Garasi'),
						'name'	=> __('Garasi'),
						'value'	=> $cars,
						'model'	=> 'PropertyAsset',
						'field'	=> 'cars', 
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.cars', 
								'value'		=> $cars, 
							), 
						),
					);

					$specifications[] = array(
						'alias'	=> __('Carport'),
						'name'	=> __('Carport'),
						'value'	=> $carports,
						'model'	=> 'PropertyAsset',
						'field'	=> 'carports', 
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.carports', 
								'value'		=> $carports, 
							), 
						),
					);

					$specifications[] = array(
						'alias'	=> __('Line Tlp.'),
						'name'	=> __('Line Telepon'),
						'value'	=> $phoneline,
						'model'	=> 'PropertyAsset',
						'field'	=> 'phoneline', 
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.phoneline', 
								'value'		=> $phoneline, 
							), 
						),
					);

					$specifications[] = array(
						'alias'	=> __('Listrik'),
						'name'	=> __('Daya Listrik'),
						'value'	=> $electricity,
						'model'	=> 'PropertyAsset',
						'field'	=> 'electricity', 
						'input'	=> array(
							array(
								'type'		=> 'number', 
								'name'		=> 'PropertyAsset.electricity', 
								'value'		=> $electricity, 
							), 
						),
					);
					
					$furnishOptions		= Common::hashEmptyField($globalData, 'furnished');
					$customFurnished	= Common::hashEmptyField($furnishOptions, $furnished);

					foreach($furnishOptions as $value => $text){
						$furnishOptions[$value] = array('value' => $value, 'text' => $text);
					}

					$placeholder		= __('Pilih Interior');
					$furnishOptions		= array_merge(array(
						array(
							'value' => '', 
							'text'	=> $placeholder, 
						), 
					), $furnishOptions);

					$specifications[] = array(
						'alias'	=> __('Interior'),
						'name'	=> __('Interior'),
						'value'	=> $customFurnished,
						'model'	=> 'PropertyAsset',
						'field'	=> 'furnished', 
						'input'	=> array(
							array(
								'type'			=> 'select', 
								'name'			=> 'PropertyAsset.furnished', 
							//	'placeholder'	=> $placeholder, 
								'text'			=> $customFurnished, 
								'value'			=> $furnished, 
								'source'		=> str_replace('"', '\'', json_encode($furnishOptions)), 
							)
						),
					);

					$placeholder		= __('Pilih Arah Bangunan');
					$specifications[]	= array(
						'alias'	=> __('Arah'),
						'name'	=> __('Arah Bangunan'),
						'value'	=> $directionName,
						'model'	=> 'PropertyAsset',
						'field'	=> 'property_direction_id', 
						'input'	=> array(
							array(
								'type'			=> 'select', 
								'name'			=> 'PropertyAsset.property_direction_id', 
							//	'placeholder'	=> $placeholder, 
								'text'			=> $directionName, 
								'value'			=> $directionID, 
								'source'		=> array_merge($baseApiURL, array(
									'ext' => 'json', 
									'property_direction', 
									'list', 
									'?' => array(
										'empty' => $placeholder, 
									),
								)), 
							), 
						),
					);

					$placeholder	= __('Pilih Tahun dibangun');
					$currentYear	= date('Y');
					$years			= array(
						array(
							'value' 	=> '', 
							'text'		=> $placeholder, 
						//	'disabled'	=> true, 
						), 
					);

					for($index = date('Y'); $index > ($currentYear - 50) ; $index--){
						$index		= intval($index);
						$years[]	= array('value' => $index, 'text' => $index);
					}

					$years = str_replace('"', '\'', json_encode($years));

					$specifications[] = array(
						'alias'	=> __('Tahun'),
						'name'	=> __('Tahun dibangun'),
						'value'	=> $year_built,
						'model'	=> 'PropertyAsset',
						'field'	=> 'year_built', 
						'input'	=> array(
							array(
								'type'			=> 'select', 
								'name'			=> 'PropertyAsset.year_built', 
							//	'placeholder'	=> $placeholder, 
								'value'			=> $year_built, 
								'source'		=> $years, 
							), 
						),
					);

					$placeholder		= __('Pilih Kondisi Bangunan');
					$specifications[]	= array(
						'alias'	=> __('Kondisi'),
						'name'	=> __('Kondisi Bangunan'),
						'value'	=> $conditionName,
						'model'	=> 'PropertyAsset',
						'field'	=> 'property_condition_id', 
						'input'	=> array(
							array(
								'type'			=> 'select', 
								'name'			=> 'PropertyAsset.property_condition_id', 
							//	'placeholder'	=> $placeholder, 
								'text'			=> $conditionName, 
								'value'			=> $conditionID, 
								'source'		=> array_merge($baseApiURL, array(
									'ext' => 'json', 
									'property_condition', 
									'list', 
									'?' => array(
										'empty' => $placeholder, 
									)
								)), 
							), 
						),
					);

					$placeholder		= __('Pilih View %s', $typeName);
					$specifications[]	= array(
						'alias'	=> __('View'),
						'name'	=> __('View %s', $typeName), 
						'value'	=> $viewSiteName,
						'model'	=> 'PropertyAsset',
						'field'	=> 'view_site_id', 
						'input'	=> array(
							array(
								'type'			=> 'select', 
								'name'			=> 'PropertyAsset.view_site_id', 
							//	'placeholder'	=> $placeholder, 
								'text'			=> $viewSiteName, 
								'value'			=> $viewSiteID, 
								'source'		=> array_merge($baseApiURL, array(
									'ext' => 'json', 
									'view_site', 
									'list', 
									'?' => array(
										'empty' => $placeholder, 
									)
								)), 
							), 
						),
					);
				}

				if($specifications){
					if($autoRender){
						$editable		= Common::hashEmptyField($options, 'editable');
						$editableMode	= Common::hashEmptyField($options, 'editable_mode', 'popup');
						$wrapperOptions	= Common::hashEmptyField($options, 'wrapper', array());
						$listOptions	= Common::hashEmptyField($options, 'list', array());
						$labelOptions	= Common::hashEmptyField($options, 'label', array());
						$textOptions	= Common::hashEmptyField($options, 'text', array());

						$wrapperTag		= Common::hashEmptyField($wrapperOptions, 'tag', 'ul');
						$listTag		= Common::hashEmptyField($listOptions, 'tag', 'li');
						$labelTag		= Common::hashEmptyField($labelOptions, 'tag', 'span');
						$textTag		= Common::hashEmptyField($textOptions, 'tag', 'strong');

						$wrapperOptions	= Hash::remove($wrapperOptions, 'tag');
						$listOptions	= Hash::remove($listOptions, 'tag');
						$labelOptions	= Hash::remove($labelOptions, 'tag');
						$textOptions	= Hash::remove($textOptions, 'tag');

						$recordID	= Common::hashEmptyField($options, 'record_id');
						$recordID	= Common::hashEmptyField($data, 'Property.id', $recordID);
						$contentLi	= '';

						foreach($specifications as $key => $specification){
							$name	= Common::hashEmptyField($specification, 'name');
							$value	= Common::hashEmptyField($specification, 'value');
							$flag	= $dataRevision ? !empty($value) : true;

							if($flag){
								$model	= Common::hashEmptyField($specification, 'model');
								$field	= Common::hashEmptyField($specification, 'field');
								$inputs	= Common::hashEmptyField($specification, 'input', array());

								if($editable && $inputs){
									$delimiter	= Common::hashEmptyField($specification, 'delimiter');
									$value		= $this->buildEditableInput($inputs, array(
										'record_id'		=> $recordID, 
										'empty_text'	=> $emptyText, 
										'delimiter'		=> $delimiter, 
										'editable_mode'	=> $editableMode, 
									));
								}

								$item = $this->Html->tag($labelTag, sprintf('%s: ', $name));
								$item.= $this->Html->tag($textTag, $value);
								$item = $this->Rumahku->getCheckRevision($model, $field, $dataRevision, $item);

								$contentLi.= $this->Html->tag($listTag, $item, $listOptions);
							}
						}

						$result = $this->Html->tag($wrapperTag, $contentLi, $wrapperOptions);
					}
					else{
						$result = $specifications;
					}
				}
			}
		}

		return $result;
	}

	public function buildEditableInput($inputs = array(), $options = array()){
		$inputs		= (array) $inputs;
		$options	= (array) $options;

		if($inputs){
			$recordID		= Common::hashEmptyField($options, 'record_id');
			$emptyText		= Common::hashEmptyField($options, 'empty_text');
			$delimiter		= Common::hashEmptyField($options, 'delimiter', '&nbsp;');
			$editableMode	= Common::hashEmptyField($options, 'editable_mode', 'popup');
			$isMultiple		= Hash::numeric(array_keys($inputs));

			if($isMultiple == false){
				$inputs = array($inputs);
			}

			foreach($inputs as &$input){
				$inputName			= Common::hashEmptyField($input, 'name');
				$inputType			= Common::hashEmptyField($input, 'type', 'text');
				$inputText			= Common::hashEmptyField($input, 'text');
				$inputValue			= Common::hashEmptyField($input, 'value', '');
				$inputSource		= Common::hashEmptyField($input, 'source');
				$inputPlaceholder	= Common::hashEmptyField($input, 'placeholder');
				$inputMandatory		= Common::hashEmptyField($input, 'mandatory');

				if(empty($inputText)){
					$inputText = $inputValue ?: $emptyText;
				}

				$inputID	= $inputName;
				$inputName	= explode('.', $inputName);
				$inputName	= sprintf('data[%s]', implode('][', $inputName));

				if($inputName){
					$inputClass		= strlen(trim($inputValue)) ? '' : 'editable-empty';
					$inputClass		= sprintf('editable editable-fullwidth editable-click %s', $inputClass);
					$inputOptions	= array(
					//	'data-id'		=> $inputID, 
						'data-name'		=> $inputName, 
						'data-type'		=> $inputType, 
						'data-value'	=> $inputValue, 
						'data-mode'		=> $editableMode, 
						'class'			=> $inputClass, 
						'escape'		=> false, 
					);

					if($recordID){
						$inputOptions['data-pk'] = $recordID;
					}

					if($inputSource){
						$inputOptions['data-source'] = is_array($inputSource) ? $this->Html->url($inputSource, true) : $inputSource;
					}

					if($inputPlaceholder){
						$inputOptions['data-placeholder'] = $inputPlaceholder;
					}

					if($inputMandatory){
						$inputOptions['data-mandatory'] = 'true';
					}

					$inputTemplate = false;

					if(in_array($inputType, array('number', 'price'))){
						$inputTemplate = sprintf("<input type='text' class='input_%s form-control input-sm'>", $inputType);
					}

					if($inputTemplate){
						$inputOptions = Hash::insert($inputOptions, 'data-tpl', $inputTemplate);
					}

					$input = $this->Html->link($inputText ?: $inputValue, '#', $inputOptions);
				}
				else{
					$input = null;
				}
			}

			$inputs = implode($delimiter, $inputs);
		}

		return $inputs;
	}
}