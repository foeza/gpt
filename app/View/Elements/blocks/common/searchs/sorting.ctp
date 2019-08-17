<?php 
        $data            = $this->request->data;
        $_display        = isset($_display)?$_display:true;
        $classSelect     = isset($classSelect)?$classSelect:'form-control';
        $displayShow     = !empty($displayShow)?$displayShow:false;
        $custom_class    = !empty($custom_class)?$custom_class:false;
        $propertyActions = !empty($propertyActions)?$propertyActions:false;

        $options = !empty($options)?$options:array(
            '' => __('Urutkan'),
            'Property.created-desc' => __('Baru ke Lama'),
            'Property.created-asc' => __('Lama ke Baru'),
            'Property.price_converter-asc' => __('Harga rendah ke tinggi'),
            'Property.price_converter-desc' => __('Harga tinggi ke rendah'),
        );


        $sortName        = $this->Rumahku->filterEmptyField($this->params, 'named', 'sort');
        $sortDirection   = $this->Rumahku->filterEmptyField($this->params, 'named', 'direction');
        $property_action = $this->Rumahku->filterEmptyField($data, 'Search', 'property_action', 1);

        $urlDirectionAsc = $urlDirectionDesc = $this->passedArgs;
        $urlDirectionAsc['sort'] = $urlDirectionDesc['sort'] = $sortName;
        $urlDirectionAsc['direction'] = 'asc';
        $urlDirectionDesc['direction'] = 'desc';
?>
<div id="listing-header" class="clearfix">
    <div class="form-control-sort <?php echo $custom_class; ?>">
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
                    'class' => $classSelect,
                    'options'=> $options,
                ));
                echo $this->Form->hidden('show', array(
                    'value' => $displayShow,
                    'class' => 'show-sort'
                ));

                echo $this->Form->hidden('property_action',array(
                    'label'=> false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'chosen-select form-control',
                    'options' => $propertyActions,
                ));
        ?>
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