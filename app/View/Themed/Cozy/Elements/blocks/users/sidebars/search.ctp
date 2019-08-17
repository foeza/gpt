<?php 
        echo $this->Html->tag('h2', __('Pencarian Agen'), array(
            'class' => 'section-title'
        ));

        echo $this->Rumahku->buildFrontEndInputForm('Search.name', false, array(
            'placeholder' => __('Nama Agen'),
            'frameClass' => 'form-group',
        ));
        echo $this->Rumahku->buildFrontEndInputForm('Search.email', false, array(
            'type' => 'text',
            'placeholder' => __('Email Agen'),
            'frameClass' => 'form-group',
        ));
        echo $this->Rumahku->buildFrontEndInputForm('Search.phone', false, array(
            'placeholder' => __('Telepon Agen'),
            'frameClass' => 'form-group',
        ));

        echo $this->Html->tag('p', $this->Form->button(__('Cari'),array(
            'type' => 'submit',
            'class' => 'btn btn-default-color', 
        )),array(
            'class' => 'center', 
        )); 
?>