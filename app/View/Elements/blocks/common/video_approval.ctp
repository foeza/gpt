<div class="status-approved-video">
<?php 
        if( !empty($videos) ) {
            $property_path = Configure::read('__Site.property_photo_folder');

            $content_list = '';
            $i = 0;
            if( !empty($videos) ) {
                foreach ($videos as $key => $value) {
                    if($i % 3 == 0){
                        echo '<div class="row-list"><div class="row">';
                    }

                    $id = $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'id');
                    $property_id = $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'property_id');
                    $approved = $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'approved');
                    $youtube_id = $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'youtube_id');
                    $title = $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'title');

                    $mediaPhoto = $this->Html->div('video-image-preview', $this->Rumahku->_callYoutubeThumbnail($youtube_id, $title));

                    $class_arr = array(
                        'class' => 'btn blue',
                        'text' => 'Pending',
                    );

                    if($approved){
                        $class_arr = array(
                            'class' => 'btn green',
                            'text' => 'Disetujui'
                        );
                    }
                    $link_reject = '';

                    if(Configure::read('User.admin')){
                        $link_reject = $this->Html->link($this->Rumahku->icon('rv4-bold-cross'), array(
                            'controller' => 'ajax',
                            'action' => 'delete_video',
                            $id,
                            $property_id,
                            'admin' => true
                        ), array(
                            'class' => 'ajax-link',
                            'escape' => false,
                            'data-wrapper-write' => '.status-approved-video',
                            'data-alert' => __('Apakah anda yakin ingin menghapus video ini?')
                        ));
                    }

                    $content = $this->Html->div('item-media', $link_reject.$mediaPhoto);

                    $link = $this->Html->div('form-group', $this->Html->link($class_arr['text'], 'javascript:void(0);', array(
                        'class' => $class_arr['class'],
                        'data-wrapper-write' => '.status-approved-video'
                    )));

                    $photoAction = '';
                    if( Configure::read('User.admin') && !$approved && !empty($property['Property']['in_update'])){
                        $photoAction = $this->Html->tag('div', $this->Form->input('PropertyVideos.options_id.'.$i, array(
                            'type' => 'checkbox',
                            'label' => array(
                                'text' => __('Pilih Video'),
                                'data-show' => '.fly-button-media',
                            ),
                            'div' => false,
                            'required' => false,
                            'hiddenField' => false,
                            'value' => $id,
                            'checked' => true,
                            'class' => 'check-option',
                        )), array(
                            'class' => 'bottom cb-checkmark',
                        ));
                    }

                    $content .= $this->Html->div('action cb-custom', $link.$photoAction);

                    echo $this->Html->tag('div', $this->Html->div('box-photo-preview', $content), array(
                        'class' => 'col-sm-4 box-bg-preview'
                    ));

                    if($i++ % 3 == 2){
                        echo '</div></div>';
                    }
                }

                if($i % 3 > 0){
                    echo '</div></div>';
                }
            }
        }else{
            echo $this->Html->div('alert-text', __('Data tidak tersedia.'));
        }
?>
</div>