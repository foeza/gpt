<?php

	$value = empty($value) ? array() : $value;

//	debug($value);

	if(Hash::check($value, 'User') && Hash::check($value, 'UserProfile')){
		$fullName	= Common::hashEmptyField($value, 'User.full_name', '');
		$groupID	= Common::hashEmptyField($value, 'User.group_id', '');
		$isAgent	= Common::validateRole('agent', $groupID);

		if($isAgent){
			$phone		= Common::hashEmptyField($value, 'UserProfile.phone', '');
			$noHp		= Common::hashEmptyField($value, 'UserProfile.no_hp', '');
			$noHp2		= Common::hashEmptyField($value, 'UserProfile.no_hp_2', '');
			$noHpIsWa	= Common::hashEmptyField($value, 'UserProfile.no_hp_is_whatsapp', false);
			$noHp2IsWa	= Common::hashEmptyField($value, 'UserProfile.no_hp_2_is_whatsapp', false);

			if($phone || $noHp || $noHp2){
				if($noHp && $noHpIsWa){
					$whatsapp = $noHp;
				}
				else if($noHp2 && $noHp2IsWa){
					$whatsapp = $noHp2;
				}
				else{
					$whatsapp = '';	
				}

				if($whatsapp && substr($whatsapp, 0, 1) == 0){
					$whatsapp = substr_replace($whatsapp, '+62', 0, 1);
				}

				$theme	= Configure::read('Config.Company.data.Theme.name');
				$slug	= Configure::read('Config.Company.data.Theme.slug');

				?>
				<script type="text/javascript">
					(function () {
						var options = {
							whatsapp		: "<?php echo($whatsapp); ?>",
							call			: "<?php echo($noHp ?: $noHp2 ?: $phone); ?>",
							call_to_action	: "<?php echo(__('Hubungi %s', $fullName ? $fullName : 'Saya')); ?>",
							button_color	: "#A8CE50",
							position		: "right",
							order			: "whatsapp,call",
						};

						var proto	= document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
						var s		= document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';

						s.onload = function () { WhWidgetSendButton.init(host, proto, options); };

						var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
					})();
				</script>
				<style type="text/css">
					<?php

						$theme_haystack = array('apartement', 'apartment', 'bigcity');
						if( in_array(strtolower($slug), $theme_haystack) ){
							echo('#wh-widget-send-button { bottom: 60px !important; }');
						}

					?>
					@media (max-width: 768px) {
						#wh-widget-send-button {
							z-index: <?php echo(in_array(strtolower($slug), array('apartement', 'apartment', 'bigcity', 'villareal', 'realtyspace')) ? 1000 : 99); ?> !important;
							bottom: 100px !important;
						}

						#wh-widget-send-button.wh-widget-left {
							left: -5px !important;
						}

						#wh-widget-send-button.wh-widget-right {
							right: -10px !important;
						}
					}
				</style>
				<?php

			}
		}
	}

?>