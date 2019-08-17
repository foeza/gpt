<?php 
        $this->Html->addCrumb($module_title);
?>
<div class="content">
	<div class="container">
		<div class="row">
			<!-- BEGIN MAIN CONTENT -->
			<div class="main col-sm-12">
                <?php 
                        echo $this->Html->tag('h1', __('Frequently Asked Questions (FAQ)'), array(
                            'class' => 'section-title',
                        ));

						if(!empty($values)){
							$i = 1;

							foreach ($values as $key => $value) {
                                $category_id = $this->Rumahku->filterEmptyField($value, 'FaqCategory', 'id');
                                $category = $this->Rumahku->filterEmptyField($value, 'FaqCategory', 'name');

                                $dataFaq = $this->Rumahku->filterEmptyField($value, 'Faq');

                                if( !empty($dataFaq) ) {
									$accordion = sprintf('accordion-%s', $i);
									$collapse_number = 1;

									echo $this->Html->tag('h3', $category);
				?>
							<div id="<?php echo $accordion?>" class="panel-group">
								<?php
										foreach ($dataFaq as $key => $val) {
                               				$question = $this->Rumahku->filterEmptyField($val, 'Faq', 'question');
                               				$answer = $this->Rumahku->filterEmptyField($val, 'Faq', 'answer');
											$answer = str_replace(PHP_EOL, '<br>', $answer);
											$collapse = sprintf('collapse-%s-%s', $category_id, $collapse_number);
								?>
								<div class="panel clearfix">
									<div class="panel-heading">
										<?php
												$link_to_collapse = $this->Html->link($question, '#'.$collapse, array(
													'data-parent' => '#'.$accordion,
													'class' => 'collapsed',
													'data-toggle' => 'collapse'
												));
												echo $this->Html->tag('h4', $link_to_collapse, array(
													'class' => 'panel-title'
												));
										?>
									</div>
									<?php
											$content_answer = $this->Html->tag('div', $answer, array(
												'class' => 'panel-body col-xs-12'
											));
											echo $this->Html->tag('div', $content_answer, array(
												'id' => $collapse,
												'class' => 'panel-collapse collapse'
											));
									?>
								</div>
								<?php
											$collapse_number++;
										}
								?>
							</div>
				<?php
									$i++;
								}
							}
							
							echo $this->element('custom_pagination');

						} else {
							echo $this->Html->tag('div', __('Data belum tersedia untuk saat ini.'), array(
								'class' => 'alert alert-danger'
							));
						}
				?>
			</div>	
			<!-- END MAIN CONTENT -->
			
		</div>
	</div>
</div>