<?php 
        if(!empty($properties)){
?>
<!-- BEGIN PROPERTIES SLIDER WRAPPER-->
<div class="parallax pattern-bg" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php 
                        echo $this->Html->tag('h1', __('Properti Terbaru'), array(
                            'class' => 'section-title',
                            'data-animation-direction' => 'from-bottom',
                            'data-animation-delay' => '50',
                        ));
                ?>
                <div id="new-properties-slider" class="owl-carousel carousel-style1">
                    <?php
                            echo $this->element('blocks/properties/frontend/items', array(
                                '_attributes' => 'data-animation-direction="from-bottom" data-animation-delay="250"',
                            ));
                    ?>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- END PROPERTIES SLIDER WRAPPER -->
<?php
    }
?>