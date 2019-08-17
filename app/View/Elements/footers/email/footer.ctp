<table border="0" cellpadding="0" cellspacing="0" width="600px;" style="margin: auto; text-align: center;">
	<tbody>
		<tr>
			<td>
				<?php

					echo($this->Html->image('prime-icon.png', array(
						'width'		=> 30, 
						'fullBase'	=> true, 
					)));

				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php

					echo($this->Html->tag('p', __('Indonesia\'s No. 1 Property Network & Technology'), array(
						'style' => 'margin: 10px 0; display: block; font-size: 14px; text-transform: uppercase; color: #8B8B8B;', 
					)));

				?>
			</td>
		</tr>
		<tr>
			<td width="100%" height="1px" style="margin: 15px 0; display: block; background: #e3e3e3;"></td>
		</tr>
		<tr>
			<td style="display: block; color: #434343">
				<?php

					echo($this->Html->tag('p', __('Download aplikasi Prime for Agent di'), array(
						'style' => 'margin: 0; font-size: 14px; font-weight: bold', 
					)));

					$imageOpts = array(
						'width'		=> '150', 
						'style'		=> 'margin:10px;', 
						'fullBase'	=> true, 
					);

					$downloadURL = 'https://itunes.apple.com/us/app/prime-for-agent/id1297181356?mt=8';
					echo($this->Html->link($this->Html->image('apple-store-badge.png', $imageOpts), $downloadURL, array(
						'target' => '_blank', 
						'escape' => false,
					)));

					$downloadURL = 'https://play.google.com/store/apps/details?id=com.primesystem.id';
					echo($this->Html->link($this->Html->image('google-play-badge.png', $imageOpts), $downloadURL, array(
						'target' => '_blank', 
						'escape' => false,
					)));

				?>
			</td>
		</tr>
		<tr>
			<td width="100%" height="1px" style="margin: 0 0 15px; display: block; background: #e3e3e3;"></td>
		</tr>
		<tr>
			<td style="margin: 0 0 30px; display: block; color: #b4b4b4">
				<?php

					echo($this->Html->tag('p', __('Email ini dibuat secara otomatis. Mohon tidak mengirimkan balasan ke email ini.'), array(
						'style' => 'margin: 0; font-size: 12px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>