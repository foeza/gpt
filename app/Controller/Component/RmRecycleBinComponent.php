<?php 
class RmRecycleBinComponent extends Component {
	public $controller;
	public $request;
	public $response;
	public $model;
	public $modelName	= 'RecycleBin';
	public $components	= array('RmCommon', 'RmImage');
	public $options		= array(
		'recycle_bin_path'		=> NULL, 
		'default_status'		=> 2, // 1 : permanent delete, 2 : move to recycle bin, 3 : restored from recycle bin (if restore)
		'new_dir_permission'	=> '0755'
	);

	protected $_methods = array();

	public function initialize(Controller $controller){
		$this->controller	= $controller;
		$this->request		= $controller->request;
		$this->response		= $controller->response;
		$this->_methods		= $controller->methods;
		$this->model		= ClassRegistry::init($this->modelName);

		$this->RmCommon->_setConfigVariable();
		if(Configure::read('__Site.recycle_bin_path')){
			$this->options['recycle_bin_path'] = Configure::read('__Site.recycle_bin_path');
		}

	//	prepare target dir
		if($this->options['recycle_bin_path']){
			$this->_createDir($this->options['recycle_bin_path'], $this->options['new_dir_permission']);
		}
	}

//	====================================================================================
//	delete / recycle file based on given path and options
//	@param	String	$filePath		source file path
//	@param	String	$savePath		main folder of source file path
//	@param	Array	$options		array of available options
//	@param	Bool	$permanent		permanent delete or temporary
//	@return Array					status and message

	public function delete($filePath = NULL, $savePath = NULL, $options = array(), $permanent = NULL){
		$referenceID		= isset($options['reference_id']) ? $options['reference_id'] : NULL;
		$referenceModel		= isset($options['reference_model']) ? Inflector::camelize($options['reference_model']) : NULL;
		$referenceField		= isset($options['reference_field']) ? $options['reference_field'] : NULL;
		$recycleBinPath		= isset($options['recycle_bin_path']) ? $options['recycle_bin_path'] : $this->options['recycle_bin_path'];
		$newDirPermission	= isset($options['new_dir_permission']) ? $options['new_dir_permission'] : $this->options['new_dir_permission'];

		if($filePath == NULL || $savePath == NULL){
			$results = array('status' => 0, 'message' => __('Please define File Path / Save Path.'));
		}
		else{
			$uploadPath		= Configure::read('__Site.upload_path');
			$fullFilePath	= $uploadPath.sprintf('/%s/%s', $savePath, $filePath);
			$fullFilePath	= $this->_formatPath($fullFilePath, DS);
			$fileName		= basename($filePath);
			$fileExtension	= pathinfo($fileName, PATHINFO_EXTENSION);

			if(file_exists($fullFilePath) === FALSE){
				$results = array('status' => 0, 'message' => __('File not found or already deleted.'));
			}
			else{
				if($permanent != NULL && in_array($permanent, array(TRUE, FALSE))){
					$permanent = $permanent;
				}
				else{
					$permanent = $this->options['default_status'] == 1 ? TRUE : FALSE;
				}

				$targetPath	= $recycleBinPath.sprintf('/%s/%s', $savePath, $filePath);
				$targetPath	= $this->_formatPath($targetPath, DS);
				$continue	= TRUE;

				if($permanent === FALSE){
				//	create new directory
					$dirCreated = $this->_createDir($targetPath, $newDirPermission);
					if($dirCreated){
						$fileMoved = $this->_moveFile($fullFilePath, $targetPath);
						if($fileMoved){
							$continue = TRUE;
						}
						else{
							$results	= array('status' => 0, 'message' => __('Unable to move File.'));
							$continue	= FALSE;
						}
					}
					else{
						$results	= array('status' => 0, 'message' => __('Unable to create new Directory.'));
						$continue	= FALSE;
					}
				}

				if($continue === TRUE){
				//	permanen atau tidak, source file tetap di delete
				//	save movement log
					$userID		= Configure::read('User.id');
					$status		= $permanent === TRUE ? 1 : 2;
					$created	= $this->RmCommon->currentDate();
					$logData	= array(
						'reference_id'		=> $referenceID, 
						'reference_model'	=> $referenceModel, 
						'reference_field'	=> $referenceField, 
						'save_path'			=> $savePath, 
						'file_path'			=> $filePath, 
						'status'			=> $status, 
						'created'			=> $created, 
						'created_by'		=> $userID
					);

				//	save movement log
					$createLog = $this->model->createLog($logData);
					if($createLog){
					//	delete source file
						$this->_deleteFile($fullFilePath);

					//	remove related thumbnail file
						$thumbnailDirs = $this->RmImage->_rulesDimensionImage($savePath, FALSE, $fileExtension);
						if($thumbnailDirs){
							$thumbnailPath = Configure::read('__Site.thumbnail_view_path');
							foreach($thumbnailDirs as $dirName => $thumbnailDimension){
								$thumbnailFullPath = $thumbnailPath.sprintf('/%s/%s/%s', $savePath, $dirName, $filePath);
								$thumbnailFullPath = $this->_formatPath($thumbnailFullPath, DS);

								if(file_exists($thumbnailFullPath) && is_file($thumbnailFullPath)){
									unlink($thumbnailFullPath);
								}
							}
						}

						$results = array('status' => 1, 'message' => __('File has been deleted.'));
					}
					else{
						if($permanent === FALSE){
						//	delete target file (yang barusan masuk recycle bin hapus lagi, kan ga jadi)
							$this->_deleteFile($targetPath);

							$results = array('status' => 0, 'message' => __('Delete process failed.'));
						}
					}	
				}
			}
		}

		return $results;
	}

//	====================================================================================

//	====================================================================================
//	restore file based on given path and options
//	@param	String	$filePath		source file path
//	@param	String	$savePath		main folder of source file path
//	@param	Array	$options		array of available options
//	@return Array					status and message

	public function restore($filePath = NULL, $savePath = NULL, $options = array()){
		$referenceID		= isset($options['reference_id']) ? $options['reference_id'] : NULL;
		$referenceModel		= isset($options['reference_model']) ? Inflector::camelize($options['reference_model']) : NULL;
		$referenceField		= isset($options['reference_field']) ? $options['reference_field'] : NULL;
		$recycleBinPath		= isset($options['recycle_bin_path']) ? $options['recycle_bin_path'] : $this->options['recycle_bin_path'];
		$newDirPermission	= isset($options['new_dir_permission']) ? $options['new_dir_permission'] : $this->options['new_dir_permission'];

		if($filePath == NULL || $savePath == NULL){
			$results = array('status' => 0, 'message' => __('Please define File Path / Save Path.'));
		}
		else{
			$fullFilePath	= $recycleBinPath.sprintf('/%s/%s', $savePath, $filePath);
			$fullFilePath	= $this->_formatPath($fullFilePath, DS);
			$fileName		= basename($filePath);
			$fileExtension	= pathinfo($fileName, PATHINFO_EXTENSION);

			if(file_exists($fullFilePath) === FALSE){
				$results = array('status' => 0, 'message' => __('File not found, it may already restored or permanently deleted.'));
			}
			else{
				$uploadPath	= Configure::read('__Site.upload_path');
				$targetPath	= $uploadPath.sprintf('/%s/%s', $savePath, $filePath);
				$targetPath	= $this->_formatPath($targetPath, DS);

			//	create new directory
				$dirCreated = $this->_createDir($targetPath, $newDirPermission);
				if($dirCreated){
					$fileMoved = $this->_moveFile($fullFilePath, $targetPath);
					if($fileMoved){
					//	save movement log
						$userID		= Configure::read('User.id');
						$status		= 3;
						$created	= $this->RmCommon->currentDate();
						$logData	= array(
							'reference_id'		=> $referenceID, 
							'reference_model'	=> $referenceModel, 
							'reference_field'	=> $referenceField, 
							'save_path'			=> $savePath, 
							'file_path'			=> $filePath, 
							'status'			=> $status, 
							'created'			=> $created, 
							'created_by'		=> $userID
						);

					//	save movement log
						$createLog = $this->model->createLog($logData);
						if($createLog){
						//	delete source file
							$this->_deleteFile($fullFilePath);

						//	restore related thumbnail file
							$thumbnail	= array('thumbnail' => $filePath);
							$generate	= $this->RmImage->_generateThumbnail($thumbnail, 'thumbnail', $savePath);
							$results	= array('status' => 1, 'message' => __('File has been restored.'));
						}
						else{
						//	delete target file (yang barusan keluar recycle bin hapus lagi, kan ga jadi)
							$this->_deleteFile($targetPath);

							$results = array('status' => 0, 'message' => __('Restore process failed.'));
						}
					}
					else{
						$results = array('status' => 0, 'message' => __('Unable to move File.'));
					}
				}
				else{
					$results = array('status' => 0, 'message' => __('Unable to create new Directory.'));
				}
			}
		}

		return $results;
	}

//	====================================================================================

//	====================================================================================
//	create new directory
//	@param	String	$dirPath		new directory path
//	@param	String	$permission		chmod permission code
//	@return Bool

	public function _createDir($dirPath = NULL, $permission = NULL){
		if($dirPath){
			$dirPath	= dirname($dirPath);
			$permission = $permission ? $permission : $this->options['new_dir_permission'];

			if(file_exists($dirPath) === FALSE || is_dir($dirPath) === FALSE){
				return mkdir($dirPath, $permission, TRUE);
			}
			else{
				return TRUE;
			}
		}

		return FALSE;
	}

//	====================================================================================

//	====================================================================================
//	move file to new destination based on given path
//	@param	String	$filePath		source file path
//	@param	String	$targetPath		destination file path
//	@return Bool

	public function _moveFile($filePath = NULL, $targetPath = NULL){
		if($filePath && $targetPath){
			if(file_exists($filePath) === TRUE && is_file($filePath) === TRUE){
				return copy($filePath, $targetPath);
			}
		}

		return FALSE;
	}
//	====================================================================================

//	====================================================================================
//	delete file based on given path (* not accepting dir)
//	@param	String	$filePath		source file to be deleted
//	@return Bool

	public function _deleteFile($filePath = NULL){
		if($filePath){
		//	buat jaga2, dicek apakah dia file atau direktori
			if(file_exists($filePath) === TRUE && is_file($filePath) === TRUE && is_dir($filePath) === FALSE){
				return unlink($filePath);
			}
		}

		return FALSE;
	}

//	====================================================================================

//	====================================================================================
//	replace unformatted path to formatted one
//	@param	String	$path			path
//	@param	Char	$replacement	replacement char
//	@return String					formatted path

	public function _formatPath($path = NULL, $replacement = '/'){
		$pattern = '"(\\\\/|\\\\+/|/\\\\|/+\\\\+)|(/+|\\\\+)"';
		if($path){
			$path = preg_replace($pattern, $replacement, $path, -1);
		}

		return $path;
	}

//	====================================================================================
}
?>