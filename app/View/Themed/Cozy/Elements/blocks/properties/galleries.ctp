<?php
        $property_path  = Configure::read('__Site.property_photo_folder');
        $data_medias    = $this->Rumahku->filterEmptyField($value, 'PropertyMedias');

        $title_alt      = $this->Property->getALtImage($value);

        if(!empty($data_medias)) {
?>
<div class="hidden-print relative">
    <?php
		//	khusus untuk terjual dan tersewa pakai stamp
			$actionID		= isset($value['Property']['property_action_id']) && $value['Property']['property_action_id'] ? $value['Property']['property_action_id'] : NULL;
			$soldStatus		= isset($value['Property']['sold']) && $value['Property']['sold'] ? TRUE : FALSE;
			$statusStamp	= NULL;

			if($soldStatus && in_array($actionID, array(1, 2))){
				$statusStamp = $actionID == 1 ? 'sold_stamp.png' : 'rent_stamp.png';
				$statusStamp = $this->Html->image(Configure::read('__Site.img_path_http').$statusStamp, array('alt' => 'status-stamp', 'class' => 'img-responsive'));

				echo($this->Html->tag('div', $statusStamp, array('class' => 'sold-banner')));
			}
	?>
    <!-- BEGIN PROPERTY DETAIL LARGE IMAGE SLIDER -->
    <div id="property-detail-large" class="owl-carousel">
        <?php
                $i = 0;
                foreach ($data_medias as $key => $media) {
                    $caption = '';
                    $title = $this->Property->_callMediaTitle($media);
                    $photo = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'name');

                    $optionLink = array(
                        'escape' => false,
                        'rel' => 'prettyPhoto[slide]',
                    );

                    $mediaPhoto = $this->Rumahku->photo_thumbnail(array(
                        'save_path' => $property_path, 
                        'src'       => $photo,
                        'size'      => 'company',
                    ), array(
                        'title' => $title_alt,
                        'alt'   => $title_alt,
                    ));

                    $url = $this->Rumahku->photo_thumbnail(array(
                        'save_path' => $property_path, 
                        'src'       => $photo,
                        'size'      => 'company',
                        'url'       => true,
                    ));

                    $mediaPhoto = $this->Html->link($mediaPhoto, $url, $optionLink);

                    if(!empty($title_alt)){
                        $caption = $this->Html->tag('div', $title_alt, array(
                            'class' => 'owlcaption'
                        ));
                    }

                    if ( $i == 0 ) {
                        $main_photo = $this->Html->tag('div', $this->Html->tag('div', $mediaPhoto, array(
                            'class' => 'image-slider-owl'
                        )), array(
                            'class' => 'item visible-print',
                        ));
                    }

                    echo $this->Html->tag('div', $this->Html->tag('div', $mediaPhoto, array(
                        'class' => 'image-slider-owl'
                    )), array(
                        'class' => 'item hidden-print',
                    ));

                    $i++;
                }
        ?>
    </div>
    <!-- END PROPERTY DETAIL LARGE IMAGE SLIDER -->
    <?php
            if ( !empty($main_photo) ) {
                echo $main_photo;
            }
    ?>
    <!-- BEGIN PROPERTY DETAIL THUMBNAILS SLIDER -->
    <div id="property-detail-thumbs" class="owl-carousel hidden-print">
        <?php
                foreach ($data_medias as $key => $media) {
                    $title = $this->Property->_callMediaTitle($media);
                    $photo = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'name');

                    $customPhoto = $this->Rumahku->photo_thumbnail(array(
                        'save_path' => $property_path, 
                        'src'=> $photo, 
                        'size'=>'m',
                    ), array(
                        'title' => $title_alt,
                        'alt'   => $title_alt,
                    ));

                    echo $this->Html->tag('div', $customPhoto, array(
                        'class' => 'item',
                    ));
                }
        ?>
    </div>
</div>
<?php
        }
?>