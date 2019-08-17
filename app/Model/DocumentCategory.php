<?php
class DocumentCategory extends AppModel {
    var $name = 'DocumentCategory';
    
    var $hasMany = array(
        'CrmProjectDocument' => array(
            'className' => 'CrmProjectDocument',
            'foreignKey' => 'document_category_id',
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $type = $this->filterEmptyField($elements, 'type');
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'DocumentCategory.order' => 'ASC',
                'DocumentCategory.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($lastupdated) ) {
            $default_options['conditions']['DocumentCategory.modified >'] = $lastupdated;
        }
        if( !empty($type) ) {
            switch ($type) {
                case 'project':
                    $default_options['conditions']['DocumentCategory.is_property'] = 0;
                    break;
                
                default:
                    $default_options['conditions']['DocumentCategory.type'] = $type;
                    break;
            }
        }

        if( !empty($options) ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        }

        $default_options = $this->_callFieldForAPI($find, $default_options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'DocumentCategory.id',
                  'DocumentCategory.name',
                  'DocumentCategory.type',
                );
            }
        }

        return $options;
    }

    function getMerge( $data, $id, $scope = false, $with_contain = true ){
        $options = array(
            'conditions' => array(
                'DocumentCategory.id' => $id,
            ),
        );

        $value = $this->getData('first', $options);

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function getDocument($value, $exclude = array(), $options = array()){
        $option_conditions = array();

        $type_change = !empty($options['type_change'])?$options['type_change']:false;  
        $type_first = !empty($options['type_first'])?$options['type_first']:false;

        $default_options = array(
            'conditions' => array(
                'DocumentCategory.is_required' => 1,
            ),
        );
        if(!empty($exclude)){
            $option_conditions = array(
                'conditions' => array(
                    'DocumentCategory.id <>' => $exclude,
                )
            );
        }

        $documentCategories = $this->getData('all', array_merge_recursive( $option_conditions, $default_options));
        if(in_array($type_change, array('kpr_application'))){
            foreach($documentCategories AS $key => $documentCategori){
                $type = !empty($documentCategori['DocumentCategory']['type'])?$documentCategori['DocumentCategory']['type']:false;
                if($type == $type_first){
                    $documentCategori['DocumentCategory']['type'] = $type_change;
                }
                $documentCategories[$key] = $documentCategori;
            }
        }

        $document_category_id = Set::extract('/DocumentCategory/id', $documentCategories);
        $documentCategories = $this->CrmProjectDocument->getByCategories($documentCategories, $value);
        
        return $documentCategories;
    }
}
?>
