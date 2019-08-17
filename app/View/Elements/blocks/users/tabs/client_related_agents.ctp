<?php
        $agent_pic_id = !empty($agent_pic_id)?$agent_pic_id:0;

        $searchUrl = !empty($searchUrl)?$searchUrl:array(
            'controller' => 'users',
            'action' => 'search',
            'client_related_agents',
            true,
            $id,
            'admin' => true,
        );

        $dataColumns = array(
            'keyword' => array(
                'name' => __('Nama'),
                'field_model' => 'User.keyword',
                'width' => '150px;',
                'filter' => 'text',
            ),
            'no_hp' => array(
                'name' => __('No. Telepon'),
                'field_model' => 'UserProfile.no_hp',
                'width' => '120px;',
                'filter' => 'text',
            ),
            'email' => array(
                'name' => __('EMAIL'),
                'field_model' => 'User.email',
                'width' => '120px;',
                'filter' => 'text',
            )
        );

        echo $this->Form->create('User', array(
            'url' => $searchUrl,
            'class' => 'form-target form-table-search',
        ));
?>

<div class="table-responsive">
    <table class="table grey">
        <?php
                $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table', array(
                    'thead' => true,
                    'sortOptions' => array(
                        'ajax' => true,
                    ),
                    'table_ajax' => true,
                    'no_clear_link' => true
                ));

                echo $fieldColumn;
        ?>
        <tbody>
            <?php
                    if( !empty($values) ) {
                        $emptyText = '-';

                        foreach( $values as $key => $value ) {
                            
                            $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
                            $no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp');
                            $email = $this->Rumahku->filterEmptyField($value, 'User', 'email');


                            $no_hp = $no_hp ? $this->Html->link($no_hp, sprintf('tel:%s', $no_hp)) : $emptyText;
                            $email = $email ? $this->Html->link($email, sprintf('mailto:%s', $email)) : $emptyText;

                            // $last_project = 'Under Development';
                            $custom_link = $this->Rumahku->_callUserFullName($value, true, array(
                                'target' => '_blank',
                            ), array(
                                'link' => array(
                                    'controller' => 'users',
                                    'action' => 'info',
                                ),
                                'modelName' => 'id',
                            ));

                            if( $id == $agent_pic_id ) {
                                $custom_link = sprintf('%s ( Agen Utama )', $custom_link);
                            }

                            echo $this->Html->tableCells(array(
                                array(
                                    $custom_link,
                                    $no_hp,
                                    $email,
                                    // $last_project,
                                ),
                            ));
                        }
                    }
            ?>
        </tbody>
    </table>
    <?php
            if( empty($values) ) {
    ?>
    <div class="filter-footer">
        <?php 
                echo $this->Html->tag('p', __('Data belum tersedia'), array(
                    'class' => 'alert alert-warning tacenter'
                ));
        ?>
    </div>
    <?php
            }
    ?>
</div>
<?php 
        echo $this->Form->end();
        echo $this->element('blocks/common/pagination', array(
            '_ajax' => true,
        ));
?>