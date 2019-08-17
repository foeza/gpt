<?php
		if(!empty($expired_date) && !empty($periode_booking_unit) && !empty($booking_code)){
			$expired_date = date('M j, Y H:i:s', strtotime($expired_date));
?>
<div class="timer padding-vert-1 align-center">
	<p class="margin-bottom-2 cgray2"><span class="prm-time large-text"></span> Batas waktu pemesanan akan berakhir pada: <span class="bold cgreen countdown-timer">0 menit 00 detik</span></p>
	<?php
			echo $this->Form->hidden('time-hidden', array(
				'value' => $expired_date,
				'class' => 'times-expired'
			));
			echo $this->Form->hidden('periode-max-expired-hidden', array(
				'value' => $periode_booking_unit,
				'class' => 'periode-max-expired'
			));
			echo $this->Form->hidden('cart-id-booking-unit-hidden', array(
				'value' => $booking_code,
				'class' => 'cart-id-booking-unit'
			));
	?>
</div>
<?php
		}else{
			echo $this->Form->hidden('time-hidden', array(
				'value' => 1,
				'class' => 'delete-cookie'
			));
		}
?>