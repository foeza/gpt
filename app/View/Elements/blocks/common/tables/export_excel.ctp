<style>
    .string{ mso-number-format:\@; }
    .tacenter {
        text-align: center;
    }
    .taright {
        text-align: right;
    }
    .taleft {
        text-align: left;
    }
    tr td {
        padding: 3px 5px;
    }
</style>
<?php 
        $module_title = !empty($module_title)?$module_title:false;
        $periods = !empty($periods)?$periods:false;
        $filename = !empty($filename)?$filename:$module_title;
        $contentHeader = !empty($contentHeader)?$contentHeader:false;
        $topHeader = !empty($topHeader)?$topHeader:false;
        $noHeader = !empty($noHeader)?$noHeader:false;

        $full_name = $this->Rumahku->filterEmptyField($User, 'full_name');

        $contentTr = isset($contentTr)?$contentTr:true;
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename='.$filename.'.xls');

        if( !empty($periods) && !empty($module_title) ) {
            $module_title .= sprintf('<br>%s', $periods);
        }
?>
<section class="content">
    <?php 
            echo $topHeader;
            
            if( !empty($customHeader) ) {
                echo $customHeader;
            } else if( !empty($module_title) && empty($noHeader) ) {
                echo $this->Html->tag('h2', $module_title, array(
                    'style' => 'text-align: center;',
                ));
            }

            echo $contentHeader;

            if( !empty($tableContent) ) {
                echo $tableContent;
            } else {
    ?>
    <table style="width: 100%;" singleSelect="true" border="1">
        <?php 
                if( !empty($tableHead) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $tableHead, array(
                        'style' => 'background-color: #069E55;color: #FFF;',
                    )));
                }
                if( !empty($tableBody) ) {
                    echo $this->Html->tag('tbody', $tableBody);
                }
        ?>
    </table>
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</div>