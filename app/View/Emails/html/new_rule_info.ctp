<?php
		$link_rule 	= Common::hashEmptyField($params, 'link_rule');
		$rule_name 	= Common::hashEmptyField($params, 'Rule.name');
		$desc 		= Common::hashEmptyField($params, 'Rule.short_desc');
		$desc 		= $this->Rumahku->truncate($desc, 165);

		$greet = sprintf(__('Perusahaan telah menambahkan peraturan baru yaitu <strong>"%s"</strong>.'), $rule_name);

		echo $this->Html->tag('p', $greet, array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		$label_desc = $this->Html->tag('p', __('Deskripsi singkat :'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		echo $this->Html->tag('div', $label_desc.$this->Html->tag('p', $desc, array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		)), array(
			'class' => 'description',
			'style' => 'color: #303030;background-color: #d0d0d0;border-radius:  5px;padding: 5px 10px;font-size: 14px;margin: 5px 0 20px;line-height: 20px;'
		));

		echo $this->Html->tag('p', __('Untuk lebih detail, silakan lihat peraturan tersebut dengan klik tombol di bawah.'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		echo $this->Html->tag('div', $this->Html->link(__('Lihat Rule'), $link_rule, array(
				'target' => '_blank',
			'style' => 'padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px; text-align: center;'
		)), array(
			'style' => 'display: block;margin: 20px 0px 0px;text-align: center;'
		));
?>