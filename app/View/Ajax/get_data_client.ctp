<?php 
		$mandatory = $this->Html->tag('span',sprintf('(%s)',__('*')),array(
			'class' => 'color-red'
		));

		switch ($action_type) {
			case 'crm':
				echo $this->element('blocks/crm/forms/crm_client_buyer', array(
					'mandatory' => $mandatory,
					'_disabled' => true,
				));
				break;
			case 'kpr':
				$template = !empty($template) ? $template : 'client_buyer';

				echo $this->element('blocks/crm/forms/'.$template, array(
					'mandatory' => $mandatory,
					'_disabled' => true,
				));
				break;

			case 'client_booking':
				$template = !empty($template) ? $template : 'client_booking';

				echo $this->element('blocks/transactions/backends/'.$template);
				break;
			
			default:
				$mandatory = $this->Rumahku->_callLblConfigValue('is_mandatory_client', '*');
				echo $this->element('blocks/crm/forms/client_info', array(
					'mandatory' => $mandatory,
				));
				break;
		}

		if( !empty($documentCategories) ) {
			echo $this->element('blocks/kpr/forms/document_kpr');
		}
?>