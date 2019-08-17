<?php
        $isDirector = $this->Rumahku->_callIsDirector();

        if( !empty($isDirector) ) {
            $save_path = Configure::read('__Site.logo_photo_folder');

            $parent = $this->Rumahku->filterEmptyField($value, 'Parent');

            $id = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'id');
            $slug = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'slug');
            $name = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'name');
            $phone = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'phone');
            $phone_2 = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'phone_2');
            $fax = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'fax');
            $logo = $this->Rumahku->filterEmptyField($parent, 'UserCompany', 'logo');

            $dataCompany = $this->Rumahku->filterEmptyField($parent, 'UserCompany');
            $location = $this->Rumahku->getFullAddress($dataCompany, ', ', true);

            $logo = $this->Rumahku->photo_thumbnail(array(
                'save_path' => $save_path, 
                'src'=> $logo, 
                'size' => 'xxsm',
            ), array(
                'title' => $name,
                'alt' => $name,
            ));

            $url = $this->Html->url(array(
                'controller' => 'users',
                'action' => 'company',
                $id,
                $slug,
            ));

            echo $this->Html->tag('h1', __('Agen Dari'), array(
                'class' => 'section-title',
                'data-animation-direction' => 'from-bottom',
                'data-animation-delay' => '50',
            ));
?>
<div class="latest-news list-style clearfix" id="widget-company">
    <div class="item col-sm-12" data-animation-direction="from-bottom" data-animation-delay="250">
        <a href="<?php echo $url; ?>">
            <div class="image">
                <?php
                        echo $logo;
                ?>
            </div>
            <div class="info-blog">
                <?php
                        $contentLi = '';

                        if( !empty($location) ) {
                            $name .= $this->Html->tag('small', $location);
                        }

                        echo $this->Html->tag('h3', $name);

                        if( !empty($phone) ) {
                            $contentLi .= $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$phone);
                        }
                        if( !empty($phone_2) ) {
                            $contentLi .= $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$phone_2);
                        }
                        if( !empty($fax) ) {
                            $contentLi .= $this->Html->tag('li', $this->Rumahku->icon('fa fa-fax').$fax);
                        }

                        if( !empty($contentLi) ) {
                            echo $this->Html->tag('ul', $contentLi, array(
                                'class' => 'top-info',
                            ));
                        }
                ?>
            </div>
        </a>
    </div>
</div>
<?php
        }
?>