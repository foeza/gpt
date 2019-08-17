<?php
        $full_name = $User['full_name'];
?>
<header class="header">
    <?php
        echo $this->Html->link(Configure::read('__Site.site_name'), '/', array(
            'class' => 'logo',
            'target' => 'blank'
        ));
    ?>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <?php
                            echo $this->Html->link(sprintf('<i class="fa fa-user"></i>
                            <span>%s <i class="caret"></i></span>', $full_name), 'javascript:void(0);', array(
                                'escape' => false,
                                'class' => 'dropdown-toggle',
                                'data-toggle' => 'dropdown'
                            ));
                    ?>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue">
                            <img src="../../img/avatar3.png" class="img-circle" alt="User Image" />
                            <?php
                                $title_user = sprintf('%s - %s <small>Member sejak %s</small>', $full_name, $User['Group']['name'], $this->Rumahku->formatDate($User['created']));
                                echo $this->Html->tag('p', ucwords($title_user));
                            ?>
                        </li>
                        <!-- Menu Body -->
                        <?php
                                if(!empty($User['group_id']) && $User['group_id'] == 20){
                        ?>
                        <li class="user-body">
                            <div class="col-xs-12 text-center">
                                <?php
                                        echo $this->Html->link('Access Control List', array(
                                            'plugin' => 'acl_manager',
                                            'controller' => 'acl',
                                            'action' => 'permissions',
                                            'admin' => true
                                        ));
                                ?>
                            </div>
                        </li>
                        <?php
                                }
                        ?>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <?php
                                        echo $this->Html->link('Sign out', array(
                                            'controller' => 'users',
                                            'action' => 'logout',
                                            'admin' => false,
                                            'plugin' => false
                                        ), array(
                                            'class' => 'btn btn-default btn-flat'
                                        ));
                                ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>