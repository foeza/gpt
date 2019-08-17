<?php 
        $this->Html->addCrumb($module_title);

        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'users',
                'action' => 'search',
                'companies',
                'admin' => false,
            ),
        ));
?>
<div class="content gray">
    <div class="container">
        <div class="row">
            <!-- BEGIN MAIN CONTENT -->
            <div class="main col-sm-12 col-md-8">
                <?php
                        echo $this->element('blocks/common/searchs/sorting', array(
                            'options' => array(
                                '' => __('Order by'),
                                'UserCompany.created-desc' => __('Baru ke Lama'),
                                'UserCompany.created-asc' => __('Lama ke Baru'),
                                'UserCompany.name-asc' => __('Nama A - Z'),
                                'UserCompany.name-desc' => __('Nama Z - A'),
                            ),
                            '_display' => false,
                        ));

                        if(!empty($values)){
                ?>
                <div id="agents-results" class="agents-list">
                    <?php
                            foreach ($values as $key => $value) {
                    ?>
                    <div class="item col-md-6 col-lg-4">
                        <?php
                                echo $this->element('blocks/users/list_company', array(
                                    'value' => $value,
                                    'with_social_media' => true,
                                    'agent_list' => true,
                                ));
                        ?>
                    </div>
                    <?php
                            }

                            echo $this->element('custom_pagination');
                    ?>
                </div>
                <?php
                        }else{
                            echo $this->Html->tag('div', __('Data tidak ditemukan.'), array(
                                'class' => 'alert alert-danger'
                            ));
                        }
                ?>
                
            </div>  
            <div class="sidebar gray col-sm-12 col-md-4">
                <?php 
                        echo $this->element('blocks/users/sidebars/search_company');
                        echo $this->element('blocks/common/sidebars/properties');
                ?>
            </div>
        </div>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>