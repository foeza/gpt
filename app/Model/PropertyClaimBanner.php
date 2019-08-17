<?php
class PropertyClaimBanner extends AppModel {
	var $name = 'PropertyClaimBanner';

	function getData( $find = 'all', $options = array(), $is_merge = true, $elements = array() ) {
        $status = isset($elements['status']) ? $elements['status']:'active';
        $type = ( isset($elements['type']) && $elements['type'] == 'banner' ) ? 0: 1;

		$default_options = array(
			'conditions' => array(
				'PropertyClaimBanner.ebrosur' => $type
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(),
		);

        switch ($status) {
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'PropertyClaimBanner.status' => 0,
				));
                break;
            
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'PropertyClaimBanner.status' => 1,
                ));
                break;
        }

		if($is_merge){
			if(!empty($options['conditions'])){
				$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
			}
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }

		}else {
			$default_options = $options;
		}

		if( $find == 'conditions' && !empty($default_options['conditions']) ) {
			$result = $default_options['conditions'];
		} else if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

    function get_total_ebrochure( $agent_id = 0, $type = 'interval', $params = array() ) {
		
		$total_ebrochure = array();
		$default_options = array(
			'conditions' => array(
				'PropertyClaimBanner.user_id' => $agent_id,
			),
			'fields' => array(
				'COUNT(PropertyClaimBanner.id) as total_ebrochure',
			),
			'order' => array(
				'total_ebrochure' => 'DESC',
			),
		);
		
		if( $type == 'interval' ) {
			
			$total_ebrochure = $this->getData('all', $default_options, true);

		} else if ( $type == 'range' ) {

			if( !empty($params) && isset($params['date_from']) && isset($params['date_to']) ){
				
				$date_from = $params['date_from'];
				$date_to = $params['date_to'];

				$default_options['conditions']["DATE_FORMAT(PropertyClaimBanner.created, '%Y-%m-%d') >="] = $date_from;
				$default_options['conditions']["DATE_FORMAT(PropertyClaimBanner.created, '%Y-%m-%d') <="] = $date_to;

				$total_ebrochure = $this->getData('all', $default_options, true);
			}
		}

		return $total_ebrochure;
    }
}
?>