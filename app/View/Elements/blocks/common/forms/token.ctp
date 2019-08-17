<?php
		$options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );      
?>
<div class="user-fill">
	<?php
			echo $this->Rumahku->buildInputForm('ApiSettingUser.user_key', array_merge($options, array(
                'label' => __('Username *'),
            )));
            echo $this->Rumahku->buildInputForm('ApiSettingUser.secret_key', array_merge($options, array(
                'label' => __('password *'),
            )));
	?>
</div>