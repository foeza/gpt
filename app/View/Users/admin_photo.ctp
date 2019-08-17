<?php 
        $userFolder = Configure::read('__Site.profile_photo_folder');
?>

<div class="step-medias">
    <div class="wrapper-upload-medias upload-photo" id="file-drop-zone" allow-multiple="false">
        <div class="options-medias-single text-center">
            <div class="btn-group">
                <?php
                        echo $this->Html->tag('div', __('Foto'), array(
                            'class' => 'btn text-white',
                        ));
                ?>
            </div>
        </div>
        <div class="content-upload-photo">
            <?php 
                    echo $this->UploadForm->load($this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'user_profile_photo',
                        'admin' => false
                    )), '', $userFolder);
            ?>
            <div class="info-upload-photo text-center">
                <?php
                        echo $this->Html->tag('div', $this->Rumahku->icon('picture-o'), array(
                            'class' => 'pict',
                        ));
                ?>
                <div class="line1">
                    <?php 
                            echo $this->Html->tag('label', __('Geser dan taruh berkas disini atau klik untuk menggunggah gambar.'));
                            echo $this->Html->tag('p', __('Anda dapat menambahkan judul pada foto, setelah proses unggah selesai.'));
                    ?>
                </div>
                <div class="line2">
                    <?php 
                            echo $this->Html->tag('p', __('Maksimum ukuran foto yang diunggah 10Mb.'));
                            echo $this->Html->tag('p', __('Foto yang diunggah harus memenuhi syarat dan ketentuan dari %s.', Configure::read('__Site.site_name')));
                            // echo $this->Html->tag('small', $this->Html->link(sprintf(__('Info detail %s'), $this->Rumahku->icon('angle-right')), '#', array(
                            //     'class' => 'terms',
                            //     'escape' => false,
                            // )));
                    ?>
                </div>
                <div class="info-upload-photo">
                    <?php 
                            echo $this->Form->button(__('Unggah Foto'), array(
                                'type' => 'file', 
                                'class'=> 'btn btn-default',
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>