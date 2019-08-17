<?php
        $adviceCategories = !empty($adviceCategories)?$adviceCategories:false;

        if( !empty($url) ) {
            echo $this->Form->create('Search', array(
                'url' => $url,
            ));
        }

        echo $this->Html->tag('h2', __('Pencarian %s', Configure::read('Global.Data.translates.id.blog')), array(
            'class' => 'section-title',
        ));
?>
<div class="form-group">
    <?php
            echo $this->Rumahku->buildFrontEndInputForm('keyword', false, array(
                'placeholder' => __('Judul atau Konten ....'),
                'frameClass' => false,
            )); 
            echo $this->Rumahku->buildFrontEndInputForm('category', false, array(
                'placeholder' => __('Kategori'),
                'frameClass' => false,
                'empty' => __('- Pilih Kategori -'),
                'options' => $adviceCategories,
            ));
            echo $this->Html->tag('p', $this->Form->button(__('Cari'), array(
                'type' => 'submit', 
                'class' => 'btn btn-default-color',
            )), array(
                'class' => 'center',
            ));
    ?>
</div>
<?php 
        if( !empty($url) ) {
            echo $this->Form->end();
        }
?>