<?php 
		$client_name = $this->Rumahku->filterEmptyField($params, 'client_name');
		$new_agent_name = $this->Rumahku->filterEmptyField($params, 'new_agent_name');
?>
<p style="color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;">
	<?php
			printf(__('Kami ingin menginformasikan bahwa klien Anda ( %s ) telah dipindahkan ke Agen %s.'), $client_name, $new_agent_name);
	?>
</p>