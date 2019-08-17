<?php
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );
        
        echo $this->Rumahku->buildInputForm('meta_title', array_merge($options, array(
            'label' => __('Meta Title'),
        )));

        echo $this->Rumahku->buildInputForm('meta_description', array_merge($options, array(
            'label' => __('Meta Description'),
        )));

?>