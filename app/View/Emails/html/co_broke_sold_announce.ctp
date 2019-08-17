<?php
		$title 			= $this->Rumahku->filterEmptyField($params, 'Property', 'title');
		$change_date 	= $this->Rumahku->filterEmptyField($params, 'Property', 'change_date');
		
		$code = $this->Rumahku->filterEmptyField($params, 'CoBrokeProperty', 'code');
		$id = $this->Rumahku->filterEmptyField($params, 'CoBrokeProperty', 'id');

		$user_name = $this->Rumahku->filterEmptyField($params, 'User', 'full_name');
		$company_name = $this->Rumahku->filterEmptyField($params, 'UserCompany', 'name');

		$label = $this->Property->getNameCustom($params);
		$price = $this->Property->getPrice($params, __('(Harap hubungi Agen terkait)'));

		$specs = $this->Property->getSpec($params);

		echo $this->Html->tag('p', __('Berikut kami sampaikan bahwa property co-broke di bawah ini sudah terjual'));
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
			?>
		</td>
	</tr>
</table>
<?php
		echo $this->Html->tag('p', __('Harap hubungi pihak agen atau principle yang bersangkutan untuk mengetahui informasi lebih lanjut, terima kasih.'));
?>