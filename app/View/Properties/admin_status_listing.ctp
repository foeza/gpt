<?php
		$property = !empty($property)?$property:false;

		$property_id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$id_property_status_listing = $this->Rumahku->filterEmptyField($property, 'PropertyStatusListing', 'id');
		$name_status_listing = $this->Rumahku->filterEmptyField($property, 'PropertyStatusListing', 'name');
		
		$data = $this->request->data;
?>
<div class="modal-subheader">
	<div id="list-property">
		<?php 
				echo $this->element('blocks/properties/items', array(
					'value' => $property,
					'fullDisplay' => false,
				));
		?>
	</div>
</div>
<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('Property', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-2 col-sm-2 taright',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
	?>
		<div class="content" id="property-sold">
			<div class="form-group">
			    <div class="row">
			        <div class="col-sm-12">
			            <div class="row">
			            	<?php
					                echo $this->Rumahku->buildInputForm('property_status_id', array_merge($options, array(
						                'label' => __('Pilih Kategori *'),
			                      		'empty' => __('Pilih'),
						                'options' => $category_status
						            )));
			                ?>
			            </div>
			        </div>
			        <?php if (!empty($id_property_status_listing)): ?>
				        <div class="col-sm-12 row-button-status">
							<div class="row">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div class="row">
												<div class="col-xl-2 col-sm-2 taright">
													<label for="PropertyPropertyStatusId" class="control-label"></label>
												</div>
												<div class="relative col-sm-9 col-xl-7">
													<?php
															echo __('Atau ').$this->Html->link('RESET KATEGORI', array(
																'controller' => 'properties',
																'action' => 'remove_status_category',
																$property_id,
																'admin' => true,
															), array(
																'escape' => false,
																'class' => 'remove-status'
															));
													?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
				        </div>
			        <?php endif ?>
			    </div>
			</div>
		</div>
		<div class="modal-footer">
			<?php 
					echo $this->Html->link(__('Batal'), '#', array(
	    	            'class' => 'close btn default',
	    	            'data-dismiss' => 'modal',
	    	            'aria-label' => 'close',
	    	        ));
					echo $this->Form->button(__('Simpan'), array(
	    	            'class' => 'btn blue',
	    	        ));
			?>
		</div>
	<?php 
	        echo $this->Form->end();
	?>
</div>