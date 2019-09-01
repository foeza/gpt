<?php 	
		$save_path 	= Configure::read('__Site.advice_photo_folder');

		$id			= Common::hashEmptyField($value, 'Advice.id');
		$slug		= Common::hashEmptyField($value, 'Advice.slug');
		$category	= Common::hashEmptyField($value, 'AdviceCategory.name');
		$title		= Common::hashEmptyField($value, 'Advice.title');
		$content	= Common::hashEmptyField($value, 'Advice.content', false);
		$photo		= Common::hashEmptyField($value, 'Advice.photo');
		$modified	= Common::hashEmptyField($value, 'Advice.modified');

		$customPhoto = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $save_path, 
			'src'		=> $photo, 
			'size' 		=> 'l',
			'url' 		=> true,
		));
		$customModified = $this->Rumahku->formatDate($modified, 'M d, Y');

		$author 	= __('@grosirpasartasik');
		$ig 		= 'https://www.instagram.com/grosirpasartasik/';
		$author 	= $this->Html->link($author, $ig, array(
			'_target' => 'blank',
		));

		$url = array(
			'controller' => 'blogs',
			'action' => 'read',
			$id,
			$slug,
			'admin' => false,
		);
		$linkUrl = $this->Html->url($url, true);

		$this->Html->addCrumb('Artikel', array(
			'action' => 'index',
		));
		$this->Html->addCrumb($title);

?>
<div class="content gray">
	<div class="container">
		<div class="row">
			<!-- BEGIN MAIN CONTENT -->
			<div class="main col-sm-12 col-md-8">
				<div class="blog-main-image center">
					<?php 
							echo $this->Html->image($repeated_img, array(
								'title' => $title,
								'alt' 	=> $title,
								'class' => 'lazy-image',
								'data-original' => $customPhoto,
							));
							echo $this->Html->tag('div', $this->Rumahku->icon('fa fa-file-text'), array(
								'class' => 'tag',
							));
					?>
				</div>
				<div class="blog-bottom-info">
					<ul>
						<?php 
								echo $this->Html->tag('li', sprintf('%s %s', $this->Rumahku->icon('fa fa-calendar'), $customModified));
								echo $this->Html->tag('li', sprintf('%s %s', $this->Rumahku->icon('fa fa-tags'), $category));
						?>
					</ul>
					<?php
							echo $this->Html->tag('div', $this->Rumahku->icon('fa fa-pencil').$author, array(
								'id' => 'post-author',
							));
					?>
				</div>
				<?php 
						echo $this->Html->tag('div', $content.$this->Rumahku->clearfix(), array(
							'class' => 'post-content read-article',
						));
						echo $this->Html->div('hidden-print', $this->element('blocks/common/share', array(
							'share_id' => $id,
							'share_type' => 'blog',
			                'url' => $linkUrl,
			                'title' => $title,
							'_comment' => true,
						)));
				?>
			</div>
			<div class="sidebar col-sm-12 col-md-4 hidden-print">
				<?php 
						echo $this->Html->tag('div', $this->element('blocks/advices/related'), array(
							'class' => 'wrapper-widget-sidebar',
						));

                //     	echo $this->element('blocks/advices/forms/searchs', array(
                //     		'url' => array(
				            //     'controller' => 'blogs',
				            //     'action' => 'search',
				            //     'index',
				            //     'admin' => false,
				            // ),
                // 		));
				?>
			</div>
		</div>
	</div>
</div>