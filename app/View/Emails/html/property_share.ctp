<?php
		$data = $params['SharingProperty'];
		$property = $params['propertyData'];

		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$link_detail = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'properties',
			'action' => 'report_detail',
			$id,
		));

		echo $this->Html->tag('p', sprintf(__('Anda mendapatkan email yang berisi tentang laporan properti dari %s, silahkan klik link di bawah ini untuk melihat detail laporan.'), $data['sender_name']));
?>
	
		<table style="border: 1px solid #f2f2f2; border-radius: 3px; margin-bottom: 30px;">
			<thead>
				<tr style="background-color: #069E55;">
					<th style="width: 200px; color:white; border-bottom: 2px solid #f2f2f2; text-align: left; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Kota</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Pengunjung</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Lead</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Hot Lead</th>
				</tr>
			</thead>
			<tbody>

				<?php
						foreach( $params['values'] as $row ) {
							echo '<tr>';
								for( $i = 0; $i < 4; $i++ ) {
									$style = 'text-align:center;';
									if( $i == 0 ){
										$style = 'text-align:left;';
									}
									echo sprintf('<td style="%s">%s</td>', $style, $row[$i]);
								}
							echo '</tr>';
						}
				?>
				
			</tbody>
		</table>
<?php
		echo $this->Html->tag('p', 
			$this->Html->link(__('Klik Link'), $link_detail, array(
					'style' => 'color: #00af00; text-decoration: none; font-size: 14px;', 'target'=> '_blank'
				)
			), array(
			'style' => 'color: #303030; font-size: 14px; margin: 0 0 20px; line-height: 20px;'
		));
?>