<?php 
		$client_name = $this->Rumahku->filterEmptyField($params, 'client_name');
		$new_agent_name = $this->Rumahku->filterEmptyField($params, 'new_agent_name');

		printf(__('Kami ingin menginformasikan bahwa klien Anda ( %s ) telah dipindahkan ke Agen %s.'), $client_name, $new_agent_name);
		echo "\n";
?>