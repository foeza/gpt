<?php 	
		$save_path = Configure::read('__Site.general_folder');

		$id = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'id');
		$title = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'name');
		$photo = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'logo');
		$created = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'created');

		$content = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'description', false, false);
        $haveTags = $this->Rumahku->_checkHTMLtag($content);

        if (!$haveTags) {
            $content = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'description', false, false, 'EOL');
        }

		$customPhoto = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $save_path, 
			'src'=> $photo, 
			'size' => 'company',
			'url' => true,
		));
		$customCreated = $this->Rumahku->formatDate($created, 'M d, Y');

		$url = array(
			'controller' => 'pages',
			'action' => 'developer_detail',
			$id,
			'admin' => false,
		);
		$linkUrl = $this->Html->url($url, true);

		$this->Html->addCrumb(__('Developers'), array(
			'controller' => 'pages',
			'action' => 'developers',
			'admin' => false,
		));
		$this->Html->addCrumb($title);
?>
<div class="content gray">
	<div class="container">
		<div class="row">
			<!-- BEGIN MAIN CONTENT -->
			<div class="main col-sm-8">
				<?php 
						echo $this->Html->tag('h1', $title, array(
							'class' => 'blog-title',
						));
				?>
				<div class="blog-main-image center">
					<?php 
							echo $this->Html->image($customPhoto, array(
								'title' => $title,
								'alt' => sprintf('%s %s', $title, Configure::read('__Site.domain')),
							));
							echo $this->Html->tag('div', $this->Rumahku->icon('fa fa-file-text'), array(
								'class' => 'tag hidden-print',
							));
					?>
				</div>
				<div class="blog-bottom-info">
					<ul>
						<?php 
								echo $this->Html->tag('li', sprintf('%s %s', $this->Rumahku->icon('fa fa-calendar'), $customCreated));
								echo $this->Html->tag('li', sprintf(__('%s <fb:comments-count href="%s"></fb:comments-count>'), $this->Rumahku->icon('fa fa-comments-o'), Router::url($linkUrl)));
						?>
					</ul>
				</div>
				<?php 
						echo $this->Html->tag('div', $content, array(
							'class' => 'post-content read-article',
						));
						echo $this->Html->div('hidden-print', $this->element('blocks/common/share', array(
							'share_id' => $id,
							'share_type' => 'developer',
			                'url' => $linkUrl,
			                'title' => $title,
							'_comment' => true,
						)));
				?>
			</div>
			<div class="sidebar gray col-sm-4 hidden-print">
				<?php 
						echo $this->element('blocks/pages/developer_related');
						echo $this->element('blocks/common/sidebars/properties');
				?>
			</div>
		</div>
	</div>
</div>