<?php
		$data 			= $this->request->data;
		$packages 	  	= !empty($packages)?$packages:false;
		$display_item 	= !empty($display_item)?$display_item:false;
		$custom_item 	= !empty($custom_item)?$custom_item:false;

		$is_premium 	= Common::hashEmptyField($_config, 'UserCompanyConfig.is_block_premium_listing');
		$selectedID 	= Common::hashEmptyField($_config, 'UserCompanyConfig.premium_listing');

		$model_name 	= Common::hashEmptyField($custom_item, 'model_name', 'UserCompanyConfig');
		$field_name 	= Common::hashEmptyField($custom_item, 'field_name', 'premium_listing');

		$data_saved 	= sprintf('data[%s][%s]', $model_name, $field_name);
		$model_selector = sprintf('%sId', $model_name);

		$membership_package_id = Common::hashEmptyField($data, 'User.membership_package_id');
		if (!empty($membership_package_id)) {
			$selectedID = $membership_package_id;
		}

		$classDisplay 	 = !empty($is_premium || $display_item)?'shows':'hide';

?>

<div id="wrapper-package" class="<?php echo $classDisplay; ?>">
    <?php
			if (!empty($packages)) {
    ?>
	<div class="container-package">
		<div class="row">
			<?php
					foreach ($packages as $key => $value) {
						$idPackage 		= Common::hashEmptyField($value, 'MembershipPackage.id');
						$packageName 	= Common::hashEmptyField($value, 'MembershipPackage.name');
						$limit_property = Common::hashEmptyField($value, 'MembershipPackage.limit_premium_property');

						$features 		= $this->Rumahku->callFeatureMembership($value);
						
						$checked = '';
						if(!empty($selectedID) && $selectedID == $idPackage){
							$checked = 'checked';
						}
			?>
					<div class="mb15 col-sm-4 applications-list user-list premium-list-item">
						<div class="table-head">
							<label class="container name-package">
								<?php
										echo $packageName;
								?>
								<input type="radio" name="<?php echo $data_saved; ?>" id="<?php echo $model_selector; ?>" value="<?php echo $idPackage?>" <?php echo $checked; ?>>
								<span class="checkmark"></span>
							</label>
						</div>
						<div class="wrap-head">
							<?php
									echo $this->Html->tag('div', $features, array(
										'class' => 'table-responsive',
									));
							?>
						</div>
					</div>
			<?php

					}
			?>

		</div>
	</div>
	<?php
			}
	?>

</div>