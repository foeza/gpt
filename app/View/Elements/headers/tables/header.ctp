<?php 
		$data_paging_api 	= Configure::read('Config.PaginateApi');
		$paging_api			= Common::hashEmptyField($data_paging_api, 'paging');

		$pageCount = $this->Paginator->counter(array('format' => '%count%'));
		$showcolumns = $this->Rumahku->filterEmptyField($sorting, 'showcolumns');
		$sortOptions = $this->Rumahku->filterEmptyField($sorting, 'options');
		$resultDelete = '';
		$pageInfo = '';
		$showcolumns_attributes = array(
			'data-url' => $this->Html->url(array(
				'controller' => 'ajax',
				'action' => 'set_sorting',
				$this->params['controller'],
				$this->action,
        		'admin' => false,
			)),
		);

		if( !empty($showcolumns['options']) ) {
			$showcolumns_attributes = $this->Rumahku->filterEmptyField($showcolumns, 'attributes', false, $showcolumns_attributes);
			$showcolumns = $showcolumns['options'];
		}

		if( !empty($pageCount) ) {
			$pagination_params = $this->Paginator->params();
			$limit = $this->Rumahku->filterEmptyField($pagination_params, 'limit');

			$page = $this->Paginator->counter(array('format' => '%page%'));
			$current = $this->Paginator->counter(array('format' => '%current%'));
			$totalShow = ($page * $limit) - ($limit - $current);
			
			$totalShow = $this->Rumahku->getFormatPrice($totalShow);
			$pageCount = $this->Rumahku->getFormatPrice($pageCount);
			$pageInfo = __('%s dari %s Data', $totalShow, $pageCount);
		}else if(!empty($paging_api)){
			$pageInfo = Common::hashEmptyField($paging_api, 'page_info.full_text');
		}

		$pageInfo = $this->Html->tag('div', 
			$this->Html->tag('span', $pageInfo), array(
            'class'=> 'pagination-info',
        ));

		if( !empty($buttonDelete) ) {
			$text = $this->Rumahku->filterEmptyField($buttonDelete, 'text', null, 'Hapus');
			$class = $this->Rumahku->filterEmptyField($buttonDelete, 'class', false, 'btn-red');
			$url = $this->Rumahku->filterEmptyField($buttonDelete, 'url', false, 'javascript:void(0);');
			$options = $this->Rumahku->filterEmptyField($buttonDelete, 'options', false, array());

			$result = $this->Html->tag('div', 
				$this->AclLink->link(__('%s %s', $this->Rumahku->icon('rv4-trash'), $text), $url, array_merge(array(
					'escape' => false,
                    'class'=> $class,
                ), $options)), array(
                'class'=> 'table-action table-page-info',
            ));

        	$resultDelete .= $result;

            if( !empty($overflowDelete) ) {
				$text = $this->Rumahku->filterEmptyField($buttonDelete, 'text', null, __('Hapus'));
				$buttonDelete['text'] = $this->Rumahku->icon('rv4-cross').$text;

				$resultDelete .= $this->Html->tag('div', 
					$this->Html->tag('div', 
						$this->Html->tag('span', 0).
						__(' Data di%s', strtolower($text)), array(
						'class' => 'counter floleft',
					)).
					$this->Html->tag('div', 
						$this->Rumahku->buildButton($buttonDelete), array(
						'class' => 'action-delete floright',
					)), array(
					'class' => 'delete-overflow clear',
				));
            }
		}
?>
<div class="table-header mb20">
	<div class="table-row">
		<div class="table-col-left">
			<div class="table-row">
				<?php 
						if( !empty($pageInfo) ) {
							echo $this->Html->tag('div', $pageInfo, array(
								'class' => 'mobile-only table-page-info',
							));
						}

						if( !empty($buttonAdd) ) {
							$text = $this->Rumahku->filterEmptyField($buttonAdd, 'text', false, __('Tambah'));
							$url = $this->Rumahku->filterEmptyField($buttonAdd, 'url', false, 'javascript:void(0);');
							$options = $this->Rumahku->filterEmptyField($buttonAdd, 'options', false, array());


							echo $this->Html->tag('div', 
								$this->AclLink->link(__('%s %s', $this->Rumahku->icon('rv4-plus'), $text), $url, array_merge($options, array(
									'escape' => false,
		                            'class'=> 'btn green',
		                        ))), array(
	                            'class'=> 'table-add floleft',
	                        ));
							echo $this->Rumahku->_callTableDivider();
						}

						if( !empty($buttonCustom) ) {
							$text = $this->Rumahku->filterEmptyField($buttonCustom, 'text');
							$url = $this->Rumahku->filterEmptyField($buttonCustom, 'url', false, 'javascript:void(0);');
							$options = $this->Rumahku->filterEmptyField($buttonCustom, 'options', false, array());

							echo $this->Html->tag('div', 
								$this->AclLink->link($text, $url, array_merge(array(
									'escape' => false,
		                            'class'=> 'btn blue',
		                        ), $options)), array(
	                            'class'=> 'table-add floleft',
	                        ));
							echo $this->Rumahku->_callTableDivider();
						}

						$buttonDivider	= empty($buttonDivider) ? false : $buttonDivider;
						$buttons		= empty($buttons) ? array() : (array) $buttons;

						if($buttons){
							$isMultiple	= Hash::numeric(array_keys($buttons));
							$buttons	= $isMultiple ? $buttons : array($buttons);
							$counter	= 1;

							foreach($buttons as &$button){
							//	extract yang bukan option button
								$divOpts	= Common::hashEmptyField($button, 'div', true, array('isset' => true));

							//	remove yang bukan option button
								$button = Hash::remove($button, 'div');

								$text		= Common::hashEmptyField($button, 'text');
								$url		= Common::hashEmptyField($button, 'url', 'javascript:void(0);');
								$options	= Common::hashEmptyField($button, 'options', array());
								$divider	= Common::hashEmptyField($button, 'divider');

								$button = $this->AclLink->link($text, $url, array_merge(array(
									'class'		=> 'btn default',
									'escape'	=> false,
								), $options));

								if($button){
									if($divOpts){
										if(is_array($divOpts) === false){
											$divOpts = array(
												'class' => is_bool($divOpts) ? 'table-add floleft' : $divOpts, 
											);
										}

									//	wrap button
										$button = $this->Html->tag('div', $button, $divOpts);
									}

									if( ($counter < count($buttons) && $buttonDivider) || !empty($divider) ){
										$button.= $this->Rumahku->_callTableDivider();
									}
								}

								$counter++;
							}

							$buttons = implode('', array_filter($buttons));
							$buttons.= $buttons ? $this->Rumahku->_callTableDivider() : '';

							echo($buttons);
						}

						if( !empty($pageInfo) ) {
							echo $this->Html->tag('div', $pageInfo, array(
								'class' => 'table-page-info desktop-only floleft',
							));
						}

						if( !empty($resultDelete) ) {
							$frameOptions = $this->Rumahku->filterEmptyField($buttonDelete, 'frameOptions');
							echo $this->Html->tag('span', 
								$this->Rumahku->_callTableDivider().
								$this->Html->tag('div', $resultDelete, array(
								'class' => 'desktop-only floleft',
							)), $frameOptions);
						}
				?>
			</div>
		</div>
		<div class="table-col-right">
			<?php 
					if( !empty($showcolumns) ) {
						echo $this->Html->tag('div', 
							$this->Rumahku->buildInputDropdown('Search.colview',  array(
				            	'frameClass' => 'columnDropdown',
								'label' => false,
								'empty' => false,
				                'default_title' => __('Table View'),
				                'options' => $showcolumns,
				                '_checkbox' => true,
				                '_checkbox_action' => false,
				                'checkboxAttr' => array_merge($showcolumns_attributes, array(
				                	'data-form' => '.form-table-search',
			                	)),
				            )), array(
				            'class' => 'table-input floleft',
			            ));
					}

					if( !empty($resultDelete) ) {
						$frameOptions = $this->Rumahku->filterEmptyField($buttonDelete, 'frameOptions');
						echo $this->Html->tag('span', $this->Html->tag('div', $resultDelete, array(
							'class' => 'mobile-only',
						)), $frameOptions);
					}

					// if( !empty($sortOptions) ) {
					// 	echo $this->Html->tag('div', 
					// 		$this->Rumahku->buildInputDropdown('Search.sort',  array(
				 //            	'frameClass' => false,
					// 			'label' => false,
					// 			'empty' => __('Urutkan'),
				 //                'default_title' => __('Urutkan'),
				 //                'options' => $sortOptions,
				 //                'attributes' => array(
     //            					'id' => 'sorted',
			  //               	),
				 //            )), array(
				 //            'class' => 'table-input floleft',
			  //           ));
					// }
			?>
		</div>
	</div>
</div>