<?php
		$data = $params['SharingProperty'];
		$property = $params['propertyData'];

		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$link_detail = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'properties',
			'action' => 'report_visitor_detail',
			$id,
		));

		echo $this->Html->tag('p', sprintf(__('Anda mendapatkan email yang berisi tentang laporan pengunjung properti dari %s, silahkan klik link di bawah ini untuk melihat detail laporan.'), $data['sender_name']));
?>
	
		<table style="border: 1px solid #f2f2f2; border-radius: 3px; margin-bottom: 30px;">
			<thead>
				<tr style="background-color: #069E55;">
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: left; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Tanggal Kunjung</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Nama</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Email</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">No. HP</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">Browser</th>
					<th style="color:white; border-bottom: 2px solid #f2f2f2; text-align: center; text-transform: uppercase; font-size: 12px; padding: 10px; vertical-align: middle;">UTM</th>
				</tr>
			</thead>
			<tbody>
				<?php
						foreach( $params['values'] as $key => $value ) {
							$visit_date = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'created');
			                $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name', '-');
			                $email = $this->Rumahku->filterEmptyField($value, 'User', 'email', '-');
			                $no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
			                $browser = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'browser', '-');
			                $utm = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'utm', '-');

		  					$customVisitDate = $this->Rumahku->formatDate($visit_date, 'd M Y');
							echo $this->Html->tableCells(array(
								array(
									$customVisitDate,
						        	$name,
						        	$email,
						        	$no_hp,
						        	$browser,
						        	$utm,
						        )
						    ));
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