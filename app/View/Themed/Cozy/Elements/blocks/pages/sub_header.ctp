<?php 
        $content = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'sub_header_content', false, false);
        
        if( !empty($content) ){
            echo $this->Html->tag('div', $this->Html->tag('div', $content, array(
                'class' => 'container center',
            )), array(
                'class' => 'action-box',
            ));
        }
?>