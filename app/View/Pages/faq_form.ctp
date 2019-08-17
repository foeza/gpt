<?php
        echo $this->Form->create('Faq');
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('faq_category_id', array(
                'label' => __('Kategori FAQ *'),
                'empty' => __('Pilih Kategori FAQ'),
                'options' => $faq_categories,
            ));
            echo $this->Rumahku->buildInputForm('question', array(
                'type' => 'text',
                'label' => __('Pertanyaan *'),
            ));
            echo $this->Rumahku->buildInputForm('answer', array(
                'type' => 'textarea',
                'label' => __('Jawaban *'),
            ));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
                        echo $this->Html->link(__('Kembali'), array(
                            'action' => 'faqs',
                            'admin' => true
                        ), array(
                            'class'=> 'btn default',
                        ));
    					echo $this->Form->button(__('Simpan'), array(
    		                'type' => 'submit', 
    		                'class'=> 'btn blue',
    		            ));
				?>
			</div>
		</div>
	</div>
</div>

<?php 
	echo $this->Form->end(); 
?>