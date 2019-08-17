<?php 
		$_flash = isset($_flash)?$_flash:true;
		$_flash_error = isset($_flash_error)?$_flash_error:true;

		if( !empty($_flash) ) {
	        echo $this->Html->tag('div', $this->element('blocks/common/flash'), array(
	        	'id' => 'alert-flash-session',
	    	));
	    } else if( !empty($result) ) {
			$status = $this->Rumahku->filterEmptyField($result, 'status');
			$msg = $this->Rumahku->filterEmptyField($result, 'msg');

			if( !empty($_flash_error) && $status == 'error' ) {
		        echo $this->Html->tag('div', $this->element('blocks/common/flash'), array(
		        	'id' => 'alert-flash-session',
		    	));
		    } else {
				echo $this->element('flash_'.$status, array(
					'message' => $msg,
				));
			}
	    }
?>