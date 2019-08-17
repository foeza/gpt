<?php

//	tambahan saat direquest lewat easy mode
	$is_easy_mode	= isset($is_easy_mode) ? $is_easy_mode : false;
	$_wrapper_ajax	= isset($_wrapper_ajax) ? $_wrapper_ajax : false;
	$_data_reload	= isset($_data_reload) ? $_data_reload : true;

?>
<div id="<?php echo($is_easy_mode && $_wrapper_ajax ? $_wrapper_ajax : 'wrapper-modal-write'); ?>">
	<?php

			if($is_easy_mode){
				$formOpts = array(
					'title' => __('Media Properti'), 
					'data-size' => 'modal-fluid', 
					'class' => 'ajax-form',
					'data-type' => 'content',
					'data-wrapper-write' => $_wrapper_ajax ? sprintf('#%s', $_wrapper_ajax) : '#wrapper-modal-write',
					'data-reload' => $_data_reload ? 'true' : 'false',
				);
			}
			else{
				$formOpts = array(
					'class' => 'ajax-form',
					'data-type' => 'content',
					'data-wrapper-write' => '#wrapper-modal-write',
					'data-wrapper-success' => '#wrapper-write',
					'data-close-modal' => 'true',
				);
			}

			echo $this->Form->create('PropertyMedias', $formOpts);

			if($is_easy_mode && $_wrapper_ajax){
				echo($this->Form->hidden(false, array(
					'name'  => 'is_easy_mode', 
					'value' => $is_easy_mode, 
				)));

				echo($this->Form->hidden(false, array(
					'name'	=> '_wrapper_ajax', 
					'value'	=> $_wrapper_ajax, 
				)));
			}

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-2 col-sm-2 taright',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
        	$periodOptions = Configure::read('__Site.periode_options');
	?>
		<div class="content">
			<?php 
					echo $this->Rumahku->buildInputForm('title', array_merge($options, array(
						'type' => 'text',
		                'label' => __('Judul Foto'),
		            )));
			?>
		</div>
		<div class="modal-footer">
			<?php 

					$property_id = empty($property_id) ? false : $property_id;

					if($is_easy_mode && $property_id){
						$ajaxURL = array(
							'admin'			=> true, 
							'controller'	=> 'properties', 
							'action'		=> 'easy_media', 
							$property_id, 
						);

						echo $this->Html->link(__('Batal'), $ajaxURL, array(
							'class' => 'btn default ajaxModal',
							'title'	=> __('Media Properti'),
							'data-size' => 'modal-fluid', 
						));
					}
					else{
						echo $this->Html->link(__('Batal'), '#', array(
							'class' => 'close btn default',
							'data-dismiss' => 'modal',
							'aria-label' => 'close',
						));
					}

					echo $this->Form->button(__('Simpan'), array(
	    	            'class' => 'btn blue',
	    	        ));
			?>
		</div>
	<?php 
	        echo $this->Form->end();
	?>
</div>