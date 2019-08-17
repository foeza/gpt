<?php
        $_profile = isset($_profile)?$_profile:true;
        $_url = !empty($_url)?$_url:false;
        $_class = !empty($_class)?$_class:'col-md-8';

        echo $this->Html->tag('h1', __('Hubungi Agen'), array(
            'class' => 'section-title',
            'id' => 'contact-agent-form',
        ));
?>
<div class="property-agent-info">
    <?php
            if( !empty($_profile) ) {
                echo $this->Html->tag('h1', $this->element('blocks/users/list_agent'), array(
                    'class' => 'agent-detail col-md-4',
                ));
            }

            echo $this->element('blocks/common/forms/contact', array(
                '_class' => $_class,
                '_url' => $_url,
            ));
    ?>
</div>