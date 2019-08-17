<?php
        $property_path = Configure::read('__Site.property_photo_folder');
        $dataMedias = $this->Rumahku->filterEmptyField($value, 'PropertyMedias');
        $status = $this->Property->getShortStatus($value, 'span', true);
        $id = $this->Rumahku->filterEmptyField($value, 'Property', 'id');
        $parent_id = Configure::read('Principle.id');
        $watermark = 'company';

        $alt_image = $this->Rumahku->getALtImage($value);

        if(!empty($dataMedias)) {
?>
<div class="hidden-print relative">
    <?php
			echo $this->Html->tag('span', $status, array(
				'class' => 'status-property',
			));

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
                foreach ($dataMedias as $key => $media) {
                    $caption = '';
                    $title = $this->Property->_callMediaTitle($media);
                    $photo = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'name');

                    $customPhoto = $this->Rumahku->photo_thumbnail(array(
                        'save_path' => $property_path, 
                        'src'=> $photo, 
                        'size' => $watermark,
                    ), array(
                        'title' => $title,
                        'alt' => $alt_image,
                    ));

                    if(!empty($title)){
                        $caption = $this->Html->tag('div', $title, array(
                            'class' => 'owlcaption'
                        ));
                    }

                    if ( $i == 0 ) {
                        $main_photo = $this->Html->tag('div', $this->Html->tag('div', $customPhoto, array(
                            'class' => 'image-slider-owl'
                        )).$caption, array(
                            'class' => 'item visible-print',
                        ));
                    }

                    echo $this->Html->tag('div', $this->Html->tag('div', $customPhoto, array(
                        'class' => 'image-slider-owl'
                    )).$caption, array(
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
                foreach ($dataMedias as $key => $media) {
                    $title = $this->Property->_callMediaTitle($media);
                    $photo = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'name');

                    $customPhoto = $this->Rumahku->photo_thumbnail(array(
                        'save_path' => $property_path, 
                        'src'=> $photo, 
                        'size'=>'m',
                    ), array(
                        'title' => $title,
                        'alt' => $alt_image,
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