<?php
class CoBrokeHelper extends AppHelper {
	var $helpers = array(
		'Rumahku', 'Html', 'Property'
	);

	function requestButton($data, $btnClass = 'btn', $just_request = false){
		$user_login_id 	= Configure::read('User.id');
		$is_admin 		= Configure::read('User.admin');

		$user_id 	= $this->Rumahku->filterEmptyField($data, 'Property', 'user_id');
		$sold 		= $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		
		$id = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'id');

		$CoBrokeUser = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser');

		$result = '';
		if(empty($sold)){
			if($user_id != $user_login_id && !$is_admin && empty($CoBrokeUser)){
				$url = array(
					'controller' => 'co_brokes',
					'action' => 'request_cobroke',
					$id,
					'admin' => true
				);

				$icon = $this->Rumahku->icon('rv4-upload');

				$result = $this->Html->link(sprintf(__('%s Request Co-Broke'), $icon) , $this->Html->url($url), array(
					'class' => $btnClass.' green ajaxModal',
					'title' => __('Ajukan Permintaan Co-Broke'),
					'escape' => false
				));
			}else if(!empty($CoBrokeUser) ){
				$approved = $this->Rumahku->filterEmptyField($CoBrokeUser, 'approved');
				$declined = $this->Rumahku->filterEmptyField($CoBrokeUser, 'declined');
				$status_cobroke = $this->Rumahku->filterEmptyField($CoBrokeUser, 'status');

				if(empty($status_cobroke) || !empty($declined)){
					$status = 'ditolak';
				}else if(!empty($status_cobroke) && !empty($approved)){
					$status = 'disetujui';
				}else if(!empty($status_cobroke) && empty($approved) && empty($declined)){
					$status = 'pending';
				}

				if(!empty($status)){
					$result = $this->Html->div('status-co-broke', sprintf(__('Status : <b>%s</b>'), $status));
				}
			}
		}else if(!$just_request){
			$sold_date = $this->Rumahku->filterEmptyField($data, 'PropertySold', 'sold_date');
			$property_action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id');

			$text_status = __('Terjual');
			if($property_action_id == 2){
				$text_status = __('Tersewa');
			}

			$result = $this->Html->div('status-co-broke', sprintf(__('Status : <b>Telah %s</b>'), $text_status));
			$result .= $this->Html->div('status-co-broke', sprintf(__('Tanggal %s : <b>%s</b>'), $text_status, $this->Rumahku->customDate($sold_date)));
		}

		return $result;
	}

	function requestUrlApplication($data){
		$result = '';

		$user_login_id 	= Configure::read('User.id');
		$is_admin 		= Configure::read('User.admin');

		$user_id 	= $this->Rumahku->filterEmptyField($data, 'Property', 'user_id');
		$sold 		= $this->Rumahku->filterEmptyField($data, 'Property', 'sold');
		
		$id = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'id');
		$CoBrokeUser = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser');

		if(empty($sold)){
			if($user_id != $user_login_id && empty($CoBrokeUser)){
				$result = array(
					'controller' => 'co_brokes',
					'action' => 'request_cobroke',
					$id,
					'admin' => true
				);
			}
		}

		return $result;
	}

	function approvalButton($data){
		$approved = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'approved');
		$declined = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'declined');
		$decline_reason = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'decline_reason');
		$id = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'id');

		$result = '';

		if(empty($approved) && empty($declined)){
			$content = $this->Html->link(__('Setujui'), array(
				'controller' => 'co_brokes',
				'action' => 'approve',
				$id,
				'backprocess' => true
			), array(
				'class' => 'btn green'
			), __('Apakah Anda yakin ingin menyetujui pengajuan Co-Broke ini?'));

			$result = $content.' ';

			$content = $this->Html->link(__('Tolak'), array(
				'controller' => 'co_brokes',
				'action' => 'rejected',
				$id,
				'admin' => true
			), array(
				'class' => 'btn red ajaxModal',
				'title' => __('Tolak Pengajuan Co-Broke')
			));

			$result .= $content.' ';
		}else if(!empty($declined) && !empty($decline_reason)){
			$content = $this->Html->link(__('Batalkan status ditolak'), array(
				'controller' => 'co_brokes',
				'action' => 'unrejected',
				$id,
				'backprocess' => true
			), array(
				'class' => 'btn orange'
			), __('Apakah Anda yakin ingin mengubah status aplikasi ini?'));

			$result .= $content.' ';
		}else if(!empty($approved) && empty($declined)){
			$content = $this->Html->link(__('Edit Aplikasi'), array(
				'controller' => 'co_brokes',
				'action' => 'edit_cobroke',
				$id,
				'backprocess' => true
			), array(
				'class' => 'btn orange ajaxModal',
				'title' => __('Edit Aplikasi')
			));

			$result .= $content.' ';
		}

		return $result;
	}

	function printButton($data, $options = array()){
		$data_company = Configure::read('Config.Company.data');

		$config 		= Common::hashEmptyField($data_company, 'UserCompanyConfig');
		$is_brochure 	= Common::hashEmptyField($config, 'is_brochure');

		$id 			= Common::hashEmptyField($data, 'CoBrokeUser.id');
		$approved 		= Common::hashEmptyField($data, 'CoBrokeUser.approved');
		$sold 			= Common::hashEmptyField($data, 'Property.sold');

		$options		= (array) $options;
		$showEbrochure	= Common::hashEmptyField($options, 'show_ebrochure', true, array('isset' => true));

		$content = '';

		if(empty($sold) && !empty($approved)){
			$content = $this->Html->link(__('Cetak Aplikasi'), array(
				'controller' => 'co_brokes',
				'action' => 'print',
				$id,
				'backprocess' => true
			), array(
				'class' => 'btn default print-window',
				'title' => __('Cetak Aplikasi'),
				'data-width' => '640',
				'data-height' => '660'
			)).' ';

			$content .= $this->Html->link(__('Download PDF'), array(
				'controller' => 'co_brokes',
				'action' => 'print',
				$id,
				1,
				'backprocess' => true
			), array(
				'class' => 'btn default',
				'title' => __('Download Aplikasi'),
			));

			if(!empty($is_brochure) && $showEbrochure){
				$content .= ' '.$this->Html->link(__('Buat Ebrosur'), array(
					'controller' => 'ebrosurs',
					'action' => 'add',
					'cobroke_id' => $id,
					'admin' => true
				), array(
					'class' => 'btn default',
					'title' => __('Buat Ebrosur'),
				));
			}
		}

		return $content;
	}

	function statusApplication($data, $text_status = true){
		$user_login_id 	= Configure::read('User.id');
		$is_admin 		= Configure::read('User.admin');

		$CoBrokeUser = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser');

		$user_id 	= $this->Rumahku->filterEmptyField($data, 'Property', 'user_id');

		$status = $status_system = '';

		if(($user_id != $user_login_id && !$is_admin && !empty($CoBrokeUser)) || !$text_status){
			$approved = $this->Rumahku->filterEmptyField($CoBrokeUser, 'approved');
			$declined = $this->Rumahku->filterEmptyField($CoBrokeUser, 'declined');

			if(empty($approved) && empty($declined)){
				$status = __('menunggu persetujuan');
				$status_system = 'pending';
			}else if(!empty($approved)){
				$status = __('disetujui');
				$status_system = 'approve';
			}else{
				$status = __('ditolak');
				$status_system = 'decline';
			}
		}

		if(!$text_status && !empty($status_system)){
			$status = $status_system;
		}

		return $status;
	}

	function statusCoBroke($data){
		$status = Common::hashEmptyField($data, 'CoBrokeProperty.status', '');
		$active = Common::hashEmptyField($data, 'CoBrokeProperty.active', '');
		$sold 	= Common::hashEmptyField($data, 'Property.sold');

		$temp = '';
		if(!empty($status) && empty($sold)){
			$temp = $this->Property->coBrokeButton($data, true);
		}else if(!empty($sold)){
			$property_action_id = Common::hashEmptyField($data, 'Property.property_action_id');

			$text_status = __('Terjual');
			if($property_action_id == 2){
				$text_status = __('Tersewa');
			}

			$temp['label'] = $text_status;
		}

		return $temp;
	}

	function deleteCoBroke($data){
		$id = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'id');
		$co_broke_user_approve_count = $this->Rumahku->filterEmptyField($data, 'CoBrokeProperty', 'co_broke_user_approve_count');

		$content = array();
		if(isset($data['CoBrokeProperty']['co_broke_user_approve_count']) && (empty($co_broke_user_approve_count) || $co_broke_user_approve_count < 1)){
			$content = array(
				'label' => __('Hapus'),
				'url' => array(
					'controller' => 'co_brokes',
					'action' => 'delete_co_broke',
					$id,
					'backprocess' => true
				),
				'alert' => __('Apakah Anda yakin ingin menghapus data Co-Broke ini?')
			);
		}

		return $content;
	}

	function revisionCommissionButton($data){
		$id 						= Common::hashEmptyField($data, 'CoBrokeUser.id');
		$co_broke_property_id 		= Common::hashEmptyField($data, 'CoBrokeUser.co_broke_property_id');
		$approved 					= Common::hashEmptyField($data, 'CoBrokeUser.approved');
		$declined 					= Common::hashEmptyField($data, 'CoBrokeUser.declined');
		$revision_commission 		= Common::hashEmptyField($data, 'CoBrokeUser.revision_commission');
		$revision_type_commission 	= Common::hashEmptyField($data, 'CoBrokeUser.revision_type_commission');

		$content = '';

		if(empty($declined) && empty($approved) && empty($revision_commission) && empty($revision_type_commission)){
			$content = $this->Html->link(__('Revisi Komisi'), array(
				'controller' => 'co_brokes',
				'action' => 'revision_request',
				$co_broke_property_id,
				$id,
				'admin' => true
			), array(
				'class' => 'btn orange ajaxModal',
				'title' => __('Revisi Komisi Co-Broke'),
			)).' ';
		}

		return $content;
	}

	function ApproveRevision($data){
		$id = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'id');
		$approved = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'approved');
		$declined = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'declined');
		$revision_commission = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'revision_commission');
		$revision_type_commission = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'revision_type_commission');
		$final_commission = $this->Rumahku->filterEmptyField($data, 'CoBrokeUser', 'final_commission');
		
		$content = '';
		if(empty($declined) && empty($approved) && (!empty($revision_commission) || !empty($revision_type_commission)) && empty($final_commission)){
			$content = $this->Html->link(__('Setujui Revisi'), array(
				'controller' => 'co_brokes',
				'action' => 'approve_revision',
				$id,
				'backprocess' => true
			), array(
				'class' => 'btn green',
				'title' => __('Setujui Revisi Komisi Co-Broke'),
			), __('Apakah Anda yakin ingin menyetujui revisi komisi tersebut?')).' ';
			
			$content .= $this->Html->link(__('Tolak Revisi'), array(
				'controller' => 'co_brokes',
				'action' => 'diapprove_revision',
				$id,
				'backprocess' => true
			), array(
				'class' => 'btn red',
				'title' => __('Tolak Revisi Komisi Co-Broke'),
			), __('Apakah Anda yakin ingin menolak revisi komisi tersebut?')).' ';
		}

		return $content;
	}

	function commissionName($commission, $type_co_broke_commission = 'in_corp', $type_price = 'percentage', $currency = 'Rp. '){
		$type_commission = array(
			'in_corp' => __('Penjualan Properti'),
		    'out_corp' => __('Komisi Pemilik Listing'),
		);

		$type_commission = Common::hashEmptyField($type_commission, $type_co_broke_commission, false, array(
			'isset' => true
		));

		$type_commission_text = '';
		if($type_commission){
			$type_commission_text = sprintf(' dari %s', $type_commission);
		}

		if($type_price == 'percentage'){
			$commission .= '%';
		}else{
			$commission = $this->Rumahku->getCurrencyPrice($commission, '', $currency);
		}

		return sprintf('%s%s', $commission, $type_commission_text);
	}
}