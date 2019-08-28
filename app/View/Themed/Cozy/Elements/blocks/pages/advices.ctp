<?php
        $advice_path = Configure::read('__Site.advice_photo_folder');
        $blog = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_blog');

        if( !empty($blog) && !empty($advices) ){
            echo $this->Html->tag('h1', Configure::read('Global.Data.translates.id.latest_blog'), array(
                'class' => 'section-title',
                'data-animation-direction' => 'from-bottom',
                'data-animation-delay' => '50',
            ));
?>
<div class="latest-news list-style clearfix">
    <?php
            foreach ($advices as $key => $value) {
                $id = $this->Rumahku->filterEmptyField($value, 'Advice', 'id');
                $slug = $this->Rumahku->filterEmptyField($value, 'Advice', 'slug');
                $title = $this->Rumahku->filterEmptyField($value, 'Advice', 'title');
                $subtitle = $this->Rumahku->filterEmptyField($value, 'Advice', 'subtitle');
                $short_content = $this->Rumahku->filterEmptyField($value, 'Advice', 'short_content');
                $category = $this->Rumahku->filterEmptyField($value, 'AdviceCategory', 'name');
                $photo = $this->Rumahku->filterEmptyField($value, 'Advice', 'photo');
                $modified = $this->Rumahku->filterEmptyField($value, 'Advice', 'modified');

                $photo = $this->Rumahku->photo_thumbnail(array(
                    'save_path' => $advice_path, 
                    'src'=> $photo, 
                    'size'=>'m',
                ), array(
                    'title'=> $title, 
                    'alt'=> $title, 
                ));
                $customModified = $this->Rumahku->formatDate($modified, 'd M, Y');

                $url = array(
                    'controller' => 'blogs',
                    'action' => 'read',
                    $id,
                    $slug,
                    'admin' => false,
                );
    ?>
    <div class="item col-sm-12" data-animation-direction="from-bottom" data-animation-delay="250">
        <div class="image">
            <?php
                    echo $this->Html->link($this->Html->tag('span', $this->Rumahku->icon('fa fa-file-o').__('Selengkapnya'), array(
                        'class' => 'btn btn-default',
                    )), $url, array(
                        'escape' => false
                    ));
                    echo $photo;
            ?>
        </div>
        <?php
                echo $this->Html->tag('div', $this->Rumahku->icon('fa fa-file-text'), array(
                    'class' => 'tag',
                ));
        ?>
        <div class="info-blog">
            <?php
                    $contentLi = $this->Html->tag('li', $this->Rumahku->icon('fa fa-calendar').$customModified);
                    $contentLi .= $this->Html->tag('li', $category);
                    $contentLi .= $this->Html->tag('li', $this->Rumahku->icon('fa fa-tags').$subtitle);
                    echo $this->Html->tag('ul', $contentLi, array(
                        'class' => 'top-info',
                    ));
                    
                    echo $this->Html->tag('h3', $this->Html->link($title, $url));
                    echo $this->Html->tag('p', $short_content);
            ?>
        </div>
    </div>
    <?php
            }
    ?>
</div>
<?php
        }
?>