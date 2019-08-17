<?php
		$_options_find = (isset($_options_find)) ? $_options_find : true;

		$options = array();

		if( !empty($url) ) {
			$options['url'] = $url;
		}
		
		if( !empty($model) ) {
			if (!isset($this->Paginator->params['paging'])) {
				return;
			}
			if (!isset($model) || $this->Paginator->params['paging'][$model]['pageCount'] < 2) {
				return;
			}
			if (!isset($options)) {
				$options = array();
			}

			$options['model'] = $model;
			$options['url']['model'] = $model;
			$this->Paginator->defaultModel = $model;
		}

		$options['show_count'] = isset($show_count)?$show_count:true;
		$optionFirst = array_merge($options, array('class' =>'first'));
		$optionPrev = array_merge($options, array('class' =>'prev'));
		$optionNumber = array_merge($options, array(
			'separator'=>'',
			'tag'=>'li',
			'modulus' => 4,
			'class' => 'page',
		));
		$optionNext = $options;
		$optionLast = array_merge($options, array('class' =>'last'));
		// debug($this->params);

		$class_box = (isset($_class_box)) ? $_class_box : '';
?>
<div class="text-left <?php echo $class_box;?>">
	<ul class="pagination pagination-centered">
		<?php if($this->Paginator->hasPrev()):?>
			<li><?php echo $this->Paginator->first('« First',$optionFirst)?></li>
			<li><?php echo $this->Paginator->prev(__('« Prev'),$optionPrev)?></li>
		<?php endif; ?>
		<?php echo $this->Paginator->numbers($optionNumber); ?>
		<?php if($this->Paginator->hasNext()):?>
			<li><?php echo $this->Paginator->next('Next  »', $optionNext); ?></li>
			<li><?php echo $this->Paginator->last(__('Last »'), $optionLast)?></li>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>