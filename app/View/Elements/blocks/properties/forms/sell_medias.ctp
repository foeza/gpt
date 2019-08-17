<?php 
        $id = !empty($id)?$id:false;
        $dataMedias = !empty($dataMedias)?$dataMedias:false;
        $session_id = !empty($session_id)?$session_id:false;
        $propertyFolder = Configure::read('__Site.property_photo_folder');
        $draft_id = Configure::read('__Site.PropertyDraft.id');
?>
<div class="step-medias">
    <div id="file-drop-zone">
        <div class="wrapper-upload-medias upload-photo">
            <?php
                    echo $this->element('blocks/properties/media_action', array(
                        'active' => 'photo',
                    ));
            ?>
            <div class="content-upload-photo">
                <div class="info-full alert photo-info-top">
                    <?php 
                            echo $this->Html->tag('p', sprintf(__('%s Dengan mengunggah foto untuk setiap ruangan (lebih dari 1 foto), membuat iklan properti Anda 40 kali lebih menarik, dan cepat terjual/tersewa.'), $this->Html->tag('strong', __('Tahukah Anda?'))));
                    ?>
                </div>
                <?php 
                        echo $this->UploadForm->load($this->Html->url(array(
                            'controller' => 'ajax',
                            'action' => 'property_photo',
                            $id,
                            'draft' => $draft_id,
                            'admin' => false
                        )), $dataMedias, $propertyFolder, array(
                            'session_id' => $session_id,
                            'id' => $id,
                        ));

                        if( empty($dataMedias) ) {
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
                        ?>
                    </div>
                </div>
                <?php 
                        }
                ?>
                <div class="photo-info-bottom">
                    <?php 
                            echo $this->Html->tag('label', __('Keterangan:'));

                            $contentLi = $this->Html->tag('li', __('Kami akan memberikan watermark pada foto yang Anda unggah untuk kepentingan perlindungan hak cipta'));
                            $contentLi .= $this->Html->tag('li', __('Klik dan geser untuk mengubah posisi/urutan foto setelah proses unggah semua foto selesai'));
                            $contentLi .= $this->Html->tag('li', sprintf(__('Klik tombol %s untuk menentukan foto utama yang ditampilkan sebagai thumbnail dan foto pertama di halaman pencarian properti. Hanya ada 1 foto utama untuk setiap iklan properti'), $this->Html->tag('strong', __('"Jadikan Foto Utama"'))));
                            $contentLi .= $this->Html->tag('li', __('Berikan judul untuk setiap foto properti yang diunggah'));
                            $contentLi .= $this->Html->tag('li', sprintf(__('Mohon hanya mengunggah file berekstensi %s'), implode(', ', Configure::read('__Site.allowed_ext'))));

                            echo $this->Html->tag('ul', $contentLi);
                    ?>
                </div>
            </div>
            <?php 
                    echo $this->Html->link($this->Rumahku->icon('rv4-cross').__(' Hapus Foto'), array(
                        'controller' => 'ajax', 
                        'action' => 'property_photo_delete',
                        $session_id,
                        $id, 
                        'draft' => $draft_id,
                        'admin' => false,
                    ), array(
                        'escape' => false,
                        'class' => 'btn red fly-button-media ajax-link',
                        'data-form' => '#fileupload',
                        'data-alert' => __('Anda yakin ingin menghapus foto ini?'),
                        'data-action' => 'reset-file-upload',
                        'data-wrapper-write' => '.wrapper-upload-medias',
                    ));
            ?>
        </div>
    </div>
    <?php
            echo $this->Form->create('PropertyMedias', array(
                'class' => 'form-horizontal',
                'id' => 'sell-property',
            ));
            
            echo $this->element('blocks/properties/sell_action', array(
                'action_type' => 'bottom',
                'labelBack' => __('Kembali'),
            ));

            echo $this->Form->hidden('Property.session_id', array(
                'value' => $session_id, 
            ));
            echo $this->Form->end();
    ?>
</div>