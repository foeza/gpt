<?php
        $property_id = !empty($property_id) ? $property_id : false;
        $url = array(
            'controller' => 'properties',
            'action' => 'preview',
            $property_id,
            'admin' => true,
        );
        $check = $this->AclLink->aclCheck($url);
?>
<div class="status-approved-image">
<?php 
        if( !empty($medias) ) {
            $property_path = Configure::read('__Site.property_photo_folder');

            $content_list = '';
            $i = 0;
            if( !empty($medias) ) {
                foreach ($medias as $key => $media) {
                    if($i % 3 == 0){
                        echo '<div class="row-list"><div class="row">';
                    }

                    $mediaPhoto = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'name');
                    $id = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'id');
                    $property_id = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'property_id');
                    $approved = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'approved');
                    $mediaTitle = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'title');
                    $primary = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'primary');

                    $mediaPhoto = $this->Html->link($this->Rumahku->photo_thumbnail(array(
                        'save_path' => $property_path, 
                        'src' => $mediaPhoto, 
                        'size' => 'l',
                    ), array(
                        'title'=> $mediaTitle, 
                        'alt'=> $mediaTitle, 
                        'class' => 'default-thumbnail',
                    )), array(
                        'controller' => 'ajax',
                        'action' => 'image',
                        $id,
                        'admin' => true
                    ), array(
                        'class' => 'ajaxModal',
                        'title' => __('Foto Properti'),
                        'escape' => false
                    ));

                    $mediaPhoto = $this->Html->div('image-preview', $mediaPhoto);

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

                    if( Configure::read('User.admin') || $check){
                        if(!$primary){
                            $link_reject = $this->AclLink->link($this->Rumahku->icon('rv4-bold-cross'), array(
                                'controller' => 'ajax',
                                'action' => 'delete_media',
                                $id,
                                $property_id,
                                'admin' => true
                            ), array(
                                'class' => 'ajax-link',
                                'escape' => false,
                                'data-wrapper-write' => '.status-approved-image',
                                'data-alert' => __('Apakah anda yakin ingin menghapus foto ini?'),
                            ));
                        }else{
                            $link_reject = $this->Html->link(__('Foto Utama'), 'javascript:void(0);', array(
                                'class' => 'btn green primary-btn-preview'
                            ));
                        }
                    }

                    $content = $this->Html->div('item-media', $link_reject.$mediaPhoto);

                    $link = $this->Html->div('form-group', $this->Html->link($class_arr['text'], 'javascript:void(0);', array(
                        'class' => $class_arr['class'],
                        'data-wrapper-write' => '.status-approved-image'
                    )));

                    $photoAction = '';
                    if( Configure::read('User.admin') && !$approved && !empty($property['Property']['in_update'])){
                        $photoAction = $this->Html->tag('div', $this->Form->input('PropertyMedias.options_id.'.$i, array(
                            'type' => 'checkbox',
                            'label' => array(
                                'text' => __('Pilih Foto'),
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