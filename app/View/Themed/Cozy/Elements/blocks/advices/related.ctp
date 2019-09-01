<?php 
		$save_path = Configure::read('__Site.advice_photo_folder');
		
		if( !empty($related) ) {
			echo $this->Html->tag('h2', __('Artikel Terkait'), array(
				'class' => 'section-title',
			));
?>
<ul class="latest-news">
	<?php 
			foreach ($related as $key => $val) {
				$id			= Common::hashEmptyField($val, 'Advice.id');
				$slug		= Common::hashEmptyField($val, 'Advice.slug');
				$title		= Common::hashEmptyField($val, 'Advice.title');
				$photo		= Common::hashEmptyField($val, 'Advice.photo');
				$modified	= Common::hashEmptyField($val, 'Advice.modified');

				$customModified = $this->Rumahku->formatDate($modified, 'M d, Y');
				
				$url = array(
					'controller' => 'blogs',
					'action' => 'read',
					$id,
					$slug,
					'admin' => false,
				);
	?>
	<li>
		<?php 
				echo $this->Html->tag('div', __('%s %s', $this->Rumahku->icon('fa fa-calendar'), $customModified));
				echo $this->Html->tag('ul', '', array(
					'class' => 'top-info',
				));
				echo $this->Html->tag('h3', $this->Html->link($title, $url, array(
					'title'=> $title, 
					'alt'=> $title, 
				)));
		?>
	</li>
	<?php 
			}
	?>
</ul>
<?php 
		}
?>