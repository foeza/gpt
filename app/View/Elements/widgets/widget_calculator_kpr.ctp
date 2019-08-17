<?php
		$value = !empty($value)?$value:false;
        $_action = $this->Rumahku->filterEmptyField($value, 'Property', 'property_action_id');
        $sold = $this->Rumahku->filterEmptyField($value, 'Property', 'sold');
        $_type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'is_building');

        if( $_action == 1 && $_type && empty($sold) ) {  
			if( !empty($bankKpr) ) {
				$bank_code = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'code', 'rumahku');
				$bank_code = strtolower($bank_code);
			} else {
				$bank_code = false;
			}

			if( !empty($bank_code) ) {
				echo $this->element('widgets/kpr/btn');
			}
		}
?>