<?php 
		if( !empty($relations) ) {
			foreach ($relations as $key => $value) {
				$id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
				$property_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'property_id');
				$user_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'user_id');
				$created = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'created', false, true, array(
					'date' => 'd M Y, H:i',
				));

				$client = $this->Rumahku->filterEmptyField($value, 'UserClient', 'full_name', '-');
				$email = $this->Rumahku->filterEmptyField($value, 'UserClient', 'email', '-');
				$clientPhoto = $this->Rumahku->filterEmptyField($value, 'UserClient', 'photo');

				$phone = $this->Rumahku->filterEmptyField($value, 'UserClient', 'phone', '-');
				$no_hp_is_whatsapp = $this->Rumahku->filterEmptyField($value, 'UserClient', 'no_hp_is_whatsapp');
				$no_hp_2_is_whatsapp = $this->Rumahku->filterEmptyField($value, 'UserClient', 'no_hp_2_is_whatsapp');
				$no_hp = $this->Rumahku->filterEmptyField($value, 'UserClient', 'no_hp', false, true, array(
					'wa' => $no_hp_is_whatsapp,
				));
				$no_hp_2 = $this->Rumahku->filterEmptyField($value, 'UserClient', 'no_hp_2', false, true, array(
					'wa' => $no_hp_2_is_whatsapp,
				));

				if( !empty($no_hp_2) ) {
					$no_hp = __('%s / %s', $no_hp, $no_hp_2);
				}

				$statusColor = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'bg_color');
				$status_label = $this->Crm->getStatus($value, 'span');
				$className = sprintf('#wrapper-project-list-%s-%s', $property_id, $user_id);
				$char = strtoupper(substr($client, 0, 1));

				if( !empty($clientPhoto) ) {
					$urlPhoto = $this->Rumahku->photo_thumbnail(array(
		                'save_path' => Configure::read('__Site.profile_photo_folder'), 
		                'src'=> $clientPhoto, 
		                'size' => 'ps',
		                'url' => true,
		                'fullbase' => true,
		            ));
					$clientPhoto = $this->Html->tag('div', $this->Html->tag('span', $char, array(
						'style' => sprintf('color: %s;', $statusColor)
					)), array(
						'class' => 'agent-photo',
						'style' => sprintf('border-color: %s;background: url(%s) no-repeat scroll center;text-indent: -99999px;', $statusColor, $urlPhoto),
						'title' => $client,
					));
				} else {
					$clientPhoto = $this->Html->tag('div', $this->Html->tag('span', $char, array(
						'style' => sprintf('color: %s;', $statusColor)
					)), array(
						'class' => 'agent-photo',
						'style' => sprintf('border-color: %s;background: transparent;', $statusColor),
						'title' => $client,
					));
					// $clientPhoto = $this->Html->tag('span', strtoupper(substr($client, 0, 1)));
				}
				
				$desc_modal = $this->Html->tag('div',
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->tag('label', __('Tgl Dibuat:')), array(
							'class' => 'col-sm-5 taright',
						)).
						$this->Html->tag('div', $created, array(
							'class' => 'col-sm-7 value',
						)), array(
						'class' => 'row mb10',
					)).
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->tag('label', __('Klien:')), array(
							'class' => 'col-sm-5 taright',
						)).
						$this->Html->tag('div', $client, array(
							'class' => 'col-sm-7 value',
						)), array(
						'class' => 'row mb10',
					)).
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->tag('label', __('Email:')), array(
							'class' => 'col-sm-5 taright',
						)).
						$this->Html->tag('div', $email, array(
							'class' => 'col-sm-7 value',
						)), array(
						'class' => 'row mb10',
					)).
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->tag('label', __('Hp:')), array(
							'class' => 'col-sm-5 taright',
						)).
						$this->Html->tag('div', $no_hp, array(
							'class' => 'col-sm-7 value',
						)), array(
						'class' => 'row mb10',
					)).
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->tag('label', __('Telp:')), array(
							'class' => 'col-sm-5 taright',
						)).
						$this->Html->tag('div', $phone, array(
							'class' => 'col-sm-7 value',
						)), array(
						'class' => 'row mb10',
					)).
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->tag('label', __('Status:')), array(
							'class' => 'col-sm-5 taright',
						)).
						$this->Html->tag('div', $status_label, array(
							'class' => 'col-sm-7 value',
						)), array(
						'class' => 'row mb5',
					)).
					$this->Html->tag('div',
						$this->Html->tag('div', $this->Html->link(__('Selengkapnya >'), array(
		                    'controller' => 'crm',
		                    'action' => 'project_detail',
		                    $id,
		                    'admin' => true,
		                ), array(
		                    'escape' => false,
		                    'class' => 'view-more',
		                )), array(
							'class' => 'col-sm-12',
						)), array(
						'class' => 'row mb5 mt10',
					)), array(
					'class' => 'content-kpr',
				));
				$desc_modal = str_replace('"', '\'', $desc_modal);

				
				echo $this->Html->tag('li', $this->Rumahku->noticeInfo($desc_modal, __('Info KPR'), array(
					'data-placement' => 'top',
					'class' => 'single',
				), $clientPhoto));
				// echo $this->Html->tag('li', $this->Html->tag('div', $this->Html->link($clientPhoto, array(
    //                 'controller' => 'crm',
    //                 'action' => 'project_detail',
    //                 $id,
    //                 'admin' => true,
    //             ), array(
    //                 'escape' => false,
    //             )), array(
				// 	'class' => 'agent-photo',
				// 	'style' => sprintf('background: %s;border: 4px solid %s;', $statusColor, $statusColor),
				// 	'title' => $client,
				// )));
			}

			if( !empty($relationCount) && $relationCount > 4 ) {
				echo $this->Html->tag('li', $this->Html->link($this->Html->tag('div', $this->Html->tag('span', __('4+')), array(
					'class' => 'agent-photo crm-plus',
				)), '#', array(
                    'escape' => false,
                    'class' => 'ajax-link',
                    'data-url' => $this->Html->url(array(
                    	'controller' => 'crm',
                    	'action' => 'project_load_more',
                    	$property_id,
                    	$user_id,
                    	'admin' => true,
                	)),
                	'data-wrapper-write' => $className,
                )));
			}
		}
?>