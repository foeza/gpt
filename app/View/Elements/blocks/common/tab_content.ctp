<?php
        $tabs_action_type = isset($tabs_action_type) ? $tabs_action_type : false;
        if( is_array($tabs_action_type) ) {
            $tabs_action_type = $this->Html->url($tabs_action_type);
        } else {
            $tabs_action_type = '#'.$tabs_action_type;
        }

        $_id = isset($_id) ? $_id : false;
        $_redirect = isset($_redirect) ? $_redirect : 'false';
        $_type = isset($_type) ? $_type : 'style1';

        if( !empty($content) ) {

            $_class = '';
            $list = '';
            $i = 0;
            if( $_type != 'style1' ) {
                $_class = 'tabs';
            }

            foreach ($content as $key => $value) {
                $class = '';
                $title_tab = $this->Rumahku->filterEmptyField($value, 'title_tab', false, false, false);
                $type = $this->Rumahku->filterEmptyField($value, 'type');
                $url = $this->Rumahku->filterEmptyField($value, 'url');
                if( empty($url) ) {
                    $url = '#'.$key;
                }

                if(empty($i)){
                    $class = 'active';
                    $i = 1;
                }

                if( $type != 'checkbox' ) {
                    $list .= $this->Html->tag('li', $this->Html->link($title_tab, $url, array(
                        'escape' => false,
                        'class' => $class,
                    )), array(
                        'class' => $class,
                    ));
                } else {
                    $list .= $this->Html->tag('div', $this->Html->tag('div', $title_tab, array(
                        'class' => 'checkall cb-checkmark',
                    )), array(
                        'class' => 'cb-custom',
                    ));
                }
            }

            $i = 0;
            $custom_content_tab = '';
            foreach ($content as $key => $value) {
                $content_tab = $this->Rumahku->filterEmptyField($value, 'content_tab');
                if( !empty($content_tab) ) {
                    $class = 'hide';
                    if( empty($i) ) {
                        $i = 1;
                        $class = '';
                    }
                    $custom_content_tab .= $this->Html->tag('div', $value['content_tab'], array(
                        'id' => $key,
                        'class' => 'tab-handle '.$class
                    ));
                }
            }

            $result = $this->Html->tag('ul', $list, array(
                'class' => $_class,
                'id' => $_id,
            ));
            $result = $this->Rumahku->wrapTag('div', $result, array(
                'class' => 'rku-tabs clear',
                'redirect' => $_redirect,
            ));

            if( $_type == 'style1' ) {
                $result = $this->Rumahku->wrapTag('div', $result, array(
                    'id' => 'rku-tabs-wrapper'
                ));
            }

            echo $this->Form->hidden('tabs_action_type', array(
                'value' => $tabs_action_type,
            ));
            echo $result;
            echo $this->Rumahku->wrapTag('div', $custom_content_tab, array(
                'class' => 'tabs-box'
            ));
        }
?>