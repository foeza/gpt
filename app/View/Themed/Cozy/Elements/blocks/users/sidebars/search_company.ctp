<?php 
        echo $this->Html->tag('h2', __('Pencarian'), array(
            'class' => 'section-title'
        ));

        echo $this->Rumahku->buildFrontEndInputForm('Search.keyword', false, array(
            'placeholder' => __('Nama Perusahaan'),
            'frameClass' => 'form-group',
        ));
        echo $this->Rumahku->buildFrontEndInputForm('Search.location', false, array(
            'type' => 'text',
            'placeholder' => __('Lokasi, Kota atau Area'),
            'frameClass' => 'form-group',
        ));

        echo $this->Html->tag('p', $this->Form->button(__('Cari'),array(
            'type' => 'submit',
            'class' => 'btn', 
        )),array(
            'class' => 'center', 
        )); 
?>