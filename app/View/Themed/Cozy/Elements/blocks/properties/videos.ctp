<?php
        $values = $this->Rumahku->filterEmptyField($value, 'PropertyVideos');
        
        if( !empty($values) ) {
?>
<div class="videos hidden-print">
    <?php 
            echo $this->Html->tag('h1', __('Video Properti'), array(
                'class' => 'section-title',
            ));
    ?>
    <div class="list-images text-center">
        <div id="property-video-list" class="row">
            <?php
                    foreach ($values as $key => $media) {
                        $title = $this->Rumahku->filterEmptyField($media, 'PropertyVideos', 'title');
                        $youtube_id = $this->Rumahku->filterEmptyField($media, 'PropertyVideos', 'youtube_id');
            ?>
            <div class="col-sm-3">
                <div class="item-gallery">
                    <div class="img">
                        <?php
                                    echo $this->Html->link($this->Html->image('http://img.youtube.com/vi/'.$youtube_id.'/0.jpg', array(
                                        'class' => 'img-thumbnail'
                                    )), 'http://www.youtube.com/watch?v='.$youtube_id, array(
                                        'title'=> $title,
                                        'class'=> 'hidden-print',
                                        'escape' => false,
                                        'rel' => 'prettyPhoto[gallery1]',
                                    ));
                                    echo $this->Html->image('http://img.youtube.com/vi/'.$youtube_id.'/0.jpg', array(
                                        'class' => 'img-thumbnail visible-print'
                                    ));
                        ?>
                    </div>
                    <div class="detail-info">
                        <?php 
                                echo $this->Html->tag('label', $title);
                        ?>
                    </div>
                </div>
            </div>
            <?php
						if(($key + 1) % 4 == 0){
							?>
							</div><div class="row">
							<?php
						}
                    }
            ?>
        </div>
    </div>
</div>
<?php
        }
?>