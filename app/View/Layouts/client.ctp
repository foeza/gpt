<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
            $default_css = array(
                'admin/jquery',
                'admin/style',
            );

            echo $this->Rumahku->initializeMeta( $_global_variable );
            echo $this->Html->css($default_css).PHP_EOL;

            if(isset($layout_css) && !empty($layout_css)){
                foreach ($layout_css as $key => $value) {
                    echo $this->Html->css($value).PHP_EOL;
                }
            }

            echo $this->Html->css(array(
                'admin/custom',
            )).PHP_EOL;
    ?>
</head>
<body>
    <div id="body-client">
        <div id="big-wrapper">
            <div id="content-wrapper">
                <?php echo $this->element('headers/header_client').PHP_EOL;?>
                <div class="container">
                    <?php 
                            echo $this->element('blocks/common/flash');
                            echo $this->element('headers/breadcrumb').PHP_EOL;
                    ?>
                    <div id="content-client">
                            <?php 
                                    echo $this->Html->tag('div', $this->fetch('content'), array(
                                        'id' => 'wrapper-write',
                                    ));
                            ?>
                    </div>
                </div>  
            </div>
        </div>
    </div>
    <?php
            $default_js = array(
                'admin/jquery.library',
                'location_home.js',
            );
            echo $this->Html->script($default_js).PHP_EOL;

            if(isset($layout_js) && !empty($layout_js)){
                foreach ($layout_js as $key => $value) {
                    echo $this->Html->script($value).PHP_EOL;
                }
            }

            echo $this->Html->script(array(
                'admin/customs.library',
                'admin/functions',
            )).PHP_EOL;

            echo $this->element('blocks/common/modal');
    ?>
</body>
</html>
