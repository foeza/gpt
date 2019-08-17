<?php 
        $step = !empty($step)?$step:false;
        $sub_step = !empty($sub_step)?$sub_step:false;

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

                case 'Medias':
                    if( $sub_step == 'video' ) {
                        echo $this->element('blocks/properties/forms/sell_videos');
                    } else if( $sub_step == 'documents' ) {
                        echo $this->element('blocks/properties/forms/sell_documents');
                    } else {
                        echo $this->element('blocks/properties/forms/sell_medias');
                    }
                    break;
                
                default:
                    echo $this->element('blocks/properties/forms/sell_basic');
                    break;
            }
    ?>
</div>