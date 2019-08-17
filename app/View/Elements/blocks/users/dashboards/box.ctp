<?php 
		$frameClass = !empty($frameClass)?$frameClass:'col-sm-4';
		$class = !empty($class)?$class:false;
		$value = !empty($value)?$value:0;
		$title = !empty($title)?$title:false;
		$icon = !empty($icon)?$icon:false;
		$url = !empty($url)?$url:false;
		$urlTitle = !empty($urlTitle)?$urlTitle:false;

		if( !empty($value) ) {
?>
<div class="<?php echo $frameClass; ?>">
	<div class="dashbox">
		<div class="quick-data <?php echo $class; ?>">
			<?php
					echo $this->Html->tag('div', $this->Rumahku->icon($icon), array(
						'class' => 'icon'
					));

					$content = $this->Html->tag('h4', $value);
					$content .= $this->Html->tag('span', $title);

					if( !empty($url) ) {
						$content .= $this->Html->link(sprintf(__('%s &raquo;'), $urlTitle), $url, array(
							'escape' => false,
						));
					}

					echo $this->Html->tag('div', $content, array(
						'class' => 'data'
					));
			?>
		</div>
	</div>
</div>
<?php 
		}
?>