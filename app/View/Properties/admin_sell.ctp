<?php  
        echo $this->element('blocks/common/welcome_words', array(
            'action_type' => 'sell',
        ));
        echo $this->element('blocks/properties/sell_action');
?>
<div class="sell-form">
    <?php 
            switch ($step) {
                case 'Asset':
                    echo $this->element('blocks/properties/forms/sell_specification');
                    break;

                case 'Address':
                    echo $this->element('blocks/properties/forms/sell_address');
                    break;

                case 'Media':
                    echo $this->element('blocks/properties/forms/sell_medias');
                    break;
                
                default:
                    echo $this->element('blocks/properties/forms/sell_basic');
                    break;
            }
    ?>
</div>