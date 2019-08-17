<?php
        $save_path = Configure::read('__Site.general_folder');
        $this->Html->addCrumb($module_title);

        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'pages',
                'action' => 'search',
                'developers',
                'admin' => false,
            ),
        ));
?>
<div class="content gray">
    <div class="container">
        <div class="row">
            <div class="main col-sm-8">
                <?php
                        echo $this->element('blocks/common/searchs/sorting', array(
                            'options' => array(
                                '' => __('Order by'),
                                'ApiAdvanceDeveloper.created-desc' => __('Baru ke Lama'),
                                'ApiAdvanceDeveloper.created-asc' => __('Lama ke Baru'),
                                'ApiAdvanceDeveloper.name-asc' => __('Judul A - Z'),
                                'ApiAdvanceDeveloper.name-desc' => __('Judul Z - A'),
                            ),
                            '_display' => false,
                        ));
                ?>
                <?php
                        if(!empty($values)){
                ?>
                <div id="blog-listing" class="grid-style1 developer-list clearfix">
                    <?php
                            $i = 0;
                            foreach ($values as $key => $value) {
                                $id         = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.id');
                                $origin_id  = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.original_id');
                                $url        = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.url', '#');
                                $title      = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.name');
                                $photo      = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.logo');
                                $short_desc = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.promo');
                                $is_article = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.is_article');
                                $type_dev   = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.type_developer');

                                $content = '';
                                
                                $customPhoto = $this->Rumahku->photo_thumbnail(array(
                                    'save_path' => $save_path, 
                                    'src'=> $photo, 
                                    'size' => 'm',
                                ), array(
                                    'alt' => sprintf('%s %s', $title, Configure::read('__Site.domain')),
                                ));
                                $options = array(
                                    'title' => $title,
                                    'escape' => false,
                                );

                                if( $i % 2 == 0 ) {
                                    echo $this->Rumahku->clearfix();
                                }
                    ?>
                    <div class="item col-md-6">
                        <?php
                                if( $type_dev == 'project_primedev' ) {
                                    $photo = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.cover_img_sync');
                                    $customPhoto = $this->Rumahku->photo_thumbnail(array(
                                        'url' => true,
                                        'save_path' => $save_path, 
                                        'src'=> $photo, 
                                        'size' => 'm',
                                    ), array(
                                        'alt' => sprintf('%s %s', $title, Configure::read('__Site.domain')),
                                    ));
                                    $options = array(
                                        'title' => $title,
                                        'escape' => false,
                                    );

                                    $customPhoto = $this->Html->image($customPhoto);

                                    $infoDev        = $this->Project->infoDeveloper($value);
                                    $dev_name       = Common::hashEmptyField($infoDev, 'Result.dev_name');
                                    $property_type  = Common::hashEmptyField($infoDev, 'Result.property_type');
                                    $result_address = Common::hashEmptyField($infoDev, 'Result.result_address');

                                    $infoProject = $this->Project->_callInfoProject($value);
                                    $title = Common::hashEmptyField($infoProject, 'Result.project_name');

                                    $TotalProduct     = Common::hashEmptyField($value, 'TotalProduct');
                                    $TotalProductUnit = Common::hashEmptyField($value, 'TotalProductUnit');

                                    // condition if dev has product
                                    if ( $TotalProduct ) {
                                        // url list product
                                        $url = array(
                                            'controller'=> 'pages', 
                                            'action' => 'list_product',
                                            $origin_id, 
                                            'admin'=> false,
                                        );

                                    } elseif ( $TotalProductUnit ) {
                                        // url list unit
                                        $url = array(
                                            'controller'=> 'pages', 
                                            'action' => 'unit_list',
                                            $origin_id, 
                                            'admin'=> false,
                                        );

                                    }
                                } elseif( !empty($is_article) && $type_dev == 'old_data' ) {
                                    $url = array(
                                        'controller' => 'pages',
                                        'action' => 'developer_detail',
                                        $id,
                                    );
                                } else if( $url != '#' ) {
                                    $options['target'] = '_blank';
                                }

                                if( !empty($url) ){
                                    $content = $this->Html->link($this->Html->tag('span', $this->Rumahku->icon('fa fa-file-o').__('Lihat'), array(
                                        'class' => 'btn btn-default',
                                    )), $url, $options);
                                }
                                
                                $content .= $customPhoto;

                                echo $this->Html->tag('div', $content, array(
                                    'class' => 'image'
                                ));
                                echo $this->Html->tag('div', $this->Rumahku->icon('fa fa-file-text'), array(
                                    'class' => 'tag'
                                ));
                        ?>
                        <div class="info-blog">
                            <?php
                                    echo $this->Html->tag('h3', $this->Html->link($title, $url, array(
                                       'escape' => false, 
                                    )));
                                    if ($type_dev == 'project_primedev') {
                                        echo $this->Html->tag('p', $property_type.$dev_name);
                                        echo $this->Html->tag('p', $result_address);
                                    }
                                    echo $this->Html->tag('p', $short_desc);
                            ?>
                        </div>
                    </div>                    
                    <?php
                                $i++;
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
            <?php 
                    echo $this->Html->tag('div', $this->element('blocks/common/sidebars/right_content', array(
                        'with_map' => false,
                    )), array(
                        'class' => 'sidebar gray col-sm-4',
                    ));
            ?>
        </div>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>