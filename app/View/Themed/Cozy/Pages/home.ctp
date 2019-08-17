<?php 
        $homeContent = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'header_content', false, false);

        echo $this->element('blocks/common/carousel', array(
            'medias' => $banners,
        ));
        echo $this->element('blocks/pages/sub_header');
        echo $this->element('blocks/pages/properties');
?>
<div class="content gray">
    <div class="container">
        <div class="row">
            <div class="main col-sm-12 col-md-8">
                <?php 
                        echo $this->element('blocks/common/sidebars/search', array(
                            '_class' => 'visible-xs-block',
                        ));

                        if( !empty($homeContent) ) {
                            echo $this->Html->tag('h1', __('Mengapa Kami'), array(
                                'data-animation-delay' => '50',
                                'data-animation-direction' => 'from-bottom',
                                'class' => 'section-title'
                            ));
                            echo $homeContent;
                        }

                        echo $this->element('blocks/pages/developers');
                        echo $this->element('blocks/pages/advices');
                ?>
            </div>
            <?php 
                    echo $this->Html->tag('div', $this->element('blocks/common/sidebars/right_content', array(
                        '_searchClass' => 'hidden-xs',
                    )), array(
                        'class' => 'sidebar gray col-sm-12 col-md-4',
                    ));
            ?>
        </div>
    </div>
</div>
<?php 
        echo $this->element('blocks/pages/partnerships');
?>