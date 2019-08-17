<?php
		$save_path = Configure::read('__Site.logo_photo_folder');
		$site_name = Configure::read('__Site.site_name');
		$default_bunga_kpr = Configure::read('__Site.bunga_kpr');
		$default_interest_rate = Configure::read('__Site.interest_rate');
		$default_kpr_credit_fix = Configure::read('__Site.kpr_credit_fix');

		$value = !empty($value)?$value:false;
		$classButtonLeft = !empty($classButtonLeft)?$classButtonLeft:false;
		$classButtonRight = isset($classButtonRight)?$classButtonRight:'no-pleft';

        $_action = $this->Rumahku->filterEmptyField($value, 'Property', 'property_action_id');
        $sold = $this->Rumahku->filterEmptyField($value, 'Property', 'sold');
        $_type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'is_building');

        if( $_action == 1 && $_type && empty($sold) ) {  
			$mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');

			$propertyPrice = $this->Rumahku->getMeasurePrice($value);

			$dp = $this->Rumahku->filterEmptyField($bankKpr, 'BankSetting', 'dp', $default_bunga_kpr);
			$interest_rate_fix = $this->Rumahku->filterEmptyField($bankKpr, 'BankSetting', 'interest_rate_fix', $default_interest_rate);
			$periode_installment = $this->Rumahku->filterEmptyField($bankKpr, 'BankSetting', 'periode_installment', $default_kpr_credit_fix);

			$bunga_kpr_persen = $this->Rumahku->_getBungaKPRPersen($dp);
			$total_loan = $propertyPrice * $bunga_kpr_persen;
			$total_dp =  $propertyPrice - $total_loan;
			$_allowKpr = $this->Rumahku->_allowKpr($value);

			if( !empty($bankKpr) ) {
				$bank_name = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'name');
				$bank_code = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'code', 'rumahku');
				$bank_code = strtolower($bank_code);
				$color = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'bg_color');
				$logo = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'logo');

				$logo = $this->Rumahku->photo_thumbnail(array(
					'save_path' => $save_path, 
					'src' => $logo, 
					'thumb' => false,
				), array(
					'title'=> $bank_name, 
					'alt'=> $bank_name, 
					'style' => 'background:#'.$color,
				));
			} else {
				$bank_name = $site_name;
				$color = '5eab1f';
				$bank_code = false;
				$logo = $this->Html->image('/img/primesystem.png');
			}

			$total_first_credit = $this->Rumahku->creditFix($total_loan, $interest_rate_fix, $periode_installment);

			$urlKpr = array(
				'controller' => 'kpr',
				'action' => 'bank_calculator',
				'slug' => 'kalkulator-kpr',
				'bank_code' => $bank_code,
				'mls_id' => $mls_id,
			);
?>

<section id="kpr" class="kpr-wrapper col-sm-12 hidden-print">
	<div class="header-kpr-btn">
		<?php
				echo $this->Html->tag('div', $this->Html->tag('div', __('Simulasi KPR'), array(
					'class' => 'title',
				)), array(
					'class' => 'left-side',
				));
				echo $this->Html->tag('div', $logo, array(
					'class' => 'right-side',
				));
		?>
	</div>
	<?php 
			echo $this->element('widgets/kpr/forms/calculator');
	?>
</section>
<?php 
		}
?>