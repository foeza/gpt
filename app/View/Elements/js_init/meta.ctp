<?php
		$meta_tag = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_tag', false, false);

        if( !empty($meta_tag) ) {
        	echo $meta_tag;
        }
		
		echo($this->element('js_init/google_analytic'));
?>