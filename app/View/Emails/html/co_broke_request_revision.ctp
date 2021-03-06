<?php 
		$owner_property = Common::hashEmptyField($params, 'owner_data');
		$broker_data 	= Common::hashEmptyField($params, 'broker_data');
		$status 		= Common::hashEmptyField($params, 'status');
		$type_commission = Common::hashEmptyField($params, 'type_commission');
		
		/*KOMISI*/
		$revision_commission 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_commission');
		$request_commission 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.request_commission');

		$request_type_commission 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.type_commission');
		$revision_type_commission 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_commission');
		$final_type_commission 		= Common::hashEmptyField($broker_data, 'CoBrokeUser.final_type_commission');

		$revision_type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_price_commission');
		$type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.type_price_commission');

		$request_text = $this->CoBroke->commissionName($request_commission, $request_type_commission, $type_price_commission);
		$revision_text = $this->CoBroke->commissionName($revision_commission, $revision_type_commission, $revision_type_price_commission);
		/*KOMISI*/
		
		$senderName 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.name');
		$senderAddress 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.address');
		$senderPhone 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.phone');
		$id 			= Common::hashEmptyField($broker_data, 'CoBrokeUser.id');

		$code 			= Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');
		$title 			= Common::hashEmptyField($broker_data, 'Property.title');

		$UserProfile 	= Common::hashEmptyField($owner_property, 'UserProfile');
		$email 			= Common::hashEmptyField($owner_property, 'User.email');
		$phone 			= Common::hashEmptyField($UserProfile, 'phone');
		$no_hp 			= Common::hashEmptyField($UserProfile, 'no_hp');
		$no_hp_2 		= Common::hashEmptyField($UserProfile, 'no_hp_2');
		$pin_bb 		= Common::hashEmptyField($UserProfile, 'pin_bb');

		$UserCompany 		= Common::hashEmptyField($owner_property, 'UserCompany');
		$company_phone 		= Common::hashEmptyField($UserCompany, 'phone');
		$company_phone_2 	= Common::hashEmptyField($UserCompany, 'phone_2');
		$contact_name 		= Common::hashEmptyField($UserCompany, 'contact_name');
		$contact_email 		= Common::hashEmptyField($UserCompany, 'contact_email');
		$fax 				= Common::hashEmptyField($UserCompany, 'fax');
		$address 			= Common::hashEmptyField($UserCompany, 'address');

		$company_region 	= Common::hashEmptyField($UserCompany, 'Region.name');
		$company_city 		= Common::hashEmptyField($UserCompany, 'City.name');
		$company_subarea 	= Common::hashEmptyField($UserCompany, 'Subarea.name');

		$domain = Common::hashEmptyField($owner_property, 'UserCompanyConfig.domain');
		
		if(!empty($domain) && substr($domain, -1) == '/'){
			$domain = rtrim('/', $domain);
		}

		$location = '';
		
		if(!empty($company_region) || !empty($company_city) || !empty($company_subarea)){
			$location = sprintf('%s, %s, %s', $company_region, $company_city, $company_subarea);
		}

		$header = $this->Html->tag('p', sprintf(__('Anda mendapatkan revisi tentang komisi Co-broke dari agen properti yang bersangkutan. dari nilai yang Anda ajukan sebesar <strong>%s</strong> menjadi <strong>%s</strong>'), $request_text, $revision_text), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		$approve_url = $this->Html->url(array(
			'controller' => 'co_brokes',
			'action' => 'approve_revision',
			$id,
			'backprocess' => true
		));

		$disapprove_url = $this->Html->url(array(
			'controller' => 'co_brokes',
			'action' => 'diapprove_revision',
			$id,
			'backprocess' => true
		));

		if(!empty($domain)){
			$approve_url = $domain.$approve_url;
			$disapprove_url = $domain.$disapprove_url;
		}
		
		$content = $this->Html->tag('p', $this->Html->link(__('Setujui'), $approve_url, array(
			'style'=> 'color: #06c; text-decoration: none; font-size: 14px;margin-right: 15px;', 
			'target'=> '_blank'
			)).$this->Html->link(__('Tolak'), $disapprove_url.'/1', array(
			'style'=> 'color: #06c;text-decoration: none;font-size: 14px;', 
			'target'=> '_blank'
			)), array(
			'style' => 'color: #303030;font-size: 14px;margin: 20px 0 15px;line-height: 20px;text-align: center;',
		));

		$content .= $this->Html->tag('p', sprintf(__('Harap hubungi pihak agen atau principle yang bersangkutan untuk mengikuti tahap kerjasama lebih lanjut, terima kasih.'), $code, $title), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		echo $header;

		$title 			= Common::hashEmptyField($broker_data, 'Property.title');
		$change_date 	= Common::hashEmptyField($broker_data, 'Property.change_date');
		$bt_commision 	= Common::hashEmptyField($broker_data, 'Property.bt');
		
		$code 	= Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');
		$id 	= Common::hashEmptyField($broker_data, 'CoBrokeProperty.id');

		$user_name 		= Common::hashEmptyField($broker_data, 'User.full_name');
		$company_name 	= Common::hashEmptyField($broker_data, 'UserCompany.name');

		$label = $this->Property->getNameCustom($broker_data);
		$price = $this->Property->getPrice($broker_data, __('(Harap hubungi Agen terkait)'));

		$specs = $this->Property->getSpec($broker_data);
?>
<style type="text/css">
	table ul{
		margin: 0px;
	    list-style: none;
	    padding-left: 0px;
	    font-size: 12px;
	}
	table ul li{
		display: inline-block;
		margin-right: 10px;
		font-size: 10px;
	}
</style>
<?php
		echo $this->Html->tag('h3', __('Data Properti:'), array(
			'style' => '    margin: 0px 0px 5px;font-size: 18px;font-weight: normal;border-bottom: 2px solid;display: inline-block;'
		));
?>
<table>
	<tr>
		<td style="width: 50%;border-right: 1px solid #ccc;padding-right: 10px;">
			<?php
					echo $this->Html->div('label', $label, array(
						'style' => 'font-size: 12px;'
					));
					echo $this->Html->div('title', $title, array(
						'style' => 'font-size: 14px;'
					));
					echo $this->Html->div('price', $price, array(
						'style' => 'font-size: 18px;font-weight: bold;margin: 15px 0px;'
					));
					echo $this->Html->div('code mt15', sprintf(__('Kode Co-Broke: #<b>%s</b>'), $code));
			?>
		</td>
		<td style="width: 50%;padding-left: 10px;">
			<?php
					echo $this->Html->tag('h4', __('Spesifikasi:'), array(
						'style' => 'margin: 0px;font-size: 14px;'
					));

					echo $this->Html->tag('div', $specs, array(
						'class' => 'specs',
					));

					if(!empty($revision_commission)){
						echo $this->Html->tag('div', sprintf('Komisi: <b>%s</b>', $revision_text), array(
							'class' => 'bt-comission',
							'style' => 'margin: 10px 0px;'
						));
					}
			?>
		</td>
	</tr>
</table>
<?php
			if(!empty($UserProfile)){
?>
<table style="line-height: 10px;font-size: 12px;margin-top: 30px;">
	<tr>
		<?php
				echo $this->Html->tag('th', __('Kontak Agen'), array(
					'style' => 'width:50%;text-align:left;'
				));
		?>
	</tr>
	<tr>
		<?php
				if(!empty($UserProfile)){
					$content_td = '';

					if(!empty($email)){
						$content_td .= $this->Html->tag('p', sprintf(__('Email : <b>%s</b>'), $email));
					}
					if(!empty($phone)){
						$content_td .= $this->Html->tag('p', sprintf(__('No. Telp : <b>%s</b>'), $phone));
					}
					if(!empty($no_hp)){
						$content_td .= $this->Html->tag('p', sprintf(__('No. Hp : <b>%s</b>'), $no_hp));
					}
					if(!empty($no_hp_2)){
						$content_td .= $this->Html->tag('p', sprintf(__('No. Hp 2 : <b>%s</b>'), $no_hp_2));
					}
					if(!empty($pin_bb)){
						$content_td .= $this->Html->tag('p', sprintf(__('PIN BB : <b>%s</b>'), $pin_bb));
					}

					if(!empty($content_td)){
						echo $this->Html->tag('td', $content_td);
					}
				}
		?>
	</tr>
</table>
<?php
			}
			
			if(!empty($UserCompany)){
?>
<table style="line-height: 10px;font-size: 12px;margin-top: 15px;">
	<tr>
		<?php
				echo $this->Html->tag('th', __('Kontak Perusahaan'), array(
					'style' => 'width:50%;text-align:left;'
				));
		?>
	</tr>
	<tr>
		<?php
				if(!empty($UserCompany)){
					$content_td = '';

					if(!empty($contact_email)){
						$content_td .= $this->Html->tag('p', sprintf(__('Email : <b>%s</b>'), $contact_email));
					}
					if(!empty($company_phone)){
						$content_td .= $this->Html->tag('p', sprintf(__('No. Telp : <b>%s</b>'), $company_phone));
					}
					if(!empty($company_phone_2)){
						$content_td .= $this->Html->tag('p', sprintf(__('No. Telp 2 : <b>%s</b>'), $company_phone_2));
					}
					if(!empty($fax)){
						$content_td .= $this->Html->tag('p', sprintf(__('Fax : <b>%s</b>'), $fax));
					}
					if(!empty($address) || !empty($location)){
						$temp = '';

						if(!empty($location)){
							$temp = $location;
						}
						if(!empty($address)){
							if(!empty($location)){
								$temp .= ' - ';
							}

							$temp .= $address;
						}

						$content_td .= $this->Html->tag('p', sprintf(__('Alamat : <b>%s</b>'), $temp));
					}

					if(!empty($content_td)){
						echo $this->Html->tag('td', $content_td);
					}
				}
		?>
	</tr>
</table>
<?php
			}

		echo $content;
?>