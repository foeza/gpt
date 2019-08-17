<?php
		// search principle
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );
        $autoUrlPrinciple = $this->Html->url(array(
            'controller' => 'ajax',
            'action' => 'list_users',
            '3,4', 
            'admin' => false,
        ));

        echo $this->Rumahku->buildInputForm('contract_date', array_merge($options, array(
            'type' => 'text',
            'label' => __('Tgl Kontrak'),
            'inputClass' => 'datepicker',
        )));
        echo $this->Rumahku->buildInputForm('date', array_merge($options, array(
            'type' => 'text',
            'label' => __('Tgl Tayang'),
            'inputClass' => 'date-range',
        )));
        echo $this->Rumahku->buildInputForm('principle_email', array_merge($options, array(
            'label' => __('Email Principle/Direktur *'),
            'attributes' => array(
                'id' => 'autocomplete',
                'autocomplete' => 'off',
                'data-ajax-url' => $autoUrlPrinciple,
            ),
        )));
        echo $this->Rumahku->buildInputForm('theme_id', array_merge($options, array(
            'label' => __('Tema *'),
            'empty' => __('Pilih Tema'),
        )));
        echo $this->Rumahku->buildInputForm('template_id', array_merge($options, array(
            'label' => __('Template'),
            'empty' => __('Pilih Template'),
        )));
        echo $this->Rumahku->buildInputForm('pic_sales_id', array_merge($options, array(
            'label' => __('Sales PIC *'),
            'options' => $list_sales,
            'empty' => __('Pilih PIC Sales'),
        )));
?>