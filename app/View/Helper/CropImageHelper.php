<?php 
class CropImageHelper extends Helper {
    var $helpers = array('Html', 'Form');

	function createForm($imagePath, $tW, $tH, $photoSize){
		$x1 = $this->Form->hidden('x1', array("value" => "", "id"=>"x1"));
		$y1 = $this->Form->hidden('y1', array("value" => "", "id"=>"y1"));
		$x2 = $this->Form->hidden('x2', array("value" => "", "id"=>"x2",));
		$y2 = $this->Form->hidden('y2', array("value" => "", "id"=>"y2"));
		$w = $this->Form->hidden('w', array("value" => "", "id"=>"w"));
		$h = $this->Form->hidden('h', array("value" => "", "id"=>"h"));
        $w_img = $this->Form->hidden('w_img', array("value" => "", "id"=>"w_img"));
        $h_img = $this->Form->hidden('h_img', array("value" => "", "id"=>"h_img"));
		
		$imgP = $this->Form->hidden('imagePath', array("value" => $imagePath));
		$html_block = '';
		$html_block .= $x1;
		$html_block .= $y1;
		$html_block .= $x2;
		$html_block .= $y2;
		$html_block .= $w;
		$html_block .= $h;
        $html_block .= $w_img;
        $html_block .= $h_img;
		$html_block .= $imgP;

        $imageWidth = !empty($photoSize[0])?$photoSize[0]:false;
        $imageHeight = !empty($photoSize[1])?$photoSize[1]:false;

        $result = $this->output($html_block);
        $result .= $this->_setData($tW, $tH);
        $result .= $this->_setPhotoSize($imageWidth, $imageHeight);

		return $result;
    }
	function createSourceBlock($imagePath) {
		$imgTum = $this->Html->image($imagePath, array(
            'id'=>'preview_image', 
            'alt'=>'Create Thumbnail', 
        ));
		return $this->output($imgTum);
	}
	function createThumbnailBlock($imagePath) {
		$imgTumPrev = $this->Html->image($imagePath, array(
            'id'=>'preview_thumbnail', 
            'alt'=>'Preview', 
        ));
		return $this->output($imgTumPrev);
	}

    function _setData ($w, $h) {
        $result = $this->Form->hidden('width_img', array(
            'id' => 'default_width',
            'value' => $w,
        ));
        $result .= $this->Form->hidden('height_img', array(
            'id' => 'default_height',
            'value' => $h,
        ));

        return $result;
    }

    function _setPhotoSize ($w, $h) {
        $result = $this->Form->hidden('width_preview_img', array(
            'id' => 'default_preview_width',
            'value' => $w,
        ));
        $result .= $this->Form->hidden('height_preview_img', array(
            'id' => 'default_preview_height',
            'value' => $h,
        ));

        return $result;
    }
}
?>