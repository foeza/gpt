<?php
        $save_path = Configure::read('__Site.general_folder');

        if(!empty($dataMedias)) {
?>
<div class="hidden-print relative">
    <!-- BEGIN PROPERTY DETAIL LARGE IMAGE SLIDER -->
    <div id="property-detail-large" class="owl-carousel">
        <?php
                $i = 0;
                foreach ($dataMedias as $key => $media) {
                    $title = Common::hashEmptyField($media, 'Media.title', '');
                    $alt_image = Common::hashEmptyField($media, 'Media.alt', '');
                    $photo = Common::hashEmptyField($media, 'Media.name');

                    $caption = '';

                    $customPhoto = $this->Rumahku->photo_thumbnail(array(
                        'url' => true,
                        'save_path' => $save_path, 
                        'src'=> $photo, 
                        'size' => 'fullsize',
                    ), array(
                        'title' => $title,
                        'alt' => $alt_image,
                    ));
                    $customPhoto = $this->Html->image($customPhoto);

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
                    $title = Common::hashEmptyField($media, 'Media.title', '');
                    $alt_image = Common::hashEmptyField($media, 'Media.alt', '');
                    $photo = Common::hashEmptyField($media, 'Media.name');

                    $customPhoto = $this->Rumahku->photo_thumbnail(array(
                        'url' => true,
                        'save_path' => $save_path, 
                        'src'=> $photo, 
                        'size'=>'fullsize',
                    ), array(
                        'title' => $title,
                        'alt' => $alt_image,
                    ));
                    $customPhoto = $this->Html->image($customPhoto);

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