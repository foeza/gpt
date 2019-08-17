<?php 
		$value = !empty($value)?$value:false;
		$crm_project_id = !empty($crm_project_id)?$crm_project_id:false;
		$session_id = !empty($session_id)?$session_id:false;
		$activity_id = !empty($activity_id)?$activity_id:false;
		$crmProject = !empty($value)?$value:false;
		$attribute_id = !empty($attribute_id)?$attribute_id:false;
		$attribute_set_id = !empty($attribute_set_id)?$attribute_set_id:false;
		$classCol = !empty($classCol)?$classCol:false;

		$attribute_set_id = $this->Rumahku->filterEmptyField($this->request->data, 'CrmProjectActivity', 'attribute_set_id', $attribute_set_id);
		$attribute_id = $this->Rumahku->filterEmptyField($this->request->data, 'CrmProjectActivity', 'attribute_id', $attribute_id);

		if( !empty($full_input) ) {
			$addClass = !empty($addClass)?$addClass:sprintf('wrapper-attribute%s', $activity_id);
			$addAtt = 'col-sm-12';
		} else {
			$addClass = false;
			$addAtt = 'col-md-4 col-sm-6 mt15';
		}
?>
<div id="<?php echo $addClass; ?>">
	<div class="<?php echo $classCol; ?>">
		<?php 
				$modelName = !empty($modelName)?$modelName:'AttributeSetOption';
				$wrapperAttribute = !empty($wrapperAttribute)?$wrapperAttribute:sprintf('wrapper-attribute%s', $activity_id);
				$dataParams = !empty($dataParams)?$dataParams:false;
				
				if( !empty($attributeSetValue) ) {
					$dataOptions = $this->Rumahku->filterEmptyField($attributeSetValue, $modelName);

					if( !empty($full_input) ) {
						echo $this->Rumahku->clearfix();
					}	

					if( !empty($dataOptions) ) {
						foreach ($dataOptions as $key => $attribute) {
							echo $this->Crm->_callAttributeOptions($attribute, false, $addAtt, array(
								'wrapperAttribute' => $wrapperAttribute,
								'dataParams' => $dataParams,
								'crm_project_id' => $crm_project_id,
								'session_id' => $session_id,
								'activity_id' => $activity_id,
								'crmProject' => $crmProject,
							));
						}
					}
				}

				if( !empty($attribute_id) ) {
					echo $this->Form->hidden('CrmProjectActivity.attribute_id', array(
						'value' => $attribute_id,
					));
				}
				if( !empty($attribute_option_id) ) {
					echo $this->Form->hidden('CrmProjectActivityAttributeOption.attribute_option_id.'.$attribute_id, array(
						'value' => $attribute_option_id,
					));
				}
		?>
	</div>
</div>