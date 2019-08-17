<?php
class ExpertHelper extends AppHelper {
	var $helpers = array(
        'Rumahku', 'Html', 'Crm'
    );

    function getStatus($slug = false, $value = false, $empty = false){
    	if($slug && $value){
	    	switch ($slug) {
	    		case 'count_component':
	    			if($value){
	    				return $this->Html->tag('span', __('Terisi'), array(
	    					'class' => 'badge badge-success',
	    				));
	    			} else {
	    				return $this->Html->tag('span', $empty, array(
	    					'class' => 'badge badge-inverse',
	    				));
	    			}
	    			break;

	    		case 'type':
	    			switch ($value) {
	    				case 'plural':
	    					return __('There is children');

	    				case 'independent':
	    					return __('Stand Alone');
	    			}
	    			break;

	    		case 'periode':
	    			switch ($value) {
	    				case 'daily':
	    					return __('Harian');
	    				
	    				case 'weekly':
	    					return __('Mingguan');

	    				case 'monthly':
	    					return __('Bulanan');

	    				case 'end_periode':
	    					return __('Akhir Periode');
	    			}
	    			break;

	    		case 'calculate_type':
	    			switch ($value) {
	    				case 'range':
	    					return __('Perbandingan');

	    				case 'next_step':
	    					return __('Bertahap');

	    				case 'this_moment':
	    					return __('Poin saat itu juga');
	    			}
	    			break;
	    		case 'schema':
	    			switch ($value) {
	    				case 'accumulation':
	    					return __('Akumulasi');

	    				case 'comparison':
	    					return __('Perbandingan');

	    				case 'direct':
	    					return __('Direct Point');
	    			}
	    	}
    	} else if($slug){
    		if($slug == 'count_component'){
    			return $this->Html->tag('span', $empty, array(
					'class' => 'badge badge-inverse',
				));
    		} else {
    			return $empty;
    		}
    	}
    }

    function getDescription($details, $empty = false, $val = array()){
    	$result = false;
    	if($details){
    		foreach ($details as $key => $detail) {
    			$slug = Common::hashEmptyField($detail, 'ExpertCategoryCompanyDetail.slug');
    			$value = Common::hashEmptyField($detail, 'ExpertCategoryCompanyDetail.value');
    			$value_end = Common::hashEmptyField($detail, 'ExpertCategoryCompanyDetail.value_end');

    			switch ($slug) {
    				case 'day':
    					$temp = $this->Rumahku->getTextDay($value);

    					$result[] = $this->Html->tag('span', strtoupper(__('Hari %s', $temp)));
    					break;

    				case 'ranges':
    				case 'time':
    					if(is_numeric($value)){
    						$value = $this->Rumahku->getFormatPrice($value);
    					}

    					if(is_numeric($value_end)){
    						$value_end = $this->Rumahku->getFormatPrice($value_end);
    					}

    					if($value && $value_end){
    						$temp = sprintf('Diantara %s sampai %s', $value, $value_end);
    					} else 
    					if($value){
    						$temp = sprintf('Dibawah %s', $value);
    					} else 
    					if($value_end){
    						$temp = sprintf('Diatas %s', $value_end);
    					}

    					$result[] = $this->Html->tag('span', $temp);
    					break;

    				// case 'time':
    				// 	$temp = $value;

    				// 	if($value_end){
    				// 		$temp .= " S/D ".$value_end; 
    				// 	}

    				// 	$result[] = $this->Html->tag('span', $temp);
    				// 	break;

    				case 'other':
    					$txt = Common::hashEmptyField($val, 'ExpertCategoryComponent.name');

    					if(is_numeric($value)){
    						$value = $this->Rumahku->getFormatPrice($value);
    					}

    					$result[] = $this->Html->tag('span', __('%s sebesar %s', $txt, $value));
    					break;
    			}
    		}
    	} else {
    		$result[] = '-';
    	}
    	return implode('<br>', $result);
    }

    function getActivityStatus ( $data, $tag = true ) {
        $lblName = Common::hashEmptyField($data, 'ActivityUser.activity_status');

        switch ($lblName) {
            case 'confirm':
                $color ='complete';
                break;
            case 'approved':
                $color ='finalize';
                break;
            case 'canceled':
                $color ='cancel';
                break;
            case 'rejected':
                $lblName = __('Ditolak');
                $color ='hot-prospect';
                break;
            default:
                $color ='prospect';
                break;
        }

        return array(
            'label_name' => $lblName,
            'label_html' => $this->Html->tag('span', $lblName, array(
                'class' => 'label for-project '.$color,
            )),
        );
    }
}