<?php
        $this->Html->addCrumb(__('Daftar Perusahaan'), array(
            'controller' => 'users',
            'action' => 'companies',
            'admin' => false,
        ));
        $this->Html->addCrumb($module_title);

        $save_path = Configure::read('__Site.logo_photo_folder');

        $domain = $this->Rumahku->filterEmptyField($value, 'UserCompanyConfig', 'domain');
        $name = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name');
        $logo = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'logo');
        $description = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'description', false, false);

        $logo = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $save_path, 
            'src'=> $logo, 
            'size' => 'xxsm',
        ), array(
            'title' => $name,
            'alt' => $name,
        ));

        if( !empty($domain) ) {
            $domain_url = $this->Rumahku->wrapWithHttpLink($domain, false);
            $domain = $this->Html->link(__('%s Visit Our Website', $this->Rumahku->icon('fa fa-globe')), $domain_url, array(
                'escape' => false,
                'class' => 'goto-website',
                'target' => '_blank',
            ));
            $logo = $this->Html->link($logo, $domain_url, array(
                'escape' => false,
                'target' => '_blank',
            ));
        }
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray">
    <div class="container">
        <div class="row">
        
            <!-- BEGIN MAIN CONTENT -->
            <div class="main col-sm-12 col-md-8">
                
                <!-- BEGIN AGENT DETAIL -->
                <div class="agent-detail clearfix">
                    <?php
                            echo $this->Html->tag('div', $logo, array(
                                'class' => 'image col-md-5',
                            ));
                    ?>
                    
                    <div class="col-md-7">
                        <?php
                                echo $this->element('blocks/users/company');
                        ?>
                    </div>
                </div>
                <!-- END AGENT DETAIL -->
                
                <?php
                        if( !empty($description) ) {
                            echo $this->Html->tag('p', str_replace(PHP_EOL, '<br>', strip_tags($description)), array(
                                'class' => 'center'
                            ));
                        }

                        if(!empty($agents)){
                            echo $this->Html->tag('h1', __('Agent %s', $name), array(
                                'class' => 'section-title',
                                'id' => 'title-agents',
                            ));
                            echo $this->Rumahku->divider('thin');
                            echo $this->Html->tag('div', 
                                $this->Html->tag('div', 
                                    $this->element('blocks/users/agents', array(
                                        'title_label' => false,
                                        '_class' => 'col-md-4',
                                        'mod' => 3,
                                    )).
                                    $this->Html->tag('div', $this->element('custom_pagination', array(
                                        'options' => array(
                                            'class' => 'ajax-link',
                                            'class-link' => 'ajax-link',
                                            'data-wrapper-write' => '#assigned-properties',
                                            'data-scroll' => '#title-agents',
                                            'data-scroll-top' => '-180',
                                            'data-scroll-time' => '0',
                                        ),
                                    )), array(
                                        'class' => 'paginate-profile',
                                    )), array(
                                    'class' => 'row',
                                )), array(
                                'id' => 'assigned-properties',
                                'class' => 'grid-style1 clearfix',
                            ));
                        }
                ?>
            </div>  
            <!-- END MAIN CONTENT -->
            
            <div class="sidebar gray col-sm-12 col-md-4">
                <?php 
                        echo $this->element('widgets/contact');
                        echo $this->element('blocks/common/sidebars/properties');
                ?>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT WRAPPER -->