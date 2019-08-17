<?php

    $content = empty($content) ? array() : (array) $content;

    if($content){
        $options    = empty($options) ? array() : (array) $options;
        $active_tab = empty($active_tab) ? false : $active_tab;

        $list       = '';
        $counter    = 0;

        foreach($content as $key => $value){
            $title_tab  = Common::hashEmptyField($value, 'title_tab');
            $url_tab    = Common::hashEmptyField($value, 'url_tab', 'javascript:void(0);');
            $check      = $this->AclLink->aclCheck($url_tab);

            if($check){
                $class = '';

                if((empty($counter) && empty($active_tab)) || in_array($active_tab, array($key, $title_tab))){
                    $class = 'active';
                    $counter++;
                }

                $list.= $this->Html->tag('li', $this->AclLink->link($title_tab, $url_tab, array(
                    'escape'    => false,
                    'class'     => $class,
                )), array(
                    'class' => $class,
                ));
            }
        }

        $result = $this->Html->tag('ul', $list, $options);
        $result = $this->Html->tag('div', $result, array('class' => 'rku-tabs clear'));

        echo($this->Html->tag('div', $result, array(
            'id' => 'rku-tabs-wrapper', 
        )));
    }

    /*
        $_options = isset($options) ? $options : array();
        $active_tab = isset($active_tab) ? $active_tab : false;

        if( !empty($content) ) {
            $list = '';
            $i = 0;

            foreach ($content as $key => $value) {
                $class = '';
                $title_tab = $this->Rumahku->filterEmptyField($value, 'title_tab', false, false, false);
                $url_tab = $this->Rumahku->filterEmptyField($value, 'url_tab', false, '#');

                $check = $this->AclLink->aclCheck($url_tab);

                if( (empty($i) && empty($active_tab)) || $active_tab == $title_tab ){
                    $class = 'active';
                    $i = 1;
                }

                if($check){
                    $list .= $this->Html->tag('li', $this->AclLink->link($title_tab, $url_tab, array(
                        'escape' => false,
                        'class' => $class,
                    )), array(
                        'class' => $class,
                    ));
                }

            }

            $result = $this->Html->tag('ul', $list, $_options);
            $result = $this->Rumahku->wrapTag('div', $result, array(
                'class' => 'rku-tabs clear',
            ));
            $result = $this->Rumahku->wrapTag('div', $result, array(
                'id' => 'rku-tabs-wrapper'
            ));

            echo $result;
        }
    */
?>