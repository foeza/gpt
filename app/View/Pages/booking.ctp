<?php
		$config = !empty($config) ? $config : array();
		$cart_data = !empty($cart_data) ? $cart_data : array();

		$to_url = Common::hashEmptyField($cart_data, 'AdditionalData.url_cart');
		$booking_code = Common::hashEmptyField($cart_data, 'Booking.booking_code');

		$status = Common::hashEmptyField($config, 'status', 0, array(
			'isset' => true
		));

		$msg = Common::hashEmptyField($config, 'msg', '', array(
			'isset' => true
		));

		if($status == 1){

			echo $this->Html->tag('p', __('Lengkapi form data diri di bawah ini, untuk kami lanjutkan ke pihak pengembang.'));

			echo $this->Form->create('BookingProfile', array(
				'class' => 'ajax-form',
				'url' => $this->here,
				'data-type' => 'content',
				'data-wrapper-write' => '#modal-wrapper-booking',
				'id' => 'modal-wrapper-booking',
				'data-to-url' => true
			));

			if(!empty($to_url) && !empty($booking_code)){
				if(is_array($to_url)){
					$to_url = $this->Html->url($to_url, true);
				}
				
				echo $this->Html->tag('div', $to_url, array(
					'id' => 'to-url',
					'class' => 'hide',
				));
			}

?>
	<div class="input-group">
		<?php
				echo $this->Form->input('BookingProfile.full_name', array(
					'div' => false,
					'label' => __('Nama'),
					'type' => 'text',
					'required' => false,
				));
		?>
	</div>
	<div class="input-group">
		<?php
				echo $this->Form->input('BookingProfile.no_hp', array(
					'div' => false,
					'label' => __('Telepon'),
					'type' => 'tel',
					'required' => false
				));
		?>
	</div>
	<div class="input-group">
		<?php
				echo $this->Form->input('BookingProfile.email', array(
					'div' => false,
					'label' => __('Email'),
					'type' => 'email',
					'required' => false
				));
		?>
	</div>
	<div class="modal-footer">
		<?php
				echo $this->Form->button('Submit', array(
					'type' => 'submit',
					'class' => 'btn btn-primary',
					'div' => false
				));
		?>
	</div>
<?php
			echo $this->Form->end();
	
		}else{
			echo $this->Html->tag('p', $msg);
		}
?>