<?php
		$allPackage = Configure::read('Global.Data.package_options.all-package');
		$package_r123 = $this->Rumahku->filterPackagePartner($allPackage, array(
			'partner_package' => 'rumah-123',
		));

		$addon_r123 = $this->Rumahku->filterEmptyField($data, 'UserIntegratedOrder', 'addon_r123');
		$trigger_form_r123 = !empty($addon_r123)?'shows':'hide';
?>

<div class="cb-custom mt0 pd-top7 mb10">
    <div class="cb-checkmark">
        <?php   
                echo $this->Form->input('addon_r123',array(
                    'div' => false,
                    'label'=> false,
                    'required' => false,
                    'class' => 'trigger-toggle',
                    'data-show' => '#wrapper-package-r123',
                    'type' => 'checkbox',
                ));
                echo $this->Form->label('addon_r123', __('Integrasi Rumah123'));
        ?>
    </div>
</div>
<div id="wrapper-package-r123" class="<?php echo $trigger_form_r123; ?>">
    <?php
    		echo $this->Html->tag('div',
    			$this->Form->input('email_r123', array(
    				'type' => 'email',
	            	'label' => __('Email integrasi ke Rumah123'),
	            	'required' => false,
                    'div' => false,
	            	'autocomplete' => 'off',
	            	'class' => 'mb10',
	        	)), array(
	            	'class' => !empty($is_email_all_addon)?'trigger-hide hide':'trigger-hide shows',
	        ));

			if (!empty($package_r123)) {
    ?>
	<div class="container-package">
		<h4>Pilih Paket Membership Rumah123</h4>
		<div class="row">
			<?php
					foreach ($package_r123['PackageAddonList'] as $key => $value) {
						$idPackage = Common::hashEmptyField($value, 'UserIntegratedAddonPackage.id');
						$packageName = Common::hashEmptyField($value, 'UserIntegratedAddonPackage.name');
						$price = Common::hashEmptyField($value, 'UserIntegratedAddonPackage.price');
						$description = Common::hashEmptyField($value, 'UserIntegratedAddonPackage.description');

						$price = $this->Rumahku->getFormatPrice($price);
						$pricePackage = sprintf('IDR. %s/Th', $price);

						$selectedID = Common::hashEmptyField($data, 'UserIntegratedAddonPackage.id');
						$checked = '';
						if(!empty($selectedID) && $selectedID == $idPackage){
							$checked = 'checked';
						}
			?>
					<div class="col-sm-4 applications-list user-list">
						<div class="table-head">
							<label class="container name-package">
								<?php
										echo $packageName;
								?>
								<input type="radio" name="data[UserIntegratedOrderAddon][r123_package_id]" id="UserIntegratedOrderAddonId" value="<?php echo $idPackage?>" <?php echo $checked; ?>>
								<span class="checkmark"></span>
							</label>
						</div>
						<div class="wrap-head">
							<?php
									echo $this->Html->tag('div', $pricePackage, array(
										'class' => 'price-package',
									));
									echo $this->Html->tag('div', $description, array(
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