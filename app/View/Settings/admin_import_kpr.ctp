<div class="group-list">
    <?php
    		echo $this->element('blocks/newsletters/forms/import_list', array(
    			'single_page' => true,
    			'link_upload' => array(
                    'controller' => 'settings',
                    'action' => 'download_xls',
                    'admin' => false,
                ),
    			'modelName' => 'KprBank',
    			'title' => array(
    				'header' => __('Upload KPR'),
    				'desc' => __('Upload berkas KPR '),
    			),
    		));
    ?>
</div>
<?php
		$searchUrl = array(
			'controller' => 'settings', 
			'action' => 'search',
			'import_kpr', 
			'admin' => TRUE,
		);

		$optionsFilter = array(
    		'Kpr.created-asc' => __('Lama ke Baru'),
    		'Kpr.created-desc' => __('Baru ke Lama'),
		);

		echo($this->element('blocks/common/forms/search/backend', array(
			'placeholder' => __('Cari aplikasi berdasarkan kode KPR, Judul Properti, Nama dan Email Klien'),
			'url' => $searchUrl,
			'sorting' => array(
		        'options' => array(
                    'optionsFilter' => $optionsFilter,
	        		'url' => $searchUrl,
	        		'class' => 'col-sm-5',
	        		'lblClass' => 'col-sm-5',
	        		'inputClass' => 'col-sm-7',
	        	),
    		),
		)));

		if(!empty($values)){
?>
<div class="project-list">
    <div class="my-properties">
        <div class="wrapper-border">
            <div id="list-property">
                <?php
                        foreach ($values as $key => $value) {
                            echo $this->element('blocks/kpr/items', array(
                                'value' => $value,
                            ));
                        }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
        echo $this->element('blocks/common/pagination');
		} else {
            echo $this->Html->tag('div', __('KPR belum tersedia'), array(
                'class' => 'alert alert-warning tacenter',
            ));
		}
?>