<?php 	
		$save_path = Configure::read('__Site.advice_photo_folder');

		$id = $this->Rumahku->filterEmptyField($value, 'Advice', 'id');
		$slug = $this->Rumahku->filterEmptyField($value, 'Advice', 'slug');
		$category = $this->Rumahku->filterEmptyField($value, 'AdviceCategory', 'name');
		$title = $this->Rumahku->filterEmptyField($value, 'Advice', 'title');
		$short_content = $this->Rumahku->filterEmptyField($value, 'Advice', 'short_content');
		$content = $this->Rumahku->filterEmptyField($value, 'Advice', 'content', false, false);
		$photo = $this->Rumahku->filterEmptyField($value, 'Advice', 'photo');
		$modified = $this->Rumahku->filterEmptyField($value, 'Advice', 'modified');

		$author = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');

		$customPhoto = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $save_path, 
			'src'=> $photo, 
			'size' => 'l',
			'url' => true,
		));
		$customModified = $this->Rumahku->formatDate($modified, 'M d, Y');

		$url = array(
			'controller' => 'advices',
			'action' => 'read',
			$id,
			$slug,
			'admin' => false,
		);
		$linkUrl = $this->Html->url($url, true);

		$this->Html->addCrumb(Configure::read('Global.Data.translates.id.blog'), array(
			'action' => 'index',
		));
		$this->Html->addCrumb($title);
?>
<div class="content gray">
	<div class="container">
		<div class="row">
			<!-- BEGIN MAIN CONTENT -->
			<div class="main col-sm-12 col-md-8">
				<?php 
						echo $this->Html->tag('h1', $title, array(
							'class' => 'blog-title',
						));
				?>
				<div class="blog-main-image center">
					<?php 
							echo $this->Html->image($customPhoto, array(
								'title' => $title,
								'alt' => $title,
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
								echo $this->Html->tag('li', sprintf(__('%s <fb:comments-count href="%s"></fb:comments-count>'), $this->Rumahku->icon('fa fa-comments-o'), Router::url($linkUrl)));
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
							'share_type' => 'berita',
			                'url' => $linkUrl,
			                'title' => $title,
							'_comment' => true,
						)));
				?>
			</div>
			<div class="sidebar gray col-sm-12 col-md-4 hidden-print">
				<?php 
						echo $this->element('blocks/advices/related');
                    	echo $this->element('blocks/advices/forms/searchs', array(
                    		'url' => array(
				                'controller' => 'advices',
				                'action' => 'search',
				                'index',
				                'admin' => false,
				            ),
                		));
				?>
			</div>
		</div>
	</div>
</div>