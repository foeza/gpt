<div id="sidebar-wrapper">
    <ul class="nav nav-pills nav-stacked">
    	<?php 
    			echo $this->Html->tag('li', $this->Html->link(__('Dashboard'), array(
                    'controller' => 'users',
                    'action' => 'account',
                    'admin' => false,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Ganti Foto Profil'), array(
                    'controller' => 'users',
                    'action' => 'edit_profile_picture',
                    'admin' => false,
                )));
    			echo $this->Html->tag('li', $this->Html->link(__('Biodata'), array(
    				'controller' => 'users',
    				'action' => 'edit',
    				'admin' => false,
				)));
                echo $this->Html->tag('li', $this->Html->link(__('Ganti Logo Perusahaan'), array(
                    'controller' => 'users',
                    'action' => 'edit_company_logo',
                    'admin' => false,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Profil Perusahaan'), array(
                    'controller' => 'users',
                    'action' => 'company',
                    'admin' => false,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Media Sosial'), array(
                    'controller' => 'users',
                    'action' => 'social_media',
                    'admin' => false,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Ganti Email'), array(
                    'controller' => 'users',
                    'action' => 'edit_email',
                    'admin' => false,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Password'), array(
                    'controller' => 'users',
                    'action' => 'edit_password',
                    'admin' => false,
                )));
                echo $this->Html->tag('li', $this->Html->link(__('Informasi Profesi'), array(
                    'controller' => 'users',
                    'action' => 'edit_user_profession',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('Tambah Admin'), array(
                    'controller' => 'users',
                    'action' => 'add_admin',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('List Admin'), array(
                    'controller' => 'users',
                    'action' => 'admins',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('Tambah Agen'), array(
                    'controller' => 'users',
                    'action' => 'add',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('List Agen'), array(
                    'controller' => 'users',
                    'action' => 'agents',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('Properti'), array(
                    'controller' => 'properties',
                    'action' => 'users',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('Kategori FAQ'), array(
                    'controller' => 'pages',
                    'action' => 'faq_category',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('FAQ'), array(
                    'controller' => 'pages',
                    'action' => 'faq',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(Configure::read('Global.Data.translates.id.blog'), array(
                    'controller' => 'advices',
                    'action' => 'index',
                    'admin' => false,
                )));

                echo $this->Html->tag('li', $this->Html->link(__('Banner'), array(
                    'controller' => 'Pages',
                    'action' => 'banner',
                    'admin' => false,
                )));

    			echo $this->Html->tag('li', $this->Html->link(__('Pengaturan'), '#'));
    	?>
    </ul>
</div>