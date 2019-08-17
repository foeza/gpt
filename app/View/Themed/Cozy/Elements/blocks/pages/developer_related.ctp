<?php 
		$save_path = Configure::read('__Site.general_folder');
		$developer_page = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_developer_page');
		
		if( !empty($developer_page) && !empty($related) ) {
			echo $this->Html->tag('h2', __('Developer Terbaru'), array(
				'class' => 'section-title',
			));
?>
<ul class="latest-news">
	<?php 
			foreach ($related as $key => $val) {
				$id = $this->Rumahku->filterEmptyField($val, 'ApiAdvanceDeveloper', 'id');
				$title = $this->Rumahku->filterEmptyField($val, 'ApiAdvanceDeveloper', 'name');
				$photo = $this->Rumahku->filterEmptyField($val, 'ApiAdvanceDeveloper', 'logo');
				$modified = $this->Rumahku->filterEmptyField($val, 'ApiAdvanceDeveloper', 'modified');

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
					'controller' => 'pages',
					'action' => 'developer_detail',
					$id,
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