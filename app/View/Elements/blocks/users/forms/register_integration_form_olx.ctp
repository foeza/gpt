<?php
		$allPackage = Configure::read('Global.Data.package_options.all-package');
		$package_olx = $this->Rumahku->filterPackagePartner($allPackage, array(
			'partner_package' => 'olx',
		));

		$addon_olx = $this->Rumahku->filterEmptyField($data, 'UserIntegratedOrder', 'addon_olx');
		$trigger_form_olx = !empty($addon_olx)?'shows':'hide';
?>

<div class="cb-custom mt0 pd-top7 mb10">
    <div class="cb-checkmark">
        <?php   
                echo $this->Form->input('addon_olx',array(
                    'div' => false,
                    'label'=> false,
                    'required' => false,
                    'class' => 'trigger-toggle',
                    'data-show' => '#wrapper-package-olx',
                    'type' => 'checkbox',
                ));
                echo $this->Form->label('addon_olx', __('Integrasi OLX'));
        ?>
    </div>
</div>
<div id="wrapper-package-olx" class="<?php echo $trigger_form_olx; ?>">
    <?php
    		echo $this->Html->tag('div',
    			$this->Form->input('email_olx', array(
    				'type' => 'email',
	            	'label' => __('Email integrasi ke OLX'),
	            	'required' => false,
                    'div' => false,
	            	'autocomplete' => 'off',
	            	'class' => 'mb10',
	        	)), array(
	            	'class' => !empty($is_email_all_addon)?'trigger-hide hide':'trigger-hide shows',
	        ));

			if (!empty($package_olx)) {
    ?>
	<div class="container-package">
		<h4>Pilih Paket Membership OLX</h4>
		<div class="row">
			<?php
					// debug($package_olx);die();
					foreach ($package_olx['PackageAddonList'] as $key => $value) {
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
								<input type="radio" name="data[UserIntegratedOrderAddon][olx_package_id]" id="UserIntegratedOrderAddonId" value="<?php echo $idPackage?>" <?php echo $checked; ?>>
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