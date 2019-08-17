<?php 
		$_config 	= Configure::read('Config.Company.data');
		$property 	= !empty($property)?$property:false;
		$periods 	= !empty($periods)?$periods:false;

		$is_co_broke 		= Common::hashEmptyField($_config, 'UserCompanyConfig.is_co_broke');
		
		$actionId 			= Common::hashEmptyField($property, 'PropertyAction.id');
		$actionName 		= Common::hashEmptyField($property, 'PropertyAction.inactive_name');
		$actionShortName 	= Common::hashEmptyField($property, 'PropertyAction.inactive_name');
		$bt 				= Common::hashEmptyField($property, 'Property.bt');
		
		$data = $this->request->data;
		$this->request->data['PropertySold']['bt_commission_percentage'] = Common::hashEmptyField($data, 'PropertySold.bt_commission_percentage', $bt);

		$class_submit_period = 'col-sm-12';
		if($actionId == 2){
			$class_submit_period = 'col-sm-10';
		}

		$bt_text = 'BT';
		if(empty($is_co_broke)){
			$bt_text = 'Perantara';
		}
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
			echo $this->Form->create('PropertySold', array(
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
			                        echo $this->Html->tag('div', $this->Form->label('price_sold', sprintf(__('Harga %s *'), $actionShortName), array(
			                            'class' => 'control-label',
			                        )), array(
			                            'class' => 'col-xl-2 col-sm-2 taright',
			                        ));
			                ?>               
			                <div class="col-sm-9 col-xl-7">
			                	<div class="input-group <?php echo $class_submit_period;?>">
				                	<?php
				                			echo $this->Form->input('PropertySold.currency_id', array(
				                                'id' => 'currency',
				                                'class' => 'input-group-addon',
				                                'label' => false,
				                                'div' => false,
				                                'required' => false,
				                                'options' => $currencies
				                            ));
				                            echo $this->Form->input('price_sold', array(
				                                'type' => 'text',
				                                'id' => 'price',
				                                'class' => 'form-control has-side-control at-left input_price',
				                                'label' => false,
				                                'div' => false,
				                                'required' => false,
				                            ));
				                	?>
			                	</div>
			                	<?php
			                			if($actionId == 2){
			                	?>
			                	<div class="input-group col-sm-2">
				                	<?php
				                            echo $this->Form->input('PropertySold.period_id', array(
				                                'class' => 'form-control',
				                                'label' => false,
				                                'div' => false,
				                                'required' => false,
				                                'options' => $periods
				                            ));
				                	?>
				                </div>
				                <?php
			                			}
			                	?>
			                </div>
			            </div>
			        </div>
			    </div>
			</div>
			<?php 
					// echo $this->Rumahku->buildInputForm('price_sold', array_merge($options, array(
					// 	'type' => 'text',
		   //              'label' => sprintf(__('Harga %s *'), $actionShortName),
		   //              'inputClass' => 'input_price',
		   //          )));

					echo $this->Rumahku->buildInputForm('sold_by_name', array_merge($options, array(
						'type' => 'text',
		                'label' => sprintf(__('%s Oleh *'), $actionName),
		                'id' => 'autocomplete',
		                'attributes' => array(
		                	'class' => 'sold-by-name',
                    		'autocomplete' => 'off',
                    		'data-ajax-url' => $this->Html->url(array(
			                    'controller' => 'ajax',
			                    'action' => 'list_users',
			                    'admin' => false,
			                )),
			                'disabled' => (!empty($this->request->data['PropertySold']['is_cobroke'])) ? true : false
                		),
		            )));

		            if(!empty($list_user_cobroke)){
		    ?>
		    <div class="form-group cobroke-box">
			    <div class="row">
			        <div class="col-sm-12">
			            <div class="row">
			                <div class="col-xl-offset-2 col-sm-offset-2 col-sm-9 col-xl-7">
			                      <?php
			                      		echo $this->Rumahku->checkbox('is_cobroke', array(
			                      			'label' => __('Agen Co-Broke?'),
			                      			'class' => 'handle-toggle-content',
				                			'data-target' => '.sold-cobroke-box',
				                			'data-disabled' => '.sold-by-name'
			                      		));

			                      		$content = $this->Form->input('sold_by_coBroke_id', array(
			                      			'label' => false,
			                      			'div' => false,
			                      			'options' => $list_user_cobroke,
			                      			'empty' => __('Pilih Agen Co Broking'),
			                      			'required' => false
			                      		));

			                      		echo $this->Html->div('sold-cobroke-box', $content, array(
								            'style' => 'display:'.(!empty($this->request->data['PropertySold']['is_cobroke']) ? 'block' : 'none')
								        ));
			                      ?>        
			                </div>
			            </div>
			        </div>
			    </div>
			</div>
		    <?php
		    		}
		    ?>
		    <?php
						if(!empty($bt)){
			?>
			<div class="form-group cobroke-box">
			    <div class="row">
			        <div class="col-sm-12">
			            <div class="row">
			                <div class="col-xl-offset-2 col-sm-offset-2 col-sm-9 col-xl-7">
			                      <?php
		                      			echo $this->Rumahku->checkbox('is_bt_commision', array(
			                      			'label' => __('Menggunakan %s?', $bt_text),
			                      			'class' => 'handle-toggle-content',
				                			'data-target' => '.sold-bt-box',
			                      		));
			                      ?>        
			                </div>
			            </div>
			        </div>
			    </div>
			</div>
			<div class="sold-bt-box" style="display: <?php echo (!empty($this->request->data['PropertySold']['is_bt_commision']) ? 'block' : 'none')?>;">
				<?php
						echo $this->Rumahku->buildInputForm('bt_name', array_merge($options, array(
							'type' => 'text',
			                'label' => __('Nama %s *', $bt_text),
			            )));

			            echo $this->Rumahku->buildInputForm('bt_address', array_merge($options, array(
							'type' => 'text',
			                'label' => __('Alamat %s *', $bt_text),
			            )));
			            
			            echo $this->Rumahku->buildInputForm('bt_commission_percentage', array_merge($options, array(
		                    'type' => 'text',
		                    'label' => __('Komisi %s *', $bt_text),
		                    'formGroupClass' => 'form-group input-text-center',
		                    'inputClass' => 'input_number',
		                    'textGroup' => '%',
		                    'class' 	=> 'relative col-sm-2 col-xl-2',
		            		'outer_group' => true
		                )));

		                echo $this->Rumahku->buildInputForm('bt_type_commission', array_merge($options, array(
			                'label' => __('Asal Komisi %s *', $bt_text),
			                'options' => $type_commission
			            )));
				?>
			</div>
		    <?php
		            }
		            
		            echo $this->Rumahku->buildInputForm('client_name', array_merge($options, array(
						'type' => 'text',
		                'label' => __('Klien'),
		                'id' => 'autocomplete2',
		                'attributes' => array(
                    		'autocomplete' => 'off',
                    		'data-ajax-url' => $this->Html->url(array(
			                    'controller' => 'ajax',
			                    'action' => 'list_users',
			                    10,
			                    'admin' => false,
			                )),
                		),
		            )));

		            if( $actionId == 2 ) {
			            echo $this->Rumahku->buildInputMultiple('sold_date', 'end_date', array(
			                'label' => sprintf(__('Tgl %s *'), $actionName),
			                'divider' => 'rv4-bold-min small',
			                'inputClass' => 'datepicker',
			                'inputClass2' => 'to-datepicker',
			                'frameClass' => 'col-sm-12',
			                'labelDivClass' => 'col-xl-2 col-sm-2',
			                'attributes' => array(
			            		'type' => 'text',
		                	),
			            ));
			        } else {
						echo $this->Rumahku->buildInputForm('sold_date', array_merge($options, array(
							'type' => 'text',
			                'label' => __('Tgl Terjual *'),
			                'inputClass' => 'datepicker',
			            )));
			        }

					echo $this->Rumahku->buildInputForm('note', array_merge($options, array(
		                'label' => __('Keterangan'),
		            )));
			?>
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