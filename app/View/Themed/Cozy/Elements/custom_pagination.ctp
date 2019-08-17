<?php 
		$options = !empty($options)?$options:array();
		$options = array_merge_recursive(array(
			'property_action' => !empty($this->params['property_action'])?$this->params['property_action']:false,
			'property_type' => !empty($this->params['property_type'])?$this->params['property_type']:false,
			'location' => !empty($this->params['location'])?$this->params['location']:false,
			'subarea' => !empty($this->params['subarea'])?$this->params['subarea']:false,
			'zip' => !empty($this->params['zip'])?$this->params['zip']:false,
			'sorting' => !empty($this->params['sorting'])?$this->params['sorting']:false,
			'limitfor' => !empty($this->params['named']['limitfor'])?$this->params['named']['limitfor']:false,
			'escape' => false
 		), $options);
 		
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

		$optionPrev = array_merge_recursive($options, array('class' =>'prev'));
		$optionPrev['tag'] = false;
		$optionNumber = array_merge($options, array(
			'separator'=>'',
			'tag'=>'li',
			'modulus' => 4,
			'currentClass' => 'active',
		));
		$optionNext = $options;
		$optionNext['tag'] = false;
		$optionLast = array_merge_recursive($options, array('class' =>'last'));
?>
<div class="pagination developer-advance">
	<?php
		if($this->Paginator->hasPrev()):
	?>
    <ul id="previous">
    	<?php
				printf('<li>%s</li>', $this->Paginator->prev(__('<i class="fa fa-chevron-left"></i>'),$optionPrev));
		?>
	</ul>
	<?php
		endif;
	?>
    <ul>
		<?php
			echo $this->Paginator->numbers($optionNumber);
		?>
	</ul>
	<?php
		if($this->Paginator->hasNext()):
	?>
	<ul id="next">
		<?php
				printf('<li>%s</li>', $this->Paginator->next('<i class="fa fa-chevron-right"></i>', $optionNext));
		?>
    </ul>
    <?php
		endif;
	?>
</div>