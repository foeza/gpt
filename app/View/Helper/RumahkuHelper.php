<?php
class RumahkuHelper extends AppHelper {
	var $helpers = array(
		'Time', 'Html', 'Form',
		'Paginator', 'Text', 'Number',
		'AclLink.AclLink',
		'Minify.Minify', 'Kpr'
	);

	/**
	*
	*	filterisasi content tag
	*
	*	@param string string : string
	*	@return string
	*/
	function safeTagPrint($string){
		if( is_string($string) ) {
			return trim(strip_tags($string));
		} else {
			return $string;
		}
	}

	/**
	*
	*	function format tanggal
	*	@param string $dateString : tanggal
	*	@param string $format : format tanggal
	*	@return string tanggal
	*/
	function formatDate($dateString, $format = false, $empty = '-') {
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

	function _callStatusChecked ( $status, $text = false ) {
        if( !empty($status) ) {
            if( !empty($text) ) {
                $labelIcon = $this->Html->tag('span', __('Ya'), array(
                    'class' => 'color-green',
                ));
            } else {
                $labelIcon = $this->icon('rv4-check no-margin', false, 'i', 'color-green');
            }
        } else {
            if( !empty($text) ) {
                $labelIcon = $this->Html->tag('span', __('Tidak'), array(
                    'class' => 'color-red',
                ));
            } else {
                $labelIcon = $this->icon('rv4-cross no-margin', false, 'i', 'color-red');
            }
        }

        return $this->Html->tag('span', $labelIcon, array(
            'class' => 'status-label-checked',
        ));
    }

	function initializeMeta( $params ) {
		$title = !empty($params['meta']['title_for_layout'])?$params['meta']['title_for_layout']:false;
		$description = !empty($params['meta']['description_for_layout'])?$params['meta']['description_for_layout']:false;

		echo $this->Html->tag('title', $title).PHP_EOL;
		echo $this->Html->meta('description', $description).PHP_EOL;
	}

	function icon($icon, $content = false, $tag = 'i', $addClass = false) {
		if( !empty($icon) ) {
			return $this->Html->tag($tag, $content, array(
				'class' => sprintf('%s %s', $icon, $addClass),
			));
		} else {
			return false;
		}
	}

	function buildInputRadio( $name, $options = array(), $attributes = array(), $default = 0 ) {
		$default_attributes = array(
			'fieldName' => $name,
			'options' => $options,
			'frameClass' => 'col-sm-8',
			'labelClass' => 'col-xl-2 col-sm-4 taright',
			'class' => 'relative col-sm-8 col-xl-4',
			'styling' => 'line',
		);

		if( !empty($attributes) ) {
			$default_attributes = array_merge($default_attributes, $attributes);
		}

		return $this->_View->element('blocks/common/forms/input_radio', $default_attributes);
	}

	function validDateBirth(){
		$day = $year = array();
		$monthname = Configure::read('__Site.monthly.named');
		$yearcounter = date("Y") - 5;
		for($i = $yearcounter; $i > 1900; $i--){
			$year[$i] = $i;
		}
		for($i = 1; $i <= 12; $i++){
			$month[$i] = $monthname[$i-1];
		}
		for($i = 1; $i <= 31; $i++){
			$day[$i] = $i;
		}

		return array(
			'day' => $day,
			'month' => $month,
			'year' => $year
		);
	}

	function filterEmptyField ( $value, $modelName, $fieldName = false, $empty = false, $removeHtml = true, $format = false ) {
		$result = '';
		
		if( empty($modelName) ) {
			$result = $empty;
		} else if( empty($fieldName) ) {
			$result = !empty($value[$modelName])?$value[$modelName]:$empty;
		} else {
			$result = !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
		}

        if( !empty($removeHtml) && !is_array($result) ) {
            $result = $this->safeTagPrint($result);
        }

        if( !empty($result) ) {
            if( is_array($format) ) {
                if( !empty($format['date']) ) {
                    $format = $format['date'];
                    $result = $this->formatDate($result, $format);
                } else if( !empty($format['wa']) ) {
                    $format = $format['wa'];
                    
                    if( !empty($format) ) {
                    	$result = __('%s %s', $result, $this->Html->tag('small', __('(WA)'), array(
                    		'title' => __('WhatsApp'),
                		)));
                    }
                } else if( !empty($format['text']) ) {
                    $format = $format['text'];
                    
                    if( !empty($format['limit']) ) {
                    	$result = $this->truncate($result, $format['limit'], '...', false);
                    }
                    if( !empty($format['type']) ) {
                    	switch ($format['type']) {
                    		case 'EOL':
		                		$result = $this->_callGetDescription($result);
                    			break;
                    	}
                    }
                }
            } else {
		        switch ($format) {
		            case 'EOL':
		                $result = $this->_callGetDescription($result);
		                break;
		            case 'remove_space':
		                $result = str_replace(' ', '', $result);
		                break;
		            case 'year':
		            	if( $result == '0000' ) {
		                	$result = $empty;
		                }
		                break;
		            case 'json_encode':
		            	$result = json_encode($result);
		                break;
		            case 'trailing_slash':
						$last_char = substr($result, -1);
						if( $last_char === '/' ) {
							$result = rtrim($result, $last_char);
						}
						break;
					case 'currency':
						$result = $this->getFormatPrice($result);
						break;
					case 'ucwords':
						$result = ucwords($result);
						break;
					case 'mailto':
						$result = $this->Html->link($result, sprintf('mailto:%s', $result));
						break;
					case 'phone':
						$result = $this->Html->link($result, sprintf('tel:%s', $result));
						break;
					case 'link':
						$result = $this->Html->link($result, $result);
						break;
					case 'link-target-blank':
						$result = $this->Html->link($result, $result, array(
							'target' => '_blank',
						));
						break;
					case 'formatNumber':
						$result = $this->formatThenumber($result);
						break;
		        }
            }
        }

		return $result;
	}

	function filterIssetField ( $value, $modelName, $fieldName = false, $empty = false, $removeHtml = true ) {
		$result = '';

		if( empty($modelName) && !is_numeric($modelName) ) {
			$result = isset($value)?$value:$empty;
		} else if( empty($fieldName) && !is_numeric($fieldName) ) {
			$result = isset($value[$modelName])?$value[$modelName]:$empty;
		} else {
			$result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
		}

		if( isset($removeHtml) && !is_array($result) ) {
			return $this->safeTagPrint($result);
		} else {
			return $result;
		}
	}

	function buildForm ( $fieldName, $fieldLabel, $options = array(), $position = 'vertical') {
		$result = '';
		$labelText = false;
		$fieldDiv = false;
		$id_form = $this->filterEmptyField($options, 'id');
		$size = $this->filterEmptyField($options, 'size');
		$type = $this->filterEmptyField($options, 'type');
		$error = $this->filterEmptyField($options, 'error', false, true);
		$_options = $this->filterEmptyField($options, 'options');
		$description = $this->filterEmptyField($options, 'description');
		$empty = $this->filterEmptyField($options, 'empty');
		$readonly = $this->filterEmptyField($options, 'readonly');
		$placeholder = $this->filterEmptyField($options, 'placeholder');
		$addClass = $this->filterEmptyField($options, 'class');
		$frameSize = $this->filterEmptyField($options, 'frame-size');
		$overflowText = $this->filterEmptyField($options, 'overflow-text', false, false, false);
		$value = $this->filterEmptyField($options, 'value');

		$inputOptions = Common::hashEmptyField($options, 'inputOptions', array());

		switch ($frameSize) {
			case 'large':
				$frameClass = $this->filterEmptyField($options, 'frame-class', false, 'col-sm-12');
				$frameLabelClass = $this->filterEmptyField($options, 'frame-label-class', false, 'col-sm-3');
				break;

			case 'medium':
				$frameClass = $this->filterEmptyField($options, 'frame-class', false, 'col-sm-6');
				$frameLabelClass = $this->filterEmptyField($options, 'frame-label-class', false, 'col-sm-3');
				break;
			
			default:
				$frameClass = $this->filterEmptyField($options, 'frame-class', false, 'col-sm-8');
				$frameLabelClass = $this->filterEmptyField($options, 'frame-label-class', false, 'col-sm-4');
				break;
		}

		$classSize = false;

		switch ($size) {
			case 'small':
				$classSize = 'col-sm-3 col-xl-4';
				break;

			case 'medium':
				$classSize = ' col-sm-5 col-xl-4';
				break;
		}

		switch ($position) {
			case 'horizontal':
				$result .= $this->Html->tag('div', $this->Form->label($fieldName, $fieldLabel, array(
					'class' => 'control-label',
				)), array(
					'class' => 'col-xl-2 taright '.$frameLabelClass,
				));

				$classSize = $classSize ?: 'col-sm-7 col-xl-4';

				$fieldDiv = array(
					'class' => 'relative '.$classSize,
				);
				break;
			
			default:
				$labelText = $fieldLabel;
				break;
		}

		$default_options = array(
			'id' => $id_form,
			'label' => $labelText,
			'required' => false,
			'div' => $fieldDiv,
			'empty' => $empty,
			'readonly' => $readonly,
			'placeholder' => $placeholder,
			'class' => 'form-control '.$addClass,
		);

		if( !empty($type) ) {
			if( $type == 'checkbox' ) {
				if(!empty($addClass) && in_array($addClass, array('checkAll', 'check-option'))){
					$default_options['class'] = $addClass;
				}else{
					$default_options['class'] = '';
				}

				$default_options['div'] = false;
			}

			$default_options['type'] = $type;
		}

		if( !is_array($options) ) {
			$default_options = array_merge_recursive($default_options, $options);
		}

		if( !empty($description) ) {
			$description = $this->Html->tag('small', $description, array(
				'class' => 'extra-text',
			));
		}

		if( !empty($overflowText) ) {
			$overflowText = $this->Html->tag('div', $overflowText, array(
				'class' => 'overflow-text',
			));
		}

		if( !empty($value) ) {
			$default_options['value'] = $value;
		}

		switch ($type) {
			case 'radio':
				$inputContent = $this->_View->element('blocks/common/forms/multiple_radio', array(
					'options' => $_options,
					'fieldName' => $fieldName,
					'error' => $error,
					'label' => $labelText,
					'classSize' => $classSize,
					'value' => $value,
					'inputOptions' => $inputOptions, 
				));

				if( $position == 'horizontal' ) {
					$result =  $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('div', $result.$inputContent, array(
						'class' => 'row',
					)), array(
						'class' => $frameClass,
					)), array(
						'class' => 'row',
					)), array(
						'class' => 'form-group',
					));
				} else {
					$result =  $this->Html->tag('div', $inputContent, array(
						'class' => 'form-group',
					));
				}
				break;
			
			default:
				if( !empty($_options) ) {
					$default_options['options'] = $_options;
				}

				if( !empty($fieldDiv) && !empty($description) ) {
					$default_options['div'] = false;
					$inputContent = $this->Html->tag('div', $this->Form->input($fieldName, $default_options).$description.$overflowText, array(
						'class' => $fieldDiv,
					));
				} else {
					$inputContent = $this->Form->input($fieldName, $default_options).$description.$overflowText;
				}

				if( $type == 'checkbox' && !empty($fieldDiv['class']) ) {
					$inputContent = $this->Html->tag('div', $this->Html->tag('div', $inputContent.$this->Html->tag('div', '', array(
						'class' => 'rku-checkbox',
					)), array(
						'class' => 'cb-custom cb-checkbox cb-checkmark relative',
					)), array(
						'class' => $fieldDiv['class'],
					));
				}

				switch ($position) {
					case 'horizontal':
						$result =  $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('div', $result.$inputContent, array(
							'class' => 'row',
						)), array(
							'class' => $frameClass,
						)), array(
							'class' => 'row',
						)), array(
							'class' => 'form-group',
						));
						break;
					
					default:
						$result =  $this->Html->tag('div', $result.$inputContent, array(
							'class' => 'form-group',
						));
						break;
				}
				break;
		}

		if( $position == 'vertical' && !empty($classSize) ) {
			$result = $this->Html->tag('div', $this->Html->tag('div', $result, array(
				'class' => 'relative '.$classSize,
			)), array(
				'class' => 'row',
			));
		}

		return $result;
	}

	function buildIncrementInput ($fieldName, $options = false) {
		$default_options = array(
			'fieldName' => $fieldName,
			'label' => false,
			'labelClass' => 'col-sm-4 col-xl-2 taright',
			'class' => 'input-group col-xs-5 col-sm-3 col-xl-2',
			'div' => false,
			'required' => false,
			'placeholder' => false,
			'textGroup' => false,
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		return $this->_View->element('blocks/common/forms/increment_input', $default_options);
	}

	function _callGetPreviewUpload ( $fieldName, $data ) {
		if( !empty($data['src']) ) {
			$basename = $this->filterEmptyField($data, 'basename');
			$data = $this->_callUnset($data, array(
				'basename',
			));

			$data['url'] = true;
			$data['fullbase'] = true;
			$data['doc_url'] = true;

            $photoUrl = $this->photo_thumbnail($data);
        	return $this->Html->tag('div', $this->Html->link($basename, $photoUrl, array(
        		'target' => 'blank',
    		)), array(
        		'class' => 'input-file-preview',
    		)).$this->Form->hidden(sprintf('%s_hide', $fieldName)).
    		$this->Form->hidden(sprintf('%s_name', $fieldName));;
        }
	}

	// New Input Form - Nanti semua akan diganti kesini
	function _callInputForm ($fieldName, $options = false) {
		$default_options = array(
			'fieldName' => $fieldName,
			// 'label' => false,
			'frameClass' => '',
			'class' => 'full-width',
			'div' => array(
				'class' => 'input',
			),
			'required' => false,
			'placeholder' => false,
			'text' => false,
			'disabled' => false,
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		$attributes = $this->_callUnset($default_options, array(
			'label',
			'fieldName',
			'frameClass',
			'text',
			'div',
		));
		$default_options['attributes'] = $attributes;
		return $this->_View->element('blocks/common/forms/frontend/input', $default_options);
	}

	function buildInputForm ($fieldName, $options = false) {
		$default_options = array(
			'fieldName' => $fieldName,
			'label' => false,
			'frameClass' => 'col-sm-8',
			'labelClass' => 'col-xl-2 col-sm-4 control-label taright',
			'class' => 'relative col-sm-8 col-xl-4',
			'div' => false,
			'required' => false,
			'placeholder' => false,
			'textGroup' => false,
			'options' => false,
			'disabled' => false,
			'autocomplete' => 'off',
			'additional_content' => '',
			'data_max_lenght' => ''
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		return $this->_View->element('blocks/common/forms/input_form', $default_options);
	}

	function buildFrontEndInputForm ($fieldName, $labelName = false, $options = false) {
		$default_options = array(
			'fieldName' => $fieldName,
			'label' => $labelName,
			'frameClass' => 'form-group',
			'labelClass' => false,
			'class' => false,
			'inputClass' => 'form-control',
			'div' => false,
			'required' => false,
			'placeholder' => false,
			'attributes' => array(),
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		return $this->_View->element('blocks/common/forms/frontend/input_form', $default_options);
	}

	function buildInputDropdown ($fieldName, $options = false) {
		$options['fieldName'] = $fieldName;
		return $this->_View->element('blocks/common/forms/input_dropdown', $options);
	}

	function buildInputToggle ($fieldName, $options = false) {
		$default_options = array(
			'fieldName' => $fieldName,
			'label' => false,
			'labelClass' => 'col-xl-2 taright col-sm-2',
			'class' => 'col-sm-7 col-xl-4',
			'div' => false,
			'required' => false,
			'frameClass' => 'col-sm-12',
			'data_toggle' => 'toggle',
			'data_width' => '100%',
			'data_height' => '30px',
			'attributes' => array(),
			'infoText' => ''
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		return $this->_View->element('blocks/common/forms/input_toggle', $default_options);
	}

	function buildInputMultiple ($fieldName1, $fieldName2, $options = false) {
		$default_options = array(
			'fieldName1' => $fieldName1,
			'fieldName2' => $fieldName2,
			'label' => false,
			'inputClass' => false,
			'inputClass2' => false,
			'labelDivClass' => 'col-sm-4 col-xl-2 taright',
			'class' => 'col-xs-5 col-sm-3 col-xl-2',
			'divider' => 'rv4-cross fs085',
			'dividerClass' => 'col-xs-1 col-sm-1',
			'frameClass' => 'col-sm-8',
			'textGroup' => false,
			'div' => false,
			'required' => false,
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		return $this->_View->element('blocks/common/forms/input_multiple', $default_options);
	}

	function buildInputGroup ($fieldName, $label = false, $options = false) {
		$default_options = array(
			'fieldName' => $fieldName,
			'label' => $label,
			'divClass' => false,
		);

		if( !empty($options) ) {
			$default_options = array_merge($default_options, $options);
		}

		return $this->_View->element('blocks/common/forms/input_group', $default_options);
	}

	function _setFormAddress($modelName, $options = array()){
		$type = $this->filterEmptyField($options, 'type');
		$aditionals = $this->filterEmptyField($options, 'aditionals');
		$currRegionID = $this->filterEmptyField($options, 'currRegionID');
		$currCityID = $this->filterEmptyField($options, 'currCityID');
		$result = '';

		if( empty($currRegionID) ) {
			if( !empty($this->request->data[$modelName]['region_id']) ) {
				$currRegionID = $this->safeTagPrint($this->request->data[$modelName]['region_id']);
			} else if( !empty($this->request->data[$modelName]['region']) ) {
				$currRegionID = $this->safeTagPrint($this->request->data[$modelName]['region']);
			}
		}

		if( empty($currCityID) ) {
			if( !empty($this->request->data[$modelName]['city_id']) ) {
				$currCityID = $this->safeTagPrint($this->request->data[$modelName]['city_id']);
			} else if( !empty($this->request->data[$modelName]['city']) ) {
				$currCityID = $this->safeTagPrint($this->request->data[$modelName]['city']);
			}
		}

		if( $type == 'all' || $type == 'areas' ) {
			$result .= $this->Form->hidden($modelName.'.current_region_id', array(
				'class' => sprintf('currRegionID%s', $aditionals),
				'value' => $currRegionID
			));
			$result .= $this->Form->hidden($modelName.'.current_city_id', array(
				'class' => sprintf('currCityID%s', $aditionals),
				'value' => $currCityID
			));
		}
		if( $type == 'all' || $type == 'locations' ) {
			$result .= $this->Form->hidden($modelName.'.latitude', array(
				'id'=>'rku-latitude', 
			));
			$result .= $this->Form->hidden($modelName.'.longitude', array(
				'id'=>'rku-longitude', 
			));
			$result .= $this->Form->hidden($modelName.'.location', array(
				'id'=>'rku-location', 
			));
			$result .= $this->Form->hidden($modelName.'.dragend', array(
				'id'=>'rku-dragend', 
			));
		}
		return $result;
	}

	function _setFormAddressArr($modelName, $options = array()){
		$type = $this->filterEmptyField($options, 'type');
		$idx = isset($options['key'])?$options['key']:null;
		$aditionals = $this->filterEmptyField($options, 'aditionals');
		$currRegionID = $this->filterEmptyField($options, 'currRegionID');
		$currCityID = $this->filterEmptyField($options, 'currCityID');
		$result = '';

		if( empty($currRegionID) ) {
			if( !empty($this->request->data[$modelName][$idx]['region_id']) ) {
				$currRegionID = $this->safeTagPrint($this->request->data[$modelName][$idx]['region_id']);
			} else if( !empty($this->request->data[$modelName][$idx]['region']) ) {
				$currRegionID = $this->safeTagPrint($this->request->data[$modelName][$idx]['region']);
			}
		}

		if( empty($currCityID) ) {
			if( !empty($this->request->data[$modelName][$idx]['city_id']) ) {
				$currCityID = $this->safeTagPrint($this->request->data[$modelName][$idx]['city_id']);
			} else if( !empty($this->request->data[$modelName][$idx]['city']) ) {
				$currCityID = $this->safeTagPrint($this->request->data[$modelName][$idx]['city']);
			}
		}

		if( $type == 'all' || $type == 'areas' ) {
			$result .= $this->Form->hidden(sprintf('%s.%s.%s',$modelName, $idx, 'current_region_id'), array(
				'class' => sprintf('currRegionID%s', $aditionals),
				'value' => $currRegionID
			));
			$result .= $this->Form->hidden(sprintf('%s.%s.%s',$modelName, $idx, 'current_city_id'), array(
				'class' => sprintf('currCityID%s', $aditionals),
				'value' => $currCityID
			));
		}

		if( $type == 'all' || $type == 'locations' ) {
			$result .= $this->Form->hidden(sprintf('%s.%s.%s',$modelName, $idx, 'latitude'), array(
				'id'=>'rku-latitude', 
			));
			$result .= $this->Form->hidden(sprintf('%s.%s.%s',$modelName, $idx, 'longitude'), array(
				'id'=>'rku-longitude', 
			));
			$result .= $this->Form->hidden(sprintf('%s.%s.%s',$modelName, $idx, 'location'), array(
				'id'=>'rku-location', 
			));
			$result .= $this->Form->hidden(sprintf('%s.%s.%s',$modelName, $idx, 'dragend'), array(
				'id'=>'rku-dragend', 
			));
		}
		return $result;
	}

	function setFormAddress ( $modelName = false, $type = 'all' , $options = array()) {
		$currRegionID = Common::hashEmptyField($options, 'region_id');
		$currCityID = Common::hashEmptyField($options, 'city_id');
		$result = '';
		$aditionals = $this->filterEmptyField($options, 'aditionals');
		
		if(!empty($this->data[$modelName][0])){
			foreach($this->data[$modelName] AS $key => $value){
				if($key > 0){
					$aditionals = $key;
				}

				$result .= $this->_setFormAddressArr($modelName, array(
					'type' => $type,
					'key' => $key,
					'aditionals' => $aditionals,
					'currRegionID' => $currRegionID,
					'currCityID' => $currCityID,
				));
			}
		}else{
			$result = $this->_setFormAddress($modelName, array(
				'type' => $type,
				'aditionals' => $aditionals,
				'currRegionID' => $currRegionID,
				'currCityID' => $currCityID,
			));
		}
		return $result;
	}

	function year($fieldName, $minYear = null, $maxYear = null, $selected = null, $attributes = array(), $tipe = '') {
		$attributes += array('empty' => true);
		if ((empty($selected) || $selected === true) && $value = $this->Form->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$selected = $year;
			} else {
				if (empty($value)) {
					if (!$attributes['empty'] && !$maxYear) {
						$selected = 'now';

					} elseif (!$attributes['empty'] && $maxYear && !$selected) {
						$selected = $maxYear;
					}
				} else {
					$selected = $value;
				}
			}
		}

		if (strlen($selected) > 4 || $selected === 'now') {
			$selected = date('Y', strtotime($selected));
		} elseif ($selected === false) {
			$selected = null;
		}
		$yearOptions = array('min' => $minYear, 'max' => $maxYear, 'order' => 'desc');
		if (isset($attributes['orderYear'])) {
			$yearOptions['order'] = $attributes['orderYear'];
			unset($attributes['orderYear']);
		}
		$attributes['data-role'] = 'none';
		return $this->Form->select($fieldName.( ($tipe == 'year_built') ? '' : '.year'), $this->Form->_generateOptions('year', $yearOptions) ,
			$attributes, $selected
		);
	}

	function getSorting ( $model = false,  $label = false, $options = false ) {
		$named = $this->params['named'];
		$options = !empty($options)?$options:array();
		$ajax = $this->filterEmptyField($options, 'ajax');
		$class = $this->filterEmptyField($options, 'class');
		$elements = $this->filterEmptyField($options, 'elements');

		$options = array(
			'escape' => false,
			'data-scroll' => $this->filterEmptyField($options, 'data-scroll', false, 'body'),
			'data-scroll-time' => $this->filterEmptyField($options, 'data-scroll-time', false, '0'),
			'data-loadingbar' => $this->filterEmptyField($options, 'data-loadingbar', false, 'true'),
			'data-pushstate' => $this->filterEmptyField($options, 'data-pushstate', false, true),
			'url' => $this->filterEmptyField($options, 'url', false, array()),
		);

		if($elements){
			$options = array_merge($options, $elements);
		}

		if($class){
			$options['class'] = $class;
		}

		if( !empty($ajax) ) {
			$options = array_merge(array(
				'class' => 'ajax-link',
				'data-scroll' => 'body',
				'data-scroll-time' => '0',
				'data-loadingbar' => 'true',
				'data-pushstate' => true,
				'data-abort' => 'true',
    			// 'data-wrapper-write' => '.form-table-search table tbody',
    			'data-wrapper-write-page' => '.table-header .pagination-info > span,.pagination-content ul.pagination,.filter-footer,.table.grey > thead > tr:first-child,.form-table-search table tbody',
			), $options);
		}
		
		if( !empty($model) && $this->Paginator->hasPage() ) {
			return $this->Paginator->sort($model, $label, $options);
		} else {
			return $label;
		}
	}

	function _allowShowColumn ( $fieldName, $addClass ) {
		$_allowShow = isset($this->request->data['Search']['colview'][$fieldName])?$this->request->data['Search']['colview'][$fieldName]:false;

		if( !empty($_allowShow) || in_array($fieldName, array( 'checkall', 'action' )) ) {
			$addClass = '';
		}

		return $addClass;
	}

	function _generateShowHideColumn ( $dataColumns, $data_type, $options = false ) {
		$result = false;
		$resultFilter = false;

		// Global Attribut
		$_class = Common::hashEmptyField($options, 'class');
		$_style = Common::hashEmptyField($options, 'style');
		$tr_class = Common::hashEmptyField($options, 'tr_class');
		$thead = Common::hashEmptyField($options, 'thead');
		$no_reset = Common::hashEmptyField($options, 'no_reset');
		$no_clear_link = Common::hashEmptyField($options, 'no_clear_link');
		$filterOptions = Common::hashEmptyField($options, 'filterOptions', array());
		$sortOptions = Common::hashEmptyField($options, 'sortOptions');
		$table_ajax = Common::hashEmptyField($options, 'table_ajax');
		$hideshow = Common::hashEmptyField($options, 'hideshow', true, array(
			'isset' => true,
		));
		$colview = Common::hashEmptyField($this->request->data, 'Search.colview');
		$mobile = Common::hashEmptyField($options, 'mobile', Configure::read('Global.Data.MobileDetect.mobile'), array(
			'isset' => true,
		));

		$default_wrapper = '.table-header .pagination-info > span,.pagination-content ul.pagination,.filter-footer,.table.grey > thead > tr:first-child,.form-table-search table tbody,#crumbtitle';
		$custom_wrapper_search = Common::hashEmptyField($options, 'custom_wrapper_search', $default_wrapper);

		if( !empty($table_ajax) ) {
			$filterOptions = !empty($filterOptions)?$filterOptions:array(
				'div' => 'form-group',
				'label' => false,
				'class' => 'form-control',
				'data-form' => '.form-target',
				'data-url-form' => 'true',
				'data-pushstate' => true,
				'data-abort' => 'true',
    			// 'data-wrapper-write' => '.form-table-search table tbody',
    			'data-wrapper-write-page' => $custom_wrapper_search,
    			'data-loadingbar' => 'true',
			);
		} else {
			$filterOptions = array();
		}

		if( !empty($dataColumns) ) {
			$childArr = array();
			$default = array();
			$reset = array(
         		$this->Html->link(__('Clear'), 'javascript:void(0);', array(
	            	'class' => 'form-reset-filter',
	        	)),
	            array(
	            	'class' => 'reset-filter',
            	),
	        );

			if( $data_type == 'show-hide' ) {
				$result['all'] = __('All');
			}

			foreach ($dataColumns as $key_field => $dataColumn) {
				$allow_acl = Common::hashEmptyField($dataColumn, 'allow_acl', true, array(
					'isset' => true,
				));
				$allow_acl = is_array($allow_acl) ? $this->AclLink->aclCheck($allow_acl) : $allow_acl;

				if($allow_acl){
					$field_model = !empty($dataColumn['field_model'])?$dataColumn['field_model']:false;
					$filter = Common::hashEmptyField($dataColumn, 'filter');
					$showhidecolumns = Common::hashEmptyField($dataColumn, 'showhidecolumns', true, array(
						'isset' => true,
					));

					// Get Data Model
					$data_model = explode('.', $field_model);
					$cnt_model = count($data_model);
					$data_model = array_filter($data_model);

					if( !empty($data_model) ) {
						if( $cnt_model == 2 ) {
							list($modelName, $fieldName) = $data_model;
						} else {
							$fieldName = $data_model[0];
						}
					} else {
						$modelName = false;
						$fieldName = false;
					}

					$style = !empty($dataColumn['style'])?$dataColumn['style']:false;
					$width = !empty($dataColumn['width'])?$dataColumn['width']:false;
					$name = !empty($dataColumn['name'])?$dataColumn['name']:false;
					$display = isset($dataColumn['display'])?$dataColumn['display']:true;
					$child = !empty($dataColumn['child'])?$dataColumn['child']:false;
					$rowspan = !empty($dataColumn['rowspan'])?$dataColumn['rowspan']:false;
					$col_span = !empty($dataColumn['colspan'])?$dataColumn['colspan']:false;
					$class = !empty($dataColumn['class'])?$dataColumn['class']:false;
					$fix_column = !empty($dataColumn['fix_column'])?$dataColumn['fix_column']:false;
					$data_options = !empty($dataColumn['data-options'])?$dataColumn['data-options']:false;
					$align = !empty($dataColumn['align'])?$dataColumn['align']:false;
					$addClass = isset($dataColumn['addClass'])?$dataColumn['addClass']:'hide';
					$col_thead = isset($dataColumn['thead'])?$dataColumn['thead']:$thead;
					$content = $resetView = false;
					$tmpFilterOptions = $filterOptions;

					if( !empty($hideshow) ) {
						if( !empty($display) || in_array($key_field, array( 'checkall', 'action' )) ) {
							if( !empty($display) && empty($colview) ) {
								$this->request->data['Search']['colview'][$key_field] = true;
							}
							if( !empty($display) && !in_array($key_field, array( 'checkall', 'action' )) ) {
								$default[] = __('input[type="checkbox"][rel="%s"]', $key_field);
							}
						}
					} else {
						$this->request->data['Search']['colview'][$key_field] = true;
					}

					switch ($data_type) {
						case 'show-hide':
							if( !in_array($key_field, array( 'checkall', 'action' )) && !empty($showhidecolumns) ) {
								$result[$key_field] = $name;
							}
							break;
						
						default:
							// Set Allow Show Column
							$addClass = $this->_allowShowColumn($key_field, $addClass);
							$content_td = $this->getSorting($field_model, $name, $sortOptions);

							// Colspan
							if( !empty($child) ) {
								$colspan = count($child);
							} elseif (!empty($col_span)) {
								$colspan = $col_span;
							} else {
								$colspan = false;
							}

							if( !empty($width) ) {
								$content_td = $this->Html->tag('div', $content_td, array(
									'style' => __('min-width: %s;', $width),
								));
							}

							$colClass	= array_filter(array($addClass, $key_field, $class, $_class));
							$colClass	= implode(' ', $colClass);
							$colOptions	= array(
								'class' => $colClass,
								'style' => $style,
								'colspan' => $colspan,
								'rowspan' => $rowspan,
								'data-options' => $data_options,
								'align' => $align,
							);

							$attributes = Common::hashEmptyField($dataColumn, 'attributes', array());

							if($attributes && is_array($attributes)){
								$colOptions = array_replace($colOptions, $attributes);
							}

							$content = $this->Html->tag('th', $content_td, $colOptions);

							if( $fix_column && empty($mobile) ) {
								$content .= '</tr></thead><thead><tr>';
							}

							// Append Child
							if( !empty($child) ) {
								$options['thead'] = $col_thead;
								$childArr[] = $this->_generateShowHideColumn( $child, $data_type, $options );
							}

							if( !empty($filter) ) {
								if( $key_field == 'checkall' ) {
									$resultFilter[] = $reset;
								} else if( is_array($filter) ) {
									$resultFilter[] = array(
						         		$this->Form->input(__('Search.%s', $key_field), array_merge($filterOptions, $filter)),
							            array(
							            	'class' => __('%s %s', $addClass, $key_field),
						            	),
							        );
								} else {
									switch ($filter) {
										case 'daterange':
											$resultFilter[] = array(
								         		$this->Form->input(__('Search.%s', $key_field), array_merge($filterOptions, array(
									            	'class' => 'date-range-calendar form-control',
									            	'data-trigger' => 'keyup',
								            	))),
									            array(
									            	'class' => __('%s %s', $addClass, $key_field),
								            	),
									        );
											break;

										case 'default':
											$resultFilter[] = array(
								         		false,
									            array(
									            	'class' => __('%s %s', $addClass, $key_field),
								            	),
									        );
											break;
										
										default:
											$resultFilter[] = array(
								         		$this->Form->input(__('Search.%s', $key_field), array_merge($filterOptions, array(
								         			'type' => $filter,
							         			))),
									            array(
									            	'class' => __('%s %s', $addClass, $key_field),
								            	),
									        );
											break;
									}
								}
							}

							break;
					}

					if( !empty($content) ) {
						$result[] = $content;
					}
				}
			}
		}

		if( $data_type != 'show-hide' ) {
			if( !empty($default) ) {
				$inputDefault = implode(',', $default);
				echo $this->Form->hidden('colview_default', array(
					'value' => $inputDefault,
					'class' => 'colview-default',
				));
			}

			if( is_array($result) ) {
				if( !empty($childArr) && is_array($childArr) ) {
					$result_child = implode('', $childArr);
					$result_child = '</tr><tr class="'.$tr_class.'" style="'.$_style.'">'.$result_child;
					$result[] = $result_child;
				}

				$result = implode('', $result);
			}

			$result = is_array($result)?implode('', $result):$result;

			if( !empty($thead) ) {
				if(!$no_reset){
					if(!$no_clear_link){
						$resultFilter[] = $reset;
					}
					
					$resetView = $this->Html->tableCells(array(
                		$resultFilter,
			        ), array(
			        	'class' => 'filter',
			        ));
				}

				$result = $this->Html->tag('thead', 
                	$this->Html->tag('tr', $result).$resetView
            	);
			}
		}
		return $result;
	}

    function _getDataColumn ( $value, $fieldName, $options = false ) {
        $style = $this->filterEmptyField($options, 'style');
        $class = $this->filterEmptyField($options, 'class');
        $class .= ' '.$fieldName;
        $currency = $this->filterEmptyField($options, 'data-currency');

        $result = false;
    	$addClass = 'hide';

        // Set Allow Show Column
        $class .= ' '.$this->_allowShowColumn($fieldName, $addClass);

        $options['style'] = $style;
        $options['class'] = $class;

        if( !empty($options['options']) ) {
            $value = !empty($options['options'][$value])?$options['options'][$value]:$value;
        } else if( !empty($currency) ) {
            $value = $this->getFormatPrice($value);
        }

        $result = array(
     		$value,
            $options,
        );

        return $result;
    }

	public function photo_thumbnail($options = array(), $parameters = array() ) {
		if( !empty($options['user_path']) && $options['user_path'] == true ) {
			$defaultSize = 'ps';
		} else {
			$defaultSize = 's';
		}

		$thumb = isset($options['thumb'])?$options['thumb']:true;
		$url = isset($options['url'])?$options['url']:false;
		$dimension  = isset($options['size'])?$options['size']:$defaultSize;
		$save_path = isset($options['save_path'])?$options['save_path']:false;
		$src = !empty($options['src'])?$options['src']:false;
		$fullbase = !empty($options['fullbase'])?$options['fullbase']:false;

		$src = ( substr($src, 0, 1) != '/' )?'/'.$src:$src;
		$src = !empty($options['project_path'])?sprintf('/%s%s',$options['project_path'], $src):$options['src'];

		$cache_view_path = Configure::read('__Site.cache_view_path');
		$fullsize = Configure::read('__Site.fullsize');
		$thumbnailPath = sprintf('/%s/%s%s', $save_path, $dimension, $src);
		$fullPath = sprintf('/%s/%s%s', $save_path, $fullsize, $src);

		$extension = $this->_callGetExt($src);

		if( $thumb ) {
			$srcImg = Configure::read('__Site.images_view').$thumbnailPath;
		} else {
			$srcImg = Configure::read('__Site.images_view').$fullPath;
			$dimension = $fullsize;
		}

		if( in_array($extension, array('pdf', 'xls', 'xlsx')) ) {
			if( $extension == 'pdf' ) {
				$url_photo = '/img/pdf.png';
			} else {
				$url_photo = '/img/excel.png';
			}
		} else if( !empty($src) ) {
			if( substr($src, 0, 4) != 'http' ) {
				if( !empty($src) && $src != '/' ) {
					$thumbnail = array(
						'src' => sprintf('%s%s', $cache_view_path, $srcImg),
					);
				} else {
					$thumbnail['src'] = sprintf('%s/errors/error_%s.jpg', $cache_view_path, $dimension);
				}
			} else {
				$thumbnail = array(
					'src' => $src,
				);
			}
		   
			$photoname = $thumbnail['src'];
			$url_photo = Configure::read('__Site.img_path_http').$photoname;
		} else {
			$url_photo = sprintf('%s/errors/error_%s.jpg', $cache_view_path, $dimension);
		}

        if( !empty($fullbase) ) {
            if( $fullbase == true ) {
                $url_photo = FULL_BASE_URL.$url_photo;
            } else {
                $url_photo = $fullbase.$url_photo;
            }
        }

		if( $url ) {
			return $url_photo;
		} else {
			return $this->Html->image($url_photo, $parameters);
		}
	}

	function setFormBirthdate ( $modelName = 'UserProfile', $options = array('day','month', 'year') ) {

		$default_attribute = array(
			'label' => false,
			'div' => array(
				'class' => 'col-sm-4'
			),
			'required' => false,
			'error' => false,
			'class' => 'form-control',
		);

		$optionBirth = $this->validDateBirth();
		$result = '';

		if( in_array("day", $options) ) {
			$default_attribute['empty'] = 'Tanggal';
			$default_attribute['options'] = $optionBirth['day'];
			$result .= $this->Form->input($modelName.'.day_birth', $default_attribute);
		}

		if( in_array("month", $options) ) {
			$default_attribute['empty'] = 'Bulan';
			$default_attribute['options'] = $optionBirth['month'];
			$result .= $this->Form->input($modelName.'.month_birth', $default_attribute);
		}

		if( in_array("year", $options) ) {
			$default_attribute['empty'] = 'Tahun';
			$default_attribute['options'] = $optionBirth['year'];
			$result .= $this->Form->input($modelName.'.year_birth', $default_attribute);
		}

		return $result;
	}

	function customDate($dateString, $format = 'd F Y') {
		return date($format, strtotime($dateString));
	}

	function _callActiveMenu ( $lblActive, $lblTag, $tag = false ) {
		$tag = !empty($tag)?$tag:$lblTag;
		$lblTag = strtolower($lblTag);
		$tag = strtolower($tag);
		$tag = $this->safeTagPrint($tag);

		if( $lblActive == $tag || $lblActive == $lblTag ) {
			return false;
		} else {
			return 'collapse';
		}
	}

	function link($text, $url, $options = false, $alert = false) {
		$_icon = $this->filterEmptyField($options, 'data-icon');
		$_wrapper = $this->filterEmptyField($options, 'data-wrapper');
		$_wrapper_options = $this->filterEmptyField($options, 'data-wrapper-options');
		$_lbl_active = $this->filterEmptyField($options, 'data-active');
		$_caret = $this->filterEmptyField($options, 'data-caret');

		$_add_class = !empty($options['class'])?$options['class']:false;
		$_tolower_text = $this->safeTagPrint(strtolower($text));
		$_tag = str_replace('#', '', $url);

		if( !empty($_wrapper) ) {
			$default_wrapper_options = false;

			if( !empty($_wrapper_options) ) {
				$default_wrapper_options = $_wrapper_options;
			}

			$text = $this->Html->tag($_wrapper, $text, $default_wrapper_options);
		}
		if( !empty($_icon) ) {
			$text = sprintf('%s %s', $this->icon($_icon), $text);
			$options['escape'] = false;

			unset($options['data-icon']);
		}
		if( $_lbl_active == $_tolower_text || $_lbl_active == $_tag ) {
			$_add_class .= ' active';
			$options['class'] = $_add_class;

			if( isset($options['aria-expanded']) ) {
				$options['aria-expanded'] = 'true';
			}
			if( isset($options['class']) ) {
				$options['class'] = str_replace('collapsed', '', $options['class']);
			}
		}

		if( !empty($_caret) ) {
			$text .= $this->Html->tag('span', '', array(
				'class' => 'caret',
			));
		}

		return $this->Html->link($text, $url, $options, $alert);
	}

	function truncate( $str, $len, $ending = '...', $stripTag = true ) {
		$str = trim($str);

		if( !empty($stripTag) ) {
			$str = $this->safeTagPrint($str);
		}

		if($len > 0){
			return $this->Text->truncate($str, $len, array(
				'ending' => $ending,
				'exact' => false
			));
		}else{
			return '';
		}
	}

	function truncateByStr($str, $len, $ending = '...', $stripTag = true){
		if(!empty($str)){

			if( !empty($stripTag) ) {
				$str = $this->safeTagPrint($str);
			}

			if(strlen($str) < $len){
				$ending = '';
			}

			$str = substr($str, 0, $len).$ending;
		}
		return $str;
	}

	function _callYoutubeThumbnail ( $youtube_id, $title = false ) {
		$youtube_thumbnail = $this->Html->image(sprintf('http://img.youtube.com/vi/%s/0.jpg', $youtube_id), array(
			'class' => 'default-thumbnail',
			'title' => $title,
			'alt' => $title,
		));
		$youtube_url = sprintf('https://www.youtube.com/watch?v=%s', $youtube_id);

		return $this->Html->link($youtube_thumbnail, $youtube_url, array(
			'escape' => false,
			'target' => '_blank',
		));
	}

	function _callGreetingDate () {
		$hour = date("H"); 

		if ( $hour > 00 && $hour < 10 || $hour == 00 ){ 
			$lblDay = 'Pagi'; 
		}else if ($hour >= 10  && $hour < 15 ){ 
			$lblDay = 'Siang'; 
		}else if ($hour >= 15 &&  $hour < 18 ){ 
			$lblDay = 'Sore'; 
		}else if ($hour >= 18 &&  $hour <= 24 ){ 
			$lblDay = 'Malam'; 
		}else { 
			$lblDay = ''; 
		} 
		
		return $lblDay;
	}

	/**
	*
	*	format display area code (phone number)
	*
	*	@param string $phone : no telepon
	*	@return string $phoneNumber
	*/
	function formatThenumber($phone) {
		// list area code with 3 number
		$list_area_code = array("061", "021", "022", "024", "031");
		$area_code = substr($phone, 0, 3);

		// format thenumber and display, ex: (021) 735-1832 or 0888-9999-6565
		if ( strlen($phone) <= 10 ) {
			if (in_array($area_code, $list_area_code)) {
				// with 3 number
				$phoneNumber = "(".substr($phone, 0, 3).") ".substr($phone, 3, 3)."-".substr($phone,6);
			} else {
				// with 4 number
				$phoneNumber = "(".substr($phone, 0, 4).") ".substr($phone, 4, 3)."-".substr($phone,7);
			}
			
		} else {
			if (in_array($area_code, $list_area_code)) {
				// with 3 number
				$phoneNumber = "(".substr($phone, 0, 3).") ".substr($phone, 3, 4)."-".substr($phone,7);
			} else {
				$phoneNumber = substr($phone, 0, 4)."-".substr($phone, 4, 4)."-".substr($phone,8);
			}
		}
		return $phoneNumber;
	}

	function toSlug($string, $separator = '-') {
		if( is_string($string) ) {
			return strtolower(Inflector::slug($string, $separator));
		} else {
			return $string;
		}
	}

	function _generateMenuSide ( $labelMenu, $icon, $caret, $active_menu, $tag, $contentLi, $addoption = array() ) {
		$classChild = $this->_callActiveMenu($active_menu, $labelMenu, $tag);
		$upperTag = strtoupper($tag);
		$tag = $this->toSlug($tag);

		$options = array(
			'data-active' => $active_menu,
			'data-wrapper' => 'span',
			'data-caret' => $caret,
			'data-toggle' => 'collapse',
			'data-parent' => '#accordion',
			'aria-expanded' => 'false',
			'aria-controls' => $tag,
			'role' => 'button',
			'class' => 'tab collapsed',
			'escape' => false
		);

		if($icon){
			$options['data-icon'] = $icon;
		}

		if(!empty($addoption)){
			$options = array_merge($options, $addoption);
		}

		$content = $this->Html->tag('div', $this->link($labelMenu, '#'.$tag, $options), array(
			'id' => 'head'.$upperTag,
			'role' => 'tab',
		));

		$optionsUl = array(
			'id' => $tag,
			'class' => $classChild,
			'role' => 'tabpanel',
			'aria-labelledby' => 'head'.$upperTag,
		);

		if( empty($classChild) ) {
			$optionsUl['aria-expanded'] = 'true';
			$optionsUl['class'] .= 'in';
		}

		$content .= $this->Html->tag('ul', $contentLi, $optionsUl);

		return $this->Html->tag('li', $content);
	}

	function _generateMenuTop ( $labelMenu, $icon, $caret, $contentLi ) {
		$content = $this->Html->tag('div', $this->link($labelMenu, '#', array(
			'data-wrapper' => 'span',
			'data-icon' => $icon,
			'data-caret' => $caret,
			'aria-expanded' => 'false',
			'aria-hashpopup' => 'true',
			'data-toggle' => 'dropdown',
			'class' => 'dropdown-toggle',
		)).$this->Html->tag('ul', $contentLi, array(
			'class' => 'dropdown-menu',
		)), array(
			'class' => 'btn-group',
		));
		return $this->Html->tag('li', $content);
	}

	function getPathPhoto ( $path, $size, $save_path, $filename ){
		$file = $path.DS.$save_path.DS.$size.$filename;
		$file = str_replace('/', DS, $file);

		return $file;
	}

	function inputTypeAllow ( $data, $fieldName, $modelName = 'PropertyType' ) {
		return $this->filterEmptyField($data, $modelName, $fieldName);
	}

	function _rulesDimensionImage($directory_name, $data_type = false, $size_type = 'label', $options = array()){
		$result = array();
		
		if( in_array($directory_name, array( 'logos' )) ) {
			if( $data_type == 'thumb' ) {
				if( $size_type == 'size' ) {
					$result = '100x40';
				} else {
					$result = 'xsm';
				}
			} else if( $data_type == 'large' ) {
				if( $size_type == 'size' ) {
					$result = '240x96';
				} else {
					$result = 'xxsm';
				}
			} else {
				$result = array(
					'xsm' => '100x40',
					'xm' => '165x165',
					'xxsm' => '240x96'
				);
			}
		} else if( in_array($directory_name, array( 'users' )) ) {
			if( $data_type == 'thumb' ) {
				if( $size_type == 'size' ) {
					$result = '100x100';
				} else {
					$result = 'pm';
				}
			} else if( $data_type == 'large' ) {
				if( $size_type == 'size' ) {
					$result = '300x300';
				} else {
					$result = 'pxl';
				}
			} else {
				$result = array(
					'ps' => '50x50',
					'pm' => '100x100',
					'pl' => '150x150',
					'pxl' => '300x300',
				);
			}
		} else if( in_array($directory_name, array( 'ebrosur' )) ) {
			if( $data_type == 'thumb' ) {
				if( $size_type == 'size' ) {
					$result = '640x453';
				} else {
					$result = 'm';
				}
			} else if( $data_type == 'large' ) {
				if( $size_type == 'size' ) {
					if(!empty($options['type_image']) && $options['type_image'] == 'potrait'){
						$result = '768x1024';
					}else{
						$result = '1024x768';
					}
				} else {
					$result = 'xl';
				}
			} else {
				if(!empty($options['type_image']) && $options['type_image'] == 'potrait'){
					$result = array(
						's' => '296x420',
						'm' => '453x640',
						'xl' => '768x1024',
					);
				}else{
					$result = array(
						's' => '420x296',
						'm' => '640x453',
						'xl' => '1024x768',
					);
				}
			}
		} else {
			if( $data_type == 'thumb' ) {
				if( $size_type == 'size' ) {
					$result = '300x169';
				} else {
					$result = 'm';
				}
			} else if( $data_type == 'large' ) {
				if( $size_type == 'size' ) {
					$result = '855x481';
				} else {
					$result = 'l';
				}
			} else {
				$result = array(
					's' => '150x84',
					'm' => '300x169',
					'l' => '855x481',
					'company' => '855x481',					
				);
			}
		}

		return $result;
	}

	function getActiveStep ( $step, $current, $data = false, $id = false ) {
		$addClass = '';

		if( !empty($data) || !empty($id) ) {
			$addClass = 'done';
		}

		if( $step == $current ) {
			$addClass = ' active';
		}

		return $addClass;
	}

	function getUrlStep ( $url, $current, $data, $id = false ) {
		$draft_id = Configure::read('__Site.PropertyDraft.id');

		if( !empty($data) || !empty($id) ) {
			if( is_array($url) ) {
				$url['draft'] = $draft_id;
			}

			return $url;
		} else {
			return '#';
		}
	}

	function buildButton ( $data, $frameClass = false, $btnClass = false ) {
		$text = $this->filterEmptyField($data, 'text', false, false, false);
		$url = $this->filterEmptyField($data, 'url');
		$alert = $this->filterEmptyField($data, 'alert');
		$display = $this->filterIssetField($data, 'display', false, true);
		$class = $this->filterEmptyField($data, 'class');
		$allow = $this->filterEmptyField($data, 'allow');

		if( !empty($display) ) {
			$options = $this->filterEmptyField($data, 'options', array());
			$options['escape'] = false;

			if( !empty($btnClass) ) {
				$options['class'] = !empty($options['class'])?$options['class']:false;
				$options['class'] .= ' '.$btnClass;
			}

			if( !empty($class) ) {
				$options['class'] .= ' '.$class;
			}

			if($allow){
				$link = $this->Html->link($text, $url, $options, $alert);
			} else {
				$link = $this->AclLink->link($text, $url, $options, $alert);
			}

			$useFrame = Common::hashEmptyField($options, 'use_frame', true, array(
				'isset' => true, 
			));

			if($useFrame){
				$link = $this->Html->tag('div', $link, array(
					'class' => $frameClass,
				));
			}

			return $link;
		} else {
			return false;
		}
	}

	function wrapWithHttpLink( $url, $link = true, $empty = '' ){
		$result = strtolower($url);
		$textUrl = 'http://';
		$textUrls = 'https://';

		if( !empty($url) ) {
			$flag = array();

			if( strpos($url, $textUrl) === false && substr($url, 0, 7) != $textUrl ) {
				$flag[] = true;
			}
			if( strpos($url, $textUrls) === false && substr($url, 0, 8) != $textUrls ) {
				$flag[] = true;
			}

			if( count($flag) == 2 ) {
				$url = sprintf("%s%s", $textUrl, $url);
			}
		} else {
			$url = $empty;
		}

		if( !empty($link) && !empty($url) ) {
			$result = $this->Html->link($url, $url, array(
				'target' => '_blank',
			));
		} else {
			$result = $url;
		}

		return $result;
	}

	function clearImageTag( $content ){
		if( !empty($content) ) {
			$content = preg_replace("/<img[^>]+\>/i", " ", $content);
		}
		return $content;
	}

	function getCurrencyPrice ($price, $empty = false, $currency = false, $decimalPlaces = 0) {
		if( !empty($empty) && empty($price) ) {
			return $empty;
		} else {
			if( empty($currency) ) {
				$currency = Configure::read('__Site.config_currency_symbol');
			}

			return $this->Number->currency($price, $currency, array('places' => $decimalPlaces));
		}
	}

	function getFormatPrice ($price, $empty = 0) {
		if( !empty($price) ) {
			return $this->Number->currency($price, '', array('places' => 0));
		} else {
			return $empty;
		}
	}

	function getCombineDate ( $startDate, $endDate, $empty = false, $emptyEndDate = ' - ..' ) {
		$customDate	= false;
		$startDate	= $startDate == '0000-00-00' ? NULL : $startDate;
		$endDate	= $endDate == '0000-00-00' ? NULL : $endDate;

		if( !empty($startDate) && !empty($endDate) ) {
			$startDate = strtotime($startDate);
			$endDate = strtotime($endDate);

			if( $startDate == $endDate ) {
				$customDate = date('d M Y', $startDate);
			} else if( date('M Y', $startDate) == date('M Y', $endDate) ) {
				$customDate = sprintf('%s - %s', date('d', $startDate), date('d M Y', $endDate));
			} else if( date('Y', $startDate) == date('Y', $endDate) ) {
				$customDate = sprintf('%s - %s', date('d M', $startDate), date('d M Y', $endDate));
			} else {
				$customDate = sprintf('%s - %s', date('d M Y', $startDate), date('d M Y', $endDate));
			}
		} else if( !empty($startDate) ) {
			$startDate = strtotime($startDate);
			$customDate = sprintf('%s%s', date('d M Y', $startDate), $emptyEndDate);
		} else if( !empty($empty) ) {
			$customDate = $empty;
		}

		return $customDate;
	}

	function _isAdmin ( $group_id = false ) {
		$admin_id = Configure::read('__Site.Admin.List.id');
		
		if( empty($group_id) ) {
			$group_id = Configure::read('User.group_id');
		}

		if( in_array($group_id, $admin_id) ) {
			return true;
		} else {
			return false;
		}
	}

	function _isCompanyAdmin ( $group_id = false ) {
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

	function _callUserFullName($user, $link = true, $options = false, $optionLink = array()) {

		$user = $this->mergeUser($user);
		$full_name = $this->filterEmptyField($user, 'full_name');

		if( empty($full_name) ) {
			$full_name = $this->filterEmptyField($user, 'first_name');
			$last_name = $this->filterEmptyField($user, 'last_name');

			if( !empty($last_name) ) {
				$full_name = sprintf('%s %s', $full_name, $last_name);
				$full_name = trim($full_name);
			}
		}

		$full_name = ucwords($full_name);

		if( !empty($link) ) {
			$id = $this->filterEmptyField($user, 'id');
			$username = $this->filterEmptyField($user, 'username');

			if(!empty($optionLink)){
				$link = $this->filterEmptyField($optionLink, 'link');
				$controller =  $this->filterEmptyField($link, 'controller');
				$action =  $this->filterEmptyField($link, 'action');
				$modelName = $this->filterEmptyField($optionLink, 'modelName');
				$fieldName = $this->filterEmptyField($optionLink, 'fieldName');
				$id = $this->filterEmptyField($user, $modelName, $fieldName);

				return $this->Html->link($full_name, array(
					'controller'=> $controller, 
					'action'=> $action, 
					$id, 
					'admin' => true,
				), $options);
			}else{
				return $this->Html->link($full_name, array(
					'controller'=>'users', 
					'action'=>'profile', 
					$id, 
					$username, 
					'admin' => false,
				), $options);
			}
			
		} else {
			return $full_name;   
		}
	}

	function mergeUser ( $user ) {
		if( !empty($user['User']) ) {
			$user = array_merge($user, $user['User']);
			unset($user['User']);
		}

		return $user;
	}

	function fieldColorPicker($name, $label, $attributes = array()){
		$default_attributes = array(
			'fieldName' => $name,
			'label' => $label,
			'frameClass' => 'col-sm-8',
			'labelClass' => 'col-sm-12',
			'class' => 'relative col-md-7 col-xs-12',
			'defaultClass' => 'col-md-5 col-xs-12',
			'dataField' => '',
			'dataDefault' => '',
		);

		if( !empty($attributes) ) {
			$default_attributes = array_merge($default_attributes, $attributes);
		}

		return $this->_View->element('blocks/common/forms/input_color_picker', $default_attributes);
	}

	function webThemeConfig($data, $get){
		/*for web config*/
		$font_size = $this->filterEmptyField($data, 'UserCompanySetting', 'font_size');
		$font_type = $this->filterEmptyField($data, 'UserCompanySetting', 'font_type');

		$main_content_color = $this->filterEmptyField($data, 'UserCompanySetting', 'main_content_color');
		$button_color = $this->filterEmptyField($data, 'UserCompanySetting', 'button_color');
		$bg_color = $this->filterEmptyField($data, 'UserCompanySetting', 'bg_color');
		$bg_color_top_header = $this->filterEmptyField($data, 'UserCompanySetting', 'bg_color_top_header');
		$font_color = $this->filterEmptyField($data, 'UserCompanySetting', 'font_color');
		$font_menu_color = $this->filterEmptyField($data, 'UserCompanySetting', 'font_menu_color');
		$font_heading_footer_color = $this->filterEmptyField($data, 'UserCompanySetting', 'font_heading_footer_color');
		$font_heading_color = $this->filterEmptyField($data, 'UserCompanySetting', 'font_heading_color');
		$font_link_color = $this->filterEmptyField($data, 'UserCompanySetting', 'font_link_color');
		$bg_footer = $this->filterEmptyField($data, 'UserCompanySetting', 'bg_footer');
		$bg_header = $this->filterEmptyField($data, 'UserCompanySetting', 'bg_header');
		$header_image = $this->filterEmptyField($data, 'UserCompanySetting', 'header_image');
		$bg_image = $this->filterEmptyField($data, 'UserCompanySetting', 'bg_image');

		if( !empty($header_image) ) {
			$header_image = $this->photo_thumbnail(array(
				'save_path' => Configure::read('__Site.general_folder'), 
				'src'=> $header_image, 
				'thumb' => false,
				'url' => true,
				'fullbase' => true,
			));
		}

		if( !empty($bg_image) ) {
			$bg_image = $this->photo_thumbnail(array(
				'save_path' => Configure::read('__Site.general_folder'), 
				'src'=> $bg_image, 
				'thumb' => false,
				'url' => true,
				'fullbase' => true,
			));
		}

		/*for get*/
		$main_content_color = $this->filterEmptyField($get, 'main_content_color', false, $main_content_color);
		$button_color = $this->filterEmptyField($get, 'button_color', false, $button_color);
		$bg_color = $this->filterEmptyField($get, 'bg_color', false, $bg_color);
		$bg_color_top_header = $this->filterEmptyField($get, 'bg_color_top_header', false, $bg_color_top_header);

		$bg_color_border_header = !empty($bg_color_top_header) ? $bg_color_top_header : '3b3b3b';
		if(strpos($bg_color_border_header,'rgba') === false){
			$bg_color_border_header = sprintf('#%s', $bg_color_border_header);
		}
		$bg_color_border_header = Common::alterColor( $bg_color_border_header, '10');

		$font_type = $this->filterEmptyField($get, 'font_type', false, $font_type);
		$font_size = $this->filterEmptyField($get, 'font_size', false, $font_size);
		$font_color = $this->filterEmptyField($get, 'font_color', false, $font_color);
		$font_menu_color = $this->filterEmptyField($get, 'font_menu_color', false, $font_menu_color);
		$font_heading_footer_color = $this->filterEmptyField($get, 'font_heading_footer_color', false, $font_heading_footer_color);
		$font_heading_color = $this->filterEmptyField($get, 'font_heading_color', false, $font_heading_color);
		$font_link_color = $this->filterEmptyField($get, 'font_link_color', false, $font_link_color);
		$bg_footer = $this->filterEmptyField($get, 'bg_footer', false, $bg_footer);
		$bg_header = $this->filterEmptyField($get, 'bg_header', false, $bg_header);

		$opt_cnt = sprintf('main_content_color=%s&button_color=%s&bg_color=%s&font_type=%s&font_size=%s&font_color=%s&font_menu_color=%s&font_heading_footer_color=%s&font_heading_color=%s&font_link_color=%s&bg_footer=%s&bg_header=%s&header_image=%s&bg_image=%s&base_url=%s&bg_color_top_header=%s&bg_color_border_header=%s', str_replace('#', '', $main_content_color), str_replace('#', '', $button_color), str_replace('#', '', $bg_color), $font_type, $font_size, str_replace('#', '', $font_color), str_replace('#', '', $font_menu_color), str_replace('#', '', $font_heading_footer_color), str_replace('#', '', $font_heading_color), str_replace('#', '', $font_link_color), str_replace('#', '', $bg_footer), str_replace('#', '', $bg_header), $header_image, $bg_image, FULL_BASE_URL, str_replace('#', '', $bg_color_top_header), str_replace('#', '', $bg_color_border_header));

		return $opt_cnt;
	}

	function getTitleCheckBox($value){
		$result = substr($value, 0, 18);

		if( strlen($value) > 18 ) {
			$result .= '..';
		}

		return $result;
	}

	function urlExport ( $url, $type ) {
		$lastChar = substr($url, -1);

		if( $lastChar != '/' ) {
			$url .= '/';
		}

		$url .= 'export:'.$type;
		return $url;
	}

	function stripHtml( $str ) {
		if( !empty($str) ) {
			if( preg_match("/^[\pL\s,.'-]+$/u", $str) ) {
				$str = preg_replace("/[^a-zA-Z0-9_.-\s]/", "", $str);
			}
		}
		return $str;
	}

	function getFullAddress( $data, $separator = ', ', $only_location = false, $break_address = true ) {
		$address = $this->filterEmptyField($data, 'address', false, false, false);
		$address = $this->_callGetDescription($address);
		
		$region = $this->filterEmptyField($data, 'Region', 'name');
		$city = $this->filterEmptyField($data, 'City', 'name');
		$subarea = $this->filterEmptyField($data, 'Subarea', 'name');
		$zip = $this->filterEmptyField($data, 'zip');
		$fulladdress = '';

		if( empty($only_location) && !empty($address) ) {
			if( !empty($break_address) ) {
				$fulladdress = $address . '<br>';
			} else {
				$fulladdress = $address . ' ';
			}
			
		}

		if( !empty($subarea) ) {
			$fulladdress .= $subarea;
		}
		if( !empty($city) ) {
			$fulladdress .= $separator. $city;
		}
		if( !empty($region) ) {
			$fulladdress .= $separator . $region . ' ' . $zip;
		}

		return $fulladdress;
	}

	function getGenerateAddress( $address, $region = false, $city = false, $subarea = false, $zip = false, $separator = ', ', $empty = false ) {
		$fulladdress = '';

		if( !empty($address) ) {
			$fulladdress = $address;
		}

		if( !empty($subarea) ) {
			if( !empty($fulladdress) ) {
				$fulladdress .= $separator. $subarea;
			} else {
				$fulladdress .= $subarea;
			}
		}
		if( !empty($city) ) {
			$fulladdress .= $separator. $city;
		}
		if( !empty($region) ) {
			$fulladdress .= $separator . $region . ' ' . $zip;
		}

		$fulladdress = trim($fulladdress);

		if( !empty($fulladdress) ) {
			return $fulladdress;
		} else {
			return $empty;
		}
	}

	function clearfix ( $tag = 'div' ) {
		return $this->Html->tag($tag, '', array(
			'class' => 'clear',
		));
	}

	function _callStatus ( $status, $classActive = 'active', $classNonActive = 'non-active' ) {
		if( !empty($status) ) {
			$custom_status =  $this->Html->tag('span', __('Ya'), array(
				'class' => 'lbl fbold '.$classActive,
			));
		} else {
			$custom_status = $this->Html->tag('span', __('Tidak'), array(
				'class' => 'lbl fbold '.$classNonActive,
			));
		}

		return $custom_status;
	}

	function divider( $class = false ) {
		$default = 'divider';
		if( !empty($class) ) {
			$default = $default.' '.$class;
		}
		return $this->Html->tag('div', '', array(
			'class' => $default,
		));
	}

	function _slideControls ( $theme ) {
		$result = false;

		switch ($theme) {
			case 'EasyLiving':
				$sliderNavigate = $this->Html->tag('span', '',array(
					'class' => 'slider-prev',
				));
				$sliderNavigate .= $this->Html->tag('span', '',array(
					'class' => 'slider-next',
				));
				$result =  $this->Html->tag('div', $sliderNavigate,array(
					'class' => 'sliderControls hidden-print',
				));
				break;
			
			default:
				# code...
				break;
		}

		return $result;
	}

	function _callGetDescription ( $str , $replace = '<br>') {
		return str_replace(PHP_EOL, $replace, $str);
	}

	function getCheckRevision($model, $field, $revision_data, $label = false){
		$field_arr = explode(',', $field);
		
		$show = false;
		foreach ($field_arr as $key => $value) {
			if(array_key_exists($model, $revision_data) && array_key_exists($value, $revision_data[$model])){
				$show = true;

				break;
			}
		}

		if($show && Configure::read('User.admin')){
			return $this->Html->tag('div', $this->Form->input(sprintf('PropertyRevision.%s-%s', $model, $field), array(
				'type' => 'checkbox',
				'label' => $label,
				'div' => array(
					'class' => 'cb-checkmark',
				),
				'required' => false,
				'error' => false,
				'checked' => true,
				'class' => 'check-option',
				// 'hiddenField' => false,
			)), array(
				'class' => 'cb-custom',
			));
		}else{
			return $label;
		}
	}

	function getMeasurePrice ( $property ) {
		$allow_multiplication = array(2, 5, 6);
		$propertyPrice = $this->filterEmptyField($property, 'Property', 'price', 0);
		$propertyPrice = $this->filterEmptyField($property, 'PropertySold', 'price_sold', $propertyPrice);
		$lot_unit = $this->filterEmptyField($property, 'Property', 'lot_unit', 1);
		$property_type_id = $this->filterEmptyField($property, 'Property', 'property_type_id');
		$rate = $this->filterEmptyField($property, 'Currency', 'rate', 1);

		if( in_array($property_type_id, $allow_multiplication) && $lot_unit != 1 ){
			$measure_size = $this->filterEmptyField($property, 'Property', 'measure_size');

			if(in_array($property_type_id, array(5,6) )){
				$measure_size = $this->filterEmptyField($property, 'PropertyAsset', 'building_size');
			}

			$propertyPrice = $propertyPrice * $measure_size;
		}

		return $propertyPrice * $rate;
	}

	function getMeasurePriceText ( $property ) {
		$property_type_id = !empty($property['Property']['property_type_id'])?$property['Property']['property_type_id']:false;
		$lot_unit = !empty($property['Property']['lot_unit'])?$property['Property']['lot_unit']:1;
		$price = !empty($property['Property']['price'])?$property['Property']['price']:0;
		$building_size = !empty($property['PropertyAsset']['building_size'])?$property['PropertyAsset']['building_size']:false;
		$lot_size = !empty($property['Property']['lot_size'])?$property['Property']['lot_size']:false;
		$detail_calculation = false;

		if( $lot_unit != 1 ) {
			if( $property_type_id == 2 && !empty($lot_size) ){
				$lot_unit = $this->getLotUnit( $lot_unit );
				$price = $this->Number->format(sprintf('%1.0f', $price));
				$detail_calculation = sprintf('@ %s x %s', $price, $lot_size.' '.$lot_unit);
			}else if( in_array($property_type_id, array(5,6)) && !empty($building_size) ){
				$lot_unit = $this->getLotUnit( $lot_unit, 'sup', 'm<sup>2</sup>' );
				$price = $this->Number->format(sprintf('%1.0f', $price));
				$detail_calculation = sprintf('@ %s x %s', $price, $building_size.' '.$lot_unit);
			}
		}

		return $detail_calculation;
	}

	function _allowKpr ( $data ) {
		$is_building = $this->filterEmptyField($data, 'PropertyType', 'is_building');

		if( !empty($is_building) ) {
			return true;
		} else {
			return false;
		}
	}

	function _getBungaKPRPersen ( $bunga_kpr = false ) {
		$bunga_kpr = !empty($bunga_kpr)?$bunga_kpr:Configure::read('__Site.bunga_kpr');
		return ( 100 - $bunga_kpr ) / 100;
	}

	function creditFix($amount, $rate, $year=20){
		if( empty($rate) ) {
			return 0;
		} else {
			if( $rate != 0 ) {
				$rate = ($rate/100)/12;
			}
			$rateYear = pow((1+$rate), ($year*12));
			$rateMin = (pow((1+$rate), ($year*12))-1);
			if( $rateMin != 0 ) {
				$rateYear = $rateYear / $rateMin;
			}
			$mortgage = $rateYear * $amount * $rate;
			return $mortgage;   
		}
	}

	function _callNewLive ( $value ) {
		return str_replace(PHP_EOL, '<br>', $value);
	}

	function _callPeriodeYear ( $maxPeriode = 50, $text = 'Thn' ) {
		$year = array();

		for ($i=1; $i <= $maxPeriode; $i++) { 
			$label_text = $i.' '.$text;
			$label_text = trim($label_text);

			$year[$i] = $label_text;
		}

		return $year;
	}

	function _callYear ( $start = 2011 ) {
		$year = array();

		for ($i=date('Y'); $i >= $start; $i--) { 
			$year[$i] = $i;
		}

		return $year;
	}

	function _getPrint ($url = array()) {
		$result = false;
		$default_attr = array(
			'escape' => false,
			'class' => false,
		);
		$urlPrint = $this->here.'export:excel';
		if(!empty($url)){
			$urlPrint = $url;
		}
		
		$_excel_attr = $default_attr;
		$_excel_attr['class'] = $default_attr['class'].' print hidden-print default btn--for excel-anchor';
		$result = $this->Html->link('<i class="fa fa-download"></i> Download Excel', $urlPrint, $_excel_attr);

		return $result;
	}

	function bootstrap_radio( $name, $options = array(), $default = 0, $add_options = array(), $default_options = array() ) {
		if (isset($options['options']) && !empty($options['options'])) {
			$rc = "";
			$options_default = array(
				'legend'=> false,
				'label'=>false,
				'required'=>false,
				'value'=>$default,
				'data-role' => 'none'
			);

			if( !empty($default_options) ) {
				$options_default = array_merge($options_default, $default_options);
			}

			foreach ($options['options'] as $key => $value) {
				$checked = ($default == $key)?'checked':'';

				$with_radio = 'radio';
				if(isset($add_options['no_class_radio']) && $add_options['no_class_radio']){
					$with_radio = '';
				}

				if(!empty($add_options['class'])){
					$rc .= "<div class='".$with_radio." ".$add_options['class']."'><label class='".$checked."'>";
				}else{
					$rc .= "<div class='".$with_radio."'><label class='".$checked."'>"; 
				}
				
				$rc .= $this->Form->radio($name, array(
					$key=>__($value),
				), $options_default); 

				$rc .= "</label></div>";
			}
			
			return($rc);
		}
		return(false);
	}

	function callEbrosurs ( $brosur, $is_flag = false, $opts = array()) {
		$id = $this->filterEmptyField($brosur, 'UserCompanyEbrochure', 'id');
		$ebrosur_photo = $this->filterEmptyField($brosur, 'UserCompanyEbrochure', 'ebrosur_photo');

		$image_url = $this->photo_thumbnail(array(
			'save_path' => Configure::read('__Site.ebrosurs_photo'), 
			'src'=> $ebrosur_photo, 
			'size' => 'xl',
			'url' => true
		));

		$image_url_s = $this->photo_thumbnail(array(
			'save_path' => Configure::read('__Site.ebrosurs_photo'), 
			'src'=> $ebrosur_photo, 
			'size' => 'm',
			'url' => true
		));

	//	Show Image
	//	define default options, note : go ahead define new options by yourself
		$defaultImgClass			= 'fancybox-thumbnail';
		$opts['img_alt']			= isset($opts['img_alt']) ? $opts['img_alt'] : 'thumbnail';
		$opts['img_class']			= isset($opts['img_class']) ? $defaultImgClass.' '.$opts['img_class'] : 'fancybox-thumbnail';
		$opts['img_width']			= isset($opts['img_width']) ? $opts['img_width'] : NULL;
		$opts['img_height']			= isset($opts['img_height']) ? $opts['img_height'] : NULL;
		$opts['img_style']			= isset($opts['img_style']) ? $opts['img_style'] : NULL;
		$opts['img_align']			= isset($opts['img_align']) ? $opts['img_align'] : NULL;
		$opts['container_class']	= isset($opts['container_class']) ? $opts['container_class'] : NULL;

		$img = $this->Html->image($image_url_s, array(
			'alt'		=> $opts['img_alt'], 
			'class'		=> $opts['img_class'], 
			'width'		=> $opts['img_width'], 
			'height'	=> $opts['img_height'], 
			'align'		=> $opts['img_align'],
			'style'		=> $opts['img_style'],
		));

		$content = $this->Html->link($img, $image_url, array('class' => 'fancybox-buttons', 'data-fancybox-group' => 'button', 'target' => '_blank', 'escape' => false));
		return $opts['container_class'] ? $this->Html->tag('div', $content, array('class' => $opts['container_class'] . ' wrapper-layer ')) : $content;
	}

	function wrapTag( $tag = false, $content = false, $options = array() ) {
		$result = $content;
		if( !empty($tag) && !empty($content) ){
			$result = $this->Html->tag($tag, $content, $options);
		}
		return $result;
	}

	function errorField($fieldName){
		$result = '';
		if ($this->Form->isFieldError($fieldName)) {
			$result = $this->Form->error($fieldName);
		}

		return $result;
	}

	function getNameEbrosur($data){
		$region = $this->filterEmptyField($data, 'Region', 'name');
		$city = $this->filterEmptyField($data, 'City', 'name');

		$PropertyAction = $this->filterEmptyField($data, 'PropertyAction', 'name');
		$EbrosurTypeRequest = $this->filterEmptyField($data, 'EbrosurTypeRequest');

		$str_type_property = '';
		if(!empty($EbrosurTypeRequest)){
			foreach ($EbrosurTypeRequest as $key => $value) {
				if(!empty($value['PropertyType']['name'])){
					$str_type_property .= $value['PropertyType']['name'].', ';
				}
			}
		}

		return sprintf('%s %s, %s', $str_type_property, $PropertyAction, $city);
	}

	function getSearchUrl($data){
		$property_type = $this->filterEmptyField($data, 'EbrosurTypeRequest', 'property_type_id');
		$agent_id = $this->filterEmptyField($data, 'agent_id');
		$data = $this->filterEmptyField($data, 'EbrosurRequest');

		$url = array(
			'controller' => 'ebrosurs',
			'action' => 'index',
			'admin' => false
		);

		if(!empty($data)){
			$property_action_id = $this->filterEmptyField($data, 'property_action_id');
			$region_id = $this->filterEmptyField($data, 'region_id');
			$city_id = $this->filterEmptyField($data, 'city_id');
			$property_direction_id = $this->filterEmptyField($data, 'property_direction_id');
			$certificate_id = $this->filterEmptyField($data, 'certificate_id');
			$furnished = $this->filterEmptyField($data, 'furnished');
			$beds = $this->filterEmptyField($data, 'beds');
			$baths = $this->filterEmptyField($data, 'baths');
			$lot_size = $this->filterEmptyField($data, 'lot_size');
			$building_size = $this->filterEmptyField($data, 'building_size');
			$min_price = $this->filterEmptyField($data, 'min_price');
			$max_price = $this->filterEmptyField($data, 'max_price');

			if(!empty($agent_id)){
				if(is_array($agent_id)){
					$agent_id = implode(',', $agent_id);
				}

				$url['user'] = $agent_id;
			}

			if(!empty($property_type)){
				if(is_array($property_type)){
					$property_type = implode(',', $property_type);
				}
				
				$url['typeid'] = $property_type;
			}

			if(!empty($property_action_id)){
				$url['property_action'] = $property_action_id;
			}
			if(!empty($region_id)){
				$url['region'] = $region_id;
			}
			if(!empty($city_id)){
				$url['city'] = $city_id;
			}
			if(!empty($property_direction_id)){
				$url['property_direction_id'] = $property_direction_id;
			}
			if(!empty($certificate_id)){
				$url['certificate_id'] = $certificate_id;
			}
			if(!empty($furnished)){
				$url['furnished'] = $furnished;
			}
			if(!empty($beds)){
				$url['beds'] = $beds;
			}
			if(!empty($baths)){
				$url['baths'] = $baths;
			}
			if(!empty($lot_size)){
				$url['lot_size'] = $lot_size;
			}
			if(!empty($building_size)){
				$url['building_size'] = $building_size;
			}

			if(!empty($min_price) && !empty($max_price)){
				$url['price'] = sprintf('%s-%s', $min_price, $max_price);
			}else if(!empty($min_price)){
				$url['price'] = sprintf('0-%s', $min_price);
			}else if(!empty($max_price)){
				$url['price'] = sprintf('%s-0', $max_price);
			}

		}

		return $url;
	}

	/**
	*
	*   konversi desimal ke string
	*   @param int $number : angka
	*   @return string hasil
	*/
	function getConvertStringDecimal ( $number, $options = false ) {
		$thou = '';
		$withTag = $this->filterEmptyField($options, 'withTag');
		$currency = $this->filterEmptyField($options, 'currency');
		$more = $this->filterEmptyField($options, 'more');
		$other_format = $this->filterEmptyField($options, 'other_format');

		$float = !empty($more)?0:1;

		if (!empty($other_format)) {
			$float = !empty($more)?1:0;

			if( $number >= 1000000000000 ) {
				$number = (string)floor($number/1000000000000);
				$thou = 'triliun';
			} else if( $number >= 1000000000 ) {
				$number = (string)floor($number/1000000000);
				$thou = 'miliar';
			} else if( $number >= 1000000 ) {
				$number = (string)floor($number/1000000);
				$thou = !empty($more)?'jt':'juta';
			} else if( $number >= 1000 ) {
				$number = (string)round(floor($number/1000),0,PHP_ROUND_HALF_DOWN);

				if ($number < 100) {
					$getThenumber = substr($number, -1);
					$number = str_replace($getThenumber,"0", $number);
				} else {
					$getThenumber = substr($number, -2);
					$number = str_replace($getThenumber,"00", $number);	
				}

				$thou = !empty($more)?'rb':'ribu';
			}
		} else {
			if( $number >= 1000000000000 ) {
				$number = str_replace(".0", "", (string)number_format (floor($number/1000000000000), $float, ".", ""));
				$thou = 'triliun';
			} else if( $number >= 1000000000 ) {
				$number = str_replace(".0", "", (string)number_format (floor($number/1000000000), $float, ".", ""));
				$thou = 'miliar';
			} else if( $number >= 1000000 ) {
				$number = str_replace(".0", "", (string)number_format (floor($number/1000000), $float, ".", ""));
				$thou = !empty($more)?'jt':'juta';
			} else if( $number >= 1000 ) {
				$number = str_replace(".0", "", (string)number_format (floor($number/1000), $float, ".", ""));

				if( !empty($more) ) {
					if ($number < 100) {
						$getThenumber = substr($number, -1);
						$number = str_replace($getThenumber,"0", $number);
					} else {
						$getThenumber = substr($number, -2);
						$number = str_replace($getThenumber,"00", $number);	
					}
					
					$thou = 'rb';
				} else {
					$thou = 'ribu';
				}
			}
		}

		if(!empty($more) && floatval($number) > 0){
			$thou = sprintf('%s-an', $thou);	
		}else{
			$thou = ucwords($thou);
		}


		if( !empty($currency) ) {
			$code = Configure::read('__Site.config_currency_symbol');
			$number = sprintf('%s%s', $code, $number);
		}

		if( $withTag ) {
			$number = sprintf('%s%s', $number, $this->Html->tag('span', $thou, array(
				'class' => 'thou'
			)));
		} else {
			$number = sprintf('%s %s', $number, $thou);
		}

		return $number;
	}

	function getSpesificationRequestEbrosur($data){
		$global_variabel = Configure::read('Global.Data');

		$text = '';

		if(!empty($data['EbrosurRequest'])){
			$property_direction = $this->filterEmptyField($data, 'PropertyDirection', 'name');
			$Certificate = $this->filterEmptyField($data, 'Certificate', 'name');

			$data = $this->filterEmptyField($data, 'EbrosurRequest');
			
			$furnished = $this->filterEmptyField($data, 'furnished');
			$beds = $this->filterEmptyField($data, 'beds');
			$baths = $this->filterEmptyField($data, 'baths');
			$lot_size = $this->filterEmptyField($data, 'lot_size');
			$building_size = $this->filterEmptyField($data, 'building_size');
			$min_price = $this->filterEmptyField($data, 'min_price');
			$max_price = $this->filterEmptyField($data, 'max_price');

			$min_lot_size = $this->filterEmptyField($data, 'min_lot_size');
			$max_lot_size = $this->filterEmptyField($data, 'max_lot_size');
			
			$min_building_size = $this->filterEmptyField($data, 'min_building_size');
			$max_building_size = $this->filterEmptyField($data, 'max_building_size');

			if(!empty($property_direction)){
				$text .= sprintf('Hadap : %s, ', $property_direction);
			}
			if(!empty($Certificate)){
				$text .= sprintf('Sertifikat : %s, ', $Certificate);
			}
			if(!empty($furnished)){
				$text .= sprintf('Interior : %s, ', $global_variabel['furnished'][$furnished]);
			}
			if(!empty($beds)){
				$text .= sprintf('K. Tidur : ≥ %s, ', $beds);
			}
			if(!empty($baths)){
				$text .= sprintf('K. Mandi : ≥ %s, ', $baths);
			}
			// if(!empty($lot_size)){
			// 	$text .= sprintf('L. Tanah : ≥ %s m2, ', $lot_size);
			// }
			// if(!empty($building_size)){
			// 	$text .= sprintf('L. Bangunan : ≥ %s m2, ', $building_size);
			// }

			if(!empty($min_lot_size) && !empty($max_lot_size)){
				$text .= sprintf('L. Tanah : %s - %s, ', $min_lot_size, $max_lot_size);
			}else if(!empty($min_lot_size)){
				$text .= sprintf('L. Tanah : diatas %s, ', $min_lot_size);
			}else if(!empty($max_lot_size)){
				$text .= sprintf('L. Tanah : dibawah %s, ', $max_lot_size);
			}

			if(!empty($min_building_size) && !empty($max_building_size)){
				$text .= sprintf('L. Bangunan : %s - %s, ', $min_building_size, $max_building_size);
			}else if(!empty($min_building_size)){
				$text .= sprintf('L. Bangunan : diatas %s, ', $min_building_size);
			}else if(!empty($max_building_size)){
				$text .= sprintf('L. Bangunan : dibawah %s, ', $max_building_size);
			}

			if(!empty($min_price) && !empty($max_price)){
				$text .= sprintf('%s - %s', $this->getConvertStringDecimal($min_price), $this->getConvertStringDecimal($max_price));
			}else if(!empty($min_price)){
				$text .= sprintf('diatas %s', $this->getConvertStringDecimal($min_price));
			}else if(!empty($max_price)){
				$text .= sprintf('dibawah %s', $this->getConvertStringDecimal($max_price));
			}
		}

		return $text;
	}

	function generateSingleMenuHeder ( $labelMenu, $url, $tag = 'li', $active_tag, $active_menu = false, $option_link = array() ) {
		$active_class = '';
		if($active_menu == $active_tag){
			$active_class = 'active';
		}

		$options = array(
			'escape' => false
		);

		if(!empty($option_link)){
			$options = array_merge($options, $option_link);
		}

		return $this->Html->tag($tag, $this->Html->link($labelMenu, $url, $options), array(
			'class' => $active_class
		));
	}

	function getPropertyTypeText($data, $model){
		$text = '';

		if(!empty($data)){
			$arr = Set::extract('/'.$model.'/name', $data);
			
			$text = implode(', ', $arr);
		}
		
		return $text;
	}

	function _date_range_limit_days($base, $result) {
		$days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		$this->_date_range_limit(1, 13, 12, "m", "y",   $base);

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

	function _date_range_limit($start, $end, $adj, $a, $b, $result) {
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

	function _callPriceConverter ($price) {
		return trim(str_replace(array( ',', '.' ), array( '', '' ), $price));
	}

	function _date_normalize($base, $result) {
		$result = $this->_date_range_limit(0, 60, 60, "s", "i", $result);
		$result = $this->_date_range_limit(0, 60, 60, "i", "h", $result);
		$result = $this->_date_range_limit(0, 24, 24, "h", "d", $result);
		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

		$result = $this->_date_range_limit_days($base, $result);

		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

		return $result;
	}

	function _callDateDiff ( $one, $two ) {
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
			$this->_date_normalize($a, $result);
		} else {
			$this->_date_normalize($b, $result);
		}

		return $result;
	}

	function dateDiff ( $startDate, $endDate, $format = false ) {
		$result = false;
		
		if( !empty($startDate) && !empty($endDate) && $startDate != '0000-00-00 00:00:00' && $endDate != '0000-00-00 00:00:00' ) {
			$from_time = strtotime($startDate);
			$to_time = strtotime($endDate);
			$datediff = intval($to_time - $from_time);
			$total_day = intval($datediff/(60*60*24));
			$total_hour = intval($datediff/(60*60));

			$dateResult = $this->_callDateDiff ( $startDate, $endDate );

			switch ($format) {
				case 'day':					
					// $result = array(
					//	 'total_d' => $total_day,
					//	 'total_hour' => $total_hour,
					// );
					// $h = $this->filterEmptyField($dateResult, 'h');
					// $i = $this->filterEmptyField($dateResult, 'i');

					// if( !empty($total_day) ) {
					//	 $result['FormatArr']['d'] = sprintf(__('%s Hari'), $total_day);
					// }
					// if( !empty($h) ) {
					//	 $result['FormatArr']['h'] = sprintf(__('%s Jam'), $h);
					// }
					// if( !empty($i) ) {
					//	 $result['FormatArr']['i'] = sprintf(__('%s Menit'), $i);
					// }

					$result = $total_day;

					break;

				default:
					$result = $dateResult;
					break;
			}
		}

		return $result;
	}

	function buildCheckOption( $modelName, $id = false, $type = 'all', $is_checked = false, $default_class = 'check-option',$disabled = false ,$attribute = false, $options = array(), $label_options = array(), $attribute_checkboxs = array()) {
		if( empty($options) ) {
			$options = array();
		}
		if( empty($label_options) ) {
			$label_options = array();
		}

		if( !empty($is_checked) ) {
			$options['checked'] = true;
		}

		switch ($type) {
			case 'all':
				return $this->Html->tag('div', $this->Html->tag('div', $this->Form->checkbox($modelName.'.checkbox_all', array_merge($options, array(
					'label' => false,
					'class' => 'checkAll',
					'div' => false,
					'hiddenField' => false,
				))).$this->Form->label($modelName.'.checkbox_all', '&nbsp;', $label_options), array(
					'class' => 'cb-checkmark',
				)), array(
					'class' => 'cb-custom mt0',
				));
				break;
			
			default:
				return $this->Html->tag('div', $this->Html->tag('div', $this->Form->checkbox($modelName.'.id.'.$id, array_merge($options, array(
					'class' => $default_class,
					'value' => $id,
					'div' => false,
					'hiddenField' => false,
					'disabled' => $disabled,
					'add-attribute' => $attribute,
				))).$this->Form->label($modelName.'.id.'.$id, '&nbsp;', $label_options), array(
					'class' => 'cb-checkmark',
				)), array(
					'class' => 'cb-custom mt0',
				));
				break;
		}
	}

	function _callGetExt ( $file = false ) {
		$fileArr = explode('.', $file);
		return end($fileArr);
	}

	function getIndoDate($date, $type = 'day'){
		if($type == 'day'){
			$day_arr = array('minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu');
			$day = date('w', strtotime($date));

			return $day_arr[$day];
		}else{
			$day_month = Configure::read('__Site.monthly.named');
		
			$month = date('n', strtotime($date));

			return $day_month[$month-1];
		}
	}

	public function getIndoDateCutom($strDate = null, $options = array()){
		$empty = $this->filterEmptyField($options, 'empty');

		if($strDate){
			$short	= $this->filterEmptyField($options, 'short');
			$type	= $this->filterEmptyField($options, 'type');
			$time	= $this->filterEmptyField($options, 'time', false, 'H:i:s');
			$zone	= $this->filterIssetField($options, 'zone', false, 'WIB');

			$monthNames	= Common::config('__Site.monthly.named', array());
			$dayNames	= array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');

		//	w : Numeric representation of the day of the week => 0 (for Sunday) through 6 (for Saturday)
		//	n : Numeric representation of a month, without leading zeros => 1 through 12
			$dateComponents	= date('d w n Y', strtotime($strDate));
			$dateComponents	= explode(' ', $dateComponents);

			$date	= Hash::get($dateComponents, 0, false);
			$day	= Hash::get($dateComponents, 1, false);
			$month	= Hash::get($dateComponents, 2, false);
			$year	= Hash::get($dateComponents, 3, false);

		//	remove daye from list
			$dateComponents = Hash::remove($dateComponents, 1);
			$dateComponents = array_values($dateComponents);

			if($month){
				$text = $monthNames[$month - 1];
				$text = $short ? substr($text, 0, 3) : $text;

				$dateComponents[1] = $text;
			}

			$dateComponents = implode(' ', $dateComponents);	

			if($type == 'day'){
				$text = $dayNames[$day];
				$text = $short ? substr($text, 0, 3) : $text;

				$dateComponents = sprintf('%s, %s', $text, $dateComponents);
			}

			if($time){
				$dateComponents = sprintf('%s, %s %s', $dateComponents, date($time, strtotime($strDate)), $zone);
			}

			return trim($dateComponents);
		}
		else{
			return $empty;
		}
	}

	function filterPeriode ( $data ) {
		$period    = Common::hashEmptyField($data, 'Search.period');

		$customDate	= false;

		if( !empty($period) ) {

			if ($period == 'daily') {
				$periode_val = 'Tanggal';
				$date        = Common::hashEmptyField($data, 'Search.date');

				if (!empty($date)) {
					$date_range  = explode(' - ', $date);

					$date_from   = str_replace('/', '-', $date_range[0]);
					$date_to     = str_replace('/', '-', $date_range[1]);
				} else {
					$date_from   = Common::hashEmptyField($data, 'Search.date_from');
					$date_to     = Common::hashEmptyField($data, 'Search.date_to');
				}

				$startDate   = $this->customDate($date_from, 'd M Y');
				$endDate     = $this->customDate($date_to, 'd M Y');

			} elseif ($period == 'monthly') {
				$periode_val = 'Bulan';
				$date_from   = Common::hashEmptyField($data, 'Search.date_from');
				$date_to     = Common::hashEmptyField($data, 'Search.date_to');

				$date_from = date_create($date_from);
				$date_to   = date_create($date_to);

				$startDate = date_format($date_from, "M Y");
				$endDate   = date_format($date_to, "M Y");

			} elseif ($period == 'yearly') {
				$periode_val = 'Tahun';
				$startDate   = Common::hashEmptyField($data, 'Search.year_from');
				$endDate     = Common::hashEmptyField($data, 'Search.year_to');


			}

			$customDate = sprintf('Periode %s: %s - %s', $periode_val, $this->Html->tag('strong', $startDate), $this->Html->tag('strong', $endDate));

		}
// debug($customDate);die();
		return $customDate;
	}

	function _callfindAgentCompanyId ( $from_id, $to_id, $agent_company_id ) {
		if( in_array($to_id, $agent_company_id) ) {
			return $to_id;
		} else if( in_array($from_id, $agent_company_id) ) {
			return $from_id;
		} else {
			return false;
		}
	}

	function getSelectedTemplate($dataTemplate, $id, $type_template, $dataBasic = array(), $model){
		$is_active = '';
		$text = __('Pilih');
		
		if(!empty($dataBasic[$model]['type_template']) && !empty($dataBasic[$model]['id_template']) && $dataBasic[$model]['type_template'] == $type_template && $dataBasic[$model]['id_template'] == $id){
			$is_active = 'is-selected';
			$text = __('Terpilih');
		}else if(!empty($dataTemplate[$model]['type_template']) && !empty($dataTemplate[$model]['id_template']) && $dataTemplate[$model]['type_template'] == $type_template && $dataTemplate[$model]['id_template'] == $id){
			$is_active = 'is-selected';
			$text = __('Terpilih');
		}

		return array(
			'class_active' => $is_active,
			'text' => $text
		);
	}

	function setFormDateRange ( $modelName = 'Search' ) {
		$result = $this->Form->hidden($modelName.'.date_from', array(
			'id' => 'DateFromPicker',
		));
		$result .= $this->Form->hidden($modelName.'.date_to', array(
			'id' => 'DateToPicker',
		));
		return $result;
	}

	function _callLblConfigValue ( $fieldName = false, $custom_value = false ) {
        $_config = Configure::read('Config.Company.data');
        $mandatory = '';

        if( !empty($fieldName) ) {
	        $is_mandatory_client = $this->filterEmptyField($_config, 'UserCompanyConfig', $fieldName);

	        if( !empty($is_mandatory_client) && !empty($custom_value) ) {
	            $mandatory = '*';
	        } else {
	            $mandatory = $is_mandatory_client;
	        }
	    }

        return $mandatory;
	}

	function _callTitleUser ( $group_id, $slug = false ) {
		switch ($group_id) {
			case '2':
				if( !empty($slug) ) {
					$titleUser = 'agent';
				} else {
					$titleUser = 'Agen';
				}
				break;
			case '3':
				if( !empty($slug) ) {
					$titleUser = 'principle';
				} else {
					$titleUser = 'Principal';
				}
				break;
			case '5':
				if( !empty($slug) ) {
					$titleUser = 'admin';
				} else {
					$titleUser = 'Admin';
				}
				break;
			default:
				$titleUser = 'Admin Primesystem';
				break;
		}

		return $titleUser;
	}

	function _getContentType ( $ext = false ) {
		$default_mime = array(
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg', 
			'jpeg' => 'image/jpeg', 
			'png' => 'image/png',
			'pjpeg' => 'image/pjpeg',
			'x-png' => 'image/x-png',
			'pdf' => 'application/pdf',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		);

		if( !empty($ext) ) {
			if( !empty($default_mime[$ext]) ) {
				return $default_mime[$ext];
			} else {
				return 'application/octet-stream';
			}
		} else {
			return $default_mime;
		}
	}

	function getALtImage($data){
		$url_without_http = $_SERVER['HTTP_HOST'];

		$property_type 	= $this->filterEmptyField($data, 'PropertyType', 'name');
		$property_act 	= $this->filterEmptyField($data, 'PropertyAction', 'name');
		$mlsid 			= $this->filterEmptyField($data, 'Property', 'mls_id');

		$PropertyAddress = $this->filterEmptyField($data, 'PropertyAddress');
					
		$subarea_name 	= $this->filterEmptyField($PropertyAddress, 'Subarea', 'name');
		$zip 			= $this->filterEmptyField($PropertyAddress, 'zip');
		$city_name 		= $this->filterEmptyField($PropertyAddress, 'City', 'name');

		return sprintf('%s %s %s, %s %s %s %s', $property_type, $property_act, $subarea_name, $city_name, $zip, $mlsid, $url_without_http);
	}

    function _callLbl ( $type, $label, $value, $status = false, $options = array()) {
        $result = false;
        $content = '';

        $val2 = $val = null;
        $classValue = Common::hashEmptyField($options, 'class-value');

        if($status){
        	$val2 = $this->Html->tag('span', $value);
        	$val2 = $this->Html->tag('div', $val2, array(
                    'class' => 'col-sm-3 color-blue',
            ));
        }else{
        	$val = $this->Html->tag('span', $value, array(
        		'class' => $classValue,
    		));
        }


        switch ($type) {
            case 'row':
                $lbl = $this->Html->tag('label', $label);

                $content = $this->Html->tag('div', $lbl, array(
                    'class' => 'col-sm-4',
                ));

                if(!empty($val)){
                	$content .= $this->Html->tag('div', $val, array(
	                   	'class' => 'col-sm-3',
	                ));	
                }

                if(!empty($val2)){
                	$content .= $val2;
                }

                $result = $this->Html->tag('div', $content, array(
                    'class' => 'row',
                ));
                break;
            
            case 'form-group':
                $lbl = $this->Html->tag('label', $label, array(
                    'class' => 'normal',
                ));
                
                $result = $this->Html->tag('div', $lbl.$val, array(
                    'class' => 'form-group',
                ));
                break;
            
            case 'table':
                $result = $this->Html->tag('td', $label, array(
                    'width' => '200',
                    'style' => 'vertical-align: top;'
                ));
                $result .= $this->Html->tag('td', $val);
                
                $result = $this->Html->tag('tr', $result);
                break;
        }

        return $result;
    }

	function _callAllowAccess ( $field ) {
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

	function checkbox($field, $options = array()){
		$mt = $this->filterEmptyField($options, 'mt', false, 'mt0');
		$label = Common::hashEmptyField($options, 'label');

		$options['label'] = '&nbsp;';
		$options['div'] = false;
		$options['type'] = 'checkbox';

		if( is_array($label) ) {
			$lbl_opt = Common::hashEmptyField($label, 'options');
			$label = Common::hashEmptyField($label, 'text');
		} else {
			$lbl_opt = array(
				'class' => 'lbl-chk',
			);
		}


		return $this->Html->div('cb-custom '.$mt, $this->Html->div('cb-checkmark', 
			$this->Form->input($field, $options)
		)).$this->Form->label($field, $label, $lbl_opt);
	}

	function getTotalProgresMigrate($data){
		$total = 0;
		$status = 'ongoing';

		$is_complete_sync 	= $this->filterEmptyField($data, 'MigrateCompany', 'is_complete_sync');
		$canceled 			= $this->filterEmptyField($data, 'MigrateCompany', 'canceled');
		$arr 				= $this->filterEmptyField($data, 'MigrateConfigCompany');

		$count_field = 0;
		$field = array();
		if(empty($canceled)){
			foreach ($arr as $key => $value) {
				$count_field++;

				$data_count = $this->filterEmptyField($value, 'document_status');
				$slug = $this->filterEmptyField($value, 'slug');

				$status_field = false;
				if(!empty($data_count) && $data_count == 'completed'){
					$total++;
					$status_field = true;
				}

				array_push($field, array(
					'field' => $slug,
					'status' => $status_field,
					'action' => $data_count
				));
			}

			if($count_field == 0){
				$count_field = 1;
			}

			$total = intval(($total / $count_field) * 100);
		}else{
			$total = 100;
		}
		
		if($canceled){
			$status = 'canceled';
		}

		return array(
			'total' => $total,
			'status' => $status,
			'fields' => $field
		);
	}

	function _callIsDirector () {
		$dataCompany = Configure::read('Config.Company.data');
        $group_id = $this->filterEmptyField($dataCompany, 'User', 'group_id');

        if( $group_id == 4 ) {
        	return true;
        } else {
        	return false;
        }
	}

	function _callSocialMedias ( $value, $tag = false, $tag_icon = false ) {
		$sociaLinks		= false;
		$socialMedias	= array(
			'facebook'		=> 'facebook', 
			'twitter'		=> 'twitter', 
			'google_plus'	=> 'google', 
			'linkedin'		=> 'linkedin', 
			'pinterest'		=> 'pinterest', 
			'instagram'		=> 'instagram', 
		);

		foreach($socialMedias as $fieldName => $icon){
			$link = $this->filterEmptyField($value, 'UserConfig', $fieldName);

			if($link){
				$icon = $this->icon(__('fa fa-%s', $icon));

				if( !empty($tag_icon) ) {
					if( is_array($tag_icon) ) {
						$tag_name = $this->filterEmptyField($tag_icon, 'tag');
						$class = $this->filterEmptyField($tag_icon, 'class');
					} else {
						$tag_name = $tag_icon;
						$class = false;
					}

					$icon = $this->Html->tag($tag_name, $icon, array(
						'class' => $class,
					));
				}

				if( !empty($tag) ) {
					if( is_array($tag) ) {
						$tag_name = $this->filterEmptyField($tag, 'tag');
						$class = $this->filterEmptyField($tag, 'class');
					} else {
						$tag_name = $tag;
						$class = false;
					}

					$sociaLinks .= $this->Html->tag($tag_name, $this->Html->link($icon, $link, array(
						'escape' => FALSE, 
						'target' => '_blank',
						'class' => $class,
					)));
				}
			}
		}

		return $sociaLinks;
	}

	function _callUnset( $data, $fieldArr, $removeField = false) {
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

	function _callPercentage ( $entry, $total ) {
		if( !empty($entry) ) {
			$percent = ( $entry / $total ) * 100;

			if( $percent > 100 ) {
				$percent = 100;
			}

			return $this->getFormatPrice($percent, 0, 2);
		} else {
			return 0;
		}
	}

	public function dropdownButtons($items = array(), $options = array()){
		$result = null;

		if($items){
			$template	= '';
			$ulOptions	= $this->filterEmptyField($options, 'ul_options', null, array());
			$liOptions	= $this->filterEmptyField($options, 'li_options', null, array());

			if($ulOptions){
				unset($options['ul_options']);
			}

			if($liOptions){
				unset($options['li_options']);
			}

			foreach($items as $item){
				if(is_array($item)){
					$text	= $this->filterEmptyField($item, 'text', null, '');
					$url	= $this->filterEmptyField($item, 'url', null, '');
					$opts	= $this->filterEmptyField($item, 'options');
					$alert	= $this->filterEmptyField($item, 'alert');
					$allow  = Common::hashEmptyField($item, 'allow');
					$domain = Common::hashEmptyField($item, 'domaina');

					if(!empty($allow)){
						$url  = $this->Html->url($url);
						$item = $this->Html->link(__($text), sprintf('%s%s', $domain, $url), $opts, $alert);
					} else {
						$item = false;
						$check = $this->AclLink->aclCheck($url);
						$url  = $this->Html->url($url);

						if($check){
							$item = $this->Html->link(__($text), sprintf('%s%s', $domain, $url), $opts, $alert);
						}
					}
				}

				if( !empty($item) ) {
					$template.= $this->Html->tag('li', $item, $liOptions);
				}
			}

			$iconButton	= $this->icon('rv4-burger');
			$iconButton	= $this->Html->link($iconButton, 'javascript:void(0);', array(
				'class'		=> 'btn icon-btn', 
				'data-role'	=> 'popover-action', 
				'escape'	=> false, 
			));

		//	wrap template
			$ulOptions = array_replace_recursive(array(
				'class'		=> 'hide popover-action-menu', 
				'data-role'	=> 'popover-content', 
			), $ulOptions);

			if( !empty($template) ) {
				$template = $iconButton.$this->Html->tag('ul', $template, $ulOptions);

				$options = array_replace_recursive(array(
					'class' => 'dropdown icon-btn-wrapper', 
				), $options);

				$result = $this->Html->tag('div', $template, $options);
			}
		}

		return $result;
	}

	function _callSplitArr( $values, $col = 1 ) {
		$result = array();
		$cnt = ceil(count($values) / $col);

		if( $col == 1 || $cnt == 1 ) {
			$result[] = $values;
		} else {
			if( !empty($values) ) {
				$idx = 0;
				$i = 1;

				foreach ($values as $key => $value) {
					$result[$idx][$key] = $value;

					if( $i >= $cnt ) {
						$i = 1;
						$idx++;
					}

					$i++;
				}
			}
		}

		return $result;
	}


	function _callSplitContentArr( $values, $col = 1 ) {
		$result = array();

		if( !empty($values) ) {
            $loop = $index = 0;

			foreach ($values as $key => $value) {
				$result[$loop][$index] = $value;

                $index++;

                if($index == $col){
                    $index = 0;
                    $loop++;
                }
			}
		}

		return $result;
	}

	function noticeInfo($desc, $title = false, $options = array(), $text = 'rv4-shortip'){
		$str_pos = strpos($text, 'rv4-');

		if( is_numeric($str_pos) ) {
			$text = $this->icon($text);
		}

		$options['data-placement'] = $this->filterEmptyField($options, 'data-placement', false, 'right');

		$content = '';
		if(!empty($desc)){
			$options = array_merge(array(
				'class' => 'static-modal',
				'data-toggle' => 'popover',
				'data-content' => $desc,
				'title' => $title,
				'escape' => false,
			), $options);

			$content = $this->Html->tag('span', $this->Html->link($text, 'javascript:void(0)', $options), array(
				'class' => 'notice-static-modal'
			));
		}

		return $content;
	}

	function _callTableDivider () {
		return $this->Html->tag('span', $this->icon('rv4-min'), array(
			'class' => 'table-action-divider floleft',
		));
	}

	function _callUrlProperty ( $value, $mls_id, $slug ) {
        if( $this->_callIsDirector() ) {
            $domain = $this->filterEmptyField($value, 'UserCompanyConfig.domain');
            $fullbase = false;
        } else {
        	$domain = null;
            $fullbase = true;
        }

        return $domain.$this->Html->url(array(
            'controller'=> 'properties', 
            'action' => 'detail',
            'mlsid' => $mls_id,
            'slug'=> $slug, 
            'admin'=> false,
        ), $fullbase);
	}

	function _checkHTMLtag ( $content ) {
		if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $content )) {
			return true;
		} else {
			return false;
		}
	}

	function setOPtions($themeConfig = false){
		$options = array();
		$is_col = Common::hashEmptyField($themeConfig, 'is_col');
		$v_max = Common::hashEmptyField($themeConfig, 'v_max');
		$col = 1;

		if($is_col){
			$col = Common::hashEmptyField($themeConfig, 'col');
		}

		$x = $col;
		while ($x <= $v_max) {
			$options[$x] = $x;
			$x = $x + $col;
		}

		return $options;
	}

	function dateInterval( $start_date, $end_date ) {
		$start_date  = date_create($start_date);
		$end_date = date_create($end_date);

		$diff  = date_diff( $start_date, $end_date );
		$dateInterval = '';

		if ($diff->y == 0) {
			$dateInterval = sprintf('%s bulan', $diff->m);
		} else {
			$dateInterval = sprintf('%s tahun, %s bulan', $diff->y, $diff->m);
		}

		return $dateInterval;
	}

	function getAction($value){
		$icon = $this->icon('rv4-pencil');
		$group_id = Common::hashEmptyField($value, 'User.group_id');

		switch ($group_id) {
			case '2':
				$action = 'edit_agent';
				break;
			case '3':
				$action = 'edit_principle';
				break;
			case '4':
				$action = 'edit_director';
				break;
			case '5':
				$action = 'edit_admin';
				break;
		}

		return $action;
	}

	function CheckActiveParent($childs  = false, $results = array()){
		if(!empty($childs)){
			foreach ($childs as $key => $value) {
				$child = Common::hashEmptyField($value, 'child');
				$allow = Common::hashEmptyField($value, 'allow', 'acl', array(
					'isset' => true,
				));

				if($allow === 'acl'){
					$url = Common::hashEmptyField($value, 'url');
					$allow = $this->AclLink->_aclCheck($url);
				}

				if($child){
					$results = $this->CheckActiveParent($child, $results);
				}
				$results[] = $allow;
			}
		}
		return $results;
	}

	function generateMenu($data_arr = false, $supports = array()){
		$data_menu = Common::hashEmptyField($supports, 'data_menu', 'main-menu');
		$data = Common::hashEmptyField($supports, 'data');
		$active_menu = Common::hashEmptyField($supports, 'active_menu');

		$sub_data = array();
		$params = $this->params->params;
		$prefix = Common::hashEmptyField($params, 'prefix');
		$controller = Common::hashEmptyField($params, 'controller');
		$action = Common::hashEmptyField($params, 'action');
		$current_url = $this->Html->url(array(
			'prefix' => $prefix,
			'controller' => $controller,
			'action' => $action,
		));
		
		if(!empty($data_arr)){
			$li = false;
			foreach ($data_arr as $key => $value) {
				$active = false;
				$options = array();
				$allow = Common::hashEmptyField($value, 'allow', 'acl', array(
					'isset' => true,
				));
				$forbidden_allow = Common::hashEmptyField($value, 'forbidden_allow', true, array(
					'isset' => true,
				));
				$class = Common::hashEmptyField($value, 'class');

				$childs = Common::hashEmptyField($value, 'child'); 

				if($childs){

					$data_submenu = Common::hashEmptyField($value, 'data_submenu');
					$options = array(
						'data-submenu' => $data_submenu
					);
					$data = $this->generateMenu($childs, array(
						'data_menu' => $data_submenu,
						'data' => $data,
						'active_menu' => $active_menu,
					));

					$allows = $this->CheckActiveParent($childs);
					$allow = in_array(true, $allows);

					// if($sub_data){
					// 	$sub_data = array_shift($sub_data);
					// 	$data[] = $sub_data;
					// }
				}

				if( $allow && !empty($forbidden_allow) ){
					$icon = Common::hashEmptyField($value, 'icon');
					$name = Common::hashEmptyField($value, 'name');
					$url = Common::hashEmptyField($value, 'url');
					$set_active = Common::hashEmptyField($value, 'active');

					if($icon){
						$icon = $this->Html->tag('span', false, array(
							'class' => $icon
						));
						$name = $icon.$name;
					}
					if( (!empty($active_menu) && !empty($set_active)) &&  ($active_menu == $set_active) ){
						$active = 'menu__link--current';
					}

					$options = array_merge(array(
						'class' => sprintf('menu__link %s %s', $class, $active),
						'escape' => false,
					), $options);

					if($allow === 'acl'){
						$url_secondary = Common::hashEmptyField($value, 'url_secondary');
						$linkView = $this->AclLink->link($name, $url, $options);
						
						if($url_secondary && $linkView === ''){
							$linkView = $this->AclLink->link($name, $url_secondary, $options);
						}
						
					} else {
						$linkView = $this->Html->link($name, $url, $options);;
					}

					if($linkView){
						$li .= $this->Html->tag('li', $linkView, array(
							'class' => 'menu__item',
							'role' => 'menuitem',
						));
					}
				}
			}

			if(!empty($li)){
				$li = $this->Html->tag('ul', $li, array(
					'data-menu' => $data_menu,
					'id' => $data_menu,
					'class' => 'menu__level',
					'tabindex' => '-1',
					'role' => 'menu',
					'aria-label' => $data_menu,
				));
			}
			$data[] = $li;
		}
		return $data;
	}

	function _callLinkLabel ( $label, $url, $options = null, $alert = null ) {
		$link = $this->AclLink->link($label, $url, $options, $alert);

		if( empty($link) ) {
			return $label;
		} else {
			return $link;
		}
	}

	function _callDisplayPhoneNumber( $value, $style = 'default', $options = '' ) {
		$field = Common::hashEmptyField($options, 'field', 'no_hp');
		$icon = Common::hashEmptyField($options, 'icon', $this->icon('fa fa-phone'), array(
			'isset' => true,
		));
		$between = Common::hashEmptyField($options, 'between', '<br>');
		$icon_position = Common::hashEmptyField($options, 'icon_position', 'outside');
		$icon_tag = Common::hashEmptyField($options, 'icon_tag', 'i');

		$no_hp = Common::hashEmptyField($value, $field);
		$no_hp_is_whatsapp = Common::hashEmptyField($value, $field.'_is_whatsapp');
		$result = false;

		if( !empty($icon) ) {
			$icon_wa = $this->icon('rv4-wa', '', $icon_tag);
		} else {
			$icon_wa = false;
		}

		if( substr($no_hp, 0,1) != "+" && substr($no_hp, 0,1) == 0 ) {
			$wa_no_hp = substr_replace($no_hp, '+62', 0, 1);
		} else {
			$wa_no_hp = $no_hp;
		}

		if($no_hp){
			$txt_no_hp = $this->formatThenumber($no_hp);
			$isMobile = Configure::read('Global.Data.MobileDetect.mobile');

			if( !empty($isMobile) ) {
    			$urlWA = 'https://api.whatsapp.com/send?phone='.$wa_no_hp;
			} else {
    			$urlWA = 'https://web.whatsapp.com/send?phone='.$wa_no_hp;
			}

			switch ($style) {
				case 'inline':
		            if( !empty($no_hp_is_whatsapp) ) {
		            	$icon = $icon_wa;
		            	
        				$txt_no_hp = $this->Html->link($icon.' '.$txt_no_hp, 'javascript:void(0);', array(
        					'escape' => false,
        					'class' => 'dropdown-toggle',
        					'data-toggle' => 'dropdown',
        					'aria-haspopup' => 'true',
        					'aria-expanded' => 'true',
    					));
            			$txt_no_hp = '<div class="dropdown inblock">
            				'.$txt_no_hp.'
        					<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu1">'.
        						$this->Html->tag('li', $this->Html->link(__('Calling'), sprintf('tel:%s', $no_hp), array(
	            					'escape' => false,
	            					'class' => 'text-center',
            					))).
            					$this->Html->tag('li', $this->Html->link(__('WhatsApp'), $urlWA, array(
	            					'escape' => false,
	            					'class' => 'text-center',
            					))).
    						'</ul>
						</div>';
		            } else {
		            	$urlPhone = sprintf('tel:%s', $no_hp);
        				$txt_no_hp = $this->Html->link($icon.' '.$txt_no_hp, $urlPhone, array('escape' => false));
		            }

            		$result = $txt_no_hp;
					break;
				
				default:
					switch ($icon_position) {
						case 'inside':
							$result = '';

				            if( !empty($no_hp_is_whatsapp) ) {
		            			$icon = $icon_wa;
	            				$txt_no_hp = $this->Html->link($icon.' '.$txt_no_hp, 'javascript:void(0);', array(
	            					'escape' => false,
	            					'class' => 'dropdown-toggle',
	            					'data-toggle' => 'dropdown',
	            					'aria-haspopup' => 'true',
	            					'aria-expanded' => 'true',
            					));

		            			$txt_no_hp = '<div class="dropdown inblock">
		            				'.$txt_no_hp.'
	            					<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu1">'.
	            						$this->Html->tag('li', $this->Html->link(__('Calling'), sprintf('tel:%s', $no_hp), array(
			            					'escape' => false,
            								'class' => 'text-center',
		            					))).
		            					$this->Html->tag('li', $this->Html->link(__('WhatsApp'), $urlWA, array(
			            					'escape' => false,
            								'class' => 'text-center',
		            					))).
            						'</ul>
        						</div>';
				            } else {
		            			$urlPhone = sprintf('tel:%s', $no_hp);
	            				$txt_no_hp = $this->Html->link($icon.' '.$txt_no_hp, $urlPhone, array('escape' => false));
				            }

		            		$result .= $txt_no_hp;
							break;
						
						default:
							$result = '';

				            if( !empty($no_hp_is_whatsapp) ) {
		            			$icon = $icon_wa;
	            				$txt_no_hp = $this->Html->link($icon.' '.$txt_no_hp, 'javascript:void(0);', array(
	            					'escape' => false,
	            					'class' => 'dropdown-toggle',
	            					'data-toggle' => 'dropdown',
	            					'aria-haspopup' => 'true',
	            					'aria-expanded' => 'true',
            					));

		            			$txt_no_hp = '<div class="dropdown inblock">
		            				'.$txt_no_hp.'
	            					<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu1">'.
	            						$this->Html->tag('li', $this->Html->link(__('Calling'), sprintf('tel:%s', $no_hp), array(
			            					'escape' => false,
			            					'class' => 'text-center',
		            					))).
		            					$this->Html->tag('li', $this->Html->link(__('WhatsApp'), $urlWA, array(
			            					'escape' => false,
			            					'class' => 'text-center',
		            					))).
            						'</ul>
        						</div>';
				            } else {
		            			$urlPhone = sprintf('tel:%s', $no_hp);
		            			$txt_no_hp = $icon.' '.$this->Html->link($txt_no_hp, $urlPhone, array('escape' => false));
				            }

		            		$result .= $txt_no_hp;
							break;
					}
					break;
			}
        }

        return $result;
	}

	function bubleType($type = 'home'){
		$data_arr = array();

		switch ($type) {
			case 'home':
				$data_arr = array(
					array(
						'type' => 'bubble-1',
						'img' => 'bubble/bubblecolor1.png',
						'style' => 'top: -240px; right: -22px;',
						'rellax_speed' => '-7',
					),
					array(
						'type' => 'bubble-2',
						'img' => 'bubble/bubblestroke1.png',
						'style' => 'top: -340px; right: -165px;',
						'rellax_speed' => '-1',
					),
					array(
						'type' => 'bubble-3',
						'img' => 'bubble/bubblecolor2.png',
						'style' => 'top: 100px; left: -27px;',
						'rellax_speed' => '-5.2',
					),
					array(
						'type' => 'bubble-4',
						'img' => 'bubble/bubblestroke2.png',
						'style' => 'top: 104px; left: -391px;',
						'rellax_speed' => '2',
					),
					array(
						'type' => 'bubble-5',
						'img' => 'bubble/bubblecolor3.png',
						'style' => 'top: 575px; left: -359px;',
						'rellax_speed' => '2.3',
					),
					array(
						'type' => 'bubble-6',
						'img' => 'bubble/bubblestroke3.png',
						'style' => 'top: 801px; left: -230px;',
						'rellax_speed' => '2.3',
					),
					array(
						'type' => 'bubble-7',
						'img' => 'bubble/bubblecolor4.png',
						'style' => 'top: 1060px; right: -274px;',
						'rellax_speed' => '-3',
					),
					array(
						'type' => 'bubble-8',
						'img' => 'bubble/bubblestroke4.png',
						'style' => 'top: 1358px; right: -243px;',
						'rellax_speed' => '.1',
					),
					array(
						'type' => 'bubble-9',
						'img' => 'bubble/bubblecolor5.png',
						'style' => 'top: 2439px; left: 180px;',
						'rellax_speed' => '1.2',
					),
					array(
						'type' => 'bubble-10',
						'img' => 'bubble/bubblecolor6.png',
						'style' => 'top: 2400px; left: 145px;',
						'rellax_speed' => '.3',
					),
				);
				break;

			case 'about':
				$data_arr = array(
					array(
						'type' => 'bubble-1',
						'img' => 'aboutbubble/bubblebg1.png',
						'style' => 'top: -17px; right: -263px;',
						'rellax_speed' => '-3.4',
					),
					array(
						'type' => 'bubble-2',
						'img' => 'aboutbubble/bubbleborder1.png',
						'style' => 'top: 100px; right: 122px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-3',
						'img' => 'aboutbubble/bubblebg2.png',
						'style' => 'top: 860px; left: -283px;',
						'rellax_speed' => '3.1',
					),
					array(
						'type' => 'bubble-4',
						'img' => 'aboutbubble/bubbleborder2.png',
						'style' => 'top: 620px; left: -460px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-5',
						'img' => 'aboutbubble/bubblebg3.png',
						'style' => 'top: 1121px; right: -84px;',
						'rellax_speed' => '-2',
					),
					array(
						'type' => 'bubble-6',
						'img' => 'aboutbubble/bubbleborder3.png',
						'style' => 'top: 1262px; right: 16px;',
						'rellax_speed' => '0',
					),
				);
				break;

			case 'feature':
				$data_arr = array(
					array(
						'type' => 'bubble-3',
						'img' => 'aboutbubble/bubblebg2.png',
						'style' => 'top: 144px; left: -323px;',
						'rellax_speed' => '-6.4',
					),
					array(
						'type' => 'bubble-4',
						'img' => 'aboutbubble/bubbleborder2.png',
						'style' => 'top: 60px; left: -500px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-5',
						'img' => 'aboutbubble/bubblebg3.png',
						'style' => 'top: 1299px; right: -84px;',
						'rellax_speed' => '-3.6',
					),
					array(
						'type' => 'bubble-6',
						'img' => 'aboutbubble/bubbleborder3.png',
						'style' => 'top: 1262px; right: 16px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-7',
						'img' => 'bubble3/bubblebg1.png',
						'style' => 'top: 2350px; left: 384px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-8',
						'img' => 'bubble3/bubblebg2.png',
						'style' => 'top: 3450px; right: 4px;',
						'rellax_speed' => '1.4',
					),
					array(
						'type' => 'bubble-9',
						'img' => 'bubble3/bubbleborder1.png',
						'style' => 'top: 3452px; right: 16px;',
						'rellax_speed' => '0',
					),
				);
				break;

			case 'price':
				$data_arr = array(
					// array(
					// 	'type' => 'bubble-3',
					// 	'img' => 'pricing/bubblebg1.png',
					// 	'style' => 'top: 457px; right: -218px;',
					// 	'rellax_speed' => '-7',
					// ),
					array(
						'type' => 'bubble-4',
						'img' => 'pricing/bubblebg2.png',
						'style' => 'top: 700px; right: -35px;',
						'rellax_speed' => '-4',
					),
					array(
						'type' => 'bubble-5',
						'img' => 'pricing/bubblebg3.png',
						'style' => 'top: 230px; left: -361px;',
						'rellax_speed' => '5',
					),
					array(
						'type' => 'bubble-6',
						'img' => 'pricing/bubbleborder1.png',
						'style' => 'top: 656px; right: -15px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-7',
						'img' => 'pricing/bubbleborder2.png',
						'style' => 'top: 444px; left: -129px;',
						'rellax_speed' => '0',
					),
				);
				break;

			case 'register':
				$data_arr = array(
					array(
						'type' => 'bubble-3',
						'img' => 'pricing/bubblebg1.png',
						'style' => 'top: 457px; right: -218px;',
						'rellax_speed' => '-5',
					),
					array(
						'type' => 'bubble-4',
						'img' => 'pricing/bubblebg2.png',
						'style' => 'top: 700px; right: -35px;',
						'rellax_speed' => '-2',
					),
					array(
						'type' => 'bubble-5',
						'img' => 'pricing/bubblebg3.png',
						'style' => 'top:-30px; left: -361px;',
						'rellax_speed' => '-7',
					),
					array(
						'type' => 'bubble-6',
						'img' => 'pricing/bubbleborder1.png',
						'style' => 'top: 656px; right: -15px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-7',
						'img' => 'pricing/bubbleborder2.png',
						'style' => 'top: 444px; left: -129px;',
						'rellax_speed' => '0',
					),
				);
				break;

			case 'payment':
				$data_arr = array(
					array(
						'type' => 'bubble-3',
						'img' => 'pricing/bubblebg1.png',
						'style' => 'top: 457px; right: -218px;',
						'rellax_speed' => '-7',
					),
					array(
						'type' => 'bubble-4',
						'img' => 'pricing/bubblebg2.png',
						'style' => 'top: 700px; right: -35px;',
						'rellax_speed' => '2',
					),
					array(
						'type' => 'bubble-5',
						'img' => 'pricing/bubblebg3.png',
						'style' => 'top: 230px; left: -361px;',
						'rellax_speed' => '-4',
					),
					array(
						'type' => 'bubble-6',
						'img' => 'pricing/bubbleborder1.png',
						'style' => 'top: 656px; right: -15px;',
						'rellax_speed' => '0',
					),
					array(
						'type' => 'bubble-7',
						'img' => 'pricing/bubbleborder2.png',
						'style' => 'top: 444px; left: -129px;',
						'rellax_speed' => '0',
					),
				);
				break;
		}
		return $data_arr;
	}

	/*
		$type berisi nilai device dan type
		- devive : android, iphone, dan ipad
		- type : soft, recommendation, dan urgent
	*/
	function mobileAppVersionConfig($type = 'device', $value = false, $empty = 'N/A'){
		$device = array(
			'android' => 'android', 
			'iphone' => 'iphone', 
			'ipad' => 'ipad'
		);

		$types = array(
			'soft' => 'soft', 
			'recommendation' => 'recommendation', 
			'urgent' => 'urgent'
		);

		if(!empty($value)){
			if($type == 'device'){
				return Common::hashEmptyField($device, $value, $empty);
			}else if($type == 'type'){
				return Common::hashEmptyField($types, $value, $empty);
			}
		}else{
			if($type == 'device'){
				return $device;
			}else if($type == 'type'){
				return $types;
			}
		}
	}

	function limitCharMore($title, $limit = 50, $more = '...', $tooltip = true, $bold = false, $plugin = true){
		$result = false;

		if(!empty($title)){
			$count = strlen($title);
			$text = ($count > $limit) ? $more : false;

            $result = substr($title, 0, $limit).$text;

            if($tooltip && $text){

            	if($plugin){
	            	$bold = !empty($bold) ? 'tooltip-sign' : false;
	            	$result = $this->Html->tag('span', $result, array(
	            		'class' => $bold,
	            		'data-toggle' => 'tooltip',
	            		'data-placement' => 'top',
	            		'data-original-title' => $title,
	            	));
            	} else {
            		$result = $this->Html->tag('span', $result, array(
            			'title' => $title,
	            	));
            	}

            }
		}
		return $result;
	}

	function _callGenerateUrlChart ( $url, $period = null ) {
		$params = Common::hashEmptyField($this->params->params, 'named');
		$params = Common::_callUnset($params, array(
			'period',
			'autoload',
		));

		if( !empty($params) ) {
			$url = array_merge($url, $params);
		} else if( !empty($period) ) {
			$url['period'] = $period;
		}

		return $url;
	}

	function _callIndicatorArrow( $percentage, $with_result = true ){
		$result = false;

		if( $percentage > 0 ) {
			$result = 'arrow-up arrow-green';
		} else if( $percentage < 0 ) {
			$result = 'arrow-down arrow-red';
		}

		if( !empty($result) ) {
			$result = $this->Html->tag('div', '', array(
				'class' => 'indicator disinblock margin-left-2 margin-right-1 '.$result,
			));

			if(!empty($with_result)){
				$result .= $this->Html->tag('div', __('%s%', abs($percentage)), array(
					'class' => 'percent disinblock',
				));
			}
		}

		return $result;
	}

	function dataBar($persentase, $position_percentage = false){
		$content = $this->Html->div('current blue-bar', '', array(
			'style' => __('width: %s%%;', $persentase)
		));
		
		$content = $this->Html->div('data-bar', $this->Html->div('total', '').$content);

		if(!empty($position_percentage) && in_array($position_percentage, array('top', 'middle'))){
			$persentase = number_format($persentase, 2);
			
			if($position_percentage == 'top'){
				$temp_content = $this->Html->tag('span', __('%s%%', $persentase), array(
					'class' => 'disblock align-right margin-bottom-1'
				)).$content;
			}else{
				$temp_content = $content.$this->Html->tag('span', __('%s%%', $persentase), array(
					'class' => 'data-bar-percentage-label'
				));
			}
			
			$content = $this->Html->div('clearfix data-bar-box-'.$position_percentage, $temp_content);
		}

		return $content;
	}

	function ranking($value){
		$score = array('F', 'E', 'D', 'C', 'B', 'A');

		if($value > 100){
			$value = 5;
		}else if($value < 0){
			$value = 0;
		}else{
			$value = $value/16;
			if($value != 0){
				if(round($value) == 0){
					$value = 0;
				}else{
					$value = round($value)-1;
				}
			}else{
				$value = 0;
			}
		}

		if(in_array($value, array(5,4))){
			$class = 'cgreen';
		}else if(in_array($value, array(3,2))){
			$class = 'cyellow';
		}else{
			$class = 'cred';
		}

		$result = Common::hashEmptyField($score, $value, 0);

		return $this->Html->tag('span', $result, array(
			'class' => 'bold '.$class
		));
	}

	/*
		- allow : ini digunakan untuk mempersilahkan link untuk melewati acl, tapi pastikan Auth harus di allow juga
	*/
	function _callDropdownAction ( $values ) {
		$result = null;

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$text = Common::hashEmptyField($value, 'text');
				$url = Common::hashEmptyField($value, 'url');
				$allow = Common::hashEmptyField($value, 'allow');
				$_btnProcess = Common::hashEmptyField($value, '_btnProcess');
				$status_request = Common::hashEmptyField($value, '_checkStatus');

				$options = Common::hashEmptyField($value, 'options', array(
					'escape' => false,
				));

				if(empty($options['class'])){
					$options['class'] = 'align-left';
				}

				$message = Common::hashEmptyField($value, 'message');

				$link = $this->AclLink->link($text, $url, $options, $message);

				if(!empty($link)){
					$in_allow = true;
				}else if(!empty($allow)){
					$link = $this->Html->link($text, $url, $options, $message);
					$in_allow = true;
				}else{
					$in_allow = false;
				}

				if(!empty($in_allow)){
					if (!empty($_btnProcess)) {
						if ($status_request == 'pending') {
							$result .= $this->Html->tag('li', $link);
						} else {
							$result .= $this->Html->tag('li', __(''));
						}
					} else {
						$result .= $this->Html->tag('li', $link);
					}
				}
			}

			if(!empty($result)){
				$result = $this->Html->tag('div', 
						$this->Html->tag('button', $this->icon('prm-ellipsis-horizontal'), array(
							'class' => 'drop-toggle cgray2',
							'id' => 'drop',
							'data-toggle' => 'dropdown',
							'aria-hashpopup' => 'true',
							'aria-expanded' => 'false',
						)).
						$this->Html->tag('div', $this->Html->tag('ul', $result), array(
							'class' => 'drop-table-content',
							'aria-labelledby' => 'drop',
						)), 
						 array(
					'class' => 'drop-table-menu',
				));
			}else{
				$result = 'No Action';
			}
				
		}

		return $result;
	}

	function generateTabs($data_arr){
		$list = array();

		if(!empty($data_arr)){
			foreach ($data_arr as $label => $value) {
				$url 		= Common::hashEmptyField($value, 'url');
				$attribute 	= Common::hashEmptyField($value, 'attribute', array());
				$active 	= Common::hashEmptyField($value, 'active', '');

				if(!empty($active)){
					$active = 'active';
				}

				$link_options = $attribute;

				$link_options['class'] .= ' '.$active;

				$list[] = $this->Html->tag('li', $this->Html->link($label, $url, $link_options));
			}
		}

		$list = implode('', $list);

		return $this->Html->div('tab', $this->Html->tag('ul', $list));
	}

//	ga bisa minify langsung semua, ada pengaruh urutan include
//	makanya dibuatin logic ini supaya urutan include ga rusak
	public function loadSource($sources = NULL, $type = 'css', $minify = true, $options = array()){
		if($sources){
			$sources	= is_array($sources) ? $sources : array($sources);
			$type		= in_array($type, array('script', 'css')) ? $type : 'css';
			$options	= $type == 'css' ? null : $options;
			$helper		= $minify ? 'Minify' : 'Html';
			$queue		= array();

			foreach($sources as $key => $source){
				$hostName	= Common::hashEmptyField(parse_url($source), 'host');
				$isExternal	= $hostName && $hostName != $this->params->host();

				if($isExternal){
					if($queue){
						echo($this->$helper->$type($queue, $options).PHP_EOL);
						$queue = array(); // langsung reset setelah di print
					}

					echo($this->Html->$type($source, $options).PHP_EOL); // untuk external langsung tarik
				}
				else{
					$queue[] = $source; // internal bisa di minify, jadi tampung dulu
				}
			}

			if($queue){
				echo($this->$helper->$type($queue, $options).PHP_EOL); // sisa queue
			}
		}
	}

	public function popover($title = false, $content = false, $options = array()){
		$popover = false;

		if($content){
			$options = (array) $options;
			$options = array_replace(array(
				'class'					=> 'static-modal', 
				'data-placement'		=> 'auto', 
				'data-toggle'			=> 'popover', 
				'data-original-title'	=> $title, 
				'data-content'			=> $content, 
			), $options);

			$icon		= Common::hashEmptyField($options, 'icon', $this->icon('rv4-shortip'));
			$options	= Hash::remove($options, 'icon');

		//	generate
			$popover = $this->Html->link($icon, 'javascript:void(0);', array_merge($options, array('escape' => false)));
			$popover = $this->Html->tag('span', $popover, array('class' => 'notice-static-modal no-padding'));
		}

		return $popover;
	}
	function pagingToNumberPage($data){
		$page_prev 	= Common::hashEmptyField($data, 'page_prev');
		$page_next 	= Common::hashEmptyField($data, 'page_next');

		$page_first = Common::hashEmptyField($data, 'page_first');
		$page_last 	= Common::hashEmptyField($data, 'page_last');

		if(!empty($page_prev)){
			$page_prev = $this->_splitPage($page_prev, 1);
		}

		if(!empty($page_next)){
			$page_next = $this->_splitPage($page_next);
		}

		if(!empty($page_first)){
			$page_first = $this->_splitPage($page_first);
		}

		if(!empty($page_last)){
			$page_last = $this->_splitPage($page_last);
		}

		return array(
			'page_first' 	=> $page_first,
            'page_prev' 	=> $page_prev,
            'page_next' 	=> $page_next,
            'page_last' 	=> $page_last,
		);
	}

	function paginateAPI($url = array()){
		$data 			= Configure::read('Config.PaginateApi');

		if(!is_array($url)){
			$url = explode('/', $raw_url);
		}

		$raw_url 		= $url;

		$paging			= Common::hashEmptyField($data, 'paging');
		$paging_list 	= Common::hashEmptyField($paging, 'paging_list');

		$page_config	= $this->pagingToNumberPage($paging);

		$page_prev 		= Common::hashEmptyField($page_config, 'page_prev');
		$page_next 		= Common::hashEmptyField($page_config, 'page_next');
		$page_first 	= Common::hashEmptyField($page_config, 'page_first');
		$page_last 		= Common::hashEmptyField($page_config, 'page_last');

		$paging = '';
		if(!empty($paging_list)){

			if(!empty($page_first)){
				$url_temp = $raw_url;
				$url_temp['page'] = $page_first;

				$link_paging = $this->Html->link('«', $url_temp, array(
					'class' 				=> 'ajax-link',
					'data-wrapper-write'	=> '.table-content-booking',
					'data-show-loading-bar'	=> 'true'
				));

				$paging .= $this->Html->tag('li', $link_paging);
			}
			if(!empty($page_prev)){
				$url_temp = $raw_url;
				$url_temp['page'] = $page_prev;

				$link_paging = $this->Html->link('‹', $url_temp, array(
					'class' 				=> 'ajax-link',
					'data-wrapper-write'	=> '.table-content-booking',
					'data-show-loading-bar'	=> 'true'
				));

				$paging .= $this->Html->tag('li', $link_paging);
			}

			foreach ($paging_list as $key => $value) {
				$page 	= Common::hashEmptyField($value, 'page');
				$url 	= Common::hashEmptyField($value, 'url');

				$class_link = $class = '';
				if(empty($url)){
					$class = 'current';
					$url_temp = 'javascript:void(0)';
				}else{
					$class_link = 'ajax-link';
					
					$url_temp = $raw_url;
					$url_temp['page'] = $page;
				}

				$link_paging = $this->Html->link($page, $url_temp, array(
					'class' 				=> $class_link,
					'data-wrapper-write'	=> '.table-content-booking',
					'data-show-loading-bar'	=> 'true'
				));

				$paging .= $this->Html->tag('li', $link_paging, array(
					'class' => $class,
				));
			}

			if(!empty($page_next)){
				$url_temp = $raw_url;
				$url_temp['page'] = $page_next;

				$link_paging = $this->Html->link('›', $url_temp, array(
					'class' 				=> 'ajax-link',
					'data-wrapper-write'	=> '.table-content-booking',
					'data-show-loading-bar'	=> 'true'
				));

				$paging .= $this->Html->tag('li', $link_paging);
			}

			if(!empty($page_last)){
				$url_temp = $raw_url;
				$url_temp['page'] = $page_last;

				$link_paging = $this->Html->link('»', $url_temp, array(
					'class' 				=> 'ajax-link',
					'data-wrapper-write'	=> '.table-content-booking',
					'data-show-loading-bar'	=> 'true'
				));

				$paging .= $this->Html->tag('li', $link_paging);
			}

			if(!empty($paging)){
				$paging = $this->Html->tag('ul', $paging, array(
					'class' => 'pageList left-box'
				));
			}
		}

		return $paging;
	}

	private function _splitPage($string, $empty = false){
		$repl = str_replace('.json', '', $string);
		$expl = explode('page:', $repl);

		return Common::hashEmptyField($expl, '1', $empty);
	}

	function setDescription($data, $options = array()){
		$limit = $this->filterIssetField($options, 'limit', false, 100);
		$max_height = $this->filterIssetField($options, 'max_height', false, '220px');
		$url = $this->filterIssetField($options, 'url', false);
		$class = $this->filterIssetField($options, 'class', false, 'text-editor');
		$is_always_open = $this->filterIssetField($options, 'is_always_open', false);
		$eol = $this->filterIssetField($options, 'EOL', false, true);
		$open_more_link_class = $this->filterIssetField($options, 'open_more_link_class', false, 'text-left');

		if(!empty($url) && is_array($url)){
			$url = $this->Html->url($url);
		}

		if(!empty($data)){
			$temp_char = $this->safeTagPrint($data);

			if( !empty($eol) ) {
				$data = str_replace(PHP_EOL, '<br>', $data);
			}

			$count_char = strlen($temp_char);

			$data = $this->Html->div('text', $data);

			$style = false;
			if(!$is_always_open && $count_char > $limit){
				$class .= ' text-load-more';
				$data .= $this->Html->div('text-editor-hide', '');
				
				if(!empty($max_height)){
					$style = sprintf('max-height:%s;', $max_height);
				}
			}else if($is_always_open){
				$class .= ' text-load-more-is-open';
			}

			$data = $this->Html->div($class, $data, array(
				'style' => $style
			));

			if(!$is_always_open && $count_char > $limit){
				$icon = $this->icon('rku-angle-bottom');

				$link_more = $this->Html->link(sprintf(__('%s Selengkapnya'), $icon), 'javascript:', array(
					'class' => 'open-more',
					'escape' => false,
					'data-url' => $url,
				));

				$data .= $this->Html->div($open_more_link_class, $link_more);
			}
		}else{
			$data = '';
		}

		return $this->Html->div('open-more-box', $data);
	}

	function getPricePaymentMethod($val_payment, $currency_options = array('places' => 2, 'thousands' => '.')){
		$title = Common::hashEmptyField($val_payment, 'ProductPaymentMethod.title');
		$id = Common::hashEmptyField($val_payment, 'ProductPaymentMethod.id');
		$description = Common::hashEmptyField($val_payment, 'ProductPaymentMethod.description');
		$short_description = Common::hashEmptyField($val_payment, 'ProductPaymentMethod.short_description');

		$price 			= Common::hashEmptyField($val_payment, 'ProductPaymentMethod.price');
		$currency_alias	= Common::hashEmptyField($val_payment, 'Currency.alias');
		
		$master_payment_method_id = Common::hashEmptyField($val_payment, 'ProductPaymentMethod.master_payment_method_id');
		
		$rate 					= Common::hashEmptyField($val_payment, 'ProductPaymentMethod.rate');
		$period_installement 	= Common::hashEmptyField($val_payment, 'ProductPaymentMethod.period_installement');
		$is_dp 	= Common::hashEmptyField($val_payment, 'ProductPaymentMethod.is_dp');
		$type_down_payment 	= Common::hashEmptyField($val_payment, 'ProductPaymentMethod.type_down_payment');
		$down_payment 	= Common::hashEmptyField($val_payment, 'ProductPaymentMethod.down_payment');
		$down_payment_price = Common::hashEmptyField($val_payment, 'ProductPaymentMethod.down_payment_price');

		$percentage = 0;
		$total_loan = 0;
		if(in_array($master_payment_method_id, array(2,3))){
			$allow_dp = false;

			if(($master_payment_method_id == 2 && !empty($is_dp)) || $master_payment_method_id == 3){
				$allow_dp = true;
			}

			if($allow_dp){
				if($type_down_payment == 'percentage'){
					$percentage = $down_payment;

					$total_down_payment = ($price * $down_payment) / 100;

					$total_loan = $price - $total_down_payment;

					$total_down_payment = $this->Number->currency($total_down_payment, $currency_alias.' ', $currency_options);

					$down_payment = $total_down_payment;
				}else{
					$down_payment = $this->Number->currency($down_payment_price, $currency_alias.' ', $currency_options);

					$total_loan = $price - $down_payment_price;
				}
			}else{
				$down_payment = 0;
			}
		}

		if($master_payment_method_id == 3){
			$creditFix = $this->Kpr->creditFix($total_loan, $rate, $period_installement);

			$creditFix = $this->Number->currency($creditFix, $currency_alias.' ', $currency_options);
		}else{
			$creditFix = 0;
		}

		$price = $this->getCurrencyPrice($price, 'N/A', $currency_alias.' ', 2);

		return array(
			'price' => $price,
			'down_payment' => $down_payment,
			'percentage' => $percentage,
			'creditFix' => $creditFix
		);
	}

	function getVoucherMessage($add_class = 'padding-top-2'){
		$data = Configure::read('__Site.data_voucher');

		$message = Common::hashEmptyField($data, 'msg');

		$result = '';
		if(!empty($message)){
			$result = $this->Html->div('success-message '.$add_class, $message);
		}

		return $result;
	}

	function _callStatusBooking ( $status, $options = array() ) {
		$status = !empty($status)?$status:0;
		
		$isRequest = Common::hashEmptyField($options, 'is_request');
		$isRaw = Common::hashEmptyField($options, 'is_raw');

		$options = Hash::remove($options, 'is_request');

		$options = array_replace(array(
			0 => array(
				'text' => 'Non-Aktif', 
				'class' => 'badge-red', 
			), 
			1 => array(
				'text' => 'Aktif', 
				'class' => 'badge-green', 
			),
			'rejected' => array(
				'text' => 'Rejected', 
				'class' => 'badge-red', 
			),
			'approved' => array(
				'text' => 'Approved', 
				'class' => 'badge-green', 
			),
			'pending' => array(
				'text' => 'Pending', 
				'class' => 'badge-yellow', 
			),
			'waiting' => array(
				'text' => 'Menunggu Pembayaran', 
				'class' => 'badge-brown', 
			), 
			'cancelled' => array(
				'text' => 'Dibatalkan', 
				'class' => sprintf('badge-%s', $isRequest ? 'red' : 'black'), 
			), 
			'expired' => array(
				'text' => 'Kadaluarsa', 
				'class' => 'badge-red', 
			), 
			'rejected' => array(
				'text' => 'Ditolak', 
				'class' => 'badge-red', 
			), 
			'refund' => array(
				'text' => 'Refund', 
				'class' => 'badge-red', 
			), 
			'completed' => array(
				'text' => 'Completed', 
				'class' => 'badge-blue', 
			),
			'pending_confirmation' => array(
				'text' => 'Menunggu Konfirmasi', 
				'class' => 'badge-blue', 
			),
			'paid' => array(
				'text' => 'Lunas', 
				'class' => 'badge-green', 
			), 
			'renewal' => array(
				'text' => 'Renewal', 
				'class' => 'badge-blue', 
			), 
			'new' => array(
				'text' => 'Baru', 
				'class' => 'badge-blue', 
			), 
		), $options);

		$text = Common::hashEmptyField($options, sprintf('%s.text', $status));
		$class = Common::hashEmptyField($options, sprintf('%s.class', $status));
		
		$options = Common::hashEmptyField($options, (int)$status);
		$options = Common::_callUnset($options, array(
			'text',
			'class',
		));

		if($isRaw){
			return array(
				'class' => $class,
				'text' => $text
			);
		}else{
			return $this->badge($class, $text, $options);	
		}
	}

	public function badge($class = 'badge-gray2', $text = null, $options = array()){
		// $availableClasses = array(
		// 	'badge-gray2', 
		// 	'badge-blue', 
		// 	'badge-red', 
		// 	'badge-yellow', 
		// 	'badge-green', 
		// );

		$badge = false;
		$options = is_array($options) ? $options : array();

		$link = Common::hashEmptyField($options, 'link');
		$div = Common::hashEmptyField($options, 'div', true, array(
			'isset' => true,
		));
		$options = Common::_callUnset($options, array(
			'link',
			'div',
		));
		
		// if($class && in_array($class, $availableClasses)){
			$badge = $this->Html->tag('div', '', array(
				'class' => sprintf('data-badge %s', $class), 
			));

			if( !empty($text) ){
				if( is_array($text) ) {
					$text_class = Common::hashEmptyField($text, 'class');
					$text = Common::hashEmptyField($text, 'text');
				} else {
					$text_class = null;
				}

				$badge.= $this->Html->tag('span', $text, array(
					'class' => $text_class,
				));
			}

			if( !empty($link) ) {
				$url = Common::hashEmptyField($link, 'url');
				$class = Common::hashEmptyField($link, 'class');

				$badge = $this->Html->link($badge, $url, array(
					'escape' => false,
					'class' => $class,
				));
			}

			if($div){
				$class = Common::hashEmptyField($div, 'class', 'data-status');

				$badge = $this->Html->tag('div', $badge, array_replace(array(
					'class' => $class, 
				), $options));
			}
		// }

		return $badge;
	}

	// call spec feature membership RKU
	function callFeatureMembership($value){
		$result 	= '-';
		$features 	= Common::hashEmptyField($value, 'MembershipPackage.features');

		if (!empty($features)) {
			$list_feature = $this->Html->tag('li', __('Feature :'));
			foreach ($features as $key => $spec_feature) {
				$txt_val 	  = ''; 
				$feature_name = Common::hashEmptyField($spec_feature, 'MembershipPackageFeature.name');
				$field_type   = Common::hashEmptyField($spec_feature, 'MembershipPackageFeature.field_type');
				
				if ($field_type == 'freetext') {
					$txt_value = Common::hashEmptyField($spec_feature, 'MembershipPackageFeatureDetail.value');
					$txt_val   = sprintf('(%s)',$txt_value);
				}

				$list_feature .= $this->Html->tag('li', sprintf('%s %s', $feature_name, $txt_val));
			}

			return $this->Html->tag('ul', $list_feature, array(
				'class' => 'list-package-feature'
			));
		}

		return $result;
	}

	function getTextDay($value){
		$textdays = Configure::read('__Site.textDays');

		return !empty($textdays[$value]) ? $textdays[$value] : false;
	}

	function getSubCategoryName($value){
		$result = false;

		if (!empty($value)) {
			$id_rule_cat = Common::hashEmptyField($value, 'Rule.rule_category_id');
      		$id_root_cat = Common::hashEmptyField($value, 'Rule.root_category_id');
			if ($id_rule_cat == $id_root_cat) {
            	$result = '-';
            } else {
				$result = Common::hashEmptyField($value, 'RuleCategory.name');
            }
		}

		return $result;
	}

	function _callRankStatus ($rank, $last_rank, $use_status = true) {
		if( ( empty($last_rank) && empty($rank) ) || empty($use_status) ) {
			return '';
		} else if( $last_rank > $rank || (empty($last_rank) && !empty($rank)) ) {
			return $this->Html->image('/img/icons/up.png');
		} else if( $last_rank < $rank ) {
			return $this->Html->image('/img/icons/down.png');
		} else {
			return $this->Html->image('/img/icons/draw.png');
		}
	}

	function pusDiagnosa ($pus, $rujukan) {
		if( $pus > $rujukan ) {
			return __('Baik');
		} else if( $pus == $rujukan ) {
			return __('Normal');
		} else {
			return __('Kurang');
		}
	}

	function generateButtonExport ($export) {
		$export_url = Common::hashEmptyField($export, 'url');
    	$title = Common::hashEmptyField($export, 'title', __('Export'));
    	$alert = Common::hashEmptyField($export, 'alert');
    	$options = Common::hashEmptyField($export, 'options', array());
    	$icon = Common::hashEmptyField($export, 'icon');
    	$export_options = array_merge(array(
			'data-title'	=> $title,
			'class'			=> 'btn blue disinblock floright crumb-export crumb-buton',
			'escape'		=> false,
		), $options);

		$title = $this->icon($icon).$title;

    	if( !empty($export_url) ) {
	    	$exportBtn	= $this->AclLink->link($title, $export_url, $export_options, $alert);
    	} else {
	    	$exportBtn	= $this->Html->link($title, 'javascript:void(0);', array_merge($export_options, array(
				'data-target'	=> 'div#content',
				'role'			=> 'capture-button',
			)), $alert);
    	}

    	return $exportBtn;
	}
}