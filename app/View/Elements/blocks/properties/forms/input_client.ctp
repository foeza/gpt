<?php 
		// search Client
		$action_type = empty($action_type) ? 'properties' : $action_type;
		$is_editable = isset($is_editable) ? $is_editable : true;

		$data = $this->request->data;
		$is_selected = false;
		$client = $this->Rumahku->filterEmptyField($data, 'UserClient');
        $mandatory = $this->Rumahku->_callLblConfigValue('is_mandatory_client', '*');

		if( !empty($client) ) {
			$is_selected = 'selected';
		}

		$autoUrl = $this->Html->url(array(
			'controller' => 'ajax',
			'action' => 'list_users',
			10,
			true,
			'admin' => false,
		));
		$ajax_blur = !isset($ajax_blur)?'ajax-blur':''; 
        
        $client_id = $this->Rumahku->filterEmptyField($data, 'Property', 'client_id');
		$client_type = $this->Rumahku->filterEmptyField($data, 'Property', 'client_type', 'registered');

        $this->request->data['Property']['client_type'] = $client_type;

	//	if( empty($id) ) {
	        if( $client_type == 'registered' && !isset($disabledClient) ) {
	        	$disabledClient = true;
	        } else {
	        	if( $client_type == 'new' ) {
	        		$disabledClient = false;
	        	} else {
	        		$disabledClient = !empty($disabledClient)?$disabledClient:false;
	        	}
	        }

	        echo $this->Rumahku->buildInputRadio('client_type', array(
				'registered' => __('Vendor terdaftar'),
				'new' => __('Vendor Baru'),
			), array(
				'frameClass' => 'col-sm-12',
				'labelClass' => 'col-xl-2 taright col-sm-3',
	            'label' => __('Jenis Vendor'),
				'divClass' => 'client-type trigger-disabled',
				'attributes' => array(
					'data-target' => '#information-client',
					'data-value' => 'registered',
					'data-autocomplete' => '#autocomplete2',
				),
	        ));
	//	}

		$clientModel = Hash::check($data, 'UserClient') ? 'UserClient' : 'Client';

		if( !empty($data[$clientModel]) ) {
			$this->request->data['Property']['client_email'] = Common::hashEmptyField($data, sprintf('%s.email', $clientModel));
		}

		echo $this->Rumahku->buildInputForm('client_email', array(
			'label' => sprintf(__('Email Vendor %s'), $mandatory),
			'data_url' => $autoUrl,
			'id' => 'autocomplete2',
			'type' => 'text',
			'autocomplete' => 'off',
			'labelClass' => 'col-xl-2 taright col-sm-3',
			'class' => 'relative  col-sm-5 col-xl-4',
			'frameClass' => 'col-sm-12',
			// 'inputClass' => $ajax_blur,
			'placeholder' => __('Email Vendor'),
			'attributes' => array(
			    'is_selected' => $is_selected,
				'data-use-current-value' => 'true',
				'data-change' => 'trigger',
				'href' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'get_data_client',
					'action_type' => $action_type,
					'is_editable' => (int) $is_editable, 
					'admin' => false,
				)),
				'data-wrapper-write' => '#information-client'
			),
		));

		echo $this->element('blocks/crm/forms/client_info', array(
			'label_type' => __('Vendor'),
			'action_type' => $action_type,
			'is_editable' => $is_editable, 
			'disabledClient' => !empty($disabledClient)?$disabledClient:false,
			'mandatory' => $mandatory,
		));
?>