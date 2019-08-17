<?php
        $budgetOptions = Configure::read('Global.Data.budget_client');

		echo $this->Html->tag('h2', __('Budget'), array(
            'class' => 'sub-heading'
        ));
                        
		echo $this->Rumahku->buildInputForm('range_budget', array_merge($options, array(
            'label' => __('Kisaran Harga'),
            'empty' => __('Masukan Kisaran Harga'),
            'class' => 'relative col-sm-5 col-xl-7',
            'infoText' => __('Harap masukan kisaran harga klien, agar report klien akurat'),
            'options' => $budgetOptions,
        )));
?>