<?php
        $id = $this->Rumahku->filterEmptyField($user, 'UserClient', 'id');
?>

<div class="client-info">
    <?php
            echo $this->element('blocks/users/headers/title');
            echo $this->element('blocks/users/client_short_desc');
            echo $this->element('blocks/common/tab_content', array(
                '_id' => 'wrapper-outer-client-detail',
                '_redirect' => 'true',
                'content' => array(
                    // 'client_activities' => array(
                    //     'content_tab' => $this->element('blocks/users/tabs/client_activities'),
                    //     'title_tab' => __('Aktivitas'),
                    //     'url' => $tabs_action_type,
                    // ),
                    'client_properties' => array(
                        'content_tab' => false,
                        'title_tab' => __('Daftar Properti'),
                        'url' => $this->Html->url(
                            array(
                                'controller' => 'users',
                                'action' => 'client_properties',
                                $id,
                                'admin' => true,
                            )
                        ),
                    ),
                    'client_related_agents' => array(
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
                    ),
                ),
                '_type' => 'style1',
            ));
    ?>
</div>