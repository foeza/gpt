<?php
		$userList  = !empty($userList) ? $userList : false;

		$options = array(
			'frameClass' => 'col-sm-12 col-md-8',
		);
		echo $this->Rumahku->buildInputForm('User.superior_id', array_merge($options, array(
            'label' => __('Atasan Anda *'),
            'empty' => __('Pilih Atasan Anda'),
            'options' => $userList,
        )));
?>