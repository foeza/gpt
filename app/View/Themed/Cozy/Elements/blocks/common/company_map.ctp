<?php 
        $_contact_info = isset($_contact_info)?$_contact_info:true;
        $_map_name = isset($_map_name)?$_map_name:'map_agency';
        $_map_class = isset($_map_class)?$_map_class:'gmap';
?>
<div id="agencies" data-animation-direction="fade" data-animation-delay="250">
    <?php 
            echo $this->Html->tag('h2', __('Kantor Kami'), array(
                'class' => 'section-title'
            ));
            
            echo $this->element('blocks/common/map_location');

            /*
            Sementara
    ?>
    <div class="mapborder">
        <?php 
                echo $this->Html->tag('div', '', array(
                    'id' => $_map_name,
                    'class' => $_map_class,
                ));
        ?>
    </div>
    <?php 
            */

            if( !empty($_contact_info) ) {
                echo $this->Html->tag('div', $this->element('blocks/common/info'), array(
                    'id' => 'contacts-home',
                ));
            }
    ?>
</div>