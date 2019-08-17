<?php
class SocialHelper extends AppHelper {
	var $helpers = array('Html');

	public function get_comment_fb($data_width = false, $url_to_comment = false) {
		
		if(empty($url_to_comment)) {
			$url_to_comment = FULL_BASE_URL.$this->here;
		}

		$options = array(
			'num_post' => 5,
			'theme' => 'light'
		);
		if($data_width){
			$options = array(
				'class' => 'fb-comments',
				'data-href' => $url_to_comment,
				'data-numposts' => $options['num_post'],
				'data-colorscheme' => $options['theme'],
				'data-width' => $data_width
			);
			$block = $this->Html->tag('div', '', $options);
		}else{
			$block = '<div class="fb-comments" data-href="'.$url_to_comment.'" data-numposts="'.$options['num_post'].'" data-colorscheme="'.$options['theme'].'"></div>';
		}
		return $block;
	}
}