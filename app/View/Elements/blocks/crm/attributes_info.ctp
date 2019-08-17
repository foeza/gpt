<?php 
		$activity = !empty($activity)?$activity:false;
		$value = !empty($value)?$value:false;
		$status = $this->Crm->getStatus($activity, false);

		$activity_id = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'id');
		$activity_date = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'activity_date');
		$created = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'created');
		$activity_time = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'activity_time');
		$activity_step = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'step');

		$customActivityDate = $this->Rumahku->formatDate($activity_date, 'd M Y');
		$customActivityTime = $this->Rumahku->formatDate($activity_time, 'H:i');
		$dataOptions = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivityAttributeOption');
		$closing = $this->Crm->_callProjectClosing($value);

		if( empty($closing) && $activity_step != 'change_status' ) {
			$activity_updated = $this->Rumahku->dateDiff($created, date('Y-m-d'), 'day');
            $createdtime = strtotime($created);

            if( $createdtime > strtotime('-5 minutes') ) {
				if( empty($activity_updated) ) {
					$deleteUrl = $this->AclLink->link($this->Rumahku->icon('rv4-bold-cross').__(' Hapus'), array(
						'controller' => 'crm',
						'action' => 'activity_delete',
						$activity_id,
						'admin' => true,
					), array(
						'escape' => false,
						'class' => 'ajax-link activity-delete',
						'title' => __('Hapus Aktivitas'),
						'data-location' => 'true',
						'data-alert' => __('Anda yakin ingin menghapus aktivitas ini?'),
					));
				} else {
					$deleteUrl = false;
				}

				$actionAtr = $this->Html->link($this->Rumahku->icon('rv4-pencil').__(' Edit'), array(
					'controller' => 'crm',
					'action' => 'activity_edit',
					$activity_id,
					'admin' => true,
				), array(
					'escape' => false,
					'class' => 'ajaxModal',
					'title' => __('Edit Aktivitas'),
					'data-location' => 'true',
				)).$deleteUrl;
			} else {
				$actionAtr = false;
			}
		} else {
			$actionAtr = false;
		}

		if( !empty($dataOptions) ) {
			foreach ($dataOptions as $key => $option) {
				$attribute_option_id = $this->Rumahku->filterEmptyField($option, 'CrmProjectActivityAttributeOption', 'attribute_option_id');
				$optionName = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'name');
				$parent_id = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'parent_id');
				$child_type = $this->Rumahku->filterEmptyField($option, 'AttributeOption', 'type');

				$valueName = $this->Rumahku->filterEmptyField($option, 'CrmProjectActivityAttributeOption', 'attribute_option_value', $optionName);
				$valueName = $this->Rumahku->filterEmptyField($option, 'AttributeOptionChild', 'name', $valueName);

				$lblName = $this->Rumahku->filterEmptyField($option, 'Attribute', 'name');
				$valueType = $this->Rumahku->filterEmptyField($option, 'Attribute', 'type');

				if( !empty($valueType) ) {
					switch ($valueType) {
						case 'price':
							echo $this->Html->tag('div', sprintf('%s: %s', $lblName, $this->Rumahku->getFormatPrice($valueName)), array(
								'class' => 'label',
							));
							break;
						
						default:
							if( !empty($parent_id) ) {
								$valueName = $this->Rumahku->filterEmptyField($option, 'CrmProjectActivityAttributeOption', 'attribute_option_value', '-');
								$valueName = $this->Rumahku->filterEmptyField($option, 'AttributeOptionChild', 'name', $valueName);

								if( !in_array($child_type, array( 'payment' )) ) {
									switch ($child_type) {
										case 'price':
											$valueName = $this->Rumahku->getFormatPrice($valueName);
											break;
									}

									echo $this->Html->tag('div', sprintf('%s: %s', $optionName, $this->Html->tag('span', $valueName)), array(
										'class' => 'tag',
									));
								}
							} else {
								$valueName = sprintf('%s, %s %s', $valueName, $customActivityDate, $customActivityTime);

								echo $this->Html->tag('div', $valueName.$actionAtr, array(
									'class' => 'label',
								));
							}
							break;
					}
				}
			}
		} else {
			echo $this->Html->tag('div', sprintf(__('%s Project, %s %s'), $status, $customActivityDate, $customActivityTime).$actionAtr, array(
				'class' => 'label',
			));
		}
?>