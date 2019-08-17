<?php 
		$name = $this->Rumahku->filterEmptyField($params, 'name', false, false, true, 'ucwords');

		$with_greet = isset($params['with_greet']) ? $params['with_greet'] : true;

		if($with_greet){
		
			if(!empty($name)) {
				printf(__('Hai,  %s'), $name);
			} else {
				echo __('Hai');
			}

		}
?>