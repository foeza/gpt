<?php 
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=kalkulator-kpr.xls');
?>
<div id="kpr-content">
	<?php 
			echo $this->Html->tag('h2', __('Simulasi KPR.'));

			$mls_id = '';
			if(!empty($property)) {
				$mls_id = $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');
				$propertyName = $this->Property->getNameCustom($property);
				$propertySlug = $this->Rumahku->toSlug($propertyName);

				echo $this->Html->tag('label', sprintf(__('<strong>Simulasi Angsuran Properti</strong> #%s'), $mls_id), array(
					'class' => 'title visible-print',
				));
			}
	?>

	<div class="content-description">
		<?php
				echo $this->Html->tag('div', $this->element('blocks/kpr/loan_summary_excel', array(
					'params' => $loan_summary,
					'mls_id' => $mls_id
				)), array(
					'id' => 'loan-summary',
				));


				echo $this->Html->tag('label',__('Catatan: '), array(
					'class' => 'kpr-note-title'
				));
				echo $this->Html->tag('span', __('Perhitungan ini berdasarkan asumsi kami pada aplikasi KPR secara umum. Data perhitungan di atas dapat berbeda dengan perhitungan bank. Untuk perhitungan yang akurat, silahkan hubungi bank penyedia pinjaman KPR.'), array(
					'class' => 'kpr-note-description'
				));
		?>
	</div>
</div>
<?php 
		echo $this->Html->tag('div', $this->element('blocks/kpr/installment_payment', array(
			'params' => $kpr_data,
			'_print' => true,
		)), array(
			'id' => 'installment-payment',
		));
?>