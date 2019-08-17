<?php
		$icon_delete = $this->Rumahku->icon('rv4-bold-cross');
		$icon_stack = $this->Rumahku->icon('rv4-stack');
		$icon_user_group = $this->Rumahku->icon('rv4-user-group');
		$icon_gear = $this->Rumahku->icon('rv4-gear');
?>
<div class="actions">
	<ul class="list-action-acl">
		<li>
			<?php 
				echo $this->Html->link(__($icon_gear.' Manage'), array('action' => 'manage'), 
					array(
						'class' => 'btn default',
						'escape' => false
					)
				); 
			?>
		</li>
		<li>
			<?php 
				echo $this->Html->link(__($icon_gear.' Permissions'), array('action' => 'permissions'), 
					array(
						'class' => 'btn default',
						'escape' => false
					)
				); 
			?>
		</li>
		<li>
			<?php 
				echo $this->Html->link(__($icon_stack.' Update ACOs'), array('action' => 'update_acos'),
					array(
						'class' => 'btn default',
						'escape' => false
					)
				); 
			?>
		</li>
		<li>
			<?php 
				echo $this->Html->link(__($icon_user_group.' Update AROs'), array('action' => 'update_aros'),
					array(
						'class' => 'btn default',
						'escape' => false
					)); 
			?>
		</li>
		<li>
			<?php 
				echo $this->Html->link(__($icon_delete.' Drop ACOs/AROs'), array('action' => 'drop'), array(
						'class' => 'btn default',
						'escape' => false
					), __("Do you want to drop all ACOs and AROs?")); 
			?>
		</li>
		<li>
			<?php 
				echo $this->Html->link(__($icon_delete.' Drop permissions'), array('action' => 'drop_perms'), array(
						'class' => 'btn default',
						'escape' => false
					), __("Do you want to drop all the permissions?")
					); 
			?>
		</li>
		<div class='clear'></div>
	</ul>
</div>
<div class='clear'></div>
