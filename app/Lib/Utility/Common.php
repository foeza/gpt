<?php
App::uses('CakeText', 'Utility');

class Common {
	public static function hashEmptyField($value, $path, $empty = null, $options = false){
		$types = !empty($options['type'])?$options['type']:'empty';
		$date = !empty($options['date'])?$options['date']:false;
		$wa = !empty($options['wa'])?$options['wa']:false;
		$truncate = !empty($options['truncate'])?$options['truncate']:false;
		$implode = !empty($options['implode'])?$options['implode']:false;
		$reverse = !empty($options['reverse'])?$options['reverse']:false;
		$urldecode = isset($options['urldecode'])?$options['urldecode']:true;
		$urldecode_double = isset($options['urldecode_double'])?$options['urldecode_double']:true;
		$addslashes = isset($options['addslashes'])?$options['addslashes']:false;
		$isset = isset($options['isset'])?$options['isset']:false;
		$result = $empty;
		$resultTmp = false;

		if(is_array($value) && !empty($types) ) {
			if( !is_array($types) ) {
				$types = array(
					$types,
				);
			}

			$validPath = $path !== null && is_bool($path) === false;

			if( !empty($value) && is_array($value) && $validPath ) {
				if( !empty($isset) ) {

					$resultTmp = $result = Hash::get($value, $path, $empty);
				} else {
					$resultTmp = $result = Hash::get($value, $path);

					if( empty($result) ) {
						$result = $empty;
					}
				}
			} else {
				$result = $empty;
			}

			foreach ($types as $key => $type) {
				switch($type){
					case 'slug':
						$result = Common::toSlug($result);
						break;
					case 'strip_tags':
						$result = Common::safeTagPrint($result);
						break;
					case 'unserialize':
						$result = unserialize($result);
						break;
					case 'htmlentities':
						$result = htmlentities($result);
						break;
					case 'EOL':
						$result = str_replace(PHP_EOL, '<br>', $result);
						break;
					case 'trailing_slash':
						$last_char = substr($result, -1);

						if( $last_char === '/' ) {
							$result = rtrim($result, $last_char);
						}
						break;
					case 'currency':
						$result = Common::getFormatPrice($result);
						break;
					case 'hash':
						$result = Common::auth_password($result);
						break;
					case 'tel':
						if(!empty($result)){
							$result = sprintf('<a href="tel:%s" title="%s">%s</a>', $result, $result, $result);
						}
						break;
					case 'mailto':
						if(!empty($result)){
							$result = sprintf('<a href="mailto:%s" title="%s">%s</a>', $result, $result, $result);
						}
						break;
					case 'array_shift':
						if( is_array($result) ) {
							$result = !empty($result) ? array_shift($result) : false;
						}
						break;
					case 'date':
						$result = Common::getDate($result, array(
							'reverse' => $reverse,
						));
						break;
					case 'json_encode':
						$result = json_encode($result);
						break;
					case 'json_decode':
						$result = json_decode($result, true);
						break;
					case 'strtolower':
						$result = strtolower($result);
						break;
					case 'strtoupper':
						$result = strtoupper($result);
						break;
					case 'ucwords':
						$result = ucwords($result);
						break;
				}
			}
		}

		if( !empty($date) ) {
			$format = $date;
			$result = Common::formatDate($result, $format, $empty);
			// $result = Common::formatDate($result, $format);
		}
		if( !empty($wa) ) {
            $format = $wa;
            
            if( !empty($format) ) {
            	$result = __('%s %s', $result, __('(WA)'));
            }
        }
        if( !empty($truncate) && is_array($truncate) ) {
    		$len = Common::hashEmptyField($truncate, 'len');
    		$ending = Common::hashEmptyField($truncate, 'ending', ' ...');

			$result = Common::truncate($result, $len, $ending);
        }
		if( !empty($implode) && !empty($resultTmp) && is_array($resultTmp) ) {
			$glue = $implode;
			$result = implode($glue, $result);
		}
		if( is_string($result) && $urldecode ) {
			$result = trim(urldecode($result));

			if( !empty($urldecode_double) ) {
				$result = urldecode($result);
			}

			if( !empty($addslashes) ) {
				$result = addslashes($result);
			}
		}

		return $result;
	}

	public static function toSlug($data, $fields = false, $glue = '-') {
		if( !empty($data) ) {
			if( !is_array($data) ) {
				$data = strtolower(Inflector::slug($data, $glue));
			} else {
				foreach ($fields as $key => $value) {
					if( is_array($value) ) {
						foreach ($value as $idx => $fieldName) {
							if( !empty($data[$key][$fieldName]) ) {
								$data[$key][$fieldName] = strtolower(Inflector::slug($data[$key][$fieldName], $glue));
							}
						}
					} else {
						$data[$value] = strtolower(Inflector::slug($data[$value], $glue));
					}
				}
			}
		}

		return $data;
	}

	public static function getFormatPrice($price, $empty = 0){
		App::uses('CakeNumber', 'Utility'); 
		if( !empty($price) ) {
			return CakeNumber::currency($price, '', array('places' => 0));
		} else {
			return $empty;
		}
	}

	public static function getCurrencyPrice ($price, $empty = false, $currency = false, $decimalPlaces = 0) {
		if( !empty($empty) && empty($price) ) {
			return $empty;
		} else {
			if( empty($currency) ) {
				$currency = Configure::read('__Site.config_currency_symbol');
			}

			App::uses('CakeNumber', 'Utility'); 
			return CakeNumber::currency($price, $currency, array('places' => $decimalPlaces));
		}
	}

	public static function safeTagPrint($string){
		if( is_string($string) ) {
			return strip_tags($string);
		} else {
			return $string;
		}
	}

	public static function formatDate($dateString, $format = false, $empty = '-') {
		if( empty($dateString) || $dateString == '0000-00-00' || $dateString == '0000-00-00 00:00:00') {
			return $empty;
		} else {
			if( !empty($format) ) {
				return date($format, strtotime($dateString));
			} else {
				return $this->Time->niceShort(strtotime($dateString));
			}
		}
	}

    public static function getDate ( $date, $options = array() ) {
    	$reverse = Common::hashEmptyField($options, 'reverse');
    	$time = Common::hashEmptyField($options, 'time');
    	$empty = '0000-00-00';

    	if( !empty($time) ) {
    		if( !is_string($time) ) {
    			$time = 'H:i';
    		}

    		$time = ' '.$time;
    		$empty = '0000-00-00 00:00:00';
    	}

		$dtString = false;
		$date = trim($date);

		if( !empty($date) && $date != $empty ) {
			if($reverse){
				$dtString = date('d/m/Y'.$time, strtotime($date));
			}else{
				$dtArr = array();

				if( !empty($time) ) {
					$dtTime = explode(' ', $date);

					if( !empty($dtTime[0]) ) {
						$dtArr = explode('/', $dtTime[0]);
					}
					if( !empty($dtTime[1]) && !empty($dtArr[0]) ) {
		    			$dtArr[0] .= ' '.$dtTime[1];
					}
		    	} else {
					$dtArr = explode('/', $date);
		    	}

				if( count($dtArr) == 3 ) {
					$dtString = date('Y-m-d'.$time, strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
				} else {
					$dtArr = explode('-', $date);

					if( count($dtArr) == 3 ) {
						$dtString = date('Y-m-d'.$time, strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
					}
				}
			}
		}
			
		return $dtString;
	}

	public static function _callUnset( $data, $fieldArr, $removeField = false) {
		if( !empty($fieldArr) ) {
			foreach ($fieldArr as $key => $value) {
				if( is_array($value) ) {
					foreach ($value as $idx => $fieldName) {
						if( isset($data[$key][$fieldName]) ) {
							unset($data[$key][$fieldName]);
						}else{
							if($removeField){
								unset($data[$key][$fieldName]);
							}
						}
					}
				} else {
					unset($data[$value]);
				}
			}
		}
		return $data;
	}

	public static function _callSet( $data, $fieldArr ) {
		if( !empty($fieldArr) && !empty($data) ) {
			$data = array_intersect_key($data, array_flip($fieldArr));
		}
		return $data;
	}

	public static function getCombineDate ( $startDate, $endDate, $empty = false, $emptyEndDate = ' - ..', $options = false ) {
	//	note : handling strtotime di erver windows vs linux / unix beda
	//	untuk tanggal yang valid jika di strtotime hasilnya pasti > 0, sedangkan tanggal tidak valid hasil nya akan <= 0

		$startDate = strtotime($startDate) > 0 ? strtotime($startDate) : false;
		$endDate = strtotime($endDate) > 0 ? strtotime($endDate) : false;

		$newline = Common::hashEmptyField($options, 'newline');
		$divider = Common::hashEmptyField($options, 'divider', '-');
		$customDate = false;

		if( !empty($newline) ) {
			$newline = '<br>';
		}

		if( !empty($startDate) && !empty($endDate) ) {
			if( $startDate == $endDate ) {
				$customDate = date('d M Y', $startDate);
			} else if( date('M Y', $startDate) == date('M Y', $endDate) ) {
				$customDate = sprintf('%s %s %s%s %s', date('d', $startDate), $divider, date('d', $endDate), $newline, date('M Y', $endDate));
			} else if( date('Y', $startDate) == date('Y', $endDate) ) {
				$customDate = sprintf('%s %s%s %s', date('d M', $startDate), $divider, $newline, date('d M Y', $endDate));
			} else {
				$customDate = sprintf('%s %s%s %s', date('d M Y', $startDate), $divider, $newline, date('d M Y', $endDate));
			}
		} else if( !empty($startDate) ) {
			$customDate = sprintf('%s%s', date('d M Y', $startDate), $emptyEndDate);
		} else if( !empty($endDate) ) {
			$customDate = sprintf('s/d %s', date('d M Y', $endDate));
		} else if( !empty($empty) ) {
			$customDate = $empty;
		}

		return $customDate;
	}

	public static function _date_range_limit($start, $end, $adj, $a, $b, $result) {
		if ($result[$a] < $start) {
			$result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
			$result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
		}

		if ($result[$a] >= $end) {
			$result[$b] += intval($result[$a] / $adj);
			$result[$a] -= $adj * intval($result[$a] / $adj);
		}

		return $result;
	}

	public static function _date_range_limit_days($base, $result) {
		$days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		Common::_date_range_limit(1, 13, 12, "m", "y",   $base);

		$year = $base["y"];
		$month = $base["m"];

		if (!$result["invert"]) {
			while ($result["d"] < 0) {
				$month--;
				if ($month < 1) {
					$month += 12;
					$year--;
				}

				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

				$result["d"] += $days;
				$result["m"]--;
			}
		} else {
			while ($result["d"] < 0) {
				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

				$result["d"] += $days;
				$result["m"]--;

				$month++;
				if ($month > 12) {
					$month -= 12;
					$year++;
				}
			}
		}

		return $result;
	}

	public static function _date_normalize($base, $result) {
		$result = Common::_date_range_limit(0, 60, 60, "s", "i", $result);
		$result = Common::_date_range_limit(0, 60, 60, "i", "h", $result);
		$result = Common::_date_range_limit(0, 24, 24, "h", "d", $result);
		$result = Common::_date_range_limit(0, 12, 12, "m", "y", $result);

		$result = Common::_date_range_limit_days($base, $result);

		$result = Common::_date_range_limit(0, 12, 12, "m", "y", $result);

		return $result;
	}

	public static function _callDateDiff ( $one, $two ) {
		$invert = false;
		$one = strtotime($one);
		$two = strtotime($two);

		if ($one > $two) {
			list($one, $two) = array($two, $one);
			$invert = true;
		}

		$key = array("y", "m", "d", "h", "i", "s");
		$a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
		$b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

		$result = array();
		$result["y"] = $b["y"] - $a["y"];
		$result["m"] = $b["m"] - $a["m"];
		$result["d"] = $b["d"] - $a["d"];
		$result["h"] = $b["h"] - $a["h"];
		$result["i"] = $b["i"] - $a["i"];
		$result["s"] = $b["s"] - $a["s"];
		$result["invert"] = $invert ? 1 : 0;
		$result["days"] = intval(abs(($one - $two)/86400));

		if ($invert) {
			Common::_date_normalize($a, $result);
		} else {
			Common::_date_normalize($b, $result);
		}

		return $result;
	}

	public static function dateDiff ( $startDate, $endDate, $format = false ) {
		$result = false;
		
		if( !empty($startDate) && !empty($endDate) && $startDate != '0000-00-00 00:00:00' && $endDate != '0000-00-00 00:00:00' ) {
			$from_time = strtotime($startDate);
			$to_time = strtotime($endDate);
			$datediff = intval($to_time - $from_time);
			$total_day = intval($datediff/(60*60*24));
			$total_hour = intval($datediff/(60*60));

			$dateResult = Common::_callDateDiff ( $startDate, $endDate );

			switch ($format) {
				case 'day':	

					$result = $total_day;

					break;

				default:
					if( !empty($dateResult[$format]) ) {
						$result = $dateResult[$format];
					} else {
						$result = $dateResult;
					}
					break;
			}
		}

		return $result;
	}

	public static function getNameFromNumber($num) {
	    $numeric = ($num - 1) % 26;
	    $letter = chr(65 + $numeric);
	    $num2 = intval(($num - 1) / 26);
	    if ($num2 > 0) {
	        return Common::getNameFromNumber($num2) . $letter;
	    } else {
	        return $letter;
	    }
	}

	public static function alterColor($color = null, $percentage = 0){
		$validColor = $color && Common::strposArray($color, array('#', 'rgb', 'rgba')) !== false;

		if($validColor && is_numeric($percentage)){
			if(strpos($color, '#') !== false){
			//	hex format
				$color = Common::hexToRgb($color, 1);
			}

			$color = str_replace(array('rgba', 'rgb', '(', ')', ' '), null, $color);
			$color = explode(',', $color);

			if(count($color) >= 3 && count($color) <= 4){
			//	no matter whether it counts 3 or 4, we just need the first 3 indexes
				$r = Common::hashEmptyField($color, 0, 0);
				$g = Common::hashEmptyField($color, 1, 0);
				$b = Common::hashEmptyField($color, 2, 0);
				$a = Common::hashEmptyField($color, 3, 0);

				$percentageVal = (255 / 100) * abs($percentage);
				if($percentage > 0){
				//	plus
					$r+= $percentageVal;
					$g+= $percentageVal;
					$b+= $percentageVal;

					$r = floor(($r > 255 ? 255 : $r));
					$g = floor(($g > 255 ? 255 : $g));
					$b = floor(($b > 255 ? 255 : $b));
				}
				else{
				//	minus
					$r-= $percentageVal;
					$g-= $percentageVal;
					$b-= $percentageVal;

					$r = floor(($r < 0 ? 0 : $r));
					$g = floor(($g < 0 ? 0 : $g));
					$b = floor(($b < 0 ? 0 : $b));
				}

				if(count($color) == 4){
					$color = sprintf('rgba(%s,%s,%s,%s)', $r, $g, $b, $a);
				}
				else{
					$color = sprintf('rgb(%s,%s,%s)', $r, $g, $b);
				}
			}
		}
		return $color;
	}

	//	Convert hexdec color string to rgb(a) string
	public static function hexToRgb($color, $opacity = false){
		$default = 'rgb(0,0,0)';

	//	return default if no color provided
		if(empty($color)){
			return $default;	
		}
		else{
		//	sanitize $color if "#" is provided 
			if(substr($color, 0, 1) == '#'){
				$color = substr($color, 1);
			}
		}

	//	check if color has 6 or 3 characters and get values
		if(strlen($color) == 6){
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		}
		elseif(strlen($color) == 3){
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		}
		else{
		//	invalid format
			return $default;
		}

	//	convert hexadec to rgb
		$rgb = array_map('hexdec', $hex);

	//	check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1){
				$opacity = 1.0;
			}

			$output = 'rgba('.implode(',', $rgb).','.$opacity.')';
		}
		else{
			$output = 'rgb('.implode(',', $rgb).')';
		}

	//	return rgb(a) color string
		return $output;
	}

	//	strpos with array as needle
	public static function strposArray($haystack, $needles = array(), $offset = 0){
        $chr = array();

        foreach($needles as $needle){
			$res = strpos($haystack, $needle, $offset);
			if ($res !== false) $chr[$needle] = $res;
        }

        if(empty($chr)) return false;
        return min($chr);
	}

//	split array into groups
	public static function splitArray($values, $col = 1){
		$result	= array();
		$count	= ceil(count($values) / $col);

		if($col == 1 || $count == 1){
			$result[] = $values;
		}
		else{
			if(!empty($values)){
				$index	= 0;
				$loop	= 1;

				foreach($values as $key => $value){
					$result[$index][$key] = $value;

					if($loop >= $count){
						$loop = 1;
						$index++;
					}
					else{
						$loop++;
					}
				}
			}
		}

		return $result;
	}

	public static function buildTree(&$records = array(), $parentID = 0, $options = array()){
		$branch = array();

		if($records){
			$model			= Common::hashEmptyField($options, 'model', 'Page');
			$alias			= Common::hashEmptyField($options, 'alias', $model);
			$parentField	= Common::hashEmptyField($options, 'parent_field', 'id');
			$childField		= Common::hashEmptyField($options, 'child_field', 'parent_id');

			foreach($records as $key => &$record){
				$recordID		= Common::hashEmptyField($record, sprintf('%s.%s', $model, $parentField));
				$recordParentID	= Common::hashEmptyField($record, sprintf('%s.%s', $model, $childField), 0);
				if($recordParentID == $parentID){
					$children = Common::buildTree($records, $recordID, $options);
					$children = Hash::sort($children, sprintf('{n}.%s.name', $alias), 'asc');

				//	append children
					$branch[] = Hash::insert($record, 'children', $children);

				//	unset record jadi looping berikut nya ga berat
					unset($records[$key]);
				}
			}

			$branch = Hash::sort($branch, sprintf('{n}.%s.order', $model), 'asc');

			if(empty($parentID) && $records){
			//	sisa navigasi yang ga punya parent pasang sebagai parent
				$order = count($branch) + 1;

				foreach($records as $record){
					$record = Hash::insert($record, sprintf('%s.order', $model), $order);
					$order++;
				}

				$branch = (array) $branch + (array) $records;
			}
		}

		return $branch;
	}
	
	public static function _callPassToken( $params ){
		$pass_token = Common::hashEmptyField($params, 'named.pass_token');
		return $pass_token;
	}

	public static function getAddress( $data, $separator = ', ', $location_display = false, $break_address = true, $separator_address = '<br>' ) {
		$address = Common::hashEmptyField($data, 'address');
		$region = Common::hashEmptyField($data, 'Region.name');
		$city = Common::hashEmptyField($data, 'City.name');
		$subarea = Common::hashEmptyField($data, 'Subarea.name');
		$zip = Common::hashEmptyField($data, 'zip');
		$no = Common::hashEmptyField($data, 'no');
		$rt = Common::hashEmptyField($data, 'rt');
		$rw = Common::hashEmptyField($data, 'rw');
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
				$address .= $separator_address.implode(', ', $detail_address);
			}

			if( !empty($break_address) ) {
				$fulladdress = $address . $separator_address;
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

	public static function _callPhoneNumber( $no_hp, $no_hp_2 = null, $empty = '' ){
		if( empty($no_hp) ) {
			$no_hp = $empty;
		} else if( !empty($no_hp) && !empty($no_hp_2) ) {
			$no_hp = __('%s / %s', $no_hp, $no_hp_2);
		}

		return $no_hp;
	}

	public static function _callPeriodeYear ( $maxPeriode = 50, $text = 'Thn' ) {
		$year = array();

		for ($i=1; $i <= $maxPeriode; $i++) { 
			$label_text = $i.' '.$text;
			$label_text = trim($label_text);

			$year[$i] = $label_text;
		}

		return $year;
	}

	public static function statisticCompare($lastData = array(), $currentData = array(), $propertyTypes = array(), $options = array()){
		$results = array();

		if($propertyTypes){
			$options	= (array) $options;
			$model		= Hash::get($options, 'model', 'ViewUnionPropertySubarea');
			$actions	= array(1 => 'jual', 2 => 'sewa');

		//	1 LOOP BY TYPE ===============================================================================================

			foreach($propertyTypes as $propertyTypeKey => $propertyType){
				if(is_array($propertyType)){
					$typeID		= Hash::get($propertyType, 'PropertyType.id');
					$typeSlug	= Hash::get($propertyType, 'PropertyType.slug');
					$typeName	= Hash::get($propertyType, 'PropertyType.name');
				}
				else{
					$typeID		= $propertyTypeKey;
					$typeSlug	= Inflector::slug(strtolower($propertyType), '-');
					$typeName	= $propertyType;
				}

				$compareDetails	= array();
				$propertyCount	= 0;
				$priceMeasure	= 0;

			//	2 LOOP BY ACTION =============================================================================================

				foreach($actions as $actionID => $actionSlug){
					$baseSelector = sprintf('/%s[property_type_id=%s][property_action_id=%s]', $model, $typeID, $actionID);

					$lastCount		= Set::extract(sprintf('%s/property_count', $baseSelector), $lastData);
					$currentCount	= Set::extract(sprintf('%s/property_count', $baseSelector), $currentData);

					$lastPrice		= Set::extract(sprintf('%s/price_measure', $baseSelector), $lastData);
					$currentPrice	= Set::extract(sprintf('%s/price_measure', $baseSelector), $currentData);

					$lastCount		= array_sum($lastCount);
					$currentCount	= array_sum($currentCount);

					$lastPrice		= array_sum($lastPrice);
					$currentPrice	= array_sum($currentPrice);

					$compareDetails[$actionSlug] = array(
						'property_action_id'		=> $actionID, 
						'property_type_id'			=> $typeID, 
						'property_count'			=> $currentCount, 
						'property_price'			=> $currentPrice, 
						'property_count_diff'		=> $currentCount - $lastCount,
						'property_price_diff'		=> $currentPrice - $lastPrice,

					//	movement percentage
						'property_count_percent'	=> Common::movementPercentage($lastCount, $currentCount),
						'property_price_percent'	=> Common::movementPercentage($lastPrice, $currentPrice),
					);

				//	3 LOOP BY SOLD STATUS ========================================================================================

					foreach(array(0, 1) as $isSold){
						$statusSlug		= sprintf('%s%s', $isSold ? 'ter' : 'di', $actionSlug);
						$fieldSelector	= sprintf('%s[sold=%s]', $baseSelector, $isSold);
						$lastCount		= Set::extract(sprintf('%s/property_count', $fieldSelector), $lastData);
						$currentCount	= Set::extract(sprintf('%s/property_count', $fieldSelector), $currentData);

						$lastPrice		= Set::extract(sprintf('%s/price_measure', $fieldSelector), $lastData);
						$currentPrice	= Set::extract(sprintf('%s/price_measure', $fieldSelector), $currentData);

						$lastCount		= array_sum($lastCount);
						$currentCount	= array_sum($currentCount);

						$lastPrice		= array_sum($lastPrice);
						$currentPrice	= array_sum($currentPrice);

						$compareDetails[$actionSlug]['detail'][$statusSlug] = array(
							'property_action_id'		=> $actionID, 
							'property_type_id'			=> $typeID, 
							'property_count'			=> $currentCount, 
							'property_price'			=> $currentPrice, 
							'property_count_diff'		=> $currentCount - $lastCount,
							'property_price_diff'		=> $currentPrice - $lastPrice,

						//	movement percentage
							'property_count_percent'	=> Common::movementPercentage($lastCount, $currentCount),
							'property_price_percent'	=> Common::movementPercentage($lastPrice, $currentPrice),
							'sold'						=> $isSold, 
						);
					}
				}

				$results[$typeID] = array(
					'PropertyType' => array(
						'id'		=> $typeID, 
						'slug'		=> $typeSlug, 
						'name'		=> $typeName, 
						'detail'	=> $compareDetails, 
					), 
				);
			}
		}

		return $results;
	}

	public static function movementPercentage($lastValue = 0, $currentValue = 0){
	//	empty last value && current value	> baru mulai insert data
	//	empty current value					> kemungkinan sudah ada transaksi sebelumnya, tapi periode saat ini tidak ada data masuk
	//	last value == current value			> jelas stagnan

		if(empty($lastValue) && $currentValue || empty($currentValue) || $lastValue == $currentValue){
			$percentage = 0;
		}
		else if($lastValue && $currentValue){
			$percentage = abs($currentValue - $lastValue) / ($lastValue / 100);

		//	increment / decrement
			$percentage = $lastValue > $currentValue ? $percentage * -1 : $percentage;
		}

		return $percentage;
	}

	public static function dataConverter( $data, $fields, $reverse = false, $round = 0 ) {
		if( !empty($data) && !empty($fields) ) {
			foreach ($fields as $type => $models) {
				$data = Common::_converterLists($type, $data, $models, $reverse, $round);
			}
		}
		return $data;
	}

	public static function _converterLists($type, $data, $models, $reverse = false, $round = 0){
    	if(!empty($type) && !empty($data) && !empty($models)){
    		if(is_array($models)){
    			foreach($models AS $loop => $model){
 	   				if(!empty($model) || $model === 0){ 	   					
	 	   				if( is_array($model) && !empty($data[$loop]) ){
	 	   					if(is_numeric($loop)){
	 	   						foreach($data AS $key => $dat){
	 	   							if(is_array($model) && !empty($dat)){

	 	   								$data[$key] = Common::_converterLists($type, $data[$key], $model, $reverse, $round);
	 	   							}
	 	   						}
	 	   					}else{	 	   						
	 	   						$data[$loop] = Common::_converterLists($type, $data[$loop], $models[$loop], $reverse, $round);
	 	   					}
	 	   				} else if( !is_array($model) ) {	 	
	 	   				   					
	 	   					if(in_array($type, array('unset', 'array_filter'))){
	 	   						if($type == 'array_filter'){
	 	   							$data[$model] = array_filter($data[$model]);
	 	   							if(empty($data[$model])){
	 	   								unset($data[$model]);
	 	   							}
	 	   						}else{
	 	   							unset($data[$model]);
	 	   						}

	 	   					} else if( !empty($data[$model]) ) {	 	   						
	 	   						$data[$model] = Common::_generateType($type, $data[$model], $reverse, $round);
	 	   					}
	 	   				}
	 	   			}
	    		}
    		}else{
    			if(in_array($type, array('unset', 'array_filter'))){
    				if($type == 'array_filter'){
						$data[$models] = array_filter($data[$models]);
						if(empty($data[$models])){
							unset($data[$models]);
						}
					}else{
						unset($data[$models]);
					}
    			}else{
    				$data[$models] = Common::_generateType($type, $data[$models], $reverse, $round);
    			}
    		}
    	}
    	return $data;
    }

    public static function _generateType($type, $data, $reverse, $round){
    	switch($type){
			case 'date' : 
			$data = Common::getDate($data, array(
            	'reverse' => $reverse,
        	));
			break;
			case 'price' : 
				$data = Common::_callPriceConverter($data, $reverse);
				break;
			case 'datetime' : 
			$data = Common::getDate($data, array(
            	'reverse' => $reverse,
            	'time' => true,
        	));
			break;
			## ADA CASE BARU TAMBAHKAN DISINI, ANDA HANYA MEMBUAT $this->FUNCTION yang anda inginkan tanpa merubah flow dari
			## function dataConverter dan _converterLists
		}
		return $data;
    }

	public static function _callPriceConverter ($price, $reverse = false) {
		$price = Common::safeTagPrint($price);

		if($price){
			if(empty($reverse)){
				return trim(str_replace(array( ',', 'Rp ' ), array( '', '' ), $price));		
			}else{
				return number_format($price, 0, '', ',');
			}
		}
	}

	public static function getMonth ( $date, $options = array() ) {
    	$reverse = Common::hashEmptyField($options, 'reverse');
    	$last_date = Common::hashEmptyField($options, 'last_date', '01');
    	$empty = '0000-00';

		$dtString = false;
		$date = trim($date);

		if( !empty($date) && $date != $empty ) {
			if($reverse){
				$dtString = date('M Y', strtotime($date));
			}else{
				$dtArr = array();
				$dtArr = explode(' ', $date);

				if( count($dtArr) == 2 ) {
					$dtString = date('Y-m-'.$last_date, strtotime(sprintf('%s-%s', $dtArr[1], $dtArr[0])));
				}
			}
		}
			
		return $dtString;
	}

	public static function _callConvertMonthRange ( $params, $date, $options = array() ) {
		$startField = Common::hashEmptyField($options, 'date_from', 'date_from');
		$endField = Common::hashEmptyField($options, 'date_to', 'date_to');
		$format = Common::hashEmptyField($options, 'format', 'Y-m-d');

		$date = urldecode($date);
		$dateArr = explode(' - ', $date);

		if( !empty($dateArr) && count($dateArr) == 2 ) {
			$fromDate = !empty($dateArr[0])?Common::getMonth($dateArr[0]):false;
			$toDate = !empty($dateArr[1])?Common::getMonth($dateArr[1], array(
				'last_date' => 't',
			)):false;

			$params[$startField] = Common::formatDate($fromDate, $format);
			$params[$endField] = Common::formatDate($toDate, $format);
		}

		return $params;
	}

	public static function _callIndicatorPercentage( $current, $before, $decimal = 2 ){
		$margin = $current - $before;
		// $margin = -3;

		if( !empty($before) ) {
			$result = ($margin / $before) * 100;
			return round($result, $decimal);
		} else {
			return 0;
		}
	}

	public static function _callTargetPercentage( $value, $target, $decimal = 2 ){
		if(empty($target)){
			$margin = 0; 
		} else {
			$margin = ($value / $target);
		}

		if( !empty($margin) ) {
			return round($margin * 100, $decimal);
		} else {
			return 0;
		}
	}

	public static function _callPeriodeDate ( $period, $options = array() ) {
		$periode_date_to = Common::hashEmptyField($options, 'date_to', date('Y-m-d'));

		switch ($period) {
			case 'monthly':
        		$periode_date_from = date ("Y-m-d", strtotime('-1 month', strtotime($periode_date_to)));
        		$periode_date_from = date ("Y-m-d", strtotime('+1 day', strtotime($periode_date_from)));
				break;
			case 'quarterly':
        		$periode_date_from = date ("Y-m-d", strtotime('-3 month', strtotime($periode_date_to)));
        		$periode_date_from = date ("Y-m-d", strtotime('+1 day', strtotime($periode_date_from)));
				break;
			case 'mid-monthly':
        		$periode_date_from = date ("Y-m-d", strtotime('-6 month', strtotime($periode_date_to)));
        		$periode_date_from = date ("Y-m-d", strtotime('+1 day', strtotime($periode_date_from)));
				break;
			case 'yearly':
        		$periode_date_from = date ("Y-m-d", strtotime('-12 month', strtotime($periode_date_to)));
        		$periode_date_from = date ("Y-m-d", strtotime('+1 day', strtotime($periode_date_from)));
				break;
			default:
        		$periode_date_from = date ("Y-m-d", strtotime('-6 day', strtotime($periode_date_to)));
				break;
		}

		return array(
			'periode_date_from' => $periode_date_from,
			'periode_date_to' => $periode_date_to,
			'title' => Common::getCombineDate($periode_date_from, $periode_date_to),
		);
	}

	public static function httpRequest($targetURL = null, $data = null, $options = array()){
		if($targetURL){
			$parse	   = Common::hashEmptyField($options, 'parse', true, array('isset' => true));
			$curl	   = Common::hashEmptyField($options, 'curl', true, array('isset' => true));
			$urldecode = Common::hashEmptyField($options, 'urldecode', false, array('isset' => false));
			$debug	   = Common::hashEmptyField($options, 'debug');

			$debugTemplate = '';

		//	set URL and other appropriate options
			$timeout	= Common::hashEmptyField($options, 'timeout', 60);
			$dataType	= Common::hashEmptyField($options, 'data_type', 'query');
			$header		= Common::hashEmptyField($options, 'header');
			$method		= Common::hashEmptyField($options, 'method', 'GET');
			$sslVersion	= Common::hashEmptyField($options, 'ssl_version', 3);
			$method		= strtoupper($method);

		//	APPEND DATA AS QUERY STRING =====================================================

			if($data && is_array($data)){
				$dataType = in_array($dataType, array('query', 'json')) ? $dataType : 'query';

				if($dataType == 'query'){
				//	data with url query format
					$data = http_build_query($data, '', '&');
				}
				else{
				//	data with raw json format
					$data = json_encode($data);
				}
			}

			if($method == 'GET' && $data){
				$connector = strpos($targetURL, '?') === false ? '?' : '&';
				$targetURL = $targetURL.$connector.$data;
			}

			if(is_string($data)){
				$dataLength	= strlen($data);
			}

			if ($urldecode) {
				$data = urldecode($data);
			}

		//	=================================================================================

		//	SET HEADER OPTIONS ==============================================================

			$header = is_array($header) ? $header : array($header);
			$header = Common::parseHeaderParam($header);

		//	=================================================================================

			if($debug){
				echo('<link rel="stylesheet" type="text/css" href="/css/request-debugger.css">');
				echo('<div class="debug-content">');
				echo('<div class="content-header">Target URL ['.$method.']</div>');
				debug($targetURL);
				echo('</div>');
				echo('<div class="debug-content">');
				echo('<div class="content-header">Header</div>');
				debug($header);
				echo('</div>');
				echo('<div class="debug-content">');
				echo('<div class="content-header">Data</div>');
				debug($data);
				echo('</div>');
			}

			if($curl){
				$curl = curl_init();

				if($method == 'GET'){
					curl_setopt($curl, CURLOPT_HTTPGET, true);
				}
				else{
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

					if($dataType == 'json'){
						curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
					}
				}

				if($header){
				//	array value
					curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				}

				curl_setopt($curl, CURLOPT_URL, $targetURL);
				curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($curl, CURLOPT_SSLVERSION, $sslVersion);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			//	execute request
				$response = curl_exec($curl);
				curl_close($curl);
			}
			else{
				$context = array(
					'http' => array(
						'method'	=> $method,
						'content'	=> $data,
						'timeout'	=> $timeout, 
					), 
				);

				if($header){
				//	array value
					$context = Hash::insert($context, 'http.header', implode('\r\n', $header));
				}

			//	execute request
				$context	= stream_context_create($context);
				$response	= @file_get_contents($targetURL, false, $context); 
			}

			if($debug){
				echo('<div class="debug-content">');
				echo('<div class="content-header">Response</div>');
				debug(json_decode(utf8_encode(trim($response)), true));
				echo('</div>');
				exit;
			}

			if($parse && $response){
				$response	= json_decode(utf8_encode(trim($response)), true);
				$error		= null;

				if($response !== null){
				//	switch and check possible JSON errors
					switch (json_last_error()){
						case JSON_ERROR_NONE:
							$error = ''; // JSON is valid // No error has occurred
							break;
						case JSON_ERROR_DEPTH:
							$error = 'The maximum stack depth has been exceeded.';
						break;
						case JSON_ERROR_STATE_MISMATCH:
							$error = 'Invalid or malformed JSON.';
						break;
						case JSON_ERROR_CTRL_CHAR:
							$error = 'Control character error, possibly incorrectly encoded.';
						break;
						case JSON_ERROR_SYNTAX:
							$error = 'Syntax error, malformed JSON.';
						break;

					//	PHP >= 5.3.3
						case JSON_ERROR_UTF8:
							$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
						break;

					//	PHP >= 5.5.0
						case JSON_ERROR_RECURSION:
							$error = 'One or more recursive references in the value to be encoded.';
						break;

					//	PHP >= 5.5.0
						case JSON_ERROR_INF_OR_NAN:
							$error = 'One or more NAN or INF values in the value to be encoded.';
						break;
						case JSON_ERROR_UNSUPPORTED_TYPE:
							$error = 'A value of a type that cannot be encoded was given.';
						break;
						default:
							$error = 'Unknown JSON error occured.';
						break;
					}
				}

			//	append to result
			    $status		= $error ? 'error' : 'success';
			    $message	= $error ? $error : 'Valid Request';
			}
			else{
				$status		= 'success';
				$message	= 'Valid Request';
			}

			$result = array(
				'status'	=> $status, 
				'msg'		=> __($message), 
				'response'	=> $response, 
			);
		}
		else{
			$result = array(
				'status'	=> 'error', 
				'msg'		=> __('Invalid Request'),
				'response'	=> null, 
			);
		}

		return $result;
	}

	public static function parseHeaderParam($params = array()){
		$params = is_array($params) ? $params : array($params);
		$result = array();

	//	list of accepted header params
	//	https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
		$validParams = array(
			'accept'							=> 'Accept', 
			'accept_charset'					=> 'Accept-Charset', 
			'accept_encoding'					=> 'Accept-Encoding', 
			'accept_language'					=> 'Accept-Language', 
			'accept_datetime'					=> 'Accept-Datetime', 
			'access_control_request_method'		=> 'Access-Control-Request-Method', 
			'access_control_request_headers'	=> 'Access-Control-Request-Headers', 
			'authorization'						=> 'Authorization', 
			'cache_control'						=> 'Cache-Control', 
			'connection'						=> 'Connection', 
			'cookie'							=> 'Cookie', 
			'content_length'					=> 'Content-Length', 
			'content_md5'						=> 'Content-MD5', 
			'content_type'						=> 'Content-Type', 
			'date'								=> 'Date', 
			'expect'							=> 'Expect', 
			'forwarded'							=> 'Forwarded', 
			'from'								=> 'From', 
			'host'								=> 'Host', 
			'if_match'							=> 'If-Match', 
			'if_modified_since'					=> 'If-Modified-Since', 
			'if_none_match'						=> 'If-None-Match', 
			'if_range'							=> 'If-Range', 
			'if_unmodified_since'				=> 'If-Unmodified-Since', 
			'max_forwards'						=> 'Max-Forwards', 
			'origin'							=> 'Origin', 
			'pragma'							=> 'Pragma', 
			'proxy_authorization'				=> 'Proxy-Authorization', 
			'range'								=> 'Range', 
			'referer'							=> 'Referer', 
			'te'								=> 'TE', 
			'user_agent'						=> 'User-Agent', 
			'upgrade'							=> 'Upgrade', 
			'via'								=> 'Via', 
			'warning'							=> 'Warning',
		);

	//	filter vald params
		$params = array_intersect_key($params, $validParams);

		if($params){
			foreach($params as $paramKey => $paramValue){
				$fieldName = Common::hashEmptyField($validParams, $paramKey);

				if($fieldName){
					$result[] = sprintf('%s: %s', $fieldName, $paramValue);
				}
			}
		}

		return $result;
	}

	function formingRowChart($data, $array_rule){
		if(!empty($data)){
			$temp = array();
			foreach ($data as $key_data => $val_data) {
				$second_temp = array();
				foreach ($array_rule as $key => $val) {
					if(is_array($val)){
						$data_type = Common::hashEmptyField($val, 'data_type');
						$val = $key;

						if(in_array($data_type, array('int', 'float', 'string'))){
							$val = Common::hashEmptyField($val_data, $val);

							if($data_type == 'int'){
								$val = (int) $val;
							}else if($data_type == 'float'){
								$val = (float) $val;
							}else if($data_type == 'string'){
								$val = (string) $val;
							}
						}else{
							$val = Common::hashEmptyField($val_data, $val);
						}
					}else{
						$val = Common::hashEmptyField($val_data, $val);
					}
					$second_temp[] = $val;
				}

				$temp[$key_data] = $second_temp;
			}

			$data = $temp;
		}

		return $data;
	}

	public static function splitYear($date_from, $date_to){
		$from 	= (int) date('Y', strtotime($date_from));
		$to 	= (int) date('Y', strtotime($date_to));

		$year = array();
		for ($i=$from; $i <= $to; $i++) { 
			$year[] = $i;
		}

		$min = min($year);
		$max = max($year);

		if( $min != $max ) {
			$temp_year = array();
			foreach ($year as $key => $value) {
				if($value == $min){
					$temp_year[$value] = array(
						'date_from' => $date_from,
						'date_to' => date('Y-m-t', strtotime(__('%s-12', $value)))
					);
				}else if($value == $max){
					$temp_year[$value] = array(
						'date_from' => __('%s-01-01', $value),
						'date_to' => $date_to
					);
				}else{
					$temp_year[$value] = array(
						'date_from' => __('%s-01-01', $value),
						'date_to' => date('Y-m-t', strtotime(__('%s-12', $value)))
					);
				}
			}
		} else {
			$temp_year[$min] = array(
				'date_from' => $date_from,
				'date_to' => $date_to,
			);
		}

		return $temp_year;
	}

	public static function monthDiff($date_from, $date_to){
		$datetime_from = date_create($date_from);
		$datetime_to = date_create($date_to);

		$date_diff = date_diff($datetime_from, $datetime_to);
		$month_diff = $date_diff->m;
		$day_diff = $date_diff->d;

		if(!empty($day_diff)){
			$month_diff++;
		}

		return $month_diff;
	}

	public static function _callCompanyParamParentId( $params ){
		$parent_id = Common::hashEmptyField($params, 'named.parent_id');
		$user_id = Common::hashEmptyField($params, 'named.user_id');

		if( !empty($parent_id) ) {
			return array(
				'parent_id' => $parent_id,
			);
		} else if( !empty($user_id) ) {
			return array(
				'user_id' => $user_id,
			);
		} else {
			return array();
		}
	}

	public static function reportFormatDate($periode_id  = false, $options = array()){
		$modelName = Common::hashEmptyField($options, 'modelName', 'Kpr');
		$fieldName = Common::hashEmptyField($options, 'fieldName', 'created');

		switch ($periode_id) {
			case '1':
				$groupByFormat = sprintf('DATE_FORMAT(%s.%s, \'%%Y-%%m-%%d\')', $modelName, $fieldName);
				$resultFormat = 'd/m/Y';
				break;
			
			default:
				$groupByFormat = sprintf('DATE_FORMAT(%s.%s, \'%%Y-%%m\')', $modelName, $fieldName);
				$resultFormat = 'M Y';
				break;
		}

		return array(
			'groupByFormat' => $groupByFormat,
			'resultFormat' => $resultFormat,
		);
	}

	public static function parseValidationError($modelName, $validationErrors = array()){
		$validationErrors	= (array) $validationErrors;
		$inputErrors		= array();

		if($modelName && $validationErrors){
		//	2 dimensi : model.field.value
		//	1 dimensi : field.value <- harus di tambah informasi modelnya (pasti model yang aktif)

			foreach($validationErrors as $key => $error){
				$dimensions = Hash::dimensions($error);

				if($dimensions < 2){
					$fieldPath			= sprintf('%s.%s', $modelName, $key);
					$validationErrors	= Hash::insert($validationErrors, $fieldPath, $error);
					$validationErrors	= Hash::remove($validationErrors, $key);
				}
			}

			foreach($validationErrors as $modelName => $errors){
				foreach($errors as $fieldName => $fieldValue){
					$inputErrors[] = array(
						'id'		=> Inflector::camelize(sprintf('%s_%s', $modelName, $fieldName)), 
						'name'		=> sprintf('data[%s][%s]', $modelName, $fieldName), 
						'message'	=> array_shift($fieldValue), 
					);
				}
			}
		}

		return $inputErrors;
	}

	public static function _getUserFullName( $user, $action = false, $model = 'User', $field_fullname = 'full_name' ) {
		if( $action == 'reverse' ) {
			$fullname = !empty($user[$model][$field_fullname])?trim($user[$model][$field_fullname]):false;
			$split = explode(' ',$fullname);
			$first_name = false;
			$last_name = false;

			if( !empty($split[0]) ) {
				$first_name = $split[0];

				if( isset($split[1]) ) {
					unset($split[0]);
					$last_name = implode(' ', $split);
					$last_name = trim($last_name);
				}
			}

			$user[$model]['first_name'] = $first_name;
			$user[$model]['last_name'] = $last_name;
		} else {
			if( !empty($user[$model][$field_fullname]) ) {
				$name = $user[$model][$field_fullname];
			} else {
				if(!empty($user[$model]['first_name'])) {
					$name = $user[$model]['first_name'];
				}
				if(!empty($user[$model]['last_name'])) {
					$name = sprintf('%s %s', $name, $user['User']['last_name']);
				}
			}

			$user[$model][$field_fullname] = trim($name);
		}

		return $user;
	}

	public static function addressFormat( $data = array() ){
		$result = '';

		$projectRegion	= Common::hashEmptyField($data, 'region');
		$projectCity	= Common::hashEmptyField($data, 'city');
		$projectSubarea	= Common::hashEmptyField($data, 'subarea');
		$projectZipCode	= Common::hashEmptyField($data, 'zip');
		$projectAddress	= Common::hashEmptyField($data, 'address');
		$projectAddress2	= Common::hashEmptyField($data, 'address2');

		$result = implode(', ', array_filter(array(
			$projectSubarea, 
			$projectCity, 
			$projectRegion, 
		)));

		$result = sprintf('%s %s', $result, $projectZipCode);
		$result = implode('. ', array_filter(array(
			$projectAddress, 
			$projectAddress2,
			$result, 
		)));

		return $result;
	}

	public static function getPropertyAddress($data = array(), $options = array()){
		$data		= (array) $data;
		$options	= (array) $options;
		$address	= '';

		if($data){
			$showArea = Common::hashEmptyField($options, 'show_area', true, array('isset' => true));

			$address		= Common::hashEmptyField($data, 'PropertyAddress.address');
			$addressNo		= Common::hashEmptyField($data, 'PropertyAddress.no');
			$addressRT		= Common::hashEmptyField($data, 'PropertyAddress.rt');
			$addressRW		= Common::hashEmptyField($data, 'PropertyAddress.rw');
			$addressZIP		= Common::hashEmptyField($data, 'PropertyAddress.zip');

			$address = implode(' ', array_filter(array(
				$address, 
				$addressNo ? sprintf('No. %s', $addressNo) : false, 
				$addressRT ? sprintf('RT. %s', $addressRT) : false, 
				$addressRW ? sprintf('RW. %s', $addressRW) : false, 
			)));

			if($showArea){
				$regionName		= Common::hashEmptyField($data, 'PropertyAddress.Region.name');
				$cityName		= Common::hashEmptyField($data, 'PropertyAddress.City.name');
				$subareaName	= Common::hashEmptyField($data, 'PropertyAddress.Subarea.name');
				$zipCode		= Common::hashEmptyField($data, 'PropertyAddress.Subarea.zip');

				$propertyArea	= array_filter(array($subareaName, $cityName, $regionName));
				$propertyArea	= implode(', ', $propertyArea);

				$propertyArea	= array_filter(array($propertyArea, $zipCode));
				$propertyArea	= implode('. ', $propertyArea);

				$address = implode('. ', array_filter(array($address, $propertyArea)));
			}
		}

		return $address;
	}

	public static function getBlokNameProduct($product_type_id){
		if(!empty($product_type_id) && in_array($product_type_id, array(3,5,15,18))){
			$text = 'Lantai';
		}else{
			$text = 'Blok';
		}

		return $text;
	}

	public static function _callConvertDateRange ( $params, $date, $options = array() ) {
		$startField = Common::hashEmptyField($options, 'date_from', 'date_from');
		$endField = Common::hashEmptyField($options, 'date_to', 'date_to');

		$date = urldecode($date);
		$dateArr = explode(' - ', $date);

		if( !empty($dateArr) && count($dateArr) == 2 ) {
			$fromDate = !empty($dateArr[0])?Common::getDate($dateArr[0]):false;
			$toDate = !empty($dateArr[1])?Common::getDate($dateArr[1]):false;

			$params[$startField] = $fromDate;
			$params[$endField] = $toDate;
		}

		return $params;
	}
	
	public static function _search($controller, $action, $addParam = false){
		$data = $controller->request->data;
		$named = Common::hashEmptyField($controller->params, 'named');

		$params = array(
			'action' => $action,
			'autoload' => true,
			$addParam,
		);

		if( !empty($named) ) {
			$params = array_merge($params, $named);
		}
		return Common::processSorting($controller, $params, $data);
	}

	public static function processSorting ( $controller, $params, $data, $with_param_id = true, $param_id_only = false, $redirect = true ) {
		$filter = Common::hashEmptyField($data, 'Search.filter');
		$sort = Common::hashEmptyField($data, 'Search.sort');
		$excel = Common::hashEmptyField($data, 'Search.excel');
		$min_price = Common::hashEmptyField($data, 'Search.min_price', 0);
		$max_price = Common::hashEmptyField($data, 'Search.max_price', 0);
		$user = Common::hashEmptyField($data, 'Search.user');
		$month_range = Common::hashEmptyField($data, 'Search.month_range');

		$named = Common::hashEmptyField($controller->params, 'named');

		if( !empty($with_param_id) ) {
			$param_id = Common::hashEmptyField($named, 'param_id');

			if( is_array($param_id) ) {
				$params = array_merge($params, $param_id);
			} else {
				$params[] = $param_id;
			}
		}

		if( !empty($param_id_only) ) {
			return $params;
		}

		if(!empty($data['Search']['change_url'])){
			unset($data['Search']['change_url']);
		}

		$dateFilter = Configure::read('Global.Data.dateFilter');
		$data = Common::dataConverter($data, array(
			'unset' => array(
				'Search' => array(
				'sort',
				'direction',
				'excel',
				'action',
				'min_price',
				'max_price',
				'colview',
				'month_range',
				'pushstate_url',
			)),	
		));

		if( !empty($dateFilter) ) {
			foreach ($dateFilter as $key => $fieldFilter) {
				$date = Common::hashEmptyField($data, 'Search.'.$fieldFilter);
				$fieldFrom = __('%s_from', $fieldFilter);
				$fieldTo = __('%s_to', $fieldFilter);

				$data = Common::_callUnset($data, array(
					'Search' => array(
						$fieldFilter,
					),
				));

				if( empty($date) ) {
					$date_from = Common::hashEmptyField($data, 'Search.'.$fieldFrom);
					$date_to = Common::hashEmptyField($data, 'Search.'.$fieldTo);

					if( !empty($date_from) && !empty($date_to) ) {
						$date = sprintf('%s - %s', $date_from, $date_to);
					}
				}

				if( !empty($date) ) {
					$params = Common::_callConvertDateRange($params, $date, array(
						'date_from' => $fieldFrom,
						'date_to' => $fieldTo,
					));
				}
			}
		}

		$dataSearch = Common::hashEmptyField($data, 'Search');
		// if( isset($dataSearch['keyword']) ) {
		// 	$dataSearch['keyword'] = urlencode(trim($dataSearch['keyword']));
		// }
		
		if( !empty($dataSearch) ) {
			foreach ($dataSearch as $fieldName => $value) {
				if( is_array($value) ) {
					$value = array_filter($value);

					if( !empty($value) ) {
						$result = array();

						foreach ($value as $id => $boolean) {
							if( !empty($id) ) {
								$result[] = $id;
							}
						}

						$value = implode(',', $result);
					}
				}

				if( !empty($value) ) {
					if( !is_array($value) ) {
						$params[$fieldName] = urlencode(trim($value));
					} else {
						$params[$fieldName] = $value;
					}
				}
			}
		}

		if( !empty($filter) ) {
			$filterArr = strpos($filter, '.');

			if( !empty($filterArr) ) {
				$sort = $filter;
			}

			$params = Common::_callUnset($params, array(
				'filter',
			));
		}

		if( !empty($sort) ) {
			$dataArr = explode('-', $sort);

			if( !empty($dataArr) && count($dataArr) == 2 ) {
				$sort = !empty($dataArr[0])?$dataArr[0]:false;
				$direction = !empty($dataArr[1])?$dataArr[1]:false;

				$sortLower = strtolower($sort);
				$directionLower = strtolower($direction);

				if( !in_array($direction, array( 'asc', 'desc' )) ) {
					$params[$sort] = $direction;
				} else {
					$params['sort'] = $sort;
					$params['direction'] = $direction;
				}
			}
		}

		if( !empty($excel) ) {
			$params['export'] = 'excel';
		}
		if( !empty($min_price) || !empty($max_price) ) {
			$min_price = $this->_callPriceConverter($min_price);
			$max_price = $this->_callPriceConverter($max_price);

			if( empty($max_price) ) {
				$price = $min_price;
			} else {
				$price = sprintf('%s-%s', $min_price, $max_price);
			}

			$params['price'] = $price;
		}

		if( !empty($month_range) ) {
			$date = $month_range;
			$params = Common::_callConvertMonthRange($params, $date);
		}

		if(!empty($user)){
			$params['user'] = $user;
		}

		return $params;
	}

	public static function getNameNominalOrPercent($category, $value, $format_price = true) {
	    switch ($category) {
	    	case 'nominal':
	    		if( !empty($format_price) ) {
	    			$result = Common::getFormatPrice($value);
	    		} else {
	    			$result = $value;
	    		}
	    		break;
	    	
	    	default:
	    		$result = __('%s%%', $value);
	    		break;
	    }

	    return $result;
	}

    public static function _callPercentageValue($price = false, $value = false, $category = false){
        if(!empty($value) && !empty($category)){
            if($category == 'percent'){
                $value = floatval(( $value / 100 ) * $price);
            }
        }

        return $value;
    }

    public static function array_random($arr, $num = 1) {
	    shuffle($arr);
	    
	    $r = array();
	    for ($i = 0; $i < $num; $i++) {
	        $r[] = $arr[$i];
	    }
	    
	    return $num == 1 ? $r[0] : $r;
	}
	
	public static function createRandomNumber( $default= 4, $variable = 'bcdfghjklmnprstvwxyz', $modRndm = 20 ) {
        $chars = $variable;
        srand((double)microtime()*1000000);
        $pass = array() ;

        $i = 1;
        while ($i != $default) {
            $num = rand() % $modRndm;
            $tmp = substr($chars, $num, 1);
            $pass[] = $tmp;
            $i++;
        }
        $pass[] = rand(1,9);

        return $pass;
    }

	public static function _callGenerateDataModel ( $data, $modelName ) {
		$result = array();

		if( !empty($data) && is_array($data) ) {
			foreach ($data as $id => $name) {
				$result[] = array(
					$modelName => array(
						'id' => $id,
						'name' => $name,
					),
				);
			}
		}

		return $result;
	}
	
	public static function _callGenerateFullUrl ( $file, $save_path = null, $size = null ) {
    	$fullsize = !empty($size)?$size:Configure::read('__Site.fullsize');
		return FULL_BASE_URL.Configure::read('__Site.cache_view_path').'/'.$save_path.'/'.$fullsize.$file;
	}

	public static function _callGetExt ( $file = false ) {
		$fileArr = explode('.', $file);
		return end($fileArr);
	}

	public static function isPrimeDomain($currentURL = null, $primeApp = 'agent'){
		$searchStr	= array('http://', 'https://', '/');
		$currentURL	= $currentURL ?: Router::url('/', true);
		$primeURL	= Configure::read(sprintf('Global.Data.landing_page.%s', $primeApp));

		$currentURL = str_replace($searchStr, null, $currentURL);
		$primeURL	= str_replace($searchStr, null, $primeURL);

		return ($currentURL && $primeURL) && ($currentURL == $primeURL);
	}

	public static function getRole($groupID = null){
		$groupID	= $groupID ?: Configure::read('User.group_id');
		$roles		= Configure::read('__Site.Role');

		if($groupID && $roles){
			foreach($roles as $roleKey => $roleID){
				if(in_array($groupID, $roleID)){
					return $roleKey;
				}
			}
		}

		return false;
	}

	public static function validateRole($roleKey, $groupID = null){
		$groupID	= isset($groupID) ? $groupID : Configure::read('User.group_id');
	//	$roles		= (array) Configure::read('__Site.Role');
		$roles		= array(
			'agent'				=> array(1, 2), 
			'independent_agent'	=> array(1), 
			'company_agent'		=> array(2), 
			'company_admin'		=> array(3, 4, 5), 
			'principal'			=> array(3), 
			'principle'			=> array(3), 
			'director'			=> array(4), 
			'head_liner'		=> array(3,4), 
			'admin'				=> array(11, 19, 20), 
		);

		$groupList	= Common::hashEmptyField($roles, $roleKey, array());

		if($groupList){
			return in_array($groupID, $groupList);
		}
		else{
			$keyList = implode(', ', array_keys($roles));
			throw new Exception(sprintf('Invalid role key "%s", available roles : %s', $roleKey, $keyList), 1);
		}
	}

	public static function _callPropertyRestrict( $statusConditions = array(), $restrict_type = 'restrict' ) {
		$is_admin = Configure::read('User.admin');
		$user_login_id = Configure::read('User.id');
		$dataCompany = Configure::read('Config.Company.data');
		$is_restrict_approval_property = Common::hashEmptyField($dataCompany, 'UserCompanyConfig.is_restrict_approval_property', false, array(
			'isset' => true,
		));

		if(!empty($is_restrict_approval_property)){
			switch ($restrict_type) {
				case 'mine':
					if( empty($is_admin) ) {
						$statusConditions[]['OR'] = array(
							array(
								'Property.active' => 1,
							),
							array(
								'Property.user_id' => $user_login_id,
							),
						);
					}
				break;
				
				default:
					$statusConditions['Property.active'] = 1;	
					$statusConditions['Property.status'] = 1;
					break;
			}
		} else {
			$statusConditions['Property.status'] = 1;
		}

		return $statusConditions;
	}

//	gauth
	public static function getAccessToken($clientID, $clientSecret, $redirectURI, $code){
		$status		= 'error';
		$message	= 'Invalid request';
		$url		= 'https://accounts.google.com/o/oauth2/token';
		$postData	= array(
			'client_id'		=> $clientID, 
			'client_secret'	=> $clientSecret, 
			'redirect_uri'	=> $redirectURI, 
			'code'			=> $code, 
			'grant_type'	=> 'authorization_code', 
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));

		$data		= json_decode(curl_exec($curl), true);
		$httpCode	= curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if($httpCode == 200){
			$status		= 'success';
			$message	= 'Valid request';
		}
		else{
			$code		= Common::hashEmptyField($data, 'error.code');
			$message	= Common::hashEmptyField($data, 'error.message');

			if($code && $message){
				$message = __('%s [ code : %s ]', $message, $code);
			}
		}

		$result = array(
			'status'	=> $status, 
			'msg'		=> $message, 
			'data'		=> $data, 
		);

		return $result;
	}

	public static function getUserProfile($accessToken){
		$status		= 'error';
		$message	= 'Invalid request';
		$url		= 'https://www.googleapis.com/plus/v1/people/me';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $accessToken));

		$data		= json_decode(curl_exec($curl), true);
		$httpCode	= curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if($httpCode == 200){
			$status		= 'success';
			$message	= 'Valid request';
		}
		else{
			$code		= Common::hashEmptyField($data, 'error.code');
			$message	= Common::hashEmptyField($data, 'error.message');

			if($code && $message){
				$message = __('%s [ code : %s ]', $message, $code);
			}
		}

		$result = array(
			'status'	=> $status, 
			'msg'		=> $message, 
			'data'		=> $data, 
		);

		return $result;
	}

	public static function isAgent ( $group_id = null ) {
		$group_id = !empty($group_id)?$group_id:Configure::read('User.group_id');
		$agentCompany = Configure::read('__Site.Role.company_agent');

		if( in_array($group_id, $agentCompany) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function _isCompanyAdmin ( $group_id = false ) {
		$admin_id = Configure::read('__Site.Admin.Company.id');

		if( empty($group_id) ) {
			$group_id = Configure::read('User.group_id');
		}

		if( in_array($group_id, $admin_id) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function translateDate($string = ''){
		if($string){
			$enDateComponents = array(
			//	long months
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 
			//	short months
				'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 
			//	long days
				'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thurs', 
			//	short days
				'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 
			);

			$idDateComponents = array(
			//	long months
				'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 
			//	short months
				'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 
			//	long days
				'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 
			//	short days
				'Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 
			);

			$string = str_replace($enDateComponents, $idDateComponents, $string);
		}

		return $string;
	}

	public static function truncate( $str, $len, $ending = ' ...', $stripTag = true ) {
		$str = trim($str);

		if( !empty($stripTag) ) {
			$str = Common::safeTagPrint($str);
		}

		if( !empty($str) ) {
			if($len > 0){
				return CakeText::truncate($str, $len, array(
					'ending' => $ending,
					'exact' => false
				));
			}else{
				return '';
			}
		} else {
			return '';
		}
	}

	public static function formatSql($strQuery = null){
		if($strQuery){
			App::import('Vendor','formatter');

			$formatter	= new Formatter();
			$strQuery	= $formatter->format($strQuery);
		}

		return $strQuery;
	}

	public static function getQRCode($source = null, $options = array()){
		$filePath = null;

		if($source){
			$options	= (array) $options;
			$fileName	= Common::hashEmptyField($options, 'filename', sprintf('%s.jpg', String::uuid()));
			$fullBase	= Common::hashEmptyField($options, 'fullbase', true, array('isset' => true));
			$savePath	= Common::hashEmptyField($options, 'save_path');
			$replace	= Common::hashEmptyField($options, 'replace');

			$fileInfo	= pathinfo($fileName);
			$baseName	= Common::hashEmptyField($fileInfo, 'basename', '');
			$extension	= Common::hashEmptyField($fileInfo, 'extension', '');

			if(in_array($extension, array('png', 'jpg', 'jpeg'))){
				$fullbaseURL	= Router::fullBaseUrl();
				$imageDir		= Configure::read('App.imageBaseUrl');

				if(empty($savePath)){
					$savePath = $imageDir.DS.'view'.DS.'qr';
					$savePath = str_replace(array('/\\', '//', '/'), DS, $savePath);
				}

				if(file_exists($savePath) === false){
					mkdir($savePath, 0777, true);
				}

				$filePath = $savePath.DS.$fileName;
				$filePath = str_replace(array('/\\', '//', '/'), DS, $filePath);

				if(file_exists($filePath) === false || $replace){
					$qrImage = @file_get_contents($source, false, stream_context_create(array(
						'ssl' => array(
							'verify_peer'		=> false,
							'verify_peer_name'	=> false,
						),
					)));

					if($qrImage && file_put_contents($filePath, $qrImage) === false){
						$filePath = null;
					}
				}

				if($filePath && $fullBase){
					$filePath = $fullbaseURL.DS.$filePath;
				}
			}
		}

		return $filePath;
	}

//	list all files and directories of given path, 
//	return empty array if target directory not found

	public static function listDir($path = '', $options = array(), $depth = 0){
		$path		= (string) $path;
		$options	= (array) $options;
		$results	= array();

		if($path){
			App::uses('Folder', 'Utility');
			App::uses('File', 'Utility');

			$path		= str_replace(array('/\\', '//', '/'), DS, $path);
			$sort		= Hash::get($options, 'sort', true);
			$exeption	= Hash::get($options, 'exeption', true);
			$fullpath	= Hash::get($options, 'fullpath', false);
			$extension	= Hash::get($options, 'extension', '*');
			$autoCreate	= Hash::get($options, 'auto_create', false);

			$fullBaseUrl	= Router::fullBaseUrl();
			$folder			= new Folder($path);

		//	read will return 2 array (0 as list of directories, and 1 as list of files)
			$contents		= $folder->read($sort, $exeption, $fullpath);
			$directories	= Hash::get($contents, 0, array());
			$files			= Hash::get($contents, 1, array());

			if($directories){
				foreach($directories as $directory){
					$subPath = $fullpath ? $directory : $path.DS.$directory;
					$dirName = basename($subPath);

					$results[] = array(
						'directory'	=> $dirName, 
						'path'		=> $subPath, 
						'file'		=> Common::listDir($subPath, $options, $depth + 1), 
					);
				}
			}

			if($files && $extension && $extension != '*'){
				$files = $folder->find(sprintf('.*\.%s', $extension), $sort);
			}

			if($files){
				if($depth){
					$results = array_merge($results, $files);
				}
				else{
					$results[] = array(
						'path' => $fullpath ? $folder->pwd() : $path, 
						'file' => $files, 
					);
				}
			}
		}

		return $results;
	}

	public static function config($path = '', $default = null){
		$config = Configure::read($path);

		if(is_null($config)){
			$config = $default;
		}

		return $config;
	}

	public static function getDateInterval($date1 = null, $date2 = null, $options = array()){
		$options	= (array) $options;
		$render		= Common::hashEmptyField($options, 'render', true, array('type' => 'isset'));
		$time		= Common::hashEmptyField($options, 'time', true, array('type' => 'isset'));
		$components	= Common::hashEmptyField($options, 'components');

		if($date1 && $date2){
			$date1	= new Datetime($date1);
			$date2	= new Datetime($date2);
			$result	= (array) $date1->diff($date2);

			if($render){
				$fields = array('y' => 'Tahun', 'm' => 'Bulan', 'd' => 'Hari', 'h' => 'Jam', 'i' => 'Menit', 's' => 'Detik');

				if($components){
					$components = (array) $components;
				}
				else{
					$components	= array('y', 'm', 'd');

					if($time){
						$components = array_merge($components, array('h', 'i', 's'));
					}
				}

				$isIncludeTime = array_intersect($components, array('h', 'i', 's'));
				$isIncludeTime = empty($isIncludeTime) === false;

				foreach($components as $key => $field){
					$label = Common::hashEmptyField($fields, $field, false);
					$value = Common::hashEmptyField($result, $field);
					$value = $value ? $value.' '.$label : $value;

					$components = Hash::insert($components, $key, $value);
				}

				$components	= array_filter($components);

				if($components){
					$result = implode(', ', $components);
				}
				else if(empty($components) && empty($isIncludeTime)){
					$hour	= Common::hashEmptyField($result, 'h');
					$minute	= Common::hashEmptyField($result, 'i');
					$second	= Common::hashEmptyField($result, 's');

					if($hour || $minute || $second){
						$result = 'Kurang dari 1 hari';
					}
					else{
						$result = '';
					}
				}
			}

			return $result;
		}
	}

	public static function _callNoteActivity($value){
		$dataOptions = Common::hashEmptyField($value, 'CrmProjectActivityAttributeOption');
		$tmpActivity = array();

		if( !empty($dataOptions) ) {
			$activity_date = Common::hashEmptyField($value, 'CrmProjectActivity.activity_date', NULL, array(
				'date' => 'd M Y',
			));
			$activity_time = Common::hashEmptyField($value, 'CrmProjectActivity.activity_time', NULL, array(
				'date' => 'H:i',
			));

			foreach ($dataOptions as $key => $option) {
				$attribute_option_id = Common::hashEmptyField($option, 'CrmProjectActivityAttributeOption.attribute_option_id');
				$optionName = Common::hashEmptyField($option, 'AttributeOption.name');
				$parent_id = Common::hashEmptyField($option, 'AttributeOption.parent_id');
				$child_type = Common::hashEmptyField($option, 'AttributeOption.type');

				$valueName = Common::hashEmptyField($option, 'CrmProjectActivityAttributeOption.attribute_option_value', $optionName);
				$valueName = Common::hashEmptyField($option, 'AttributeOptionChild.name', $valueName);

				$lblName = Common::hashEmptyField($option, 'Attribute.name');
				$valueType = Common::hashEmptyField($option, 'Attribute.type');

				if( !empty($valueType) ) {
					switch ($valueType) {
						case 'price':
							$tmpActivity[] = __('%s: %s', $lblName, Common::getFormatPrice($valueName));
							break;
						
						default:
							if( !empty($parent_id) ) {
								$valueName = Common::hashEmptyField($option, 'CrmProjectActivityAttributeOption.attribute_option_value', '-');
								$valueName = Common::hashEmptyField($option, 'AttributeOptionChild.name', $valueName);

								if( !in_array($child_type, array( 'payment' )) ) {
									switch ($child_type) {
										case 'price':
											$valueName = Common::getFormatPrice($valueName);
											break;
									}

									$tmpActivity[] = __('%s: %s', $optionName, $valueName);
								}
							} else {
								$tmpActivity[] = $valueName;
								$tmpActivity[] = __('%s %s', $activity_date, $activity_time);
							}
							break;
					}
				}
			}
		}

		return $tmpActivity;
	}

//	buat bantu debugging doang
	public static function getMicrotime(){
		$time	= microtime(true);
		$micro	= sprintf('%06d', ($time - floor($time)) * 1000000);
		$date	= new DateTime(date('Y-m-d H:i:s.'.$micro, $time));

		return $date->format('Y-m-d H:i:s.u');
	}

	public static function _callPhoneWA ( $options = array() ) {
		$isMobile = Configure::read('Global.Data.MobileDetect.mobile');
		$isTablet = Configure::read('Global.Data.MobileDetect.tablet');
		$params = array();

	 	$no_hp = Common::hashEmptyField($options, 'no_hp');
	 	$text = Common::hashEmptyField($options, 'text');

		if( !empty($isMobile) || !empty($isTablet) ) {
			$urlWA = 'https://api.whatsapp.com/send';
		} else {
			$urlWA = 'https://web.whatsapp.com/send';
		}

		if( !empty($no_hp) ) {
			if( substr($no_hp, 0,1) != "+" && substr($no_hp, 0,1) == 0 ) {
				$no_hp = substr_replace($no_hp, '+62', 0, 1);
			}

			$params[] = __('phone=%s', $no_hp);
		}
		if( !empty($text) ) {
			$params[] = __('text=%s', $text);
		}

		if( !empty($params) ) {
			return __('%s?%s', $urlWA, implode('&', $params));
		} else {
			return false;
		}
	}
	
	public static function _getHeadLinerID ( $group_id, $user ) {
		$head_liner = Common::validateRole('head_liner', $group_id);

		if( !empty($head_liner) ) {
			$principle_id = Common::hashEmptyField($user, 'User.id');
		} else {
			$principle_id = Common::hashEmptyField($user, 'User.parent_id');
		}

		return $principle_id;
	}

	public static function _callAllowAccess ( $field ) {
		$isAdmin		= Configure::read('User.admin');
		$authGroupID	= Configure::read('User.group_id');

		if( $isAdmin || $authGroupID == 1 ) {
			return true;
		} else {
			$_config	= Configure::read('Config.Company.data');
			$is_allow	= Common::hashEmptyField($_config, sprintf('UserCompanyConfig.%s', $field));

			return $is_allow;
		}
	}

	public static function getRecordParentID($user = false){
		if($user){
			$id = Common::hashEmptyField($user, 'User.id');
			$parent_id = Common::hashEmptyField($user, 'User.parent_id');
			$group_id = Common::hashEmptyField($user, 'User.group_id');

			if(in_array($group_id, array(3, 4))){
				return $id;
			} else {
				return $parent_id;
			}
		}
	}
}