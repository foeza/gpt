<?php
class RecycleBin extends AppModel {
	public $name = 'RecycleBin';
	public function createLog($data = array()){
		if($data){
			$data = array(
				'reference_id'		=> isset($data['reference_id']) ? $data['reference_id'] : NULL, 
				'reference_model'	=> isset($data['reference_model']) ? $data['reference_model'] : NULL, 
				'reference_field'	=> isset($data['reference_field']) ? $data['reference_field'] : NULL, 
				'save_path'			=> isset($data['save_path']) ? $data['save_path'] : NULL, 
				'file_path'			=> isset($data['file_path']) ? $data['file_path'] : NULL, 
				'status'			=> isset($data['status']) ? $data['status'] : NULL, 
				'created'			=> isset($data['created']) ? $data['created'] : NULL, 
				'created_by'		=> isset($data['created_by']) ? $data['created_by'] : NULL
			);

			$status		= TRUE;
			$dataSource	= $this->getDataSource();

		//	begin transaction
			$dataSource->begin();

		//	if has reference, update the related field
			$referenceID	= $data['reference_id'];
			$referenceModel	= $data['reference_model'];
			$referenceField	= $data['reference_field'];

			if($referenceID && $referenceModel && $referenceField){
				$referenceModel		= Inflector::camelize($referenceModel);
				$this->Reference	= ClassRegistry::init($referenceModel);

				if($data['status'] == 3){
				//	restore
					$value = $data['file_path'];
				}
				else{
				//	delete
					$value = NULL;
				}

				$updateData = array($referenceModel => array($referenceField => $value));

				$this->Reference->id = $referenceID;
				$status = $this->Reference->save($updateData);
			}

			if($status && $this->save($data)){
				$dataSource->commit();
				return TRUE;
			}
			else{
				$dataSource->rollback();
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
}
?>