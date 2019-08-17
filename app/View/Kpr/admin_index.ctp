<?php
		$searchUrl = array(
			'controller' => 'kpr',
			'action' => 'search',
			'index',
			'admin' => true,
		);
		$optionsFilter = array(
    		'Kpr.created-asc' => __('Lama ke Baru'),
    		'Kpr.created-desc' => __('Baru ke Lama'),
		);
		
        echo $this->Html->tag('div', $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Cari aplikasi berdasarkan kode KPR, Judul Properti, Nama dan Email Klien'),
        	'url' => $searchUrl,
        	'sorting' => array(
		        'options' => array(
                    'optionsFilter' => $optionsFilter,
                    'optionsStatus' => array(
                        'pending' => __('Pending'),
                        'rejected' => __('Ditolak'),
                        'process' => __('Proses'),
                        'approved' => __('Appraisal'),
                        'reschedule_pk' => __('Reschedule Akad'),
                        'approved_credit' => __('Akad Disetujui'),
                        'completed' => __('Completed'),
                    ),
	        		'url' => $searchUrl,
	        		'class' => 'col-sm-5',
	        		'lblClass' => 'col-sm-5',
	        		'inputClass' => 'col-sm-7',
	        	),
    		),
    	)), array(
            'class' => 'kpr-list-header',
        ));

        if(!empty($values)){
?>
<div class="crm kpr-list">
    <?php 
            echo $this->element('blocks/kpr/legend');
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
    ?>
</div>
<?php      
        }else{
            echo $this->Html->tag('div', __('KPR belum tersedia'), array(
                'class' => 'alert alert-warning tacenter',
            ));
        }
?>