<?php
        $logo_path = Configure::read('__Site.logo_photo_folder');

        if( !empty($partnerships) ){
?>
<!-- BEGIN PARTNERS WRAPPER -->
<div class="parallax pattern-bg" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php 
                        echo $this->Html->tag('h1', __('Partner Kami'), array(
                            'data-animation-direction' => 'from-bottom',
                            'data-animation-delay'=> '50',
                            'class' => 'section-title'
                        ));
                ?>
                <div id="partners">
                    <?php
                            foreach ($partnerships as $key => $value) {
                                $photo = $this->Rumahku->filterEmptyField($value, 'Partnership', 'photo');
                                $title = $this->Rumahku->filterEmptyField($value, 'Partnership', 'title');
                                $url = $this->Rumahku->filterEmptyField($value, 'Partnership', 'url');

                                $photo = $this->Rumahku->photo_thumbnail(array(
                                    'save_path' => $logo_path, 
                                    'src'=> $photo, 
                                    'thumb' => false,
                                ), array(
                                    'title' => $title,
                                    'alt' => $title,
                                ));
                                $url = $this->Rumahku->wrapWithHttpLink($url, false);

                                if( !empty($url) ) {
                                    $content = $this->Html->link($photo, $url, array(
                                        'escape' => false,
                                        'target' => '_blank',
                                    ));
                                } else {
                                    $content = $photo;
                                }

                                echo $this->Html->tag('div', $content, array(
                                    'data-animation-direction' => 'from-bottom',
                                    'data-animation-delay'=> '250',
                                    'class' => 'item'
                                ));
                            }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PARTNERS WRAPPER -->
<?php
    }
?>