<?php 
class RmImageComponent extends Component {
	public $components = array(
		'RmCommon', 'RmRecycleBin',
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _setValue ( $options ) {
		$allow_only_favicon = array('ico');

		if(!empty($options['allowed_all_ext'])){
			$allowed_ext = Configure::read('__Site.allowed_all_ext');
		}else{
			$allowed_ext = Configure::read('__Site.allowed_ext');
		}

		$allow_only_mimefavicon = array(
			'image/vnd.microsoft.icon', 
			'image/ico', 'image/icon', 
			'text/ico', 'application/ico',
			'image/x-icon'
		);
		$default_mime = array(
			'image/gif', 'image/jpeg', 
			'image/png', 'image/pjpeg', 'image/x-png',
			'application/pdf', 'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		);
		$allowed_mime = $default_mime;
		$baseuploadpath = Configure::read('__Site.upload_path');

		if(!empty($options) && !empty($options['favicon'])) {
			$allowed_ext = $allow_only_favicon;
			$allowed_mime = $allow_only_mimefavicon;
		}

    	return array(
			'max_size' => Configure::read('__Site.max_image_size'),
			'max_width' => Configure::read('__Site.max_image_width'),
			'max_height' => Configure::read('__Site.max_image_height'),
			'allowed_ext' => $allowed_ext,
			'allowed_mime' => $allowed_mime,
			'baseuploadpath' => $baseuploadpath,
		);
	}

	private function _setNameConvertJpg( $prefix, $convertExtension, $upload_sub_path, $uploadPhotoPath ) {
		$filename = sprintf('%s.%s', $prefix, $convertExtension);
		$uploadFilename = $upload_sub_path.$filename;
		return array(
			'filename' => $filename,
			'filenameConverter' => $uploadFilename,
			'pathNameConverter' => str_replace('/', DS, $uploadPhotoPath.$uploadFilename),
		);
	}

	private function _setDimensionList ( $save_path, $extension = false, $options ) {
		$dimensionList = $this->_rulesDimensionImage($save_path, false, $extension, $options);

		return $dimensionList;
	}

	function _createFolderThumb ( $thumbnailPath, $filename ) {
        $srcName = explode('/', $filename);

        if( count($srcName) == 5 ) {
	        $year = !empty($srcName[1])?$srcName[1]:date('Y');
			$month = !empty($srcName[2])?$srcName[2]:date('m');
			$char = isset($srcName[3])?DS.$srcName[3]:false;
			$name = !empty($srcName[4])?$srcName[4]:false;
        } else {
        	$year = !empty($srcName[1])?$srcName[1]:date('Y');
			$month = !empty($srcName[2])?$srcName[2]:date('m');
			$name = !empty($srcName[3])?$srcName[3]:false;
			$char = false;
        }
		$thumbnailPath = $thumbnailPath.DS.$year.DS.$month.$char;

		if(!file_exists($thumbnailPath)) {
			mkdir($thumbnailPath, 0755, true);
		}

		return $thumbnailPath.DS.$name;
	}

	function _allowGeneratePhoto () {
		return array(
			'AllowWatermark' => array(
				Configure::read('__Site.profile_photo_folder'), 
				Configure::read('__Site.advice_photo_folder'),
				Configure::read('__Site.ebrosurs_photo'), 
				Configure::read('__Site.ebrosurs_template'), 
			),
			'AllowFullsize' => array(
				Configure::read('__Site.profile_photo_folder'), 
				Configure::read('__Site.logo_photo_folder'), 
				Configure::read('__Site.advice_photo_folder'), 
				Configure::read('__Site.badge_photo_folder'), 
				Configure::read('__Site.general_folder'), 
				Configure::read('__Site.ebrosurs_photo'),
				Configure::read('__Site.file_folder'),
				Configure::read('__Site.document_folder'),
			),
		);
	}

	function _generateThumbnail ( $filename, $field, $save_path, $options = false, $extension = false ) {
		if( !empty($filename[$field])) {

			$photoPath = Configure::read('__Site.upload_path');
			$thumbnailPath = Configure::read('__Site.thumbnail_view_path');
			$allowAction = $this->_allowGeneratePhoto();
			$allowWatermark = $allowAction['AllowWatermark'];
			$allowFullsize = $allowAction['AllowFullsize'];

			$company_name = $this->RmCommon->filterEmptyField($this->controller->data_company, 'UserCompany', 'name');
			$logo_company = $this->RmCommon->filterEmptyField($this->controller->data_company, 'UserCompany', 'logo');
			$watermark_type = $this->RmCommon->filterEmptyField($this->controller->data_company, 'UserCompanyConfig', 'watermark_type');
			$watermark_solid = $this->RmCommon->filterEmptyField($this->controller->data_company, 'UserCompanyConfig', 'watermark_solid');

			$save_path = str_replace(array( '/', DS ), array( '', '' ), $save_path);
			$dimensionList = $this->_setDimensionList( $save_path, $extension, $options );
			$filename[$field] = $this->replaceSlash($filename[$field], 'reverse');

			$filePhotoPath = $this->replaceSlash($photoPath.DS.$save_path.$filename[$field]);

			if( file_exists($filePhotoPath) ) {
				$groupID		= Configure::read('User.data.group_id');
				$fullName		= Configure::read('User.data.full_name');
				$isIndependent	= Common::validateRole('independent_agent', $groupID);

				if($isIndependent){
					$watermark		= true;
					$watermark_type	= 'text';
				}
				else{
					if(in_array($save_path, array('ebrosur', 'ebrosur_template')) || in_array($save_path, $allowWatermark) ) {
						$watermark = false;
					} else {
						$watermark = true;
					}
				}

				if( in_array($save_path, $allowFullsize) ) {
					$dimensionList['fullsize'] = 'fullsize';
				}

				$optionsPhoto = array(
					'watermark' => $watermark,
					'watermark_text' => false,
					'watermark_logo' => false,
				);

				if( !empty($dimensionList) ) {
					foreach ($dimensionList as $key => $dimension) {
						$thumbnail_size_path = str_replace('/', DS, $thumbnailPath.DS.$save_path.DS.$key);
						$thumbnail_filename = $this->_createFolderThumb( $thumbnail_size_path, $filename[$field] );
						
						if( $dimension != 'fullsize' ) {
							list($thumbnail_width, $thumbnail_height) = explode('x', $dimension);

							if( $key == 'company' ){
								if($watermark_type == 'text'){
									$watermark_text = false;

									if(($isIndependent && $fullName)){
										$watermark_text = $fullName;
									}
									else if(empty($isIndependent) && $company_name){
										$watermark_text = $company_name;
									}

									$optionsPhoto['watermark_text'] = $watermark_text ?: false;
								}else if($watermark_type == 'logo' && !empty($logo_company)){
									$optionsPhoto['watermark_logo'] = $logo_company;
									$optionsPhoto['watermark_solid'] = $watermark_solid;
								}
							}

							$this->_createThumbnail($filePhotoPath, $thumbnail_filename, $thumbnail_width, $thumbnail_height, $optionsPhoto);
						} else if( !file_exists($thumbnail_filename) ) {
							copy($filePhotoPath, $thumbnail_filename);
						}
					}
				}
			}
		} else {
			return false;
		}
	}

	function _updatePhotoThumbnail ( $data, $field, $save_path, $modelName, $options = false ) {
		if( !empty($data) && empty($data['is_generate_photo']) && !empty($data['id']) ) {
			if( is_array($field) && !empty($field) ) {
				foreach ($field as $key => $value) {
					$this->_generateThumbnail( $data, $value, $save_path, $options );
				}
			} else {
				$this->_generateThumbnail( $data, $field, $save_path, $options );
			}

			$this->unGeneratePhoto( $data['id'], $modelName );
		}
	}

	function _getDataArr ( $data, $indexName ) {
		if( !empty($indexName) && !empty($data[$indexName]) ) {
			$dataArr = $data[$indexName];
		} else {
			$dataArr = $data;
		}

		return $dataArr;
	}

	function _callGenerateThumbnail ( $data, $modelName = false, $field, $save_path, $is_loop = false, $options = false ) {
		if( is_array($modelName) ) {
			$tmpModelName = $modelName;
			$indexName = !empty($tmpModelName[0])?$tmpModelName[0]:false;
			$modelName = !empty($tmpModelName[1])?$tmpModelName[1]:false;
		} else {
			$indexName = $modelName;
		}
		$dataArr = $this->_getDataArr( $data, $indexName );

		if( !empty($is_loop) && !empty($data) ) {
			foreach ($data as $key => $media) {
				$dataArr = $this->_getDataArr( $media, $indexName );

				if( !empty($dataArr) && empty($dataArr['is_generate_photo']) ) {
					$this->_updatePhotoThumbnail( $dataArr, $field, $save_path, $modelName, $options );
				}
			}
		} else if( !empty($dataArr) && empty($dataArr['is_generate_photo']) ) {
			$this->_updatePhotoThumbnail( $dataArr, $field, $save_path, $modelName, $options );
		}
	}

    function upload($uploadedInfo, $uploadTo, $prefix = null, $options = array()){
		$this->options = $this->_setValue( $options );
		$uploadTo = sprintf('/%s/', $uploadTo);

		if( !empty($options) ) {
			foreach($options as $key=>$value) {
				$this->options[$key] = $value;
			}
		}
		$result = $this->validateFile($uploadedInfo);

		if( empty($result['error']) ) {
			$upload_sub_path = '';
			$file_info = pathinfo($uploadedInfo['name']);
			$basename = $this->RmCommon->filterEmptyField($file_info, 'basename');

			$upload_dir = str_replace('/', DS, $uploadTo);
			$save_path = str_replace('/', '', $uploadTo);
			$upload_path = $this->options['baseuploadpath'].$upload_dir;
			$photo_extension = strtolower($file_info['extension']);
			$filename = sprintf('%s.%s', $prefix, $photo_extension);
			$thumbnailPath = Configure::read('__Site.thumbnail_view_path').$uploadTo;

			// if( !in_array($photo_extension, array( 'pdf', 'xls', 'xlsx' )) ) {
			// 	$convertExtension = 'jpg';
			// } else {
				$convertExtension = $photo_extension;
			// }

			$photo_subpath = $this->generateSubPathFolder($filename);
			$upload_sub_path = $this->makeDir( $upload_path, $photo_subpath );

			if( empty($upload_sub_path) ) {
				$result = false;
			} else {
				$uploadPhotoPath = str_replace('/', DS, $upload_path);
				$uploadFilename =  str_replace('/', DS, $upload_sub_path.$filename);
				$uploadSource = $uploadPhotoPath.$uploadFilename;

				$upload_result = false;

				if( !empty($uploadedInfo["tmp_name"]) ) {
					$tmp_name = $uploadedInfo["tmp_name"];
				} else if( !empty($uploadedInfo["uri"]) ) {
					$tmp_name = $uploadedInfo["uri"];
				}

				if( !empty($uploadedInfo['byte']) ){
					$img = base64_decode( $uploadedInfo['byte'] );
	 
					if( $fp = fopen( $uploadSource , 'wb' ) ){
						fwrite( $fp, $img );
						 
						fclose( $fp );

						$upload_result = true;
					}
				}else if( !empty($tmp_name) && move_uploaded_file( $tmp_name, $uploadSource ) ){
					$upload_result = true;
				}

				if( $upload_result ){
					// if($photo_extension != "jpg" && !in_array($photo_extension, array( 'pdf', 'xls', 'xlsx' )) ) {
					// 	$resultConverter = $this->_setNameConvertJpg( $prefix, $convertExtension, $upload_sub_path, $uploadPhotoPath );

					// 	if( !empty($resultConverter) ) {
					// 		$uploadFilename = $resultConverter['filenameConverter'];
					// 		$pathNameConverter = $resultConverter['pathNameConverter'];
					// 	}

					// 	$resultConvertToJPG = $this->_convertToJPG($uploadSource, $pathNameConverter);

					// 	if( !empty($resultConvertToJPG) ) {
					// 		@unlink($uploadSource);
					// 		$uploadSource = $resultConvertToJPG;
					// 	}
					// }

					$name_for_db = str_replace(DS, '/', '/'.$uploadFilename);

					$sizes = getimagesize($uploadSource);
					$width = $sizes[0];
					$height = $sizes[1];

					if( !in_array($photo_extension, array( 'pdf', 'xls', 'xlsx' )) ) {
						$dimensionThumb = $this->_rulesDimensionImage($save_path, 'thumb', $photo_extension, $options);
						$imagePath = Configure::read('__Site.cache_view_path').str_replace(DS, '/', $upload_dir.$dimensionThumb.DS.$uploadFilename);

						$scale = $this->getScale( $width, $height, $this->options['max_width'], $this->options['max_height'] );
						$uploaded = $this->_resizeImage($uploadSource, $width, $height, $scale, $options, $photo_extension);
					} else {
						$scale = 1;

						if( $photo_extension == 'pdf' ) {
							$imagePath = '/img/pdf.png';
						} else {
							$imagePath = '/img/excel.png';
						}
					}

					$allowAction = $this->_allowGeneratePhoto();
					$allowWatermark = $allowAction['AllowWatermark'];
					$allowFullsize = $allowAction['AllowFullsize'];
					$data = array(
						'src' => DS.$uploadFilename,
						'is_generate_photo' => false,
					);

					$this->_generateThumbnail( $data, 'src', $save_path, $options, $photo_extension );

					$result = array(
						'error' => 0,
						'baseName' => $basename, 
						'imagePath' => $imagePath, 
						'imageName' => $name_for_db, 
						'imageWidth' => ceil($width * $scale), 
						'imageHeight' => ceil($height * $scale),
					);
				} else {
					$result = false;
				}
			}
		}
        return $result;
    }

    function upload_by_path ($photoPath, $uploadTo, $prefix, $options = array()){
		if( file_exists($photoPath) ){
			$this->options = $this->_setValue( $options );
			$uploadTo = sprintf('/%s/', $uploadTo);

			$upload_sub_path = '';
			$file_info = pathinfo($photoPath);
			$basename = $this->RmCommon->filterEmptyField($file_info, 'basename');

			$upload_dir = str_replace('/', DS, $uploadTo);
			$save_path = str_replace('/', '', $uploadTo);
			$upload_path = $this->options['baseuploadpath'].$upload_dir;
			$photo_extension = !empty($file_info['extension'])?strtolower($file_info['extension']):'jpg';
			$filename = sprintf('%s.%s', $prefix, $photo_extension);

			$photo_subpath = $this->generateSubPathFolder($filename);
			$upload_sub_path = $this->makeDir( $upload_path, $photo_subpath );

			if( empty($upload_sub_path) ) {
				$result = false;
			} else {
				$uploadPhotoPath = str_replace('/', DS, $upload_path);
				$uploadFilename =  str_replace('/', DS, $upload_sub_path.$filename);
				$uploadSource = $uploadPhotoPath.$uploadFilename;
				$name_for_db = str_replace(DS, '/', '/'.$uploadFilename);

				if( copy($photoPath, $uploadSource) ){
					$sizes = getimagesize($uploadSource);
					$width = $sizes[0];
					$height = $sizes[1];

					if( !in_array($photo_extension, array( 'pdf', 'xls', 'xlsx' )) ) {
						$dimensionThumb = $this->_rulesDimensionImage($save_path, 'thumb', $photo_extension, $options);
						$imagePath = Configure::read('__Site.cache_view_path').str_replace(DS, '/', $upload_dir.$dimensionThumb.DS.$uploadFilename);

						$scale = $this->getScale( $width, $height, $this->options['max_width'], $this->options['max_height'] );
						$uploaded = $this->_resizeImage($uploadSource, $width, $height, $scale, $options, $photo_extension);
					} else {
						$scale = 1;

						if( $photo_extension == 'pdf' ) {
							$imagePath = '/img/pdf.png';
						} else {
							$imagePath = '/img/excel.png';
						}
					}

					$allowAction = $this->_allowGeneratePhoto();
					$allowWatermark = $allowAction['AllowWatermark'];
					$allowFullsize = $allowAction['AllowFullsize'];
					$data = array(
						'src' => DS.$uploadFilename,
						'is_generate_photo' => false,
					);

					$this->_generateThumbnail( $data, 'src', $save_path, $options, $photo_extension );

					$result = array(
						'error' => 0,
						'baseName' => $basename, 
						'imagePath' => $imagePath, 
						'imageName' => $name_for_db, 
						'imageWidth' => ceil($width * $scale), 
						'imageHeight' => ceil($height * $scale),
					);
				} else {
					$result = false;
				}
			}
		} else {
			$result = false;
		}

        return $result;
    }

	/**
	* Proses Pembuatan Thumbnail
	*
	* @param string $source - Direktori folder yang akan diupload
	* @param array $pathToThumbs - Direktori upload file Thumbnail
	* @param string $filename - Nama file
	* @param string $dimensionName - ID Dimensi
	* @param string $dimensionSize - ukuran Dimensi
	* @param string $thumbWidth - Lebar Dimensi
	* @param string $thumbHeight - Panjang Dimensi
	* @param string $dir - open direktori folder
	* @param array $info - Informasi File
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $upload_sub_path - Direktori folder year[yyyy]/month[mm]
	* @param string $sourcePath - Direktori folder yang akan diupload sudah berikut $upload_sub_path
	* @param Object $this->thumb - Open Library Thumb
	* @param Object $imgCrop - Proses thumbnail sesuai dengan ukuran dimensi
	*/
    private function _createThumbnail($source, $destination, $width, $height, $options = false ) {
    	$watermark = isset($options['watermark'])?$options['watermark']:true;
    	$min_watermark_width = isset($options['min_watermark_width'])?$options['min_watermark_width']:300;
    	$watermark_text = $this->RmCommon->filterEmptyField($options, 'watermark_text');
    	$watermark_logo = $this->RmCommon->filterEmptyField($options, 'watermark_logo');
    	$watermark_solid = $this->RmCommon->filterEmptyField($options, 'watermark_solid');

    	$info = pathinfo($source);
        $info['extension'] = strtolower($info['extension']);

        if (class_exists('imagick')) {
        	$watermark_image = Configure::read('__Site.upload_path').DS."watermark".DS."watermark.png";
        	
        	if(!empty($watermark_logo)){
        		$watermark_logo = str_replace('/', DS, Configure::read('__Site.thumbnail_view_path').DS.Configure::read('__Site.logo_photo_folder').DS.'xxsm'.$watermark_logo);
        		
        		if(file_exists($watermark_logo)){
        			$watermark_image = $watermark_logo;
        		}else{
        			$watermark_image = '';
        		}
        	}

			$image = new Imagick($source);

			if( in_array($info['extension'], array( 'png', 'gif' )) ){
				$image->setImageBackgroundColor(new ImagickPixel('transparent')); 
				$image->thumbnailImage ($width, $height, true, false);
			} else {
				$image->thumbnailImage ($width, $height, true, true);
			}
			
			$image_width = $image->getImageWidth();
			$image_height = $image->getImageHeight();

			if( $watermark && $image_width > $min_watermark_width) {
				$watermark = new Imagick();

				if( !empty($watermark_text) ) {
					$watermark_text = strtoupper($watermark_text);
					$mask = new Imagick();
					$draw = new ImagickDraw();

					// Define dimensions
					$width = $image->getImageWidth();
					$height = $image->getImageHeight();

					// Create some palettes
					$watermark->newImage($width, $height, new ImagickPixel('grey60'));
					$mask->newImage($width, $height, new ImagickPixel('black'));

					// Set font properties
					$draw->setFont('fonts/montserrat-bold-webfont.ttf');
					$draw->setFontSize(50);
					$draw->setFillColor('grey40');

					// Position text
					$draw->setGravity(Imagick::GRAVITY_CENTER);

					// Draw text on the watermark palette
					$watermark->annotateImage($draw, 10, 12, 0, $watermark_text);

					// Draw text on the mask palette
					$draw->setFillColor('white');
					$mask->annotateImage($draw, 11, 13, 0, $watermark_text);
					$mask->annotateImage($draw, 10, 12, 0, $watermark_text);
					$draw->setFillColor('black');
					$mask->annotateImage($draw, 9, 11, 0, $watermark_text);

					// This is apparently needed for the mask to work
					$mask->setImageMatte(false);

					// Apply mask to watermark
					$watermark->compositeImage($mask, Imagick::COMPOSITE_COPYOPACITY, 0, 0);

					// Overlay watermark on image
					$image->compositeImage($watermark, Imagick::COMPOSITE_DISSOLVE, 0, 0);
				} else if( !empty($watermark_image) ) {
					$watermark->readImage($watermark_image);

					if(!empty($watermark_logo)){

						if (!$watermark_solid) {
							// $watermark->setImageOpacity(0.6);
							$watermark->evaluateImage(Imagick::EVALUATE_MULTIPLY, 0.5, Imagick::CHANNEL_ALPHA);
						}
						
					}
			 
					$watermark_width  = $watermark->getImageWidth();
					$watermark_height = $watermark->getImageHeight();

					if ($image_height < $watermark_height || $image_width < $watermark_width) {
						$watermark->scaleImage($image_width/3, 0);

						$watermark_width = $watermark->getImageWidth();
						$watermark_height = $watermark->getImageHeight();
					}
					$x = ($image_width - $watermark_width) / 2;
					$y = ($image_height - $watermark_height) / 2;

					$image->compositeImage($watermark, imagick::COMPOSITE_OVER, $x, $y);
				}
			}
			
			$image->writeImage($destination);			
		} else {
			$this->_oldCreateThumbs( $source, $destination, $width, $height );
		}

		return $source;
	}

	function _oldCreateThumbs( $source, $destination, $width, $height )  {
		App::import('Vendor', 'thumb', array('file' => 'thumb'.DS.'ThumbLib.inc.php'));
        // parse path for the extension
        $info = pathinfo($source);
        $info['extension'] = strtolower($info['extension']);

		copy($source, $destination);

		if( !in_array($info['extension'], array( 'ico', 'pdf', 'xls' )) ){
			$this->thumb = PhpThumbFactory::create($destination);
			$imgCrop = $this->thumb->adaptiveResize($width, $height);
		}

		if($info['extension'] == "png"){
            @imagepng($imgCrop->workingImageCopy, $destination, 9);
        } elseif($info['extension'] == "jpg" || $info['extension'] == "jpeg") {
            @imagejpeg($imgCrop->workingImageCopy, $destination, 90);
        } elseif($info['extension'] == "gif") {
            @imagegif($imgCrop->workingImageCopy, $destination);
        }
    }

	/**
	* Scaling Foto
	*
	* @param number $w - Lebar Foto
	* @param number $h - Panjang Foto
	* @param number max_w - Maksimal Lebar Foto
	* @param number max_h - Maksimal Panjang Foto
	* @param number wscale - Lebar Skala
	* @param number hscale - Panjang Skala
	* @param number scale - Skala Foto
	* @return number - Skala Foto
	*/
    function getScale ( $w, $h, $max_w, $max_h ) {
    	if (($w > $max_w) && ($h > $max_h)){
			$wscale = $max_w/$w;
			$hscale = $max_h/$h;
			if($wscale <= $hscale) {
				$scale = $wscale;	
			} else {
				$scale = $hscale;	
			}
		} elseif ($w > $max_w){
			$scale = $max_w/$w;
		} elseif ($h > $max_h){
			$scale = $max_h/$h;
		} else {
			$scale = 1;
		}
		return $scale;
    }

	/**
	* Craete Direktori
	*
	* @param string $upload_path - Direktori folder yang akan diupload
	* @param array $thumbnailPath - Direktori upload file Thumbnail
	* @param string $year - Tahun Upload
	* @param string $month - Bulan Upload
	* @param string $yearDir - Direktori Folder Tahun
	* @param string $monthDir - Direktori Folder Bulan
	* @param string $yearFullsizeDir - Direktori Folder Tahun untuk file ukuran sebenarnya
	* @param string $monthFullsizeDir - Direktori Folder Bulan untuk file ukuran sebenarnya
	* @return string - Direktori Folder File
	*/
    function makeDir( $upload_path = false, $photo_subpath = '', $year = false, $month = false ) {
    	$year = !empty($year)?$year:date('Y');
    	$month = !empty($month)?$month:date('m');

    	if( !empty($upload_path) ) {
	    	$yearDir = $upload_path.$year.DS;
	    	$monthDir = $yearDir.$month.DS;

	    	if( !file_exists($yearDir) ) {
	    		mkdir($yearDir, 0777, true);
	    	}

	    	if( !file_exists($monthDir) ) {
	    		mkdir($monthDir, 0777, true);
	    	}

	    	if($photo_subpath != '') {
		    	$subDir = $monthDir.$photo_subpath.DS;

		    	if( !file_exists($subDir) ) {
		    		mkdir($subDir, 0777, true);
		    	}
		    }
	    	
    	}

		if($photo_subpath != '') {
			return sprintf('%s/%s/%s/', $year, $month, $photo_subpath);
		} else {
			return sprintf('%s/%s/', $year, $month);
		}
    }

	/**
	* Validasi File
	*
	* @param array $file - data file berupa name, type, tmp_name, error, size
	*		array name - Nama file
	*		string type - Tipe file
	*		string tmp_name - Direktori penyimpanan dilocal
	*		boolean error - status upload file, True terjadi error, False tidak terjadi Error
	*		number size - Besar ukuran file
	* @param array $error - hasil validasi foto berupa error, message
	*		boolean error - status upload file, True terjadi error, False tidak terjadi Error
	*		string message - Notifikasi pesan alert
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @return array - hasil validasi foto berupa error, message
	*/
	function validateFile($file) {
		$error = array(
			'error' => 0,
			'message' => ''
		);

		if($file['error'] != 0) {
			$error = array(
				'error' => 1,
				'message' => __('File tidak valid')
			);
		} else {
			$file_info = pathinfo($file["name"]);
			$file_info['extension'] = strtolower($file_info['extension']);
			
			if(!in_array($file_info['extension'], $this->options['allowed_ext'])) {
				$error = array(
					'error' => 1,
					'message' => sprintf(__('Mohon hanya mengunggah file berekstensi %s'), implode(', ', $this->options['allowed_ext']))
				);
			} else if(!in_array($file['type'], $this->options['allowed_mime'])) {
				$error = array(
					'error' => 1,
					'message' => sprintf(__('Mohon hanya mengunggah file berekstensi %s'), implode(', ', $this->options['allowed_ext']))
				);
			} else if(!empty($file['size']) && $file['size'] > $this->options['max_size']) {
				$error = array(
					'error' => 1,
					'message' => sprintf(__('Besar file maksimum adalah %s'), $this->format_size($this->options['max_size'], 'MB'))
				);
			}
		}
		return $error;
	}

	function rotateImage($image, $direction) {
		$direction = strtolower($direction);
		$degrees = $direction == 'cw' ? 270 : ($direction == 'ccw' ? 90 : NULL); 

		if(!$degrees) {
			return $image;
		}

		$width = imagesx($image);
		$height = imagesy($image);
		$side = $width > $height ? $width : $height;
		$imageSquare = imagecreatetruecolor($side, $side);

		imagecopy($imageSquare, $image, 0, 0, 0, 0, $width, $height);
		imagedestroy($image);

		$imageSquare = imagerotate($imageSquare, $degrees, 0, -1);
		$image = imagecreatetruecolor($height, $width);
		$x = $degrees == 90 ? 0 : ($height > $width ? 0 : ($side - $height));
		$y = $degrees == 270 ? 0 : ($height < $width ? 0 : ($side - $width));

		imagecopy($image, $imageSquare, 0, 0, $x, $y, $height, $width);
		imagedestroy($imageSquare);

		return $image;
	}

	function _callGenerateTransparent ( $photo_extension, $image_source, $new_image ) {
		if( in_array($photo_extension, array( 'gif', 'png' )) ){
		    $transparencyIndex = @imagecolortransparent($image_source); 
	        $transparencyColor = array('red' => 255, 'green' => 254, 'blue' => 254); 
	         
	        if ($transparencyIndex >= 0) { 
	            $transparencyColor = @imagecolorsforindex($image_source, $transparencyIndex);    
	        } 
	        
	        $transparencyIndex = @imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']); 
	        @imagefill($new_image, 0, 0, $transparencyIndex); 
	     	@imagecolortransparent($new_image, $transparencyIndex);
     	}

     	return $new_image;
	}

	function autoRotateImage($image) { 
		$orientation = $image->getImageOrientation(); 

		switch($orientation) { 
			case imagick::ORIENTATION_BOTTOMRIGHT: 
				$image->rotateimage("#000", 180); // rotate 180 degrees 
			break; 

			case imagick::ORIENTATION_RIGHTTOP: 
				$image->rotateimage("#000", 90); // rotate 90 degrees CW 
			break; 

			case imagick::ORIENTATION_LEFTBOTTOM: 
				$image->rotateimage("#000", -90); // rotate 90 degrees CCW 
			break; 
		} 

		// Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image! 
		$image->setImageOrientation(imagick::ORIENTATION_TOPLEFT); 
	}

	/**
	* Resize Foto
	*
	* @param string $image - Direktori folder yang akan diupload
	* @param number $width - Ukuran lebar file yang diupload
	* @param number $height - Ukuran panjang file yang diupload
	* @param number scale - Skala Foto
	* @param string $options - Opsi tambahan parameter
	*		boolean favicon - True file berupa favicon, allow extension ico
	*		number max_size - Maksimal ukuran foto yang diupload
	*		number max_width - Maksimal lebar foto yang diupload
	*		number max_height - Maksimal panjang foto yang diupload
	*		array allowed_ext - Extension foto yang diperbolehkan
	*		boolean prefix_as_name - True menggunakan Prefix sebagai Nama file
	*		boolean rar - Allow extension rar
	* @param number $newImageWidth - Ukuran lebar yang akan diresize
	* @param number $newImageHeight - Ukuran panjang yang akan diresize
	* @param string $newImage - Generate image sesuai dengan panjang dan lebar yang telah diresize
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $white - Alokasi warna Putih
	* @param string $source - hasil resize foto
	* @return string - hasil resize foto
	*/
    function _resizeImage( $image, $width, $height, $scale, $options=array(), $photo_extension ) {
    	if (class_exists('imagick')) {
    		$source = $image;
			$image = new Imagick($source);

			if( in_array($photo_extension, array( 'png', 'gif' )) ){
				$image->setImageBackgroundColor(new ImagickPixel('transparent')); 
				$image->thumbnailImage ($width, $height, true, false);
			} else {
				$image->thumbnailImage ($width, $height, true, true);
			}
			
			$image_width = $image->getImageWidth();
			$image_height = $image->getImageHeight();

			$this->autoRotateImage($image);
			$image->writeImage($source);			
		} else {
	        $newImageWidth = ceil($width * $scale);
	        $newImageHeight = ceil($height * $scale);
	        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
			
			$file_info = pathinfo($image);
			
			if((isset($this->options['career']) && $this->options['career'] == 1) || (isset($this->options['excess']) && $this->options['excess'] == 1) || (isset($this->options['transparent']) && $this->options['transparent'] == 1)) {
				$white = imagecolorallocate($newImage, 238, 238, 238);
			} else if( !in_array($photo_extension, array( 'gif', 'png' )) ){
				$white = imagecolorallocate($newImage, 255, 255, 255);
			}

	        $source = "";

	        if( in_array($photo_extension, array('jpg', 'jpeg')) && $width > 300 ){
	        	$exif = @exif_read_data($image);
	        }else{
	        	$exif = false;
	        }

	        if($photo_extension == "png"){
				$source = @imagecreatefrompng($image);
	        } elseif ($photo_extension == "jpg" || $photo_extension == "jpeg"){
	            $source = @imagecreatefromjpeg($image);
	        } elseif ($photo_extension == "gif"){
	            $source = @imagecreatefromgif($image);
	        }

	        $newImage = $this->_callGenerateTransparent($photo_extension, $source, $newImage);

	        if($exif && isset($exif['Orientation'])) {
	        	$orientation = $exif['Orientation'];
	        	if($orientation != 1){
	        		$deg = 0;
	        		switch ($orientation) {
	        			case 3:
							$newImage = imagerotate($source, 180, 0);
		        			break;
	    				case 6:
							$newImage = $this->rotateImage($source, 'cw');
	            			break;
	        			case 8:
							$newImage = $this->rotateImage($source, 'ccw');
	        				break;
	    			}
				}
			}

	        if( $source ) {
			  	if($photo_extension != 'ico'){
		        	@imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
			  	}

			  	if($photo_extension == "png"){
		            @imagepng($newImage,$image, 9);
		        }elseif($photo_extension == "jpg" || $photo_extension == "jpeg"){
	        		@imagejpeg($newImage, $image, 90);
		        }elseif($photo_extension == "gif"){
		            @imagegif($newImage,$image);
		        }

		    	@imagedestroy($image);
		    }

	    	return $image;
	    }
    }
    
	private function _convertToJPG($imagefile, $new_imagefile) {
		$imageInfo = pathinfo($imagefile);
		$extension = $imageInfo['extension'];

        if (class_exists('imagick')) {
			
			$im = new Imagick($imagefile);

			$im->setImageBackgroundColor('white');
			$im = $im->flattenImages();
			$im->setImageFormat('jpg');

			if($im->writeImage($new_imagefile)) {
				return $new_imagefile;
			} else {
				return false;
			}
		}
		else if ($extension == 'png') {
			$image	= imagecreatefrompng($imagefile);
			$bg		= imagecreatetruecolor(imagesx($image), imagesy($image));

			imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
			imagealphablending($bg, true);
			imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
			imagedestroy($image);

			$quality = 50; // 0 = worst / smaller file, 100 = better / bigger file

			imagejpeg($bg, $new_imagefile, $quality);
			imagedestroy($bg);

			return $new_imagefile;
		}
		else {
			return false;
		}
	}

	/**
	* Resize Thumbnail Foto
	*
	* @param string $thumb_image_name - Direktori folder Thumbnail yang akan diupload
	* @param string $image - Direktori folder yang akan diupload
	* @param number $width - Ukuran lebar file yang diupload
	* @param number $height - Ukuran panjang file yang diupload
	* @param number $start_width - Posisi Lebar Crop Foto
	* @param number $start_height - Posisi Panjang Crop Foto
	* @param number scale - Skala Foto
	* @param string $options - Opsi tambahan parameter
	*		boolean favicon - True file berupa favicon, allow extension ico
	*		number max_size - Maksimal ukuran foto yang diupload
	*		number max_width - Maksimal lebar foto yang diupload
	*		number max_height - Maksimal panjang foto yang diupload
	*		array allowed_ext - Extension foto yang diperbolehkan
	*		boolean prefix_as_name - True menggunakan Prefix sebagai Nama file
	*		boolean rar - Allow extension rar
	* @param number $newImageWidth - Ukuran lebar yang akan diresize
	* @param number $newImageHeight - Ukuran panjang yang akan diresize
	* @param string $newImage - Generate image sesuai dengan panjang dan lebar yang telah diresize
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $white - Alokasi warna Putih
	* @param string $source - hasil resize foto
	* @return string - hasil resize foto
	*/
    function resizeThumbnailImage($image, $width, $height, $start_width, $start_height, $scale, $width_image, $height_image){
		$file_info = pathinfo($image);
		$photo_extension = strtolower($file_info['extension']);
		
    	if (class_exists('imagick')) {
    		$source = $image;
			$image = new Imagick($source);

			if( in_array($photo_extension, array( 'png', 'gif' )) ){
				$image->setImageBackgroundColor(new ImagickPixel('transparent')); 
				// $image->thumbnailImage ($width, $height, true, false);
			} else {
				// $image->thumbnailImage ($width, $height, true, true);
			}
			
			// $image_width = $image->getImageWidth();
			// $image_height = $image->getImageHeight();

			// $this->autoRotateImage($image);

			$image->cropImage($width,$height,$start_width,$start_height);
			$image->writeImage($source);

			// header("Content-Type: image/jpg");
   //  		echo $image->getImageBlob();
		} else {
	        $newImageWidth = ceil($width * $scale);
	        $newImageHeight = ceil($height * $scale);
	        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	        $source = "";
	        
	        if( in_array($photo_extension, array('jpg', 'jpeg')) && $width > 300 ){
	        	$exif = @exif_read_data($image);
	        }else{
	        	$exif = false;
	        }
	        
	        if($photo_extension == "png"){
	            $source = @imagecreatefrompng($image);
	        } elseif($photo_extension == "jpg" || $photo_extension == "jpeg"){
	            $source = @imagecreatefromjpeg($image);
	        } elseif($photo_extension == "gif"){
	            $source = @imagecreatefromgif($image);
	        }

	        $newImage = $this->_callGenerateTransparent($photo_extension, $source, $newImage);

	        if($exif && isset($exif['Orientation'])) {
	        	$orientation = $exif['Orientation'];
	        	if($orientation != 1){
	        		$deg = 0;
	        		switch ($orientation) {
	        			case 3:
		        			$deg = 180;
		        			break;
	    				case 6:
	    					$deg = 270;
	            			break;
	        			case 8:
	        				$deg = 90;
	        				break;
	    			}

					if ($deg) {
						$newImage = imagerotate($source, $deg, 0);
					}

				}
			}

			if( $width > $width_image ) {
				$width = $width_image;
			}
			if( $height > $height_image ) {
				$height = $height_image;
			}

	        @imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);

	        if($photo_extension == "png"){
	            imagepng($newImage,$image, 9);
	        }elseif($photo_extension == "jpg" || $photo_extension == "jpeg"){
	            imagejpeg($newImage,$image, 90);
	        }elseif($photo_extension == "gif"){
	            imagegif($newImage,$image);
	        }
	        return $image;
	    }
    }

    function cropPhoto( $data, $uploadTo, $thumb_width = 300 ){
		$x1 = $this->RmCommon->filterEmptyField($data, 'x1');
		$y1 = $this->RmCommon->filterEmptyField($data, 'y1');
		$x2 = $this->RmCommon->filterEmptyField($data, 'x2');
		$y2 = $this->RmCommon->filterEmptyField($data, 'y2');
		$w = $this->RmCommon->filterEmptyField($data, 'w');
		$h = $this->RmCommon->filterEmptyField($data, 'h');
		$w_img = $this->RmCommon->filterEmptyField($data, 'w_img');
		$h_img = $this->RmCommon->filterEmptyField($data, 'h_img');
		$imagePath = $this->RmCommon->filterEmptyField($data, 'imagePath');

    	if( !empty($w) ) {
	    	App::import('Vendor', 'thumb', array('file' => 'thumb'.DS.'ThumbLib.inc.php'));
			
	        $scale = $thumb_width/$w;
			$pathPhoto = Configure::read('__Site.upload_path');
			$sourceImage = $this->getPathPhoto($pathPhoto, false, $uploadTo, $imagePath);
			$sizeImg = @getimagesize($sourceImage);

			$image_w = !empty($sizeImg[0])?$sizeImg[0]:false;
			$image_h = !empty($sizeImg[1])?$sizeImg[1]:false;

			if( !empty($sizeImg) ) {
				$w_scale = $image_w/$w_img;
				$h_scale = $image_h/$h_img;

				$x1 = ceil($x1 * $w_scale);
				$y1 = ceil($y1 * $h_scale);
				$w = ceil($w * $w_scale);
				$h = ceil($h * $h_scale);
			}

	        $this->resizeThumbnailImage($sourceImage,$w,$h,$x1,$y1,$scale,$w_img,$h_img);
			$data = array(
				'src' => $imagePath,
				'is_generate_photo' => false,
			);

			$this->_generateThumbnail( $data, 'src', $uploadTo );

	        return $imagePath;
	    } else {
	    	return false;
	    }
    }

	/**
	* Check Format Ukuran File
	*
	* @param number file_size - Maksimal ukuran foto yang diupload
	* @param string $sizetype - Tipe Ukuran Foto
	* @param number $filesize - Convert berdasarkan Tipe Ukuran File
	* @return number - Convert Ukuran File
	*/
	function format_size($file_size, $sizetype) {
		switch(strtolower($sizetype)){
			case "kb":
				$filesize = $file_size * .0009765625; // bytes to KB
			break;
			case "mb":
				$filesize = $file_size * .0009765625 * .0009765625; // bytes to MB
			break;
			case "gb":
				$filesize = $file_size * .0009765625 * .0009765625 * .0009765625; // bytes to GB
			break;
		}
		if($filesize <= 0){
			$filesize = 0;
		} else {
			$filesize = round($filesize, 2).' '.$sizetype;
		}
		return $filesize;
	}
	
	/**
	* Check Format Ukuran File
	*
	* @param string image - Nama File
	* @param string $path - Nama Path Folder
	*/
	function delete($image, $path) {
		if($path) {
			$path = $path.DS;
		}
        @unlink(Configure::read('__Site.upload_path').DS.$path.$image);
	}

    /**
	*
	*	aturan dalam mengupload gambar
	*	@param string $directory_name : nama directory gambar
	*	@return array
	*/
    function _rulesDimensionImage($directory_name, $data_type = false, $photo_extension = false, $options = array()){
    	$type_image = $this->RmCommon->filterEmptyField($options, 'type_image', false, 'landscape');
    	$result = array();
    	
    	if( in_array($directory_name, array( 'logos' )) ) {
    		if( $data_type == 'thumb' ) {
    			$result = 'xsm';
    		} else if( $data_type == 'large' ) {
    			$result = 'xxsm';
    		} else {
	    		$result = array(
					'xsm' => '100x40',
					'xm' => '200x200',
					'xxsm' => '240x96'
				);
	    	}
    	} else if( in_array($directory_name, array( 'users' )) ) {
    		if( $data_type == 'thumb' ) {
    			$result = 'pm';
    		} else if( $data_type == 'large' ) {
    			$result = 'pxl';
    		} else {
	    		$result = array(
					'ps' => '50x50',
					'pm' => '100x100',
					'pl' => '150x150',
					'pxl' => '300x300',
				);
	    	}
    	} else if( in_array($directory_name, array( 'ebrosur', 'ebrosur_template' )) ) {
            if( $data_type == 'thumb' ) {
    			$result = 'm';
    		} else if( $data_type == 'large' ) {
    			$result = 'xl';
    		} else {
				$isBuilder = Common::config('Config.Company.data.UserCompanyConfig.is_ebrochure_builder');

    			if($type_image == 'potrait'){
    				$result = array(
						's' => '296x420',
						'm' => '453x640',
						'xl' => $isBuilder ? '768x1024' : '724x1024',
					);
    			}else{
    				$result = array(
						's' => '420x296',
						'm' => '640x453',
						'xl' => $isBuilder ? '1024x768' : '1024x724',
					);
    			}
	    	}
        } else if( in_array($directory_name, array( 'files', 'crms', 'document', 'documents')) && in_array($photo_extension, array( 'pdf', 'xls', 'xlsx' )) ) {
			$result = false;
        } else {
    		if( $data_type == 'thumb' ) {
    			$result = 'm';
    		} else if( $data_type == 'large' ) {
    			$result = 'l';
    		} else {
	    		$result = array(
					's' => '150x84',
					'm' => '300x169',
					'l' => '855x481',
					'company' => '855x481'
				);
	    	}
    	}

    	return $result;
    }

	/**
	*
	*	generate subfolder
	* 	ketentuan xxxxx-[x]xxxx-xxxxxx satu huruf di tali ke 2
	*	@param string $filename : nama file
	*	@return string
	*/
    function generateSubPathFolder($filename) {
    	$photo_subpath = '';
    	$sub_part = explode('-',$filename);
    	
    	if(!empty($sub_part[1])) {
			$photo_subpath = substr($sub_part[1], 0, 1);
		}
    	
    	return (string)$photo_subpath;
    }

    function replaceSlash ( $file, $action = false ) {
    	if( $action == 'reverse' ) {
    		return str_replace(DS, '/', $file);
    	} else if( $action == 'remove' ) {
    		return str_replace(array( '/', DS ), array( '', '' ), $file);
    	} else {
    		return str_replace('/', DS, $file);
    	}
    }

	function unGeneratePhoto ( $id, $modelName ) {
		$this->{$modelName} = ClassRegistry::init($modelName); 
		$this->{$modelName}->id = $id;
		$this->{$modelName}->set('is_generate_photo', 1);
		$this->{$modelName}->save();
	}

	function _callDataPosition ( $data, $modelName ) {
		return array(
			'x1' => $this->RmCommon->filterEmptyField($data, $modelName, 'x1'),
			'y1' => $this->RmCommon->filterEmptyField($data, $modelName, 'y1'),
			'x2' => $this->RmCommon->filterEmptyField($data, $modelName, 'x2'),
			'w' => $this->RmCommon->filterEmptyField($data, $modelName, 'w'),
			'h' => $this->RmCommon->filterEmptyField($data, $modelName, 'h'),
			'w_img' => $this->RmCommon->filterEmptyField($data, $modelName, 'w_img'),
			'h_img' => $this->RmCommon->filterEmptyField($data, $modelName, 'h_img'),
			'imagePath' => $this->RmCommon->filterEmptyField($data, $modelName, 'imagePath'),
		);
	}

    function getPathPhoto ( $path, $size, $save_path, $filename ){
        $file = $path.DS.$save_path;

        if( !empty($size) ) {
        	$file .= DS.$size;
        }

    	$file .= $filename;
        $file = str_replace('/', DS, $file);

        return $file;
    }

    function _uploadPhoto ( $data, $modelName, $fieldName, $save_path, $unset = false, $options = false ) {
    	$keep_file_name = Common::hashEmptyField($options, 'keep_file_name');

    	if( !empty($data) ) {
    		$hideField = $fieldName.'_hide';
    		$hideSavePath = $fieldName.'_save_path';
    		$nameField = $fieldName.'_name';
    		$rest = $this->controller->Rest->isActive();

    		$photoHide = $this->RmCommon->filterEmptyField($data, $modelName, $hideField);
    		$savePathHide = $this->RmCommon->filterEmptyField($data, $modelName, $hideSavePath);


    		if( !empty($data[$modelName][$fieldName]['name']) ) {
	    		$tmpName = $data[$modelName][$fieldName];
				$data[$modelName][$fieldName] = $tmpName['name'];

				$uploaded = $this->upload($tmpName, '/'.$save_path.'/', String::uuid(), $options);

	            if( isset($uploaded['error']) && $uploaded['error'] != 1 ) {
	            	$data[$modelName][$hideField] = $data[$modelName][$fieldName] = $uploaded['imageName'];
	            	$data[$modelName][$hideSavePath] = $save_path;
	            	$data[$modelName][$nameField] = $uploaded['baseName'];
	            } else if( empty($keep_file_name) ) {
            		$data[$modelName][$fieldName] = false;
	            }

            	$data['Upload'][$fieldName] = $uploaded;
			} else if( !empty($photoHide) ) {
    			$data[$modelName][$fieldName] = $photoHide;
    		} else {
				$data[$modelName][$fieldName] = false;
			}

			if( (!empty($unset) || !empty($rest)) && empty($data[$modelName][$fieldName]) ) {
				unset($data[$modelName][$fieldName]);
			}
		}

		return $data;
    }

    function getPathFolder ( $filename ) {
    	$pathArr = explode('/', $filename);
    	array_pop($pathArr);
    	$path = implode('/', $pathArr);

    	return $path;
    }

    function getPathEbrosur ( $filename ) {
		$webroot = WWW_ROOT;
		$dir = 'img';
		$dir_ebrosur = sprintf('%s%s', $webroot, $dir);
        $urlEbrosur = sprintf('%s/%s%s', FULL_BASE_URL, $dir, $filename);
        $pathEbrosur =  $dir_ebrosur.$this->getPathFolder($filename);
        $fileEbrosur = sprintf('%s%s', $dir_ebrosur, $filename);

        return array(
        	'UrlEbrosur' => $urlEbrosur,
        	'FileEbrosur' => $fileEbrosur,
        	'PathEbrosur' => $pathEbrosur,
    	);
	}

	// DATA Yang diperlukan
	/*
	'property_photo' => array(
		'url'
		'path'
	),
	'logo_company' => array(
		'url'
		'path'
	),
	'name'
	'phone'
	'description'
	'price'
	'title'
	'location'
	'property_action_id'
	'background_color'
	'background_class'
    */
    function create_ebrosur($data , $options = array()){
    	$property_photo = $this->RmCommon->filterEmptyField($data, 'property_photo');
    	$name = $this->RmCommon->filterEmptyField($data, 'name');
    	$code = $this->RmCommon->filterEmptyField($data, 'code');
    	$created = $this->RmCommon->filterEmptyField($data, 'created');

    	$regenerate = $this->RmCommon->filterEmptyField($data, 'regenerate');

    	if(!empty($created)){
    		$created = date('d/m/Y H:i:s', strtotime($created));
    	}else{
    		$created = date('d/m/Y H:i:s');
    	}

    	$phone = $this->RmCommon->filterEmptyField($data, 'phone');
    	$price = $this->RmCommon->filterEmptyField($data, 'price');
    	$title = $this->RmCommon->filterEmptyField($data, 'title');
    	$description = $this->RmCommon->filterEmptyField($data, 'description', false, '', array(
    		'urldecode' => false
    	));
    	$location = $this->RmCommon->filterEmptyField($data, 'location');
    	$property_action_id = $this->RmCommon->filterEmptyField($data, 'property_action_id');
    	$background_color = $this->RmCommon->filterEmptyField($data, 'background_color');
    	$background_class = $this->RmCommon->filterEmptyField($data, 'background_class');
    	$logo_company = $this->RmCommon->filterEmptyField($data, 'logo_company');
    	$photo_profile = $this->RmCommon->filterEmptyField($data, 'photo_profile');

    	$delta_x_code = $this->RmCommon->filterEmptyField($data, 'delta_x_code');
    	$delta_y_code = $this->RmCommon->filterEmptyField($data, 'delta_y_code');
    	$delta_x_created = $this->RmCommon->filterEmptyField($data, 'delta_x_created');
    	$delta_y_created = $this->RmCommon->filterEmptyField($data, 'delta_y_created');
    	$delta_x_mlsid = $this->RmCommon->filterEmptyField($data, 'delta_x_mlsid');
    	$delta_y_mlsid = $this->RmCommon->filterEmptyField($data, 'delta_y_mlsid');
    	$layout = $this->RmCommon->filterEmptyField($data, 'layout', false, 'landscape');
    	
    	$brochure_footer_color = $this->RmCommon->filterEmptyField($data, 'brochure_footer_color');
    	$brochure_content_color = $this->RmCommon->filterEmptyField($data, 'brochure_content_color');
    	
    	$brochure_footer_color = $this->RmCommon->getColorCode($brochure_footer_color, '#', 'left', '', true);
    	$brochure_content_color = $this->RmCommon->getColorCode($brochure_content_color, '#', 'left', '', true);

    	$mls_id = $this->RmCommon->filterEmptyField($data, 'mls_id');
    	$with_mls_id = $this->RmCommon->filterEmptyField($data, 'with_mls_id');

    	if(!empty($description)){
    		$limit_str = 200;

    		$description = $this->RmCommon->truncateByStr($description, $limit_str);
    	}

    	$arr_action = array(
			'1' => 'dijual',
			'2' => 'disewakan'
		);

		$background = '';
    	if(!empty($data['background_sell']) || !empty($data['background_rent'])){
    		if($property_action_id == 1){
    			$background = $data['background_sell'];
    		}else{
    			$background = $data['background_rent'];
    		}
    	}

		$property_action = $arr_action[$property_action_id];

		$font_type_footer = $font_type = APP.'webroot'.DS.'fonts'.DS.'droidsans-webfont.ttf';
		$font_type_temp = APP.'webroot'.DS.'fonts'.DS.'droidsans-webfont.ttf';
		$font_type_bold = APP.'webroot'.DS.'fonts'.DS.'droidsans-bold-webfont.ttf';
		
		$quality = 100;
		$ebrosur_height = $height = 724;

		if($layout == 'potrait'){
			$height = 1024;
		}else{
			$isBuilder = Common::config('Config.Company.data.UserCompanyConfig.is_ebrochure_builder');

			if( !empty($isBuilder) ) {
				$ebrosur_height = $height = 768;
			}
			
			$name = $this->RmCommon->truncate($name, 20, '');
		}
		
		$this->options = $this->_setValue( $options );

		$path_target = Configure::read('__Site.ebrosurs_photo');

		$uploadTo = sprintf('/%s/', $path_target);

		$upload_sub_path = '';
		$upload_dir = str_replace('/', DS, $uploadTo);
		$save_path = str_replace('/', '', $uploadTo);
		$upload_path = $this->options['baseuploadpath'].$upload_dir;
		$photo_extension = 'jpg';
		$filename = sprintf('%s.%s', String::uuid(), $photo_extension);
		$thumbnailPath = Configure::read('__Site.thumbnail_view_path').$uploadTo;

		$photo_subpath = $this->generateSubPathFolder($filename);
		$upload_sub_path = $this->makeDir( $upload_path, $photo_subpath );

		$uploadPhotoPath = str_replace('/', DS, $upload_path);
		$uploadFilename =  str_replace('/', DS, $upload_sub_path.$filename);

		$uploadSource = $uploadPhotoPath.$uploadFilename;
		$image_width = 1024;
		$ebrosur_photo = '';

		// if the file already exists dont create it again just serve up the original	
		if (!file_exists($uploadSource)) {
			if(empty($background)){
				if($layout == 'potrait'){
					$file_bg_name = $property_action.'_potrait_'.$background_class.'.jpg';
				}else{
					$file_bg_name = $property_action.'_'.$background_class.'.jpg';
				}

				$bg_action_url = Configure::read('__Site.webroot_files_path').DS.'bg'.DS.$file_bg_name;
			}else{
				$bg_action_url = Configure::read('__Site.upload_path').DS.$path_target.str_replace('/', DS, $background);
			}
			
			if(file_exists($bg_action_url)){
				// define the base image that we lay our text on
				if($this->_getExtension($bg_action_url) == 'png'){
					$im = imagecreatefrompng($bg_action_url);
				}else{
					$im = imagecreatefromjpeg($bg_action_url);
				}

				// setup the text colours
				$color_text = imagecolorallocate($im, 0, 0, 0); // hitam
				if($background_color > 5){
					$color_text = imagecolorallocate($im, 255, 255, 255); // putih
				}
				
				if(!empty($brochure_footer_color) && is_array($brochure_footer_color)){
					$footer_color = imagecolorallocate($im, $brochure_footer_color[0], $brochure_footer_color[1], $brochure_footer_color[2]);
				}else{
					if($layout == 'potrait'){
						$footer_color = imagecolorallocate($im, 0, 0, 0);
					}else{
						if($background_color == 5){
							$footer_color = imagecolorallocate($im, 255, 255, 255);
						}else{
							$footer_color = $color_text;
						}
					}
				}

				if(!empty($brochure_content_color) && is_array($brochure_content_color)){
					$content_color = imagecolorallocate($im, $brochure_content_color[0], $brochure_content_color[1], $brochure_content_color[2]);
				}else{
					$content_color = $color_text;
				}
				// end setup color
				
				$next_delta_y = 0;

				if($layout == 'potrait'){
					$start_x = 53;
					$end_x = 637;
				}else{
					$start_x = 420;
					$end_x = 850;
				}

				$width_box = $end_x - $start_x;

				if($layout == 'potrait'){
					if(empty($brochure_content_color)){
						$content_color = imagecolorallocate($im, 0, 0, 0); // hitam
					}

					$total_line_for_title = 0;

					/*membuat title + location*/
					if($location){
						$title.= "\n".$location;
					}

					$font_size = 20;
					$text = explode("\n", wordwrap($title, 41)); // <-- you can change this number
					
					$delta_y = 368;

					foreach($text as $line) {
					    $next_delta_y = 184 + $delta_y;

					    $delta_x = $start_x + $this->_centerText($line, $font_size, $width_box);

					    imagettftext($im, $font_size, 0, $delta_x, $next_delta_y, $content_color, $font_type_bold, $line);
					
					    $delta_y += 30;

					    ++$total_line_for_title;
					}
					/*end membuat title*/

					/*membuat price*/
					$font_size = 20;
					$text = explode("\n", wordwrap($price, 60)); // <-- you can change this number
					$delta_y = $next_delta_y+40;

					$delta_x = $start_x + $this->_centerText($price, $font_size, $width_box);

					imagettftext($im, $font_size, 0, $delta_x, $delta_y, $content_color, $font_type_bold, $price);

					++$total_line_for_title;
					/*end membuat price*/

					/*membuat deskripsi*/
					$font_size = 18;
					$text = explode("\n", wordwrap($description, 55)); // <-- you can change this number
					$delta_y = $delta_y+40;
					$line_max = 8 - $total_line_for_title;
					$idx = 0;
					foreach($text as $line) {
						$delta_x = 53 + $this->_centerText($line, $font_size, 628);
						
					    imagettftext($im, $font_size, 0, $delta_x, $delta_y, $content_color, $font_type_temp, $line);
					
					    $delta_y += 25;

					    $idx++;
					    if($idx >= $line_max){
					    	break;
					    }
					}
					/*end membuat deskripsi*/

					/*membuat name*/
					$name_agen = sprintf('%s - %s', ucwords($name), $phone);
					if(!empty($name_agen)){
						$bottom_y = 910;
						$delta_x = $this->_centerText($name_agen, 14, $ebrosur_height);
						imagettftext($im, 14, 0, $delta_x, $bottom_y, $content_color, $font_type_bold, $name_agen);
					}
					/*end membuat name*/

					$default_delta_y = 940;
				}else{
					/*membuat title*/
					$font_size = 22;
					$text = explode("\n", wordwrap($title, 28)); // <-- you can change this number
					$delta_y = 30;

					$total_line_for_title = 0;

					foreach($text as $line) {
					    $next_delta_y = 158 + $delta_y;

					    $delta_x = $start_x + $this->_centerText($line, $font_size, $width_box);

					    imagettftext($im, $font_size, 0, $delta_x, $next_delta_y, $content_color, $font_type_bold, $line);
					
					    $delta_y += 30;

					    ++$total_line_for_title;
					}
					/*end membuat title*/

					/*membuat location*/
					$font_size = 22;
					$text = explode("\n", wordwrap($location, 28)); // <-- you can change this number
					$delta_y = $next_delta_y+30;

					foreach($text as $line) {
						$delta_x = $start_x + $this->_centerText($line, $font_size, $width_box);

					    imagettftext($im, $font_size, 0, $delta_x, $delta_y, $content_color, $font_type_bold, $line);
					
					    $delta_y += 30;
					    ++$total_line_for_title;
					}
					/*end membuat location*/

					if($delta_y <= 270){
						$delta_y = 275;
					}

					/*membuat deskripsi*/
					$font_size = 20;
					$text = explode("\n", wordwrap($description, 41)); // <-- you can change this number
					$delta_y = $delta_y+30;
					$line_max = 10 - $total_line_for_title;
					$idx = 0;
					
					foreach($text as $line) {
						$delta_x = 430 + $this->_centerText($line, $font_size, 530);
						
					    imagettftext($im, $font_size, 0, $delta_x, $delta_y, $content_color, $font_type_temp, $line);
					
					    $delta_y += 30;

					    ++$idx;
					    if($idx > $line_max){
					    	break;
					    }
					}
					/*end membuat deskripsi*/

					/*membuat price*/
					$font_size = 28;
					$text = explode("\n", wordwrap($price, 25)); // <-- you can change this number
					$delta_y = $height - 130;
					
					foreach($text as $line) {
						$delta_x = 400 + $this->_centerText($line, $font_size, 530);
						
					    imagettftext($im, $font_size, 0, $delta_x, $delta_y, $content_color, $font_type_bold, $line);
					
					    $delta_y += 45;
					}
					/*end membuat price*/

					$bottom_y = $height;

					/*membuat name*/
					if(!empty($name)){
						$bottom_y = $bottom_y - 140;
						$delta_x = 38 + $this->_centerText(ucwords($name), 20, 364);
						imagettftext($im, 22, 0, $delta_x, $bottom_y, $content_color, $font_type_bold, ucwords($name));
					}
					/*end membuat name*/

					/*membuat phone*/
					if(!empty($phone)){
						$bottom_y = $bottom_y + 30;
						$delta_x = 38 + $this->_centerText($phone, 20, 364);
						imagettftext($im, 22, 0, $delta_x, $bottom_y, $content_color, $font_type_bold, $phone);
					}
					/*end membuat phone*/

					$default_delta_y = 717;
				}

				/*membuat code*/
				if(empty($delta_y_code)){
					$delta_y_code = $default_delta_y;
				}

				if(empty($delta_x_code)){
					if($layout == 'potrait'){
						$delta_x_code = 30;
					}else{
						$delta_x_code = 25;
					}
				}

				imagettftext($im, 12, 0, $delta_x_code, $delta_y_code, $footer_color, $font_type_footer, strtoupper($code));
				/*end membuat code*/

				/*membuat created*/
				if(empty($delta_y_created)){
					$delta_y_created = $default_delta_y;
				}

				if(empty($delta_x_created)){
					if($layout == 'potrait'){
						$x = 630;
					}else{
						$x = 930;
					}

					$delta_x_created = $x - $this->_rightText($created, 12, 234);
				}

				imagettftext($im, 12, 0, $delta_x_created, $delta_y_created, $footer_color, $font_type_footer, $created);
				/*end membuat created*/

				// create mlsid
				if(!empty($with_mls_id) && !empty($mls_id)){
					$all_width = 1024;
					if($layout == 'potrait'){
						$all_width = $ebrosur_height;
					}

					$delta_x = $this->_centerText(strtoupper($mls_id), 12, $all_width);
					$delta_y = $default_delta_y;

					if(!empty($delta_y_mlsid)){
						$delta_y = $delta_y_mlsid;
					}

					if(!empty($delta_x_mlsid)){
						$delta_x = $delta_x_mlsid;
					}
					
					imagettftext($im, 12, 0, $delta_x, $delta_y, $footer_color, $font_type_footer, strtoupper($mls_id));
				}
				// end create mlsid

				// create the image
				imagejpeg($im, $uploadSource, $quality);

				$root_path = Configure::read('__Site.upload_path');

				if(!empty($property_photo['url']) && !empty($property_photo['path'])){
					if($layout == 'potrait'){
						$x = 66;
						$y = 159;
						$width = 592;
						$height = 366;
					}else{
						$x = 45;
						$y = 167;
						$width = 350;
						$height = 370;
					}

					$this->_mergeImage( $uploadSource, $property_photo, $x, $y, $width, $height, $quality, $regenerate );
				}

				if(!empty($logo_company['url']) && !empty($logo_company['path']) && empty($background)){
					if($layout == 'potrait'){
						$x = 454;
						$y = 14;
					}else{
						$x = 35;
						$y = 26;
					}

					$this->_mergeImage( $uploadSource, $logo_company, $x, $y, 240, 96, $quality, $regenerate );
				}

				if(!empty($photo_profile['url']) && !empty($photo_profile['path'])){
					if($layout == 'potrait'){
						$x = 319;
						$y = 780;
					}else{
						$x = 880;
						$y = 167;
					}

					$this->_mergeImage( $uploadSource, $photo_profile, $x, $y, 100, 100, $quality, $regenerate );
				}

				$ebrosur_photo = DS.$uploadFilename;
			}else{
				$this->RmCommon->_saveLog(__('[EBROSUR] Gagal membuat ebrosur, file background tidak ditemukan'), $data, false, 1, 307);
			}
		}else{
			$this->create_ebrosur($data , $options);
		}
		
		$data['ebrosur_photo'] = $ebrosur_photo;
		
		$this->_generateThumbnail( $data, 'ebrosur_photo', $path_target, array(
			'type_image' => $layout
		) );
		
		return '/'.str_replace(DS, '/', $uploadFilename);
	}

	function _getExtension($text){
		$text_exp = explode('.', $text);

		$count_arr = count($text_exp);

		return $text_exp[$count_arr-1];
	}

	function _mergeImage($uploadSource, $data, $x, $y, $width, $height, $quality = 100, $regenerate = false){
		$max_width = $width;
		$max_height = $height;
		$root_path = Configure::read('__Site.upload_path');

		$image_from = imagecreatefromjpeg($uploadSource);
		$path = $data['path'];
		$url = $data['url'];
		
		$data = str_replace('/', DS, $url);
		$extension = strtolower($this->_getExtension($url));
		
		$file_path = $root_path.DS.$path.$data;
		$full_root_no_ext = $root_path.DS.$path;
		
		if($path == 'logos'){
			$size_temp = getimagesize($file_path);
			
			if(!empty($size_temp[0]) && $size_temp[0] < $width){				
				$width = $size_temp[0];
			}

			if(!empty($size_temp[1]) && $size_temp[1] < $height){
				$height = $size_temp[1];
			}
		}

		if(file_exists($file_path)){
			$options['watermark'] = false;
			
			$name_file = explode('.', $url);
			unset($name_file[count($name_file)-1]);
			$name_file = str_replace('/', DS, implode('', $name_file));

			if(in_array($extension, array('jpg', 'jpeg'))){
				$temp_thumb = $full_root_no_ext.$name_file.'-copy.'.$extension;
				$temp_original = $full_root_no_ext.$name_file.'-original.'.$extension;

				if(!file_exists($temp_original)){
					copy($file_path, $temp_original);

					$file_path = $temp_original;
				}else{
					$file_path = $temp_original;
				}
				
				if(!file_exists($temp_thumb) || $regenerate ){
					if($regenerate && file_exists($temp_thumb)){
						unlink($temp_thumb);
					}

					// $this->_getNormalizeScale($file_path, $max_width, $max_height, $extension);

					$this->_createThumbnail($file_path, $temp_thumb, $width, $height, $options);
				}
			} else {
				$temp_thumb = $full_root_no_ext.$name_file.'.'.$extension;
			}
			// else{
			// 	$jpg_thumb = $full_root_no_ext.$name_file.'.jpg';

			// 	if(!file_exists($temp_thumb) || $regenerate){
			// 		// if($regenerate && file_exists($temp_thumb)){
			// 		// 	unlink($temp_thumb);
			// 		// }

			// 		$this->_convertToJPG($temp_thumb, $jpg_thumb);
			// 		$temp_thumb = $jpg_thumb;
			// 		debug($temp_thumb);die();

			// 		imagejpeg(imagecreatefromstring(file_get_contents($file_path)), $temp_thumb);
			// 		$this->_createThumbnail($temp_thumb, $temp_thumb, $width, $height, $options);
			// 	}
			// }

			if(!in_array($extension, array('jpg', 'jpeg'))){
				$jpg_thumb = $full_root_no_ext.$name_file.'.jpg';

				if( file_exists($temp_thumb) && !file_exists($jpg_thumb) ) {
					$this->_convertToJPG($temp_thumb, $jpg_thumb);
					$this->_createThumbnail($jpg_thumb, $jpg_thumb, $width, $height, $options);
				}
				
				$temp_thumb = $jpg_thumb;
			}

			$size_temp = getimagesize($temp_thumb);
			
			if(!empty($size_temp[0]) && $size_temp[0] < $width){
				$max_width = $width;
				$width = $size_temp[0];

				$x += (ceil($max_width - $width) / 2);
			}

			if(!empty($size_temp[1]) && $size_temp[1] < $height){
				$height = $size_temp[1];
			}

			$image_tomerge = $this->_createImage('jpg', $temp_thumb);

			imagecopymerge($image_from, $image_tomerge, $x, $y, 0, 0, $width, $height, 100);
			imagejpeg($image_from, $uploadSource, $quality);
		}
	}

	// function make_center($text, $half_symmetry){
	// 	return $half_symmetry - imagesx($text)/2;
	// }

	function _centerText($string, $font_size, $width){
		$font_type = APP.'webroot'.DS.'fonts'.DS.'droidsans-webfont.ttf';

		$dimensions = imagettfbbox($font_size, 0, $font_type, $string);
		
		return ceil(($width - $dimensions[4]) / 2);				
	}

	function _rightText($string, $font_size, $width){
		$font_type = APP.'webroot'.DS.'fonts'.DS.'droidsans-webfont.ttf';

		$dimensions = imagettfbbox($font_size, 0, $font_type, $string);
		
		return ceil($width - $dimensions[4]);				
	}

	function _createImage($extension, $temp_thumb){
		if(in_array($extension, array('jpg', 'jpeg'))){
			$image_tomerge = imagecreatefromjpeg($temp_thumb);
		}else if( $extension == 'png' ){
			$image_tomerge = imagecreatefrompng($temp_thumb);
		}else if( $extension == 'gif' ){
			$image_tomerge = imagecreatefromgif($temp_thumb);
		}

		return $image_tomerge;
	}

	function fileExist($folder, $size, $file_name){
		if( Common::strposArray($file_name, array('http', 'https')) === false ){
			$full_path = Configure::read('__Site.thumbnail_view_path').DS.$folder.DS.$size.str_replace('/', DS, $file_name);

			if(file_exists($full_path)){
				return $full_path;
			}else{
				return false;
			}
		} else {
			return $file_name;
		}
	}

	function copy_image_to_uploads($path_file, $from_foder = 'ebrosur', $to_foder = 'ebrosur',  $field = 'ebrosur_photo'){
		if($to_foder == 'ebrosur'){
			$full_path = str_replace('/', DS, Configure::read('__Site.webroot_files_path').$path_file);
		}else{
			$full_path = str_replace('/', DS, Configure::read('__Site.upload_path').'/'.$from_foder.$path_file);
		}
		
		if( file_exists($full_path) ) {
			$uploads_url = Configure::read('__Site.upload_path');

			$explode = explode('/', $path_file);

			if($to_foder == 'ebrosur'){
				$filename = $explode[3];
			}else{
				$filename = $explode[4];
			}

			$photo_subpath = substr($filename, 0, 1);

			$upload_dir = str_replace('/', DS, $to_foder);
			$save_path = str_replace('/', '', $to_foder);
			$upload_path = $uploads_url.DS.$upload_dir.DS;

			$upload_sub_path = str_replace('/', DS, $this->makeDir( $upload_path, $photo_subpath ).$filename);
			
			$upload_path .= str_replace('/', DS, $upload_sub_path);
			
			if(!file_exists($upload_path)){
				$data[$field] = str_replace(DS, '/', DS.$upload_sub_path);
				
				copy($full_path, $upload_path);

				$this->_generateThumbnail( $data, $field, $to_foder);

				return $data[$field];
			}else{
				return str_replace(DS, '/', DS.$upload_sub_path);
			}
		}else{
			return false;
		}
	}

	private function _getNormalizeScale($uploadSource, $max_width, $max_height, $extension, $options = array()){

		$options 				= $this->_setValue( $options );
		$options['max_width'] 	= $max_width;
		$options['max_height'] 	= $max_height;

		if(!empty($uploadSource) && file_exists($uploadSource)){
			$sizes = getimagesize($uploadSource);
			$width = $sizes[0];
			$height = $sizes[1];

			$scale = $this->getScale( $width, $height, $max_width, $max_height );
			$this->_resizeImage($uploadSource, $width, $height, $scale, $options, $extension);
		}
	}

	public function restore($filePath = NULL, $savePath = NULL, $options = array()){
		$rootPath = Configure::read('__Site.upload_path');
		$fullFilePath	= sprintf('%s/%s%s', $rootPath, $savePath, $filePath);
		$fileName		= basename($filePath);
		$fileExtension	= pathinfo($fileName, PATHINFO_EXTENSION);

		if(file_exists($fullFilePath) === FALSE){
			$results = array('status' => 0, 'message' => __('File not found, it may already restored or permanently deleted.'));
		}
		else{
			$thumbnail	= array('thumbnail' => $filePath);
			$generate	= $this->_generateThumbnail($thumbnail, 'thumbnail', $savePath);
			$results	= array('status' => 1, 'message' => __('File has been restored.'));
		}

		return $results;
	}

	function generatePathFolder ( $filename, $path, $subpath = false ) {
		$baseuploadpath = Configure::read('__Site.upload_path');
		$upload_path = $baseuploadpath.DS.$path;

		if( empty($subpath) ) {
			$subpath = $this->generateSubPathFolder($filename);
			$subpath = $this->makeDir( $upload_path.DS, $subpath );
			$subpath =  $this->replaceSlash($subpath);
			
			$uploadFilename =  DS.$this->replaceSlash($subpath.$filename);
		} else {
			$uploadFilename =  DS.$filename;
		}

		$uploadPhotoPath = $this->replaceSlash($upload_path);
		$uploadSource = $uploadPhotoPath.$uploadFilename;

		return array(
			'filename' => $this->replaceSlash($uploadFilename, 'reverse'),
			'filename_path' => $uploadSource,
		);
	}

	function _callGetFolderUploadPath ( $filename, $path ) {
		$baseuploadpath = Configure::read('__Site.upload_path');
		$upload_path = $baseuploadpath.DS.$path;
		$uploadPhotoPath = $this->replaceSlash($upload_path.$filename);

		return $uploadPhotoPath;
	}

	/*
		function primedev, get document media image, video, document
		$type (array) mempunyai nilai : image, video dan document
	*/
	function getImage($document_id, $document_type, $type = array('image')){
		$this->ApiDeveloperDocumentMedia = ClassRegistry::init('ApiDeveloperDocumentMedia'); 
		
		$result = array();

		if (!empty($type) && is_array($type)) {
			foreach ($type as $key => $value) {
				if($value == 'image'){
					$arr_key = 'Gallery';
				}else if($value == 'video'){
					$arr_key = ucfirst($value).'s';
				}else{
					$arr_key = ucfirst($value);
				}

				$temp_result = $this->ApiDeveloperDocumentMedia->find('all', array(
					'conditions' => array(
						'ApiDeveloperDocumentMedia.document_id' => $document_id,
						'ApiDeveloperDocumentMedia.document_type' => $document_type,
						'ApiDeveloperMedia.type' => $value
					),
					'contain' => array(
						'ApiDeveloperMedia'
					),
					'order' => array(
						'ApiDeveloperDocumentMedia.document_sub_type' => 'DESC'
					)
				));

				$result[$arr_key] = $temp_result;
			}
		}

		return $result;
	}
}
?>