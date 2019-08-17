<?php
        $id = $this->Rumahku->filterEmptyField($user, 'UserClient', 'id');
        $isAdmin = Configure::read('User.admin');
        $content = array(
            // 'client_activities' => array(
            //     'content_tab' => false,
            //     'title_tab' => __('Aktivitas'),
            //     'url' => $this->Html->url(
            //         array(
            //             'controller' => 'users',
            //             'action' => 'client_activities',
            //             $id,
            //             'admin' => true,
            //         )
            //     ),
            // ),
            'client_properties' => array(
                'content_tab' => $this->element('blocks/users/tabs/client_properties'),
                'title_tab' => __('Daftar Properti'),
                'url' => $tabs_action_type,
            ),
        );

        if( !empty($isAdmin) ) {
            $content['client_related_agents'] = array(
                'content_tab' => false,
                'title_tab' => __('Agen Terhubung'),
                'url' => $this->Html->url(
                    array(
                        'controller' => 'users',
                        'action' => 'client_related_agents',
                        $id,
                        'admin' => true,
                    )
                ),
            );
        }
?>
<div class="client-info">
    <?php
            echo $this->element('blocks/users/headers/title');
            echo $this->element('blocks/users/client_short_desc');
            echo $this->element('blocks/common/tab_content', array(
                '_id' => 'wrapper-outer-client-detail',
                '_redirect' => 'true',
                'content' => $content,
                '_type' => 'style1',
            ));
    ?>
</div>