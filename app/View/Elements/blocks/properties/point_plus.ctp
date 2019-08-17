<?php 
        $data = $this->request->data;
        $pointPlus = $this->Rumahku->filterEmptyField($data, 'PropertyPointPlus', 'name');
?>
<div class="form-group plus" id="point-plus">
    <div class="row desc">
        <div class="col-sm-10 col-sm-offset-1">
            <?php 
                    echo $this->Html->tag('h4', __('Nilai Lebih Properti'));
                    echo $this->Html->tag('p', __('Berikan informasi mengenai nilai lebih dari properti yang Anda tawarkan. Hal ini juga dapat membantu properti Anda lebih cepat terjual/tersewa.'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-1">
            <div class="extra-plus-list form-added">
                <ul>
                    <?php 
                            if( !empty($pointPlus) ) {
                                $idx = 0;

                                foreach ($pointPlus as $key => $value) {
                                    echo $this->element('blocks/properties/forms/point_plus_items', array(
                                        'idx' => $idx,
                                        'value' => $value,
                                    ));
                                    $idx++;
                                }
                            } else {
                                for ($i=0; $i < 2; $i++) {
                                    echo $this->element('blocks/properties/forms/point_plus_items', array(
                                        'idx' => $i,
                                    ));
                                }
                            }
                    ?>
                </ul>
                <?php 
                        $contentLink = $this->Html->tag('span', $this->Rumahku->icon('rv4-bold-plus'), array(
                            'class' => 'btn dark small-fixed',
                        ));
                        $contentLink .= $this->Html->tag('span', __('Tambah Nilai Lebih'));
                        echo $this->Html->link($contentLink, '#', array(
                            'escape' => false,
                            'role' => 'button',
                            'class' => 'field-added',
                        ));
                ?>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <?php 
                    // echo $this->Html->tag('p', sprintf(__('%sTambah kategori akan membutuhkan persetujuan dari Rumahku.com untuk memastikan validitas data. %s'), $this->Html->tag('strong', __('Keterangan: ')), $this->Html->link(__('Info detil'), '#')));
            ?>
        </div>
    </div> -->
</div>