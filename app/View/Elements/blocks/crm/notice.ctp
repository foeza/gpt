<?php 
		$btnAdd = false;
		$session_id = !empty($session_id)?$session_id:false;
		$id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
		$property_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'property_id');
		$status = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'slug');
		$statusName = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'name');

		$payment_type = $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment', 'type');
		$interest_rate = $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment', 'interest_rate');

		$kpr = $this->Rumahku->filterEmptyField($value, 'Kpr');
		$kprBanks = $this->Rumahku->filterEmptyField($value, 'KprBank');
		$bookingFee = $this->Rumahku->filterEmptyField($value, 'BookingFee');
		$documents = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument');
		$documentContent = false;

		$approved_arr = Set::classicExtract($kprBanks, '{n}.KprBank.document_status'); 
		$flagApproved = (!empty($approved_arr) && in_array('credit_process', $approved_arr)) ? true : false;

		switch ($status) {
			case 'finalisasi':
				$accessKpr = $this->AclLink->_aclCheck(array(
					'controller' => 'kpr',
					'action' => 'index',
					'admin' => true,
				));

				if( empty($accessKpr) && $payment_type == 'kpr' ) {
					$kpr = false;
					$payment_type = 'cash';
				}

				if( (empty($kpr) && $payment_type == 'cash') || empty($property_id) ) {
					$completed = Configure::read('__Site.Global.Variable.CRM.Completed');
					$completed = implode(',', $completed);
					$documentCategories = !empty($documentCategories)?$documentCategories:false;
					$uncompleted = $this->Rumahku->filterEmptyField($documentCategories, 'uncompleted');
					$kpr_application_id = $this->Rumahku->filterEmptyField($value, 'Kpr', 'id');

					if( !empty($uncompleted) ) {
						$contentAlert = false;
						$btn = __('Upload Dokumen');
						$btnStatus = $this->Form->button($btn, array(
							'type' => 'submit',
							'class' => 'btn blue',
						));

						if( $payment_type == 'kpr' && !empty($kpr_application_id) ) {
							$contentAlert .= $this->Html->tag('p', sprintf(__('Untuk melihat proses dan informasi %s, dapat klik link di bawah ini:'), $this->Html->tag('strong', __('KPR'))));
							$contentAlert .= $this->Html->tag('p', $this->Html->link(__('Info KPR'), array(
								'controller' => 'kpr',
								'action' => 'application_detail',
								$kpr_application_id,
								'admin' => true,
							), array(
								'class' => 'btn darkblue',
								'target' => 'blank',
							)), array(
								'class' => 'mt10',
							));
							$contentAlert .= $this->Html->tag('p', $this->Html->tag('i', __('Atau')), array(
								'class' => 'separator mt20 mb10',
							));
						}

						$contentAlert .= $this->Html->tag('p', sprintf(__('Anda dapat melengkapi beberapa %s terkait, di bawah ini:'), $this->Html->tag('strong', __('Dokumen'))));

						$documentContent = $this->Crm->getDocumentStatus($documentCategories, array(
							'hideFile' => 'false',
						));

					} else {
						if(($payment_type == 'kpr' && !$flagApproved) && !empty($property_id)){
							$contentAlert = $this->Html->tag('p', sprintf(__('Anda dapat mengajukan %s dari beberapa %s dengan klik tombol dibawah ini:'), $this->Html->tag('strong', __('KPR')),$this->Html->tag('strong', __('Bank'))));

							$attribute = 'submission-kpr';
							$attribute_set = 'finalisasi';
							$btn = __('Pengajuan KPR');

							$btnStatus = $this->Html->link($btn, array(
								'controller' => 'kpr',
								'action' => 'add',
								$id,
								'admin' => true,
							), array(
								'class' => 'btn blue',
							));

						}else{
							$contentAlert = $this->Html->tag('p', sprintf(__('Anda dapat meningkatkan status project menjadi %s dengan klik tombol dibawah ini:'), $this->Html->tag('strong', __('Complete'))));

							$btn = __('Complete');
							$btnStatus = $this->Html->link($btn, array(
								'controller' => 'crm',
								'action' => 'change_status',
								$id,
								'complete',
								'admin' => true,
							), array(
								'class' => 'btn green ajaxModal',
								'data-size' => 'modal-md',
								'title' => __('PERHATIAN'),
							));
						}
						
					}
				} else {
					switch ($payment_type) {
						case 'kpr':

							if(!empty($kpr)){
								$kpr_id = $this->Rumahku->filterEmptyField($value, 'Kpr', 'id');

								if(empty($kprBanks)){
									$contentAlert = $this->Html->tag('p', __('Anda telah melengkapi form informasi KPR'));
									$contentAlert .= $this->Html->tag('p', __('Untuk proses pengajuan KPR, harap pilih daftar bank yang telah terdaftar :'));

									$attribute = 'informasi-pembayaran';
									$attribute_set = 'finalisasi';
									$btn = __('Pengajuan KPR');

									$btnStatus = $this->Html->link($btn, array(
										'controller' => 'kpr',
										'action' => 'filing',
										$kpr_id,
										'admin' => true,
									), array(
										'class' => 'btn blue',
									));
								}else{
									$contentAlert = $this->Html->tag('p', __('Anda telah melengkapi form informasi & pengajuan KPR, silakan klik button %s untuk melihat detail KPR.', $this->Html->tag('b', __('Lihat KPR'))));

									$attribute = 'informasi-pembayaran';
									$attribute_set = 'finalisasi';
									$btn = __('Lihat KPR');

									$btnStatus = $this->Html->link($btn, array(
										'controller' => 'kpr',
										'action' => 'index',
										'kpr_id' => $kpr_id,
										'admin' => true,
									), array(
										'title' => __('Lihat KPR'),
										'class' => 'btn blue',
									));
								}
							}else{
								$contentAlert = $this->Html->tag('p', __('Anda telah memilih pembayaran dengan KPR:'));
								$contentAlert .= $this->Html->tag('p', __('Untuk proses informasi & pengajuan KPR, harap lengkapi form Aplikasi KPR:'));

								$attribute = 'informasi-pembayaran';
								$attribute_set = 'finalisasi';
								$btn = __('Lengkapi Form');

								$btnStatus = $this->Html->link($btn, array(
									'controller' => 'kpr',
									'action' => 'add',
									$id,
									'admin' => true,
								), array(
									'class' => 'btn blue',
								));
							}

							break;
						
						default:
							$contentAlert = $this->Html->tag('p', sprintf(__('Anda telah mengubah status project menjadi %s, Anda dapat menambahkan informasi pembayaran dengan klik tombol dibawah ini:'), $this->Html->tag('strong', $statusName)));
								$attribute = 'informasi-pembayaran';
								$attribute_set = 'finalisasi';
								$btn = __('Informasi Pembayaran');

								$btnStatus = $this->Html->link($btn, array(
									'controller' => 'crm',
									'action' => 'attributes',
									$id,
									$attribute,
									'session_id' => $session_id,
									'attribute_set' => $attribute_set,
									'admin' => true,
								), array(
									'class' => 'btn blue ajax-link',
									'data-wrapper-write' => '#wrapper-attribute',
									'data-hide' => '.activity-hide',
									'data-on-focus' => '.on-focus',
									'data-location' => 'true',
								));
							break;
					}
				}
				break;
			case 'booking-fee':
				// if( empty($bookingFee) ) {	
				// 	$contentAlert = $this->Html->tag('p', sprintf(__('Anda telah mengubah status project menjadi %s.'), $this->Html->tag('strong', $statusName)));
				// 	$contentAlert .= $this->Html->tag('p', __('Selanjutnya, Anda dapat menambahkan informasi booking fee dengan klik tombol dibawah ini:'));

				// 	$attribute = 'pembayaran-booking-fee';
				// 	$attribute_set = 'closing';
				// 	$btn = __('Booking Fee');

				// 	$btnStatus = $this->Html->link($btn, array(
				// 		'controller' => 'crm',
				// 		'action' => 'attributes',
				// 		$id,
				// 		$attribute,
				// 		'session_id' => $session_id,
				// 		'payment' => $payment_type,
				// 		'attribute_set' => $attribute_set,
				// 		'admin' => true,
				// 	), array(
				// 		'class' => 'btn blue ajax-link',
				// 		'data-wrapper-write' => '#wrapper-attribute',
				// 		'data-hide' => '.activity-hide',
				// 		'data-on-focus' => '.on-focus',
				// 		'data-location' => 'true',
				// 	));
				// } else {
					$contentAlert = $this->Html->tag('p', sprintf(__('Anda dapat meningkatkan status project menjadi %s, dan melengkapi pembayaran dengan klik tombol dibawah ini:'), $this->Html->tag('strong', __('Finalisasi'))));
					$btn = __('Finalisasi');

					$btnStatus = $this->Html->link($btn, array(
						'controller' => 'crm',
						'action' => 'change_status',
						$id,
						'finalisasi',
						'admin' => true,
					), array(
						'class' => 'btn blue ajaxModal',
						'data-size' => 'modal-md',
						'title' => __('PERHATIAN'),
					));
				// }
				break;
			case 'hot-prospek':
				// $contentAlert = $this->Html->tag('p', sprintf(__('Anda dapat meningkatkan status project menjadi %s dengan klik tombol dibawah ini:'), $this->Html->tag('strong', __('Closing'))));
				// $btn = __('Closing');

				// $btnStatus = $this->Html->link($btn, array(
				// 	'controller' => 'crm',
				// 	'action' => 'change_status',
				// 	$id,
				// 	'closing',
				// 	'admin' => true,
				// ), array(
				// 	'class' => 'btn blue ajaxModal',
				// 	'data-size' => 'modal-md',
				// 	'title' => __('PERHATIAN'),
				// ));
				$contentAlert = $this->Html->tag('p', sprintf(__('Anda telah mengubah status project menjadi %s.'), $this->Html->tag('strong', $statusName)));
				$contentAlert .= $this->Html->tag('p', __('Selanjutnya, Anda dapat menambahkan informasi booking fee dengan klik tombol dibawah ini:'));

				$attribute = 'pembayaran-booking-fee';
				$attribute_set = 'closing';
				$btn = __('Booking Fee');

				$btnStatus = $this->Html->link($btn, array(
					'controller' => 'crm',
					'action' => 'attributes',
					$id,
					$attribute,
					'session_id' => $session_id,
					'payment' => $payment_type,
					'attribute_set' => $attribute_set,
					'admin' => true,
				), array(
					'class' => 'btn blue ajax-link',
					'data-wrapper-write' => '#wrapper-attribute',
					'data-hide' => '.activity-hide',
					'data-on-focus' => '.on-focus',
					'data-location' => 'true',
				));
				break;
			case 'prospek':
				$contentAlert = $this->Html->tag('p', sprintf(__('Anda dapat meningkatkan status project menjadi %s dengan klik tombol dibawah ini:'), $this->Html->tag('strong', __('Hot Prospek'))));
				$btn = __('Hot Prospek');

				$btnStatus = $this->Html->link($btn, array(
					'controller' => 'crm',
					'action' => 'change_status',
					$id,
					'hot-prospek',
					'admin' => true,
				), array(
					'class' => 'btn blue ajaxModal',
					'data-size' => 'modal-md',
					'title' => __('PERHATIAN'),
				));
				break;
		}

		if( !empty($contentAlert) ) {
			echo $this->Form->create('CrmProjectDocument', array(
				'type' => 'file',
				'url' => array(
					'controller' => 'crm',
					'action' => 'project_upload_documents',
					$id,
					'admin' => true,
				),
			));

			echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', __('Tips Project')).$contentAlert.$documentContent, array(
				'class' => 'wrapper-alert',
			)).$this->Html->tag('div', $btnStatus.$btnAdd, array(
				'class' => 'action-button',
			)), array(
				'class' => 'crm-tips',
			));
			echo $this->Form->end(); 
		}
?>