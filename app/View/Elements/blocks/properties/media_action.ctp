<?php
        $active = !empty($active)?$active:false;
        $draft_id = Configure::read('__Site.PropertyDraft.id');

        $photoClass = '';
        $videoClass = '';
        $documentClass = '';

        if( $active == 'video' ) {
            $videoClass = 'active';
        } else if( $active == 'document' ) {
            $documentClass = 'active';
        } else {
            $photoClass = 'active';
        }

        if( !empty($id) ) {
            $urlPhoto = array(
                'controller' => 'properties', 
                'action' => 'edit_medias',
                $id,
                'admin' => true,
            );
            $urlVideo = array(
                'controller' => 'properties', 
                'action' => 'edit_videos',
                $id,
                'admin' => true,
            );
            $urlDocument = array(
                'controller' => 'properties', 
                'action' => 'edit_documents',
                $id,
                'admin' => true,
            );
        } else {
            $urlPhoto = array(
                'controller' => 'properties', 
                'action' => 'medias',
                'admin' => true,
            );
            $urlVideo = array(
                'controller' => 'properties', 
                'action' => 'videos',
                'admin' => true,
            );
            $urlDocument = array(
                'controller' => 'properties', 
                'action' => 'documents',
                'admin' => true,
            );
        }

        if( !empty($draft_id) ) {
            $urlPhoto['draft'] = $draft_id;
            $urlVideo['draft'] = $draft_id;
            $urlDocument['draft'] = $draft_id;
        }
?>
<div class="options-medias text-center">
    <ul class="tabs clear">
        <?php
                echo $this->Html->tag('li', $this->Html->link(__('Foto'), $urlPhoto, array(
                    'class' => $photoClass,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Video'), $urlVideo, array(
                    'class' => $videoClass,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Dokumen'), $urlDocument, array(
                    'class' => $documentClass,
                )));
        ?>
    </ul>
</div>