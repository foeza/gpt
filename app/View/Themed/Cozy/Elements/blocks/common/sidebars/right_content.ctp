<?php 
        $action = $this->action;

		$_searchClass = isset($_searchClass) ? $_searchClass : '';
        $with_map = isset($with_map) ? $with_map : true;
        $form_api_code = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'form_api_code', false, false);

        if ($action == 'detail_unit') {
            echo $this->element('blocks/common/sidebars/project_contact');
        }
        
        echo $this->element('blocks/common/sidebars/search', array(
        	'_class' => $_searchClass,
        ));

        echo $this->element('blocks/common/sidebars/properties');

        if ($with_map) {
            echo $this->element('blocks/common/company_map');
        }

        if( !empty($form_api_code) ) {
        	echo $this->Html->tag('div', $form_api_code, array(
                'class' => 'api-wrapper',
            ));
        }
?>
