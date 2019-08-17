<?php 
        $data = $this->request->data;
        $displayShow = !empty($displayShow)?$displayShow:false;
        $_display = isset($_display)?$_display:true;
        $options = !empty($options)?$options:array(
            '' => __('Urutkan'),
            'Property.created-desc' => __('Baru ke Lama'),
            'Property.created-asc' => __('Lama ke Baru'),
            'Property.price_converter-asc' => __('Harga rendah ke tinggi'),
            'Property.price_converter-desc' => __('Harga tinggi ke rendah'),
        );

        // $urlDirection = $this->passedArgs;
        $sortName = $this->Rumahku->filterEmptyField($this->params, 'named', 'sort');
        $sortDirection = $this->Rumahku->filterEmptyField($this->params, 'named', 'direction');

        $urlDirectionAsc = $urlDirectionDesc = $this->passedArgs;
        $urlDirectionAsc['sort'] = $urlDirectionDesc['sort'] = $sortName;
        $urlDirectionAsc['direction'] = 'asc';
        $urlDirectionDesc['direction'] = 'desc';
?>
<div id="listing-header" class="clearfix">
    <div class="form-control-small">
        <?php
                echo $this->Form->input('sort', array(
                    'label'=> false,
                    'div' => array(
                        'class' => 'controls'
                    ),
                    'id' => 'sort_by',
                    'autocomplete'=> false,
                    'empty'=> false,
                    'error' => false,
                    'onChange' => 'submit()',
                    'class' => 'form-control',
                    'options'=> $options,
                ));
                echo $this->Form->hidden('show', array(
                    'value' => $displayShow,
                    'class' => 'show-sort'
                ));
        ?>
    </div>

    <div class="sort">
        <ul>
            <?php   
                    // echo $this->Html->tag('li', $this->Html->link($this->Html->tag('i', '', array(
                    //     'data-toggle' => 'tooltip',
                    //     'data-placement' => 'top',
                    //     'class' => 'fa fa-chevron-down',
                    // )), $urlDirectionAsc, array(
                    //     'escape' => false,
                    // )), array(
                    //     'class' => ( $sortDirection == 'asc' )?'active':'',
                    // ));

                    // echo $this->Html->tag('li', $this->Html->link($this->Html->tag('i', '', array(
                    //     'data-toggle' => 'tooltip',
                    //     'data-placement' => 'top',
                    //     'class' => 'fa fa-chevron-up',
                    // )), $urlDirectionDesc, array(
                    //     'escape' => false,
                    // )), array(
                    //     'class' => ( $sortDirection == 'desc' )?'active':'',
                    // ));
            ?>
        </ul>
    </div>
    <?php 
            if( !empty($_display) ) {
    ?>
    <div class="view-mode">
        <?php 
                echo $this->Html->tag('span', __('Mode Tampilan'));
        ?>
        <ul>
            <?php
                    echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-th'), array_merge($this->passedArgs, array(
                        'show' => 'grid'
                    )), array(
                        'escape' => false
                    )), array(
                        'data-view' => 'grid-style1',
                        'rel' => 'grid',
                        'class' => ($displayShow == 'grid') ? 'active' : '',
                    ));

                    echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-th-list'), array_merge($this->passedArgs, array(
                        'show' => 'list'
                    )), array(
                        'escape' => false
                    )), array(
                        'data-view' => 'list-style',
                        'rel' => 'list',
                        'class' => ($displayShow == 'list') ? 'active' : '',
                    ));
            ?>
        </ul>
    </div>
    <?php 
            }
    ?>
</div>