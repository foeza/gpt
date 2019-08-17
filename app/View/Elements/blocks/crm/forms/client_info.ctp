<?php 
	    // Set Build Input Form
		$data = $this->request->data;

	    $action_type = empty($action_type) ? false : $action_type;
		$is_editable = isset($is_editable) ? $is_editable : true;

	    $disabledClient = !empty($disabledClient)?$disabledClient:false;
	    $mandatory = !empty($mandatory)?$mandatory:false;
	    $label_type = !empty($label_type)?$label_type:__('Klien');

	    switch ($action_type) {
			case 'easy_mode':
				$client_type	= false;
				$label_type		= __('Vendor');
				$options		= array(
					'wrapperClass'		=> 'row',
					'frameClass'		=> 'col-sm-12',
					'rowFormClass'		=> 'row',
				//	'formGroupClass'	=> 'form-group', 
					'labelClass'		=> $is_editable ? 'col-sm-4 col-md-3 no-pright' : 'col-xl-2 taright col-sm-3',
					'class'				=> $is_editable ? 'col-sm-8 col-md-4 no-pleft' : 'relative col-sm-3 col-xl-4',
				//	'inputClass'		=> 'input-sm', 
				);

				$clientModel = Hash::check($data, 'UserClient') ? 'UserClient' : 'Client';

				if( !empty($data[$clientModel]) ) {
					$this->request->data['Property']['client_name']	= Common::hashEmptyField($data, sprintf('%s.full_name', $clientModel));
					$this->request->data['Property']['client_hp']	= Common::hashEmptyField($data, sprintf('%s.no_hp', $clientModel));
				}

				$fieldUserName = 'Property.client_name';
				$fieldUserNoHp = 'Property.client_hp';
			break;

	    	case 'properties':
	    		$client_type = false;
			    $options = array(
	                'wrapperClass' => 'row',
	                'frameClass' => 'col-sm-12',
	                'labelClass' => 'col-xl-2 taright col-sm-3',
	                'rowFormClass' => 'row',
	                'class' => 'relative  col-sm-5 col-xl-4',
			    );

			    if( !empty($data['UserClient']) ) {
				    $this->request->data['Property']['client_name'] = $this->Rumahku->filterEmptyField($data, 'UserClient', 'full_name');
				    $this->request->data['Property']['client_hp'] = $this->Rumahku->filterEmptyField($data, 'UserClient', 'no_hp');
				}

		        $fieldUserName = 'Property.client_name';
		        $fieldUserNoHp = 'Property.client_hp';
	    		break;
	    	
	    	default:
	    		$client_type = true;
			    $options = array(
			    	'wrapperClass' => false,
			        'frameClass' => false,
			        'labelClass' => false,
			        'rowFormClass' => false,
			        'class' => false,
			    );
		        $fieldUserName = 'Client.full_name';
		        $fieldUserNoHp = 'ClientProfile.no_hp';
	    		break;
	    }
?>
<div id="information-client">
   	<?php
			echo $this->Rumahku->buildInputForm($fieldUserName, array_merge($options, array(
				'type' => 'text',
                'label' => __('Nama %s %s', $label_type, $mandatory),
                'placeholder' => __('Nama %s', $label_type),
                'readonly' => $disabledClient,
            )));
			echo $this->Rumahku->buildInputForm($fieldUserNoHp, array_merge($options, array(
				'type' => 'text',
                'label' => __('No. HP %s %s', $label_type, $mandatory),
                'placeholder' => __('No. HP %s', $label_type),
                'readonly' => $disabledClient,
            )));
	?>
</div>