<?php 	
		$save_path = Configure::read('__Site.advice_photo_folder');

		$this->Html->addCrumb($module_title);

		echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'blogs',
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
			<div class="main col-sm-12 col-md-12">
                <?php
                        echo $this->element('blocks/common/searchs/sorting', array(
                            'options' => array(
                                '' => __('Order by'),
                                'Advice.modified-desc' 	=> __('Baru ke Lama'),
                                'Advice.modified-asc' 	=> __('Lama ke Baru'),
                                'Advice.title-asc' 		=> __('Judul A - Z'),
                                'Advice.title-desc' 	=> __('Judul Z - A'),
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
									$category	= Common::hashEmptyField($value, 'AdviceCategory.name');

									$id			= Common::hashEmptyField($value, 'Advice.id');
									$slug		= Common::hashEmptyField($value, 'Advice.slug');
									$title		= Common::hashEmptyField($value, 'Advice.title');
									$content	= Common::hashEmptyField($value, 'Advice.short_content', false);
									$photo		= Common::hashEmptyField($value, 'Advice.photo');
									$modified	= Common::hashEmptyField($value, 'Advice.modified');

	                                $modified 	= $this->Rumahku->formatDate($modified, 'M d, Y');

	                                $customPhoto = $this->Rumahku->photo_thumbnail(array(
										'save_path' => $save_path, 
										'src'		=> $photo, 
										'size' 		=> 'm',
										'url' 		=> true,
									));

									$url = array(
										'controller' => 'blogs',
										'action' => 'read',
										$id,
										$slug,
										'admin' => false,
									);

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

										echo $this->Html->image($repeated_img, array(
											'title' => $title,
											'alt' 	=> $title,
											'class' => 'lazy-image',
											'data-original' => $customPhoto,
										));
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
											echo $this->Html->tag('li',
												__('%s %s', $this->Rumahku->icon('fa fa-calendar'), $modified));
											echo $this->Html->tag('li',
												__('%s %s', $this->Rumahku->icon('fa fa-tags'), $category));
									?>
								</ul>
								<?php 
										echo $this->Html->tag('h3',
											$this->Html->link($title, $url, array(
												'escape' => false,
											))
										);
										echo $this->Html->tag('p', $content);
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
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>