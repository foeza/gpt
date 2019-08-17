<?php
class Certificate extends AppModel {
	var $name = 'Certificate';
    
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);

        $dataCompany = Configure::read('Config.Company.data');
        $lang = $this->filterEmptyField($dataCompany, 'UserCompanyConfig', 'language', 'id');

        $this->virtualFields['name'] = __('%s.name_%s', $this->alias, $lang);
    }

    function getData( $find='all', $options = array(), $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $property_type_id = isset($elements['property_type_id'])?$elements['property_type_id']:false;
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');

        $dataCompany = Configure::read('Config.Company.data');
        $lang = $this->filterEmptyField($dataCompany, 'UserCompanyConfig', 'language', 'id');

		$default_options = array(
			'conditions'=> array(
                'Certificate.is_lang' => array(
                    'all',
                    $lang,
                ),
            ),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Certificate.modified >'] = $lastupdated;
        }

        switch ($status) {
            case 'all':
                $default_options['conditions']['Certificate.status'] = array(0,1);
                break;

            case 'non-active':
                $default_options['conditions']['Certificate.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Certificate.status'] = 1;
                break;
        }

        if( !empty($property_type_id) ) {
            $default_options['conditions']['Certificate.property_type_id'] = array( 0, $property_type_id );
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'Certificate.id',
                  'Certificate.slug',
                  'Certificate.name',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $modelName = false, $fieldName = 'Certificate.id', $elements = array() ) {
        $empty = $this->filterEmptyField($elements, 'empty');

        if( empty($data['Certificate']) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            $value = $this->getData('first', $options);

            if( !empty($value) ) {
                if( !empty($modelName) ) {
                    $data[$modelName] = array_merge($data[$modelName], $value);
                } else {
                    $data = array_merge($data, $value);
                }
            }
        }

        if( !empty($empty) && empty($data['Certificate']) ) {
            $data['Certificate'] = array();
        }

        return $data;
    }
}
?>