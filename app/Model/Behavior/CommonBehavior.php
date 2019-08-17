<?php
App::uses('ModelBehavior', 'Model');

/**
 * Common Behavior.
 *
 * Enables a model to access some common function for manipulate data
 */
class CommonBehavior extends ModelBehavior {
	public function queryLog(Model $model, $showLast = false){
		$dataSource	= $model->getDataSource();
		$queryLog	= $dataSource->getLog();

		if($showLast){
			$lastQuery	= Hash::get($queryLog, 'log');
			$lastQuery	= $lastQuery ? array_pop($lastQuery) : array();
			$queryLog	= array_replace($queryLog, array(
				'log' => $lastQuery, 
			));
		}

		$time = Hash::get($queryLog, 'time', 0);
		$time = $time ? $time / 1000 : 0;

		$queryLog = array_replace($queryLog, array(
			'time' => sprintf('%s seconds', $time), 
		));

		return $queryLog;
	}

	function toSlug(Model $model, $data, $fields = false, $glue = '-') {
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

	function getSelectList(Model $model, $optionConditions = array(), $fields = array('id', 'name')){
		$data = $model->getData( 'list', array(
			'field' => $fields,
			'conditions' => $optionConditions
		));
		return $data;
	}

	function filterEmptyField(Model $model, $value, $modelName, $fieldName = false, $empty = null, $options = false){
		$type = !empty($options['type'])?$options['type']:'empty';
		$trim = isset($options['trim'])?$options['trim']:true;
		$addslashes = isset($options['addslashes'])?$options['addslashes']:false;
		$result = false;

		switch($type){
			case 'isset':
				if(empty($fieldName) && isset($value[$modelName])){
					$result = $value[$modelName];
				} else {
					$result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
			
			default:
				if(empty($fieldName) && !empty($value[$modelName])){
					$result = $value[$modelName];
				} else {
					$result = !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
		}

		if( !empty($result) && is_string($result) ) {
			$result = urldecode($result);
			$result = urldecode($result);

			if( !empty($trim) ) {
				$result = trim($result);
			}

			if( !empty($addslashes) ) {
				$result = addslashes($result);
			}
		}

		return $result;
	}

	function filterIssetField (Model $model, $value, $modelName, $fieldName = false, $empty = null ) {
		$result = '';
		
		if( empty($modelName) && !is_numeric($modelName) ) {
			$result = isset($value)?$value:$empty;
		} else if( empty($fieldName) && !is_numeric($fieldName) ) {
			$result = isset($value[$modelName])?$value[$modelName]:$empty;
		} else {
			$result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
		}

		return $result;
	}

	function validatePhoneNumber( model $model, $data ) {
        if(!empty($data['phone'])) {
            $phoneNumber = $data['phone'];
        } else if(!empty($data['no_hp'])) {
            $phoneNumber = $data['no_hp'];
        } else if(!empty($data['no_hp_2'])) {
            $phoneNumber = $data['no_hp_2'];
        } else if(!empty($data['client_hp'])) {
            $phoneNumber = $data['client_hp'];
        }

		if( !empty($phoneNumber) ) {
			if (preg_match('/^[0-9]{1,}$/', $phoneNumber)==1 || ( substr($phoneNumber, 0,1)=="+" && preg_match('/^[0-9]{1,}$/', substr($phoneNumber, 1,strlen($phoneNumber)))==1 ))
           		return true; 
		}
        
        return false;
    }

    function isNumber(model $model, $data, $field){
        if(!empty($data[$field]) && is_numeric($data[$field]) && $data[$field] >= 1){
            return true;
        }else{
            return false;
        }
    }

    function getDate ( model $model,  $date, $reverse = false ) {
		$dtString = false;
		$date = trim($date);

		if( !empty($date) && $date != '0000-00-00' ) {
			if($reverse){
				$dtString = date('d/m/Y', strtotime($date));
			}else{
				$dtArr = explode('/', $date);

				if( count($dtArr) == 3 ) {
					$dtString = date('Y-m-d', strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
				} else {
					$dtArr = explode('-', $date);

					if( count($dtArr) == 3 ) {
						$dtString = date('Y-m-d', strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
					}
				}
			}
		}
		
		return $dtString;
	}

	function safeTagPrint(model $model, $string){
		if( is_string($string) ) {
			return strip_tags($string);
		} else {
			return $string;
		}
	}

	function _callPriceConverter (model $model, $price) {
		$price = $this->safeTagPrint($model, $price);
		return trim(str_replace(array( ',', '.', 'Rp ' ), array( '', '', '' ), $price));
	}

    function dataConverter (model $objModel, $data, $fields, $reverse = false ) {
		if( !empty($fields) ) {
			foreach ($fields as $type => $models) {
				switch ($type) {
					case 'date':
						if( !empty($models) ) {
							if( is_array($models) ) {
								foreach ($models as $modelName => $model) {
									if( !empty($model) ) {
										if( is_array($model) ) {
											foreach ($model as $key => $fieldName) {
												if( !empty($data[$modelName][$fieldName]) ) {
													$data[$modelName][$fieldName] = $this->getDate($objModel, $data[$modelName][$fieldName], $reverse);
												}
											}
										} else {
											if( !empty($data[$model]) ) {
												$data[$model] = $this->getDate($objModel, $data[$model], $reverse);
											}
										}
									}
								}
							} else {
								if( !empty($data[$models]) ) {
									$data[$models] = $this->getDate($objModel, $data[$models], $reverse);
								}
							}
						}
						break;
					case 'price':					
						if( !empty($models) ) {
							if( is_array($models) ) {
								foreach ($models as $modelName => $model) {
									if( !empty($model) ) {
										if( is_array($model) ) {
											foreach ($model as $key => $fieldName) {
												if( !empty($data[$modelName][$fieldName]) ) {
													$data[$modelName][$fieldName] = $this->_callPriceConverter($objModel, $data[$modelName][$fieldName], $reverse);
												}
											}
										} else {
											if( !empty($data[$model]) ) {
												$data[$model] = $this->_callPriceConverter($objModel, $data[$model], $reverse);
											}
										}
									}
								}
							} else {
								if( !empty($data[$models]) ) {
									$data[$models] = $this->_callPriceConverter($objModel, $data[$models], $reverse);
								}
							}
						}
						break;
					case 'array_filter':
						if( !empty($models) ) {
							if( is_array($models) ) {
								foreach ($models as $modelName => $model) {
									if( !empty($model) ) {
										if( is_array($model) ) {
											foreach ($model as $key => $fieldName) {
												if( !empty($data[$modelName][$fieldName]) && is_array($data[$modelName][$fieldName]) ) {
													$data[$modelName][$fieldName] = array_filter($data[$modelName][$fieldName]);

													if( empty($data[$modelName][$fieldName]) ) {
														unset($data[$modelName][$fieldName]);
													}
												}
											}
										} else {
											if( !empty($data[$model]) && is_array($data[$model]) ) {
												$data[$model] = array_filter($data[$model]);

												if( empty($data[$model]) ) {
													unset($data[$model]);
												}
											}
										}
									}
								}
							} else {
								if( !empty($data[$models]) && is_array($data[$models]) ) {
									$data[$models] = array_filter($data[$models]);

									if( empty($data[$models]) ) {
										unset($data[$models]);
									}
								}
							}
						}
						break;
					case 'phone':
						if( !empty($models) ) {
							if( is_array($models) ) {
								foreach ($models as $modelName => $model) {
									if( !empty($model) ) {
										if( is_array($model) ) {
											foreach ($model as $key => $fieldName) {
												if( !empty($data[$modelName][$fieldName]) ) {
													$data[$modelName][$fieldName] = str_replace(array( ' ', '-', '.' ), array( '', '', '' ), $data[$modelName][$fieldName]);
												}
											}
										} else {
											if( !empty($data[$model]) ) {
												$data[$model] = str_replace(array( ' ', '-', '.' ), array( '', '', '' ), $data[$model]);
											}
										}
									}
								}
							} else {
								if( !empty($data[$models]) ) {
									$data[$models] = str_replace(array( ' ', '-', '.' ), array( '', '', '' ), $data[$models]);
								}
							}
						}
						break;
				}
			}
		}

		return $data;
	}

	function getData(model $model, $find = 'all', $options = array()){
		$default_options = array(
			'conditions'=> array(),
			'order'=> array(),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

		return $this->merge_options($model, $default_options, $options, $find);
	}

	function getMerge( model $model, $data, $id, $options = array()){
		$modelName = $model->name;
		$find = $model->filterEmptyField($options, 'find', false, 'first'); 
		$fieldName = $model->filterEmptyField($options, 'fieldName', false, 'id');
    	$virtualModel = $model->filterEmptyField($options, 'virtualModel', false, $modelName);	
    	$elements = $model->filterEmptyField($options, 'elements', false, array());	
    	$conditions = $model->filterEmptyField($options, 'conditions', false, array());	
    	$group = $model->filterEmptyField($options, 'group', false, array());	
    	$merge = $model->filterEmptyField($options, 'merge');
    	$order = $model->filterEmptyField($options, 'order', false, array(
    		sprintf('%s.id', $modelName) => 'ASC',
    	));

    	if( empty($data[$virtualModel]) && !empty($id) ){
    		$default_options = array();

			$default_options['conditions'][sprintf('%s.%s', $modelName, $fieldName)] = $id;

			if($conditions){
				$default_options['conditions'] = array_merge($default_options['conditions'], $conditions);
			}

			if($order){
				$default_options['order'] = $order;
			}

			if($group){
				$default_options['group'] = $group;
			}

            $value = $model->getData( $find, $default_options, $elements);
            
            if(!empty($value)){
            	if($virtualModel <> $modelName){
	           		$value_temp = $value;

	           		if($find <> 'count'){
						$modelTemp = $value_temp;
	           		}

	           		unset($value);
	           		$value[$virtualModel] = ($find == 'count') ? $value_temp : $value_temp[$modelName];
	           		unset($value_temp);
	           	}
	           	if(!empty($value[0])){
	           		$value_temp = $value;
	           		unset($value);
	           		$value[$virtualModel] = $value_temp;
	           	}

	           	if(!empty($merge)){
            		$data[$merge] = array_merge($data[$merge], $value);
	           	}else{
            		$data = array_merge($data, $value);	           		
	           	}

            }

        }
        return $data;
	}

	function getList(model $model, $options = array()){
		$modelName = $model->name;
		$customFields = $this->filterEmptyField($model, $options, 'customFields');

		if(empty($customFields)){
			$customFields = array(
				sprintf('%s.id', $modelName),
				sprintf('%s.name', $modelName),
			);
		}
		
		
		$value = $model->getData('list', array(
			'fields' => $customFields,
		));

		return $value;

	}

	function merge_options(model $model, $default_options, $options = array(), $find = null){
		if( !empty($options) ){
			if(!empty($options['conditions'])){
				$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
			}
			if(!empty($options['joins'])){
				$default_options['joins'] = $options['joins'];
			}
			if(isset($options['order'])){
				$default_options['order'] = $options['order'];
			}
			if( isset($options['contain']) && empty($options['contain']) ) {
				$default_options['contain'] = false;
			} else if(!empty($options['contain'])){
				$default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
			}
			if(isset($options['limit'])){
				$default_options['limit'] = $options['limit'];
			}
			if(!empty($options['fields'])){
				$default_options['fields'] = $options['fields'];
			}
			if(!empty($options['group'])){
				$default_options['group'] = $options['group'];
			}
			if(!empty($options['offset'])){
				$default_options['offset'] = $options['offset'];
			}
		}

		if(in_array($find, array('all', 'first', 'count', 'threaded', 'list', 'paginate', 'conditions'))){
			if( $find == 'conditions' && !empty($default_options['conditions']) ) {
				$result = $default_options['conditions'];
			} else if( $find == 'paginate' ) {
				if( empty($default_options['limit']) ) {
					$default_options['limit'] = Configure::read('__Site.config_new_table_pagination');
				}

				$result = $default_options;
			} else {
				$result = $model->find($find, $default_options);
			}

			return $result;
		}
		else{
			return $default_options;
		}
	}

	public function callSet( Model $model, $data, $fieldArr ) {
		if( !empty($fieldArr) && !empty($data) ) {
			$data = array_intersect_key($data, array_flip($fieldArr));
		}
		return $data;
	}

	public function callUnset( Model $model, $data = false, $fieldArr = false ) {
		if( !empty($fieldArr) ) {
			foreach ($fieldArr as $key => $value) {

				if( is_array($value) ) {
					foreach ($value as $idx => $fieldNames) {

						if(is_array($fieldNames)){
							foreach ($fieldNames as $i => $fieldName) {
								$flag = isset($data[$key][$idx][$fieldName]);
								
								if( $flag || ($flag == null)) {
									unset($data[$key][$idx][$fieldName]);
								}	
							}
						}else{
							$flag = isset($data[$key][$fieldNames]);

							if( $flag || ($flag == null)) {
								unset($data[$key][$fieldNames]);
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

	public function _getMerge( model $model, $data, $modelName,  $id = false, $options = array() ) {
		$options = !empty($options)?$options:array();
		$elements = !empty($options['elements'])?$options['elements']:array();
		$cache = !empty($options['cache'])?$options['cache']:array();
		$alias = $this->filterEmptyField($model, $options, 'uses');
		$uses = $this->filterEmptyField($model, $options, 'uses', false, $modelName);
		$foreignKey = !empty($options['foreignKey'])?$options['foreignKey']:'id';
		$primaryKey = !empty($options['primaryKey'])?$options['primaryKey']:$foreignKey;
		$position = !empty($options['position'])?$options['position']:'outside';
		$order = !empty($options['order'])?$options['order']:false;
		$group = !empty($options['group'])?$options['group']:false;
		$type = !empty($options['type'])?$options['type']:'first';
		$grab_parents = Common::hashEmptyField($options, 'grab_parent');
		$parentModelName = $model->name;

		if($grab_parents && is_array($grab_parents) && !empty($grab_parents[0])){
			foreach ($grab_parents as $key => $grab_parent) {
				$field_grab = Common::hashEmptyField($grab_parent, 'fieldName');
				$target_field = Common::hashEmptyField($grab_parent, 'targetField');

				if($field_grab && $target_field){
					$val_grab = Common::hashEmptyField($data, $field_grab);

					if($val_grab){
						$options['conditions'][$target_field] = $val_grab;
					}
				}
			}
		}

		$optionsModel = $this->callSet($model, $options, array(
			'conditions',
			'fields',
			'group',
			'limit',
			'order',
		));

		if(empty($data[$modelName])){

			if(!empty($uses)){
				if( $uses == $model->name ) {
					$model = $model;
				} else {
					$model = empty($model->$uses) ? ClassRegistry::init($uses) : $model->$uses;
				}
			}else{
				$model = empty($model->$modelName) ? ClassRegistry::init($modelName) : $model->$modelName;
			}

			$optionsModel['conditions'][sprintf('%s.%s', $uses, $primaryKey)] = $id;

			if($order){
				$optionsModel['order'] = $order;
			}


			if( !empty($cache) ) {
				$cache_name = $this->filterEmptyField($model, $cache, 'name', false, $modelName);
				$cache_config = $this->filterEmptyField($model, $cache, 'config', false, 'default_master');

				$cache_name = __('%s.%s', $cache_name, $id);
				$value = Cache::read($cache_name, $cache_config);

				if( empty($value) ) {
					$value = $model->getData($type, $optionsModel, $elements);
					
					Cache::write($cache_name, $value, $cache_config);
				}
			} else {
				$value = $model->getData($type, $optionsModel, $elements);
			}

			if(!empty($value)){
				switch ($type) {
					case 'count':
						$data[$modelName] = $value;
						break;
					case 'list':
						$data[$modelName] = $value;
						break;
					
					default:
						if(!empty($alias) ){
							if( !empty($value[$alias]) ) {
								$data[$modelName] = $value[$alias];
							} else if(!empty($value[0])){
								$data[$modelName] = $value;
							}
						}else{
							if(!empty($value[0])){
								$data[$modelName] = $value;
							}else{
								switch ($position) {
									case 'inside':
										if( !empty($parentModelName) ) {
											$parentDataModel = !empty($data[$parentModelName])?$data[$parentModelName]:array();
											$data[$parentModelName] = array_merge($parentDataModel, $value);
										} else {
											$data = array_merge($data, $value);
										}
										break;
									
									default:
										$data = array_merge($data, $value);
										break;
								}
							}
						}
						break;
				}
			}
		}	

		return $data;
	}

	function _callMergeData ( Model $model, $value, $element, $options, $modelName ) {
		$mergeParents = $this->filterEmptyField($model, $element, 'mergeParent', false, array());
		$generateMultiples = $this->filterEmptyField($model, $element, 'generateMultiple', false, array());

		$defaultConditions = array();

		if( !is_array($options) ) {
			$modelName = $uses = $options;
			$optionsParams = array();
		} else {
			$mergeParent = $this->filterEmptyField( $model, $options, 'modelParent');

			$options = $this->callUnset($model, $options, array(
				'modelParent',
			));

			$optionsParams = $options; ## CONDITIONS, ELEMENTS for getData 
			$uses = $this->filterEmptyField($model, $options, 'uses', false, $modelName);

			if( !empty($options) ) {
				$containRecursive = $this->filterEmptyField( $model, $options, 'contain');

				if( empty($containRecursive) && !empty($options[0]) ) {
					$containRecursive = $options;
				}
			}
		}

		$type = $this->filterEmptyField($model, $optionsParams, 'type');
		$forceMerge = $this->filterEmptyField($model, $optionsParams, 'forceMerge');

		if( empty($value[$modelName]) || !empty($forceMerge) ){
			if( !empty($mergeParent) ) {
				$modelParent = $this->filterEmptyField($model, $mergeParent, 0);
				$foreignKey = $this->filterEmptyField($model, $mergeParent, 1);
			} else {
				$modelParent = $model->name;
				$foreignKey = 'id';
				
				$useDefaultConditions = isset($options['useDefaultConditions'])?$options['useDefaultConditions']:true;

				if( !empty($options['foreignKey']) && is_array($options) ) {
					$foreignKey = $options['foreignKey'];

					if( !empty($useDefaultConditions) ) {
						$optionsParams['conditions'] = Common::hashEmptyField($options, 'conditions', $defaultConditions);
					}
				} else if( !empty($model->belongsTo[$uses]['foreignKey']) ) {
					$foreignKey = $model->belongsTo[$uses]['foreignKey'];
					$optionsParams = array_merge($optionsParams, array(
						'foreignKey' => $foreignKey,
						'primaryKey' => 'id',
					));
				} else if( !empty($model->hasOne[$uses]['foreignKey']) ) {
					$foreignKey = 'id';
					$optionsParams = array_merge($optionsParams, array(
						'primaryKey' => $model->hasOne[$uses]['foreignKey'],
						'foreignKey' => $foreignKey,
					));
					
					if( !empty($useDefaultConditions) ) {
						$optionsParams['conditions'] = Common::hashEmptyField($model->hasOne, $uses.'.conditions', $defaultConditions);
					}
				} else if( !empty($model->hasMany[$uses]['foreignKey']) ) {
					$primaryKey = $model->hasMany[$uses]['foreignKey'];
					$optionsParams = array_merge($optionsParams, array(
						'foreignKey' => $foreignKey,
						'primaryKey' => $primaryKey,
					));
					$type_custom = 'all';
				}

				if( empty($type) && !empty($type_custom) ) {
					$optionsParams['type'] = $type_custom;
				}
			}

			if( !empty($value[$modelName]) ) {
				$value = $this->callUnset($model, $value, array(
					$modelName,
				));
			}
			$id = $this->filterEmptyField( $model, $value, $modelParent, $foreignKey);

			if( empty($id) ) {
				$id = $this->filterEmptyField( $model, $value, $foreignKey);
			}

			if( !empty($id) ) {
				## MERGEDATA JIKA DATA YANG INGIN DI MERGE BERSIFAT JAMAK/MULTIPLE 
				## FUNGSI GETMERGE DI MODEL TERSEBUT HARUS DITAMBAHKAN PARAMETER KETIGA FIND = 'ALL/FIRST/ DLL'
				$value = $this->_getMerge( $model, $value, $modelName, $id, $optionsParams);
				## KETIKA SUDAH DI BUILD DENGAN FUNGSI GETMERGE UNTUK DATA JAMAK HARUS
				## MODEL => INDEX => MODEL => VALUE, ANDA BISA UBAH DATA DENGAN generateMultiples ATAU mergeParents

				## KETIKA DATA MULTIPlE SUDAH DIBUILD dengan GENERATEMULTIPLE DIBAWAH INI, MENJADI MODEL => IDX => VALUE
				if(in_array( $modelName, $generateMultiples)){
					if(!empty($value[$modelName])){
						if(!empty($value[$modelName][0])){
							$temp_model = array();
							foreach($value[$modelName] AS $key_multiple => $modelParams){
								$temp_model[$key_multiple] = $modelParams[$modelName];
							}
							$value[$modelName] = $temp_model;
						}
						
					}
				## KETIKA DATA MULTIPlE SUDAH DIBUILD dengan MERGEPARENT DIBAWAH INI, MENJADI PARENTMODEL => MODEL => IDX => VALUE
				}elseif(in_array($modelName,$mergeParents)){

					if(!empty($value[$modelName])){

						if(!empty($value[$modelName][0])){
							$temp_model = array();
							foreach($value[$modelName] AS $key_merge => $modelParams){
								$temp_model[$key_merge] = $modelParams[$contain];
							}
							$value[$this->name][$modelName] = $temp_model;
							unset($value[$modelName]);
						}else{
							$value[$this->name][$modelName] = $value[$contain];
							unset($value[$modelName]);
						}
					}
				}
			}
		}

		if(!empty($containRecursive)){
			$valueTemps = array();

			if( $model->name != $uses ) {
				$model = $model->$uses;
			}
			
			if(!empty($value[$modelName])){
				$valueTemps = $this->getMergeList($model, $value[$modelName], array(
					'contain' => $containRecursive,
				));

				if(!empty($valueTemps)){
					$value = $this->callUnset($model, $value, array(
						$modelName
					));
					$value[$modelName] = $valueTemps;
				}
			}
		}

		return $value;
	}

	public function getMergeList( Model $model, $values, $options, $element = false){
		$contains = $this->filterEmptyField($model, $options, 'contain');

		if(!empty($values)){
			if(!empty($values[0])){
				foreach($values AS $key => $value){
					foreach($contains AS $modelName => $options){
						$value = $this->_callMergeData($model, $value, $element, $options, $modelName);
					}
					$values[$key] = $value;
				}

			}else{
				foreach($contains AS $modelName => $options){
					$values = $this->_callMergeData($model, $values, $element, $options, $modelName);
				}
			}
		}
		return $values;
	}

	public function mergeList( Model $model, $values, $options, $element = false){
		$contains = $this->filterEmptyField($model, $options, 'contain');

		if(!empty($values)){
			if(!empty($values[0])){
				foreach($values AS $key => $value){
					foreach($contains AS $modelName => $options){
						$value = $this->_callMergeData($model, $value, $element, $options, $modelName);
					}
					$values[$key] = $value;
				}

			}else{
				foreach($contains AS $modelName => $options){
					$values = $this->_callMergeData($model, $values, $element, $options, $modelName);
				}
			}
		}
		return $values;
	}

	public function callMergeList( Model $model, $values, $options, $element = false){
		$values = $this->getMergeList($model, $values, $options, $element);

		return $values;
	}

	public function getLastModified(model $model, $conditions = array(), $elements = array(), $params = array()){
		$modelName = $model->name;

		$field = $this->filterEmptyField($model, $params, 'field', false, 'modified');

		$value = $model->getData('first', array(
			'conditions' => $conditions,
			'order' => array(
				$field =>  'DESC'
			),
		), $elements);
		
		$lastModified = $this->filterEmptyField($model, $value, $modelName, $field);
		return $lastModified;
	}

	public function array_random(Model $model, $arr, $num = 1) {
		shuffle($arr);
		
		$r = array();
		for ($i = 0; $i < $num; $i++) {
			$r[] = $arr[$i];
		}
		return $num == 1 ? $r[0] : $r;
	}

	public function createRandomNumber( Model $model, $default= 4, $variable = 'bcdfghjklmnprstvwxyz', $modRndm = 20 ) {
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
		$rand_code = $this->array_random($model, $pass, count($variable));

		return $pass;
	}

	function buildCache ( Model $model, $cache = array(), $options = array() ) {
        if( !empty($cache) ) {
            $cache_name = $this->filterEmptyField($model, $cache, 'name', false, $model->alias);
            $cache_config = $this->filterEmptyField($model, $cache, 'config', false, 'default_master');

            $options['cache'] = $cache_name;
            $options['cacheConfig'] = $cache_config;
        }

        return $options;
	}

	public function validateURL(Model $model, $data = array()){
		$value = array_shift($data);
		if($value){
			$pattern = '/^(?:(?:https?|ftp):\/\/)?(?:[a-z0-9-]+\.)*((?:[a-z0-9-]+\.)[a-z]+)/';

			return preg_match($pattern, $value);
		}
		else{
			return true;
		}
	}

	function callIsDirector (Model $model) {
		$dataCompany = Configure::read('Config.Company.data');
        $group_id = $this->filterEmptyField($model, $dataCompany, 'User', 'group_id');

        if( $group_id == 4 ) {
        	return true;
        } else {
        	return false;
        }
	}

	function callFieldOr($field, $value){
		if( !empty($field['OR']) ) {
			$fieldOr = $field['OR'];
			$fields = array();

			unset($field['OR']);

			foreach ($fieldOr as $key => $fieldName) {
				$fields[sprintf('%s LIKE', $fieldName)] = $value;
			}

			$field['OR'] = $fields;
		}

		return $field;
	}
	
	function typeOptionParams($model, $named, $slug, $option){
		$flag = $code = false;
		$type = Common::hashEmptyField($option, 'type');
		$field = Common::hashEmptyField($option, 'field');
		$value = Common::hashEmptyField($named, $slug, false, array(
        	'addslashes' => true,
        	'urldecode_double' => false,
    	));

		if($value){
			switch ($type) {
				case 'like':
					$value = '%'.$value.'%';

					if( !empty($field['OR']) ) {
						$field = $this->callFieldOr($field, $value);
					} else {
						$field = sprintf('%s LIKE', $field);
					}
					break;

				case 'boolean':

					if($value == 'active'){
						$value = true;
						$flag = true;
					}else if($value == 'inactive'){
						$value = false;
						$flag = true;
					}else{
						$value = false;
					}
					break;

				case 'operator':
					$select_field = Common::hashEmptyField($option, 'select_field');
			    	
			    	switch ($select_field) {
			    		case 'notequal':
			    			$code = '<>';
			    			break;
			    		case 'more':
			    			$code = '>=';
			    			break;
			    		case 'less':
			    			$code = '<=';
			    			break;
			    	}

					break;
				case 'equal':
					if( !empty($field['OR']) ) {
						$field = $this->callFieldOr($field, $value);
					}
					break;
				case 'parent':
					$sourceField = Common::hashEmptyField($option, 'use.sourceField');
					$val = $value;
					$value = $model->getData('list', array(
						'conditions' => array(
							sprintf('%s like', $sourceField) => '%'.$val.'%',
						),
						'fields' => array('id', 'id'),
					));

					if(!empty($val) && empty($value)){
						$value = $model->getData('list', array(
							'fields' => array('id', 'id'),
						));

						$field = sprintf('%s <>', $field);
					}
					break;
			}
		}

		return array_merge($option, array(
			'flag' => $flag,
			'field' => $field,
			'value' => $value,
			'code' => $code,
		));
	}

	function defaultOptionParams(model $model, $data, $default_options = false, $options = array()){
		$modelName = $model->name;
		$named = Common::hashEmptyField($data, 'named');

		if(!empty($options) && !empty($named)){
			foreach ($options as $slug => $option) {
				$contain_arr = Common::hashEmptyField($option, 'contain');
				$type = Common::hashEmptyField($option, 'type');
				$contain = array();

				if($contain_arr){
					if(!is_array($contain_arr)){
						$contain[] = $contain_arr;
					}else{
						$contain = $contain_arr;
					}
				}

		    	$option = $this->typeOptionParams($model, $named, $slug, $option);
				$field = Common::hashEmptyField($option, 'field');
				$code = Common::hashEmptyField($option, 'code');
				$flag = Common::hashEmptyField($option, 'flag');
				$value = Common::hashEmptyField($option, 'value', false, array(
		        	'addslashes' => true,
		        	'urldecode' => false,
		    	));
				$virtualFields = Common::hashEmptyField($option, 'virtualFields');

		    	if($value || (in_array($type, array('boolean')) && !empty($flag))){
		    		if( is_array($field) ) {
		    			$default_options['conditions'][] = $field;
		    		} else {
		    			if($code){
		    				$field = sprintf('%s %s', $field, $code);	
		    			}
		    			$default_options['conditions'][$field] = $value;
		    		}

			    	if($contain){
			    		if(!empty($default_options['contain'])){
			    			$default_options['contain'] = array_merge($default_options['contain'], $contain);
			    		}else{
			    			$default_options['contain'] = $contain;
			    		}
			    	}

			    	if( !empty($virtualFields) ) {
			    		foreach ($virtualFields as $modelName => $virtuals) {
			    			if( !empty($virtuals) ) {
			    				foreach ($virtuals as $vfield => $nvirtual) {
					    			if( $model->name == $modelName ) {
					    				$model->virtualFields[$vfield] = $nvirtual;
					    			} else {
					    				$model->$modelName->virtualFields[$vfield] = $nvirtual;
					    			}
			    				}
			    			}
			    		}
			    	}
		    	}
			}
		}
		return $default_options;
	}

	function callBindHasMany ( model $model, $modelName = null, $options = array(), $foreignKey = 'company_id', $elements = array( 'status' => 'active' ), $optionsModel = array() ) {
		$model->unbindModel(array(
			'hasMany' => array(
				$modelName, 
			), 
		));

		$model->bindModel(array(
			'hasOne' => array(
				$modelName => array(
					'foreignKey' => $foreignKey, 
				), 
			), 
		), false);

		$model->virtualFields[sprintf('%s_count', strtolower($modelName))] = sprintf('COUNT(%s.id)', $modelName);

		$modelOptions = $model->$modelName->getData('paginate', $optionsModel, $elements);
        $options['contain'][$modelName]['conditions'] = Common::hashEmptyField($modelOptions, 'conditions');

        return $options;
	}

    function isValidateNumber(model $model, $data, $field){
        if(!empty($data[$field]) && is_numeric($data[$field]) && $data[$field] >= 1){
            return true;
        }else{
            return false;
        }
    }
	
	function callGenerateUserCode(model $model, $length = 4, $type = 'findByCode') {
		$new_code = '';
		$flag = true;

		while ($flag) {
			$new_code = Common::createRandomNumber($length);
			$rand_code = Common::array_random($new_code, count($new_code));
			$str_code = strtoupper(implode('', $rand_code));
			$check_user = $model->$type($str_code);
			
			if( empty($check_user) ) {
				$flag = false;
			}
		}

		return $str_code;
	}
}
