<?php 	
		$save_path = Configure::read('__Site.advice_photo_folder');

		$this->Html->addCrumb($module_title);

		echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'advices',
                'action' => 'search',
                'index',
                'admin' => false,
            ),
        ));
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray">
	<div class="container">
		<div class="row">
			<!-- BEGIN MAIN CONTENT -->
			<div class="main col-sm-12 col-md-8">
                <?php
                        echo $this->element('blocks/common/searchs/sorting', array(
                            'options' => array(
                                '' => __('Order by'),
                                'Advice.modified-desc' => __('Baru ke Lama'),
                                'Advice.modified-asc' => __('Lama ke Baru'),
                                'Advice.title-asc' => __('Judul A - Z'),
                                'Advice.title-desc' => __('Judul Z - A'),
                            ),
                            '_display' => false,
                        ));

						if( !empty($values) ) {
				?>
				<!-- BEGIN BLOG LISTING -->
				<div id="blog-listing" class="grid-style1 clearfix">
					<div class="row">
						<?php 
								$i = 0;

								foreach ($values as $key => $value) {
	                                $id = $this->Rumahku->filterEmptyField($value, 'Advice', 'id');
	                                $slug = $this->Rumahku->filterEmptyField($value, 'Advice', 'slug');
	                                $category = $this->Rumahku->filterEmptyField($value, 'AdviceCategory', 'name');
	                                $title = $this->Rumahku->filterEmptyField($value, 'Advice', 'title');
	                                $short_content = $this->Rumahku->filterEmptyField($value, 'Advice', 'short_content');
	                                $photo = $this->Rumahku->filterEmptyField($value, 'Advice', 'photo');
	                                $modified = $this->Rumahku->filterEmptyField($value, 'Advice', 'modified');

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
									$linkUrl = $this->Html->url($url, true);

									if( !empty($i) && $i%2 == 0 ) {
					                    echo $this->Rumahku->clearfix();
					                }
						?>
						<div class="item col-md-6">
							<div class="image">
								<?php 
										echo $this->Html->link($this->Html->tag('span', $this->Rumahku->icon('fa fa-file-o').__('Selengkapnya'), array(
											'class' => 'btn btn-default',
										)), $url, array(
											'escape' => false,
										));
										echo $customPhoto;
								?>
							</div>
							<?php 
									echo $this->Html->tag('div', $this->Rumahku->icon('fa fa-file-text'), array(
										'class' => 'tag',
									));
							?>
							<div class="info-blog">
								<ul class="top-info">
									<?php 
											echo $this->Html->tag('li', sprintf('%s %s', $this->Rumahku->icon('fa fa-calendar'), $customModified));

											echo $this->Html->tag('li', sprintf(__('%s <fb:comments-count href="%s"></fb:comments-count>'), $this->Rumahku->icon('fa fa-comments-o'), Router::url($linkUrl)));
											echo $this->Html->tag('li', sprintf('%s %s', $this->Rumahku->icon('fa fa-tags'), $category));
									?>
								</ul>
								<?php 
										echo $this->Html->tag('h3', $this->Html->link($title, $url, array(
											'escape' => false,
										)));
										echo $this->Html->tag('p', $short_content);
								?>
							</div>
						</div>
						<?php 
									$i++;
								}
						?>
					</div>
				</div>
				<?php 
						} else {
							echo $this->Html->tag('div', __('Data belum tersedia.'), array(
								'class' => 'alert alert-danger',
							));
						}

        				echo $this->element('custom_pagination');
				?>
				
			</div>	
            <div class="sidebar gray col-sm-12 col-md-4">
                <?php 
                    echo $this->Html->div('search-placeholder', $this->element('blocks/advices/forms/searchs'));
                ?>
            </div>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>