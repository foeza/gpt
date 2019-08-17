<?php 
		$url = !empty($url)?$url:'#';
?>
<div class="small-action">
	<div class="row">
		<?php
				echo $this->Html->tag('div', $this->Html->link($this->Rumahku->icon('rv4-arrow-left').__('Kembali'), $url, array(
					'escape' => false,
					'class' => 'back-to',
				)), array(
					'class' => 'col-sm-2',
				));
		?>
	</div>
</div>