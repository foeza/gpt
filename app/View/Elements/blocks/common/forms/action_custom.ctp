<?php
		$_column_class 	= isset( $_column_class ) ? $_column_class : 'col-sm-12';
		$_margin_class 	= isset( $_margin_class ) ? $_margin_class : 'pd0';
		$_float_class	= isset( $_float_class ) ? $_float_class : 'floright';
		$_with_submit 	= isset( $_with_submit ) ? $_with_submit : false;
		$_button_text 	= isset( $_button_text ) ? $_button_text : 'Cari';
		$_button_color 	= isset( $_button_color ) ? $_button_color : 'blue';
		$_button_class 	= isset( $_button_class ) ? $_button_class : '';
		$_attributes 	= isset( $_attributes ) ? $_attributes : array();
		$_urlBack 		= isset( $_urlBack ) ? $_urlBack : false;
		$_textBack 		= isset( $_textBack ) ? $_textBack : __('Kembali');
		$_classBack 	= isset( $_classBack ) ? $_classBack : 'btn default';
		$_buttons 		= !empty( $_buttons ) ? $_buttons : false;

		$default_attributes = array(
			'type' => 'submit', 
			'class' => sprintf('btn %s %s', $_button_class, $_button_color),
		);
		
		if( !empty($_attributes) ) {
			$default_attributes = array_merge($default_attributes, $_attributes);
		}
?>

<div class="row">
	<div class="<?php echo $_column_class; ?>">
        <div class="action-group <?php echo $_margin_class; ?>">
            <div class="btn-group <?php echo $_float_class; ?>">
				<?php
						if( !empty($_with_submit) ) {
							echo $this->Form->button($_button_text, $default_attributes);
						}

						if( !empty($_urlBack) ) {
							echo $this->Html->link($_textBack, $_urlBack, array(
								'class'=> $_classBack,
							));
						}
						
						if( !empty($_buttons) ) {
							foreach ($_buttons as $key => $btn) {
								$label = $this->Rumahku->filterEmptyField($btn, 'label');
								$url = $this->Rumahku->filterEmptyField($btn, 'url');
								$attributes = $this->Rumahku->filterEmptyField($btn, 'attributes');
								
								echo $this->Html->link($label, $url, $attributes);
							}
						}
				?>
			</div>
		</div>
	</div>
</div>