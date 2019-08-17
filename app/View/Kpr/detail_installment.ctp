<?php
		$kpr_data = !empty($kpr_data) ? $kpr_data : false;
		$bank_setting = !empty($bank_setting) ? $bank_setting : false;

		$product_name = $this->Rumahku->filterEmptyField($bank_setting, 'BankProduct', 'name');
		$text_promo = $this->Rumahku->filterEmptyField($bank_setting, 'BankProduct', 'text_promo');
		$bank_name = $this->Rumahku->filterEmptyField($bank_setting, 'Bank', 'name');
		$bankNameCustom = $this->Html->tag('strong', $bank_name);

		if(!empty($kpr_data)){

			$title = __('Rincian installment %s', $bankNameCustom);
			$title = !empty($product_name) ? sprintf('%s | #%s', $title, $product_name) : $title;
?>

<div class="content" style="margin-bottom: 100px;">
	<div class="container">
		<div class="app-setup">

		</div>
	</div>
</div>
	<div class = "content">
		<div class = "container">
			<div class = "row">
			<div class="col-sm-12">
				<div id="wrapper-kpr">	
					<?php
							echo $this->Html->tag('h2', $title, array(
								'class' => 'hidden-print'
							));
					?>
				</div>
			</div>
		<?php
				echo $this->Html->tag('div', $this->element('blocks/kpr/installment_payment', array(
					'params' => $kpr_data,
					'not_scroll' => true,
				)));
		?>
		</div>
	</div>
</div>
<?php
		}
?>