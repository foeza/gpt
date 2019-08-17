<?php 
        $general_path = Configure::read('__Site.general_folder');
        
        if( !empty($medias) ) {
            $cnt = count($medias);
?>
<div id="carousel-gallery" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <?php
                foreach ($medias as $key => $media) {
                    if( empty($key) ) {
                        $addClass = 'active';
                    } else {
                        $addClass = '';
                    }

                    echo $this->Html->tag('li', '', array(
                        'class' => $addClass,
                        'data-target' => '#carousel-gallery',
                        'data-slide-to' => $key,
                    ));
                }
        ?>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
        <?php 
                if( !empty($medias) ) {
                    foreach ($medias as $key => $media) {
                        $content = '';
                        $photo = $this->Rumahku->filterEmptyField($media, 'BannerSlide', 'photo');
                        $title = $this->Rumahku->filterEmptyField($media, 'BannerSlide', 'title');
                        $url = $this->Rumahku->filterEmptyField($media, 'BannerSlide', 'url');
                        $is_video = $this->Rumahku->filterEmptyField($media, 'BannerSlide', 'is_video');

                        $mediaPhoto = $this->Rumahku->photo_thumbnail(array(
                            'save_path' => $general_path, 
                            'src' => $photo, 
                            'thumb' => false,
                        ), array(
                            'title'=> $title, 
                            'alt'=> $title, 
                            'class' => 'default-thumbnail',
                        ));

                        if( !empty($url) ) {
                            $optionLink = array(
                                'escape' => false,
                            );

                            if( !empty($is_video) ) {
                                $optionLink['rel'] = 'prettyPhoto[slide1]';
                                $url .= '&autoplay=1';
                            }

                            $mediaPhoto = $this->Html->link($mediaPhoto, $url, $optionLink);
                        }

                        $content = $mediaPhoto;

                        if( empty($key) ) {
                            $addClass = 'active';
                        } else {
                            $addClass = '';
                        }

                        if( !empty($title) ) {
                            $content .= $this->Html->tag('div', $title, array(
                                'class' => 'carousel-caption',
                            ));
                        }

                        echo $this->Html->tag('div', $content, array(
                            'class' => 'item '.$addClass,
                        ));
                    }
                }
        ?>
    </div>
    <?php 
            if( $cnt > 1 ) {
    ?>
    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-gallery" role="button" data-slide="prev">
        <?php 
                echo $this->Rumahku->icon('fa fa-angle-left', false, 'i', ' arrow-left');
                echo $this->Html->tag('span', __('Previous'), array(
                    'class' => 'sr-only',
                ));
        ?>
    </a>
    <a class="right carousel-control" href="#carousel-gallery" role="button" data-slide="next">
        <?php 
                echo $this->Rumahku->icon('fa fa-angle-right', false, 'i', ' arrow-right');
                echo $this->Html->tag('span', __('Next'), array(
                    'class' => 'sr-only',
                ));
        ?>
    </a>
    <?php 
            }
    ?>
</div>
<?php
        }
?>