<?php 
        $action = $this->action;
        $data = $this->request->data;
        $group_id = !empty($group_id)?$group_id:false;
        $principle_id = !empty($principle_id)?$principle_id:false;
        $urlBack = !empty($urlBack)?$urlBack:false;
?>
<div class="user-fill">
	<?php
			echo $this->Form->create('GroupTarget', array(
		        'id' => 'target-form',
		    ));

			if($attributeOptions){
				foreach ($attributeOptions as $attributeId => $attributeName) {
					$listID	= uniqid();

		            echo $this->Rumahku->buildInputForm(sprintf('GroupTarget.%s.value', $attributeId), array(
		                'type' => 'text',
		                'label' => $attributeName,
		                'textGroup' => __('Per Hari'),
		                'formGroupClass' => 'form-group input-text-center',
		                'class' => 'col-sm-6 col-xl-4',
		                'data' => $data,
		                'placeholder' => __('Masukkan Target'),
		            ));
					echo $this->Form->hidden(sprintf('GroupTarget.%s.attribute_option_id', $attributeId), array(
						'value' => $attributeId,
					));
				}
			}

	        echo $this->element('blocks/users/form_action', array(
	            'action_type' => 'bottom',
            	'urlBack' => $urlBack,
	        ));
			echo $this->Form->end();
	?>
</div>