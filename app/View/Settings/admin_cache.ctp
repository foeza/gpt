<?php
        $options = array(
            'frameClass' => 'col-sm-7',
            'labelClass' => 'col-xl-2 col-sm-3',
            'class' => 'relative col-sm-8 col-xl-7',
        );
        $viewCache = empty($viewCache) ? NULL : $viewCache;

        echo $this->Html->tag('h2', __('Cache'), array(
            'class' => 'sub-heading'
        ));
        echo $this->Form->create('Setting');
?>
<div class="form-group form-group-static">
    <div class="row">
        <div class="col-sm-8">
            <div class="row">
                <?php
                        $viewCache = empty($viewCache) ? NULL : $viewCache;

                        $label  = $this->Html->tag('label', __('Cache View'), array('class' => 'control-label'));
                        $link   = $this->Html->url(array('view', 1), TRUE);
                        $text   = __('%s Cache View', count($viewCache));

                        echo($this->Html->div('col-sm-4 control-label taright', $label));

                        echo($this->Html->div('col-sm-4', $this->Html->tag('p', $text, array(
                            'class' => 'form-control-static', 
                        ))));

                        echo($this->Html->div('col-sm-4', $this->Html->link(__('Kosongkan Cache'), $link, array(
                            'class' => 'btn default', 
                        ))));
                ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group form-group-static">
    <div class="row">
        <div class="col-sm-8">
            <div class="row">
                <?php
                        $queryCache     = empty($queryCache) ? NULL : $queryCache;
                        $groupCache     = $this->Rumahku->filterEmptyField($queryCache, 'group');
                        $singleCache    = $this->Rumahku->filterEmptyField($queryCache, 'single');

                        $text   = $groupCache ? __('%s Cache Group', count($groupCache)) : '';
                        $label  = $this->Html->tag('label', __('Cache Query'), array('class' => 'control-label'));
                        $link   = $this->Html->url(array('group_query', 1), TRUE);

                        echo($this->Html->div('col-sm-4 control-label taright', $label));
                        echo($this->Html->div('col-sm-4', $this->Html->tag('p', $text, array(
                            'class' => 'form-control-static', 
                        ))));
                        echo($this->Html->div('col-sm-4', $this->Html->link(__('Kosongkan Cache'), $link, array(
                            'class' => 'btn default', 
                        ))));
                ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group form-group-static">
    <div class="row">
        <div class="col-sm-8">
            <div class="row">
                <?php

                        $text   = $singleCache ? __('%s Cache Single', count($singleCache)) : '';
                        $label  = $this->Html->tag('label', '&nbsp;', array('class' => 'control-label'));
                        $link   = $this->Html->url(array('single_query', 1), TRUE);

                        echo($this->Html->div('col-sm-4 control-label taright', $label));
                        echo($this->Html->div('col-sm-4', $this->Html->tag('p', $text, array(
                            'class' => 'form-control-static', 
                        ))));
                        echo($this->Html->div('col-sm-4', $this->Html->link(__('Kosongkan Cache'), $link, array(
                            'class' => 'btn default', 
                        ))));

                ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group">
                <?php
                        $link   = $this->Html->url(array('all', 1), TRUE);
                        echo $this->Html->link(__('Kosongkan Semua'), $link, array(
                            'class'=> 'btn blue floright',
                        ));
                ?>
            </div>
        </div>
    </div>
</div>
<?php
        echo $this->Form->end();
?>