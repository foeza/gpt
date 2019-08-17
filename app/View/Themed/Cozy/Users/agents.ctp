<?php 
        $displayShow = !empty($displayShow)?$displayShow:false;
        $this->Html->addCrumb($module_title);

        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'users',
                'action' => 'search',
                'agents',
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
                                'User.created-desc' => __('Baru ke Lama'),
                                'User.created-asc' => __('Lama ke Baru'),
                                'User.full_name-asc' => __('Nama A - Z'),
                                'User.full_name-desc' => __('Nama Z - A'),
                            ),
                        ));

                        if(!empty($values)){
                ?>
                <div id="agents-results" class="agents-<?php echo $displayShow;?>">
                    <?php
                            $i = 0;
                            foreach ($values as $key => $value) {
                                if($i%3 == 0){
                                    echo '<div class="row">';
                                }
                    ?>
                        <div class="item col-md-6 col-lg-4">
                            <?php
                                    echo $this->element('blocks/users/list_agent', array(
                                        'value' => $value,
                                        'with_social_media' => true,
                                        'agent_list' => true,
                                    ));
                            ?>
                        </div>
                    <?php
                            if($i++ % 3 == 2){
                                echo '</div>';
                                $i = 0;
                            }
                        }

                        if($i%3 > 0){
                            echo '</div>';
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
                        echo $this->Html->div('search-placeholder', $this->element('blocks/users/sidebars/search'));
                        echo $this->element('blocks/common/sidebars/properties');
                ?>
            </div>
        </div>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>