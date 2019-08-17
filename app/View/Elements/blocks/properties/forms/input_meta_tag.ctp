<?php 
		$data = $this->request->data;
		$meta_title = Common::hashEmptyField($data, 'PageConfig.meta_title');
		$meta_description = Common::hashEmptyField($data, 'PageConfig.meta_description');

		$options = array(
			'labelClass' => 'col-xl-2 taright col-sm-3',
			'class' => 'relative  col-sm-5 col-xl-4',
			'frameClass' => 'col-sm-12',
		);
?>
<div class="form-group plus">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 label">
            <?php 
                    echo $this->Html->tag('h4', __('Meta Tag SEO ( Optional )'));
            ?>
        </div>
    </div>
</div>
<?php 
		echo $this->Rumahku->buildInputForm('PageConfig.meta_title', array_merge($options, array(
			'label' => __('Meta Title'),
			'placeholder' => __('Meta Title Tag - SEO'),
			'attributes' => array(
				'value' => $meta_title,
			),
		)));
		echo $this->Rumahku->buildInputForm('PageConfig.meta_description', array_merge($options, array(
			'type' => 'textarea',
			'label' => __('Meta Deskripsi'),
			'placeholder' => __('Meta Deskripsi Tag - SEO'),
			'attributes' => array(
				'value' => $meta_description,
			),
		)));
?>