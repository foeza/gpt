<?php
        $client_id = !empty($client_id)?$client_id:0;
        $agent_pic = !empty($agent_pic)?$agent_pic:array();
?>

<div class="wrapper-mapping-client-agent mapping-filter">
    <div class="row">
        <div class="col-sm-6 pr0"> 
            <div id="filter-related-agent" class="wrapper-filter-agent left-side">
                <?php
                        echo $this->Html->tag('h3', __('Agen Terhubung'));
                        echo $this->element('blocks/common/forms/search/backend', array(
                            'autocomplete' => 'off',
                            '_seachButtonType' => 'button',
                            '_form' => false,
                        ));
                ?>
                <div class="table-responsive">
                    <?php
                            echo $this->Form->create('UserClientRelation', array(
                                'class' => 'form-target',
                            ));
                    ?>
                    <table class="table grey">
                        <tbody>
                            <?php
                                    if( !empty($agent_pic) ) {
                                        $name = $this->Rumahku->filterEmptyField($agent_pic, 'User', 'full_name');
                                        $email = $this->Rumahku->filterEmptyField($agent_pic, 'User', 'email');

                                        echo $this->Html->tableCells(array(
                                            array(
                                                array(
                                                    __('Agen Utama'),
                                                    array(
                                                        'style' => 'width: 65px;',
                                                        'class' => 'tacenter',
                                                    ),
                                                ),
                                                sprintf('%s ( %s )', $name, $email),
                                            ),
                                        ));
                                    }

                                    if( !empty($related_agents) ) {
                                        foreach( $related_agents as $key => $value ) {
                                            $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
                                            $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
                                            $email = $this->Rumahku->filterEmptyField($value, 'User', 'email');

                                            echo $this->Html->tableCells(array(
                                                array(
                                                    array(
                                                        $this->Rumahku->buildCheckOption('UserClientRelation', $id, 'default'),
                                                        array(
                                                            'style' => 'width: 65px;',
                                                            'class' => 'taright',
                                                        ),
                                                    ),
                                                    sprintf('%s ( %s )', $name, $email),
                                                ),
                                            ));
                                        }
                                    }
                            ?>
                        </tbody>
                    </table>
                    <?php
                            echo $this->Form->end();
                    ?>
                </div>
            </div>
        </div>
        <div class="wrapper-arrow-up-down hide">
            <?php
                    echo $this->Html->tag('div', __('&laquo;'), array(
                        'class' => 'arrow-mapping arrow-left',
                        'data-from' => 'filter-agent',
                        'data-to' => 'filter-related-agent',
                    ));
                    echo $this->Html->tag('div', __('&raquo;'), array(
                        'class' => 'arrow-mapping arrow-right',
                        'data-from' => 'filter-related-agent',
                        'data-to' => 'filter-agent',
                    ));
            ?>
        </div>
        <div class="col-sm-6 pl0"> 
            <div id="filter-agent" class="wrapper-filter-agent right-side">
                <?php
                        echo $this->Html->tag('h3', __('Daftar Agen'));
                        echo $this->element('blocks/common/forms/search/backend', array(
                            'autocomplete' => 'off',
                            '_seachButtonType' => 'button',
                            '_form' => false,
                        ));
                ?>
                <div class="table-responsive">
                    <table class="table grey">
                        <tbody>
                            <?php
                                    if( !empty($agents) ) {
                                        foreach( $agents as $key => $value ) {
                                            $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
                                            $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
                                            $email = $this->Rumahku->filterEmptyField($value, 'User', 'email');
                                            
                                            echo $this->Html->tableCells(array(
                                                array(
                                                    array(
                                                        $this->Rumahku->buildCheckOption('UserClientRelation', $id, 'default'),
                                                        array(
                                                            'style' => 'width: 65px;',
                                                            'class' => 'taright',
                                                        ),
                                                    ),
                                                    sprintf('%s ( %s )', $name, $email),
                                                ),
                                            ));
                                        }
                                    }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="wrapper-arrow">
        <?php
                echo $this->Html->tag('div', __('&laquo;'), array(
                    'class' => 'arrow-mapping arrow-left',
                    'data-from' => 'filter-agent',
                    'data-to' => 'filter-related-agent',
                ));
                echo $this->Html->tag('div', __('&raquo;'), array(
                    'class' => 'arrow-mapping arrow-right',
                    'data-from' => 'filter-related-agent',
                    'data-to' => 'filter-agent',
                ));
        ?>
    </div>
</div>

<div class="row hidden-print">
    <div class="col-sm-12">
        <div class="action-group mt20">
            <div class="btn-group floleft">
                <?php
                        echo $this->Html->link(__('Simpan'), array(
                            'controller' => 'users',
                            'action' => 'client_agent_mapping_multiple',
                            $client_id,
                            'admin' => true,
                        ), array(
                            'class' => 'btn blue btnSaveMapping',
                            'data-from' => 'filter-related-agent'
                        ));

                        echo $this->Html->link(__('Batal'), array(
                            'controller' => 'users',
                            'action' => 'client_info',
                            'admin' => true,
                        ), array(
                            'class'=> 'btn default',
                        ));
                ?>
            </div>
        </div>
    </div>
</div>