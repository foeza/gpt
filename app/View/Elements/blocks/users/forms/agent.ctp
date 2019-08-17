<?php
		$inactive = !empty($inactive) ? $inactive : false;
		$photo = Common::hashEmptyField($value, 'User.photo');
		$user_id = Common::hashEmptyField($value, 'User.id');
		$group_id = Common::hashEmptyField($value, 'User.group_id');
		$full_name = Common::hashEmptyField($value, 'User.full_name');
		$email = Common::hashEmptyField($value, 'User.email');
		$no_hp = Common::hashEmptyField($value, 'UserProfile.no_hp', false, array(
			'urldecode' => false,
		));
		$no_hp_is_whatsapp = Common::hashEmptyField($value, 'UserProfile.no_hp_is_whatsapp');
		$no_hp_2 = Common::hashEmptyField($value, 'UserProfile.no_hp_2', false, array(
			'urldecode' => false,
		));
		$no_hp_2_is_whatsapp = Common::hashEmptyField($value, 'UserProfile.no_hp_2_is_whatsapp');

		$property_count = Common::hashEmptyField($value, 'User.property_count');
		$client_count = Common::hashEmptyField($value, 'User.client_count');

		$address = $this->User->getAddress($value);

		$photo = $this->Rumahku->photo_thumbnail(array(
			'save_path' => Configure::read('__Site.profile_photo_folder'), 
			'src'=> $photo, 
			'size' => 'pm',
		), array(
			'alt' => $full_name,
			'title' => $full_name,
			'class' => 'default-thumbnail',
		));
?>
<div class="modal-subheader">
	<div id="list-property">
		<div class="item row">
			<div class="col-sm-3 col-sm-offset-1">
				<?php
						echo $this->Html->tag('div', $photo, array(
							'class' => 'centered',
						));
				?>
			</div>
			<div class="col-sm-8 no-pleft">
				<div class="row">
					<div class="col-sm-6">
						<?php
								if($full_name){
									echo $this->Html->tag('div', __('Nama: %s', $this->Html->tag('strong', $full_name)));
								}

								if($email){
									echo $this->Html->tag('div', __('Email: %s', $this->Html->tag('strong', $email)));
								}

								if($no_hp){
									$no_hp = !empty($no_hp_is_whatsapp) ? sprintf('%s (WA)', $no_hp) : $no_hp;
									echo $this->Html->tag('div', __('No HP: %s', $this->Html->tag('strong', $no_hp)));
								}

								if($no_hp_2){
									$no_hp_2 = !empty($no_hp_2_is_whatsapp) ? sprintf('%s (WA)', $no_hp_2) : $no_hp_2;
									echo $this->Html->tag('div', __('No HP 2: %s', $this->Html->tag('strong', $no_hp_2)));
								}

								if($address){
									echo $this->Html->tag('div', __('Alamat: %s', $this->Html->tag('strong', $address)));
								}
						?>
					</div>
					<?php
							if($group_id == 2){
					?>
					<div class="col-sm-6">
						<?php
									if($property_count > 0){
										$link = $this->Html->link($property_count, array(
											'controller' => 'properties',
											'action' => 'info',
											$user_id,
											'admin' => true,
										), array(
											'target' => '_blank',
											'class' => 'direct-link',
										));
									} else {
										$link = 0;
									}

									echo $this->Html->tag('div', __('Jumlah Properti: %s', $this->Html->tag('strong', $link)), array(
										'class' => 'mt15',
									));

									if($client_count > 0){
										$link = $this->Html->link($client_count, array(
											'controller' => 'users',
											'action' => 'client_info',
											$user_id,
											'admin' => true,
										), array(
											'target' => '_blank',
											'class' => 'direct-link',
										));
									} else {
										$link = 0;
									}
									
									echo $this->Html->tag('div', __('Jumlah Klien: %s', $this->Html->tag('strong', $link)), array(
									));
						?>
					</div>
					<?php
							}
					?>
				</div>
			</div>
		</div>
	</div>
</div>