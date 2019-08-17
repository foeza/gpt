<?php
        echo $this->Form->create('AdviceCategory');
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('name', array(
                'label' => __('Kategori *'),
            ));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
			            echo $this->AclLink->link(__('Kembali'), array(
							'action' => 'advice_categories',
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