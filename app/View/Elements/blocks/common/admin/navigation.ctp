<?php
        $dashboard_link = array(
            'controller' => 'users',
            'action' => 'dashboard',
            'plugin' => false
        );
        $menuTb = array(
            'dashboard' => array(
                'group' => array(13,14,19,20),
                'label' => __('Dashboard'),
                'icon' => 'fa fa-dashboard',
                'url' => $dashboard_link,
                'class' => (!empty($active_menu) && $active_menu == 'dashboard')?'open':'',
            ),
            'user' => array(
                'group' => array(11,14,18,19,20),
                'label' => __('User'),
                'icon' => 'fa fa-user',
                'url' => false,
                'childs' => array(
                    'user' => array(
                        'group' => array(11,18,19,20),
                        'label' => __('List User'),
                        'icon' => 'fa fa-user',
                        'url' => array(
                            'controller' => 'users',
                            'action' => 'index',
                            'plugin' => false
                        ),
                        'attr' => array(
                            'escape' => false
                        )
                    ),
                )
            ),
        );
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="../../img/avatar3.png" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <?php 
                        echo $this->Html->tag('p', sprintf('Hello, %s', $User['first_name']));
                ?>
            </div>
        </div>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php
                    $isDropdown = '';
                    $menuUrl = array();
                    $adminUrl = array('admin' => true);
                    $classAttribute = array('escape' => false);
                    foreach($menuTb as $menu){
                        if(in_array($logged_group, $menu['group'])) {
                            $label = $menu['label'];

                            if(!empty($menu['childs'])) {
                                $isDropdown = 'treeview';
                                $linkAttribute = array(
                                    'class' => 'dropdown-toggle',
                                    'data-toggle' => 'dropdown-disabled-hide',
                                );
                                $label = '<span>'.$label . '</span> <i class="fa fa-angle-left pull-right"></i>';
                            }

                            if(!empty($menu['class'])) {
                                $isDropdown = sprintf(' %s', $menu['class']);
                            }
                            
                            if(!empty($linkAttribute))
                                $classAttribute = array_merge($linkAttribute, $classAttribute);

                            if(!empty($menu['url']))
                                $menuUrl = array_merge($menu['url'], $adminUrl);

                            echo '<li class="'.$isDropdown.'">';
                                echo $this->Html->link('<i class="'.$menu['icon'].'"></i> '.$label, $menuUrl, $classAttribute);

                                if(!empty($menu['childs'])) {
                                    echo '<ul class="treeview-menu">';
                                    foreach($menu['childs'] as $child) {
                                        if(in_array($logged_group, $child['group'])) {
                                            $attr = false;

                                            if(!empty($child['url'])) {
                                                $childUrl = array_merge($child['url'], $adminUrl);
                                            }
                                            if(!empty($child['attr'])) {
                                                $attr = $child['attr'];
                                            }

                                            echo '<li>';
                                            echo $this->Html->link('<i class="fa fa-angle-double-right"></i> '.$child['label'], $childUrl, $attr);
                                            echo '</li>';
                                        }
                                    }
                                    echo '</ul>';
                                }
                            echo '</li>';
                        }
                    }   
            ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>