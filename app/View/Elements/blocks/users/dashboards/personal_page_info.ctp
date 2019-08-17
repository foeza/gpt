<!--div class="wrapper-selector" id="personal-page-info-wrapper">
	<div class="dashbox">
		<div class="chart">
			<div class="chart-head">
				<div class="row">
					<div class="col-xs-12">
						<h3>Website Personal</h3>
					</div>
				</div>
			</div>
			<div class="chart-body">
				
			</div>
			<div class="chart-foot">
				<div class="summary-applied">
					<h3>Nama Paket</h3>
					<span><strong>Paket Membership Personal 1</strong></span>
					<span>Masa aktif 01 - 07 Jun 2018</span>
				</div>
			</div>
		</div>
	</div>
</div-->
<?php

	$authGroupID	= Configure::read('User.grup_id');
	$isAgent		= Common::validateRole('agent', $authGroupID);

	if($isAgent){
		$userData		= Configure::read('User.data');
		$hasMembership	= Hash::check($userData, 'MembershipPackage');

		if($hasMembership){
			$packageID		= Common::hashEmptyField($userData, 'MembershipPackage.id');
			$packageName	= Common::hashEmptyField($userData, 'MembershipPackage.name');
			$domain			= Common::hashEmptyField($userData, 'UserConfig.personal_web_url');
			$liveDate		= Common::hashEmptyField($userData, 'UserConfig.live_date');
			$endDate		= Common::hashEmptyField($userData, 'UserConfig.end_date');
			$activeDate		= $this->Rumahku->getCombineDate($liveDate, $endDate, '-');

			$currentDate	= date('Y-m-d');
			$isExpired		= strtotime($currentDate) > strtotime($endDate);

			?>
			<div class="dashbox">
				<div class="quick-data blueclr">
					<div class="icon">
						<i class="rv4-time"></i>
					</div>
					<div class="data">
						<?php

							echo($this->Html->tag('h4', __($packageName)));
							echo($this->Html->tag('span', $activeDate));

						//	nanti pake ini pas payment udah jalan
						//	<div class="quick-data-action">
						//		<a href="/admin/users/edit" class="btn default">Perpanjang</a>
						//		<a href="/admin/users/edit" class="btn default">Lihat Website</a>
						//	</div>

							if($domain){
								$icon = $this->Rumahku->icon('rv4-www');
								echo($this->Html->link(__('%s %s', $icon, $domain), $domain, array(
									'target' => '_blank', 
									'escape' => false, 
								)));
							}
							else{
								echo($this->Html->tag('small', __('Domain belum ditentukan'), array(
									'class' => 'color-red', 
								)));
							}

						?>
					</div>
				</div>
			</div>
			<?php

		}
	//	else{
		//	tawarin paket membership (next dev)
	//	}
	}

?>
