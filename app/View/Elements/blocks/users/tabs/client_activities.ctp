<div class="table-responsive">
    <?php
            echo $this->Form->create('User', array(
                'class' => 'form-target',
            ));

            if( !empty($values) ) {
                $dataColumns = array(
                    'project' => array(
                        'name' => __('Proyek'),
                    ),
                    'agent' => array(
                        'name' => __('Agen Yang Ditugaskan'),
                    ),
                    'start_date' => array(
                        'name' => __('Tanggal Mulai'),
                    ),
                    'end_date' => array(
                        'name' => __('Tanggal Berakhir'),
                    ),
                );
                $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
    ?>
    <table class="table grey">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
        <tbody>
            <?php
                    /* PENDING */
                    foreach( $values as $key => $value ) {

                    }
            ?>
        </tbody>
    </table>
    <?php 
            } else {
                echo $this->Html->tag('p', __('Data belum tersedia'), array(
                    'class' => 'alert alert-warning'
                ));
            }

            echo $this->Form->end();
    ?>
</div>
<?php 
        // echo $this->element('blocks/common/pagination');
?>