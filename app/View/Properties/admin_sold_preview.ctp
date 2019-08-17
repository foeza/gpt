<div class="content">
	<div id="list-property">
		<?php 
				echo $this->element('blocks/properties/items', array(
					'value' => $value,
					'propertySold' => $propertySold,
					'fullDisplay' => false,
				));
		?>
	</div>
</div>