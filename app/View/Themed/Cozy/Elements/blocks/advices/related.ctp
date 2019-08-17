<?php 
		$save_path = Configure::read('__Site.advice_photo_folder');
		
		if( !empty($related) ) {
			echo $this->Html->tag('h2', __('%s Terkait', Configure::read('Global.Data.translates.id.blog')), array(
				'class' => 'section-title',
			));
?>
<ul class="latest-news">
	<?php 
			foreach ($related as $key => $val) {
				$id = $this->Rumahku->filterEmptyField($val, 'Advice', 'id');
				$slug = $this->Rumahku->filterEmptyField($val, 'Advice', 'slug');
				$title = $this->Rumahku->filterEmptyField($val, 'Advice', 'title');
				$photo = $this->Rumahku->filterEmptyField($val, 'Advice', 'photo');
				$modified = $this->Rumahku->filterEmptyField($val, 'Advice', 'modified');

				$customModified = $this->Rumahku->formatDate($modified, 'M d, Y');
				$customPhoto = $this->Rumahku->photo_thumbnail(array(
					'save_path' => $save_path, 
					'src'=> $photo, 
					'size'=>'m',
				), array(
					'title'=> $title, 
					'alt'=> $title, 
				));
				$url = array(
					'controller' => 'advices',
					'action' => 'read',
					$id,
					$slug,
					'admin' => false,
				);
	?>
	<li>
		<div class="image">
			<?php 
					echo $this->Html->link('', $url);
					echo $customPhoto;
			?>
		</div>
		
		<ul class="top-info">
			<?php 
					echo $this->Html->tag('li', sprintf('%s %s', $this->Rumahku->icon('fa fa-calendar'), $customModified));
			?>
		</ul>
		<?php 
				echo $this->Html->tag('h3', $this->Html->link($title, $url));
		?>
	</li>
	<?php 
			}
	?>
</ul>
<?php 
		}
?>