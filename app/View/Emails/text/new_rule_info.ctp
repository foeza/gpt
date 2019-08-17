<?php 
		$link_rule 	= Common::hashEmptyField($params, 'link_rule');
		$rule_name 	= Common::hashEmptyField($params, 'Rule.name');

		$greet = sprintf(__('Perusahaan telah menambahkan peraturan baru yaitu "%s". Silakan lihat peraturan tersebut dengan klik link di bawah.'), $rule_name);

		$title_properti = sprintf('%s - #%s', $title, $mls_id);

		$label = $this->Property->getNameCustom($params);
		$slug = $this->Rumahku->toSlug($label);

		$url = $this->Html->url($link_rule, true);

		echo $greet."\n\n";
		printf(__('Lihat Rule : %s'), $url);
		echo "\n";
?>