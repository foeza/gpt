<?php 
        $general_path = Configure::read('__Site.general_folder');

        $company_name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
        $description  = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'description', false, false);

        $customBg     = '/images/about.png';

        $this->Html->addCrumb($module_title);
?>
<div class="content about-description">
    <div class="container">
        <div class="row">
            <div class="main col-sm-6">
                <div class="center">
                    <?php
                            echo $this->Html->tag('h2', $company_name, array(
                                'class' => 'section-highlight',
                                'data-animation-direction' => 'from-left',
                                'data-animation-delay' => '50'
                            ));

                            if(!empty($description)){
                                echo $this->Html->tag('p', $description, array(
                                    'data-animation-direction' => 'from-left',
                                    'data-animation-delay' => '650'
                                ));
                            }
                    ?>
                </div>
            </div>
            <div class="col-sm-6 main">
                <?php
                        echo $this->Html->image($customBg, array(
                            'data-animation-direction' => 'from-right',
                            'data-animation-delay' => '200',
                            'id' => "about-img"
                        ));
                ?>
            </div>
        </div>
    </div>
</div>
<!-- 
<div class="parallax colored-bg pattern-bg" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
                        echo $this->Html->tag('h2', __('Apa yang Anda cari?'), array(
                            'class' => 'section-title search-product-about',
                            'data-animation-direction' => 'from-bottom',
                            'data-animation-delay' => '50'
                        ));
                ?>
                <ul class="property-large-buttons2 clearfix">
                    <li data-animation-direction="from-bottom" data-animation-delay="250">
                        <?php
                                echo $this->Rumahku->icon('fa fa-home').'<br>';
                                echo $this->Html->tag('h4', __('Rumah'));
                                echo $this->Html->link('View All', array(
                                    'controller' => 'properties',
                                    'action' => 'find',
                                    'property_action' => 1,
                                    'typeid' => 1,
                                ), array(
                                    'class' => 'btn btn-default'
                                ));
                        ?>
                    </li>
                    <li data-animation-direction="from-bottom" data-animation-delay="450">
                        <?php
                                echo $this->Rumahku->icon('fa fa-tags').'<br>';
                                echo $this->Html->tag('h4', __('Apartemen'));
                                // echo $this->Html->link('View All', array(
                                //     'controller' => 'properties',
                                //     'action' => 'find',
                                //     'property_action' => 1,
                                //     'typeid' => 3,
                                // ), array(
                                //     'class' => 'btn btn-default'
                                // ));
                        ?>
                    </li>
                    <li data-animation-direction="from-bottom" data-animation-delay="650">
                        <?php
                                echo $this->Rumahku->icon('fa fa-leaf').'<br>';
                                echo $this->Html->tag('h4', __('Tanah'));
                                // echo $this->Html->link('View All', array(
                                //     'controller' => 'properties',
                                //     'action' => 'find',
                                //     'property_action' => 1,
                                //     'typeid' => 2,
                                // ), array(
                                //     'class' => 'btn btn-default'
                                // ));
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
-->