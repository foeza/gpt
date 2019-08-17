<?php 
        $general_path = Configure::read('__Site.general_folder');
        $developer_page = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_developer_page');

        if( !empty($developer_page) && !empty($developers) ){
            echo $this->Html->tag('h1', __('Developers'), array(
                'class' => 'section-title',
                'data-animation-direction' => 'from-bottom',
                'data-animation-delay' => '50',
            ));
            echo $this->Html->tag('p', __('Berikut adalah developer-developer yang telah bekerja sama dengan kami dalam memasarkan properti mereka.'), array(
                'class' => 'center',
                'data-animation-direction' => 'from-bottom',
                'data-animation-delay' => '150',
            ));
?>
<div id="property-gallery" class="owl-carousel property-gallery">
    <?php
            foreach ($developers as $key => $value) {
                $id = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'id');
                $photo = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'logo');
                $title = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'name');
                $description = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'description');
                $url = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'url');
                $content = '';

                if( !empty($description) ) {
                    $url = array(
                        'controller' => 'pages',
                        'action' => 'developer_detail',
                        $id,
                        'admin' => false,
                    );
                }

                if( !empty($url) ){
                    $content = $this->Html->link($this->Html->tag('span', '+', array(
                        'class' => 'btn btn-default',
                    )), $url, array(
                        'escape' => false,
                        'title' => $title,
                        'target' => '_blank',
                    ));
                }
                
                $content .= $this->Rumahku->photo_thumbnail(array(
                    'save_path' => $general_path, 
                    'src' => $photo, 
                    'size' => 'm',
                ));

                echo $this->Html->tag('div', $content, array(
                    'data-animation-delay' => '350',
                    'data-animation-direction' => 'from-bottom',
                    'class' => 'item'
                ));
            }
    ?>
</div>
<?php
            echo $this->Html->tag('div', '', array(
                'style' => 'clear:both;',
            ));
            echo $this->Html->tag('div', $this->Html->link(__('Selengkapnya'), array(
                'controller' => 'pages',
                'action' => 'developers'
            ), array(
                'class' => 'btn btn-default-color btn-lg',
                'target' => '_blank'
            )));
        }
?>