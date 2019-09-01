<?php
        echo $this->element('blocks/common/carousel', array(
            'medias' => $banners,
        ));
        // echo $this->element('blocks/pages/sub_header');
        echo $this->element('blocks/pages/properties');
?>
<div class="content gray">
    <div class="container">
        <div class="row">
            <div class="main col-sm-12 col-md-8">
                <?php
                        // echo $this->element('blocks/pages/developers');
                ?>
            </div>
            <div class="col-sm-12 col-md-4">
            <?php 
                    echo $this->element('blocks/common/sidebars/search');
            ?>
            </div>
            
        </div>
    </div>
</div>
<?php 
        // echo $this->element('blocks/pages/partnerships');
?>