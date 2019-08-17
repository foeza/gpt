<?php
		$data 	= Configure::read('Config.PaginateApi');
		$url 	= !empty($url) ? $url : explode('/', $this->here);

		$raw_url = $url 	= array_map('trim', $url);

		$paging			= Common::hashEmptyField($data, 'paging');
		$paging_list 	= Common::hashEmptyField($paging, 'paging_list');

		$page_config	= $this->Rumahku->pagingToNumberPage($paging);

		$page_prev 		= Common::hashEmptyField($page_config, 'page_prev');
		$page_next 		= Common::hashEmptyField($page_config, 'page_next');
		$page_first 	= Common::hashEmptyField($page_config, 'page_first');
		$page_last 		= Common::hashEmptyField($page_config, 'page_last');

		echo $this->element('blocks/common/forms/pushstate_url');
?>
<div class="pagination-content clear">
	<ul class="pagination">
		<?php
				if(!empty($page_first)){
					$temp_url = $raw_url;
					$temp_url['page'] = $page_first;

					$link = $this->Html->link('«', $temp_url, array(
						'class' => 'ajax-link',
						'data-scroll' => 'body',
						'data-scroll-time'=>'0',
						'data-loadingbar' => 'true',
						'data-pushstate' => '1',
						'show_count' => '1',
						'rel' => 'first'
					));

					$content = $this->Html->tag('span', $link, array(
						'class' => 'first',
					));

					echo $this->Html->tag('li', $content, array(
						'data-toggle' => 'tooltip',
						'title' => __('Pertama'),
					));
				}

				if(!empty($page_prev)){
					$temp_url = $raw_url;
					$temp_url['page'] = $page_prev;

					$link = $this->Html->link('‹', $temp_url, array(
						'class' => 'ajax-link',
						'data-scroll' => 'body',
						'data-scroll-time'=>'0',
						'data-loadingbar' => 'true',
						'data-pushstate' => '1',
						'show_count' => '1',
						'rel' => 'prev'
					));

					$content = $this->Html->tag('span', $link, array(
						'class' => 'prev',
					));

					echo $this->Html->tag('li', $content, array(
						'data-toggle' => 'tooltip',
						'title' => __('Sebelumnya'),
					));
				}

				if(!empty($paging_list)){
					$paging = '';
					foreach ($paging_list as $key => $value) {
						$page 	= Common::hashEmptyField($value, 'page');
						$url 	= Common::hashEmptyField($value, 'url');

						$class_link = $class = '';
						if(empty($url)){
							$class = 'current';

							$link_paging = $page;
						}else{
							$class_link = 'ajax-link';
							
							$temp_url = $raw_url;
							$temp_url['page'] = $page;

							$link_paging = $this->Html->link($page, $temp_url, array(
								'class' => 'ajax-link',
								'data-scroll' => 'body',
								'data-scroll-time'=>'0',
								'data-loadingbar' => 'true',
								'data-pushstate' => '1',
								'show_count' => '1'
							));
						}

						$paging .= $this->Html->tag('li', $link_paging, array(
							'class' => $class.' page',
						));
					}

					echo $paging;
				}

				if(!empty($page_next)){
					$temp_url = $raw_url;
					$temp_url['page'] = $page_next;

					$link = $this->Html->link('›', $temp_url, array(
						'class' => 'ajax-link',
						'data-scroll' => 'body',
						'data-scroll-time'=>'0',
						'data-loadingbar' => 'true',
						'data-pushstate' => '1',
						'show_count' => '1',
						'rel' => 'next'
					));

					$content = $this->Html->tag('span', $link, array(
						'class' => 'next',
					));

					echo $this->Html->tag('li', $content, array(
						'data-toggle' => 'tooltip',
						'title' => __('Selanjutnya'),
					));
				}

				if(!empty($page_last)){
					$temp_url = $raw_url;
					$temp_url['page'] = $page_last;

					$link = $this->Html->link('»', $temp_url, array(
						'class' => 'ajax-link',
						'data-scroll' => 'body',
						'data-scroll-time'=>'0',
						'data-loadingbar' => 'true',
						'data-pushstate' => '1',
						'show_count' => '1',
						'rel' => 'last'
					));

					$content = $this->Html->tag('span', $link, array(
						'class' => 'last',
					));

					echo $this->Html->tag('li', $content, array(
						'class' => $class,
						'data-toggle' => 'tooltip',
						'title' => __('Terakhir'),
					));
				}
		?>
	</ul>
</div>