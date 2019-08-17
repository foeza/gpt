<div class="launcher-themes mt30">
    <div class="row mb30">
        <?php
                if( !empty($values) ) {
                    foreach( $values as $key => $value ) {
                        $id     = $this->Rumahku->filterEmptyField($value, 'ReportType', 'id');
                        $name   = $this->Rumahku->filterEmptyField($value, 'ReportType', 'name');

                        if( in_array($id, array(2,3)) && $logged_group == 2 ) {
                            break;
                        } else {
                            echo $this->element('blocks/properties/report/report_item', array(
                                'customPhoto' => false,
                                'name' => $name,
                                'itemUrl' => $this->Html->url(array(
                                    'controller' => 'properties',
                                    'action' => 'report_selection',
                                    $id,
                                    'admin' => true,
                                )),
                            ));
                        }
                    }
                }
        ?>
    </div>
</div>