<?php
		$isAgent = Common::isAgent();

		if( empty($isAgent) ) {
?>
<div id="wrapper-write-crm-agent" class="info-wrapper">
	<?php 
			echo $this->Html->tag('h1', __('Informasi Agen'), array(
				'class' => 'info-title',
			));
	?>
	<div class="row">
		<?php 
				echo $this->Rumahku->buildInputGroup('user', __('Nama / Email Agen'), array(
					'placeholder' => __('Masukan nama / email Agen'),
					'divClass' => 'col-sm-12',
					'errorFieldName' => 'user_id',
					'error' => true,
					'attributes' => array(
	            		'id' => 'autocomplete',
			            'autocomplete' => 'off',
			            'data-ajax-url' => $this->Html->url(array(
			            	'controller' => 'ajax',
			            	'action' => 'list_users',
			            	2,
			            	1,
			            	'admin' => false,
		            	)),
		            	'href' => $this->Html->url(array(
			            	'controller' => 'crm',
			            	'action' => 'get_agent',
			            	'admin' => true,
		            	)),
		            	'data-change' => 'true',
		            	'data-wrapper-write' => '#wrapper-write-crm',
					),
				));
		?>
	</div>
</div>
<?php
		}
?>