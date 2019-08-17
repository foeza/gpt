<?php  
App::uses('AppHelper', 'View/Helper');

/**
* Helper to load the upload form
*
* NOTE: If you want to use it out of this plugin you NEED to include the CSS files in your Application.
* The files are loaded in `app/Plugins/FileUpload/View/Layouts/default.ctp` starting at line 16
*
*/
class UploadFormHelper extends AppHelper {
	var $helpers = array(
		'Rumahku', 'Html', 'Form',
		'Property'
	);

	/**
	*	Load the form
	* 	@access public
	*	@param String $url url for the data handler
	*   @param Boolean $loadExternal load external JS files needed
	* 	@return void
	*/
	public function load( $url = '/file_upload/handler', $data = false, $save_path = false, $options = false )
	{
		// Remove the first `/` if it exists.
	    if( $url[0] == '/' )
	    {
	        $url = substr($url, 1);
	    }

		$this->_loadScripts($data, $options);

		$this->_loadTemplate( $url, $data, $save_path, $options );

		// if( $loadExternal )
		// {
		// 	$this->_loadExternalJsFiles();	
		// }
		
	}

	public function loadUser( $url, $data = false, $save_path = false, $options = false ) {
		$this->_loadUserScripts($data);
		$this->_loadUserTemplate( $url, $data, $save_path, $options );
	}

	public function loadFile( $url = '/file_upload/handler', $data = false, $save_path = false, $options = false ) {
		// Remove the first `/` if it exists.
	    if( $url[0] == '/' )
	    {
	        $url = substr($url, 1);
	    }

		$this->_loadFileScripts($data, $options);
		$this->_loadFileTemplate( $url, $data, $save_path, $options );
	}

	private function _loadFileScripts($data, $options = false)
	{
		$msgDelete = __('Anda yakin ingin menghapus dokumen ini ?');
		$photoAction = $this->Html->tag('div', $this->Html->tag('div', $this->Form->input('CrmProjectDocument.is_share', array(
			'type' => 'checkbox',
			'label' => array(
				'text' => __('Share untuk Project Lain'),
				'for' => 'CrmProjectDocumentIsShare{%=file.id%}',
			),
			'id' => 'CrmProjectDocumentIsShare{%=file.id%}',
			'div' => false,
			'required' => false,
            'hiddenField' => false,
			'class' => 'share-file',
			'value' => 1,
			'rel' => '{%=file.id%}',
		)), array(
			'class' => 'sharing-document cb-checkmark',
		)), array(
			'class' => 'cb-custom mt0',
		));
		$photoAction .=$this->Html->tag('div', $this->Form->input('CrmProjectDocument.title', array(
			'type' => 'text',
			'label' => false,
			'id' => 'CrmProjectDocumentTitle{%=file.id%}',
			'div' => false,
			'required' => false,
            'hiddenField' => false,
			'class' => 'form-control change-file-title',
			'rel' => '{%=file.id%}',
			'value' => '',
			'placeholder' => __('Masukan Judul Dokumen'),
		)), array(
			'class' => 'title-document',
		));
		$photoOption = $this->Html->tag('div', $this->Html->tag('div', $this->Form->input('CrmProjectDocument.options_id.', array(
			'type' => 'checkbox',
			'label' => array(
				'text' => __('Pilih Dokumen'),
				'data-show' => '.fly-button-media',
				'for' => 'CrmProjectDocumentOptionsId{%=file.id%}',
			),
			'id' => 'CrmProjectDocumentOptionsId{%=file.id%}',
			'div' => false,
			'required' => false,
            'hiddenField' => false,
			'value' => '{%=file.id%}',
			'class' => 'check-option',
		)), array(
			'class' => 'bottom cb-checkmark disable-drag',
		)), array(
			'class' => 'cb-custom mt0',
		));

		echo '<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-upload fade col-sm-3">
		        <div class="preview relative"><span class="fade"></span></div>
		        {% if (file.error) { %}
		            <div class="error-full alert" colspan="2">{%=file.message%}</div>
		        {% } else if (o.files.valid && !i) { %}
		            <div>
		                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
		            </div>
		        {% } %}
		    </div>
		{% } %}
		</script>
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <li class="template-download col-sm-4 ajax-parent item" rel="{%=file.id%}">
				<div class="item">
			        {% if (file.error) { %}
			            <div class="error-full alert" colspan="2">{%=file.message%}</div>
			        {% } else { %}
			            <div class="preview relative">
							<img src="{%=file.thumbnail_url%}">
						</div>
						<div class="action">
							'.$photoAction.'
					        {% if (file.primary != 1) { %}
								'.$photoOption.'
					        {% } %}
						</div>
			        {% } %}
				</div>
		    </li>
		{% } %}
		</script>';

	}
	
	private function _loadFileTemplate( $url = null, $data = false, $save_path = false, $options = false )
	{
		$content = '';
		$id = $this->Rumahku->filterEmptyField($options, 'id');
		$label_input = $this->Rumahku->filterEmptyField($options, 'label', false, __('Tambah Foto'), false);
		$label_class = $this->Rumahku->filterEmptyField($options, 'label_class');

		if( !empty($data) ) {
			$idx = 0;

			foreach ($data as $key => $value) {
				$photoId = false;
				$photoName = false;
				$photoTitle = false;
				$isShare = false;

				switch ($save_path) {
					case 'files':
						$photoId = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'id');
						$isShare = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'is_share');
						$photoName = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'file');
						$photoTitle = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'title');
						break;
				}

				$photo = $this->Rumahku->photo_thumbnail(array(
					'save_path' => $save_path, 
					'src' => $photoName, 
					'size' => 'm',
				), array(
					'title'=> $photoTitle, 
					'alt'=> $photoTitle, 
				));

				$photoAction = $this->Html->tag('div', $this->Html->tag('div', $this->Form->input('CrmProjectDocument.is_share', array(
					'type' => 'checkbox',
					'label' => array(
						'text' => __('Share untuk Project Lain'),
						'for' => 'CrmProjectDocumentIsShare'.$photoId,
					),
					'id' => 'CrmProjectDocumentIsShare'.$photoId,
					'div' => false,
					'required' => false,
		            'hiddenField' => false,
		            'class' => 'share-file',
					'value' => 1,
					'rel' => $photoId,
				)), array(
					'class' => 'sharing-document cb-checkmark',
				)), array(
					'class' => 'cb-custom mt0',
				));

				$photoAction .=$this->Html->tag('div', $this->Form->input('CrmProjectDocument.title', array(
					'type' => 'text',
					'label' => false,
					'id' => 'CrmProjectDocumentTitle'.$photoId,
					'div' => false,
					'required' => false,
		            'hiddenField' => false,
					'class' => 'form-control change-file-title',
					'rel' => $photoId,
					'placeholder' => __('Masukan Judul Dokumen'),
				)), array(
					'class' => 'title-document',
				));

				$content .= '<li class="template-download fade col-sm-3 ajax-parent in no-hover centered" rel="'.$photoId.'">
					<div class="item">
						<div class="preview relative">
							'.$photo.'
						</div>
						<div class="action">
							'.$photoAction.'
						</div>
					</div>
	    		</li>';

				$idx++;
			}
		}

		echo '<form id="fileupload" action="'.Router::url('/', true).$url.'" method="POST" enctype="multipart/form-data">
	        <div class="fileupload-buttonbar">
	            <div class="span7 tacenter action-upload">
	                <div class="fileinput-button">
	                    <i class="icon-plus icon-white"></i>
	                    <a href="#" class="btn uploads '.$label_class.'">'.$label_input.'</a>
	                    <input type="file" name="data[files][]" multiple>
	                </div>
	            </div>
	        </div>
	        <div class="fileupload-loading"></div>
	        <br>
	        <ul class="files row row-centered" data-toggle="modal-gallery" data-target="#modal-gallery">'.$content.'</ul>
	    </form>
		<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd">
		    <div class="modal-header">
		        <a class="close" data-dismiss="modal">&times;</a>
		        <h3 class="modal-title"></h3>
		    </div>
		    <div class="modal-body"><div class="modal-image"></div></div>
		    <div class="modal-footer">
		        <a class="btn modal-download" target="_blank">
		            <i class="icon-download"></i>
		            <span>Download</span>
		        </a>
		        <a class="modal-play modal-slideshow" data-slideshow="5000">
		            <i class="icon-play icon-white"></i>
		            <span>Slideshow</span>
		        </a>
		        <a class="btn btn-info modal-prev">
		            <i class="icon-arrow-left icon-white"></i>
		            <span>Previous</span>
		        </a>
		        <a class="btn btn-primary modal-next">
		            <span>Next</span>
		            <i class="icon-arrow-right icon-white"></i>
		        </a>
		    </div>
		</div>
		';
	}

	private function _loadUserScripts($data) {
		echo '<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-upload fade">
		        {% if (file.error) { %}
		            <div class="error-full alert" colspan="2">{%=file.message%}</div>
		        {% } else if (o.files.valid && !i) { %}
		            <div>
		                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
		            </div>
		            <div class="start">{% if (!o.options.autoUpload) { %}
		                <button class="btn btn-primary">
		                    <i class="icon-upload icon-white"></i>
		                    <span>{%=locale.fileupload.start%}</span>
		                </button>
		            {% } %}</div>
		        {% } %}
		    </div>
		{% } %}
		</script>
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <li class="template-download ajax-parent" rel="{%=file.id%}">
				<div class="item">
			        {% if (file.error) { %}
			            <div class="error-full alert" colspan="2">{%=file.message%}</div>
			        {% } else { %}
			            {%=file.actions%}
			        {% } %}
				</div>
		    </li>
		{% } %}
		</script>';

	}

	private function _loadUserTemplate( $url = null, $data = false, $save_path = false, $options = false )
	{
		$label_input = $this->Rumahku->filterEmptyField($options, 'label', false, __('Tambah Foto'), false);
		$label_class = $this->Rumahku->filterEmptyField($options, 'label_class');

		echo '<form id="single-fileupload" action="'.$url.'" method="POST" enctype="multipart/form-data">
	        <div class="fileupload-buttonbar">
	            <div class="span7 tacenter action-upload">
	                <div class="fileinput-button">
	                    <i class="icon-plus icon-white"></i>
	                    <a href="#" class="btn uploads '.$label_class.'">'.$label_input.'</a>
	                    <input type="file" name="data[files][]">
	                </div>
	            </div>
	        </div>
	        <div class="fileupload-loading"></div>
	        <div class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></div>
	    </form>
		<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd">
		    <div class="modal-header">
		        <a class="close" data-dismiss="modal">&times;</a>
		        <h3 class="modal-title"></h3>
		    </div>
		    <div class="modal-body"><div class="modal-image"></div></div>
		    <div class="modal-footer">
		        <a class="btn modal-download" target="_blank">
		            <i class="icon-download"></i>
		            <span>Download</span>
		        </a>
		        <a class="modal-play modal-slideshow" data-slideshow="5000">
		            <i class="icon-play icon-white"></i>
		            <span>Slideshow</span>
		        </a>
		        <a class="btn btn-info modal-prev">
		            <i class="icon-arrow-left icon-white"></i>
		            <span>Previous</span>
		        </a>
		        <a class="btn btn-primary modal-next">
		            <span>Next</span>
		            <i class="icon-arrow-right icon-white"></i>
		        </a>
		    </div>
		</div>
		';
	}

	/**
	*	Load the scripts needed.
	* 	@access private
	* 	@return void
	*/
	private function _loadScripts($data, $options = false)
	{
		$msgDelete = __('Anda yakin ingin menghapus foto ini ?');
		$dataCategories = $this->Property->_callCategoryMedias();
		$categoryMedias = $this->Rumahku->filterEmptyField($dataCategories, 'categoryMedias');

		$session_id = $this->Rumahku->filterEmptyField($options, 'session_id');
    	$draft_id = Configure::read('__Site.PropertyDraft.id');     

		$photoAction = $this->Form->input('PropertyMedias.category_media_id.', array(
			'label' => false,
			'div' => array(
				'class' => 'form-group',
			),
			'class' => 'form-control disable-drag label-image',
			'required' => false,
		//	'title' => __('Label Foto'),
			'empty' => __('Pilih Label'),
			'options' => $categoryMedias,
		//	'data-size' => 'modal-md', 
		//	'data-wrapper-write' => '.content-upload-photo', 
			'data-url' => $this->Html->url(array(
				'controller' => 'ajax',
				'action' => 'property_photo_title',
				$session_id,
				'draft' => $draft_id,
				'admin' => false,
			)),
		));
		$photoOption = $this->Html->tag('div', $this->Form->input('PropertyMedias.options_id.', array(
			'type' => 'checkbox',
			'label' => array(
				'text' => __('Pilih Foto'),
				'data-show' => '.fly-button-media',
				'for' => 'PropertyMediasOptionsId{%=file.id%}',
			),
			'id' => 'PropertyMediasOptionsId{%=file.id%}',
			'div' => false,
			'required' => false,
            'hiddenField' => false,
			'value' => '{%=file.id%}',
			'class' => 'check-option',
		)), array(
			'class' => 'bottom cb-checkmark disable-drag',
		));

		echo '<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-upload fade col-sm-3">
		        <div class="preview relative"><span class="fade"></span></div>
		        {% if (file.error) { %}
		            <div class="error-full alert" colspan="2">{%=file.message%}</div>
		        {% } else if (o.files.valid && !i) { %}
		            <div>
		                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
		            </div>
		            <div class="start">{% if (!o.options.autoUpload) { %}
		                <button class="btn btn-primary">
		                    <i class="icon-upload icon-white"></i>
		                    <span>{%=locale.fileupload.start%}</span>
		                </button>
		            {% } %}</div>
		        {% } %}
		    </div>
		{% } %}
		</script>
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <li class="template-download col-sm-4 ajax-parent {% if (file.primary == 1) { %}disable-drag{% } %} " rel="{%=file.id%}">
				<div class="item">
			        {% if (file.error) { %}
			            <div class="error-full alert" colspan="2">{%=file.message%}</div>
			        {% } else { %}
			            <div class="preview relative">
			            	{% if (file.primary == 1) { %}
								<div class="property-primary-photo">
									<img class="default-thumbnail" src="{%=file.thumbnail_url%}">
								</div>
							{% } else { %}
								<img class="default-thumbnail" src="{%=file.thumbnail_url%}">
							{% } %}
							<div class="primary-file">
						        {% if (file.primary == 1) { %}
									<a href="javascript:" class="btn green primary">Foto Utama</a>
						        {% } else { %}
									<a href="{%=file.primary_url%}" class="btn default ajax-link disable-drag" data-alert="Anda yakin ingin menjadikan foto utama ?" data-wrapper-write-page="#file-drop-zone .content-upload-photo,.property-primary-photo">Jadikan Foto Utama</a>
						        {% } %}
							</div>
						</div>
						<div class="action cb-custom">
							'.$photoAction.'
					        {% if (file.primary != 1) { %}
								'.$photoOption.'
					        {% } %}
						</div>
			        {% } %}
				</div>
		    </li>
		{% } %}
		</script>';

	}
	
	private function _loadTemplate( $url = null, $data = false, $save_path = false, $options = false )
	{
		$content = '';
    	$draft_id = Configure::read('__Site.PropertyDraft.id');

		$id = $this->Rumahku->filterEmptyField($options, 'id');
		$session_id = $this->Rumahku->filterEmptyField($options, 'session_id');
		$label_input = $this->Rumahku->filterEmptyField($options, 'label', false, __('Tambah Foto'), false);
		$label_class = $this->Rumahku->filterEmptyField($options, 'label_class');

		$orderUrl = $this->Html->url(array(
			'controller' => 'ajax',
			'action' => 'property_photo_order',
			$session_id,
			$id,
			'draft' => $draft_id,
			'admin' => false,
		));

		if( !empty($data) ) {
			$idx = 0;

			foreach ($data as $key => $value) {
				$photoId = false;
				$photoSessionId = false;
				$photoPropertyId = false;
				$photoName = false;
				$photoTitle = false;
				$primary = false;
				$photoCategoryId = false;
				$categoryMedias = false;

				switch ($save_path) {
					case 'properties':
						$photoId = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'id');
						$photoCategoryId = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'category_media_id');
						$photoSessionId = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'session_id');
						$photoPropertyId = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'property_id');
						$photoName = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'name');
						$photoTitle = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'title');
						$primary = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'primary');

						$dataCategories = $this->Property->_callCategoryMedias($photoSessionId, $photoCategoryId, $photoTitle);
						$categoryMedias = $this->Rumahku->filterEmptyField($dataCategories, 'categoryMedias');
						$photoCategoryId = $this->Rumahku->filterEmptyField($dataCategories, 'category_id');
						break;
				}

				if( !empty($primary) ) {
					$contentPrimary = $this->Html->link(__('Foto Utama'), 'javascript:', array(
						'class' => 'btn green primary',
					));
				} else {
					$contentPrimary = $this->Html->link(__('Jadikan Foto Utama'), array(
						'controller' => 'ajax', 
						'action' => 'property_photo_primary', 
						$photoId,
						$photoSessionId,
						'draft' => $draft_id,
						'admin' => false,
					), array(
						'class' => 'btn default ajax-link disable-drag',
						'data-alert' => __('Anda yakin ingin menjadikan foto utama ?'),
						'data-type' => 'content',
						'data-wrapper-write-page' => '#file-drop-zone .content-upload-photo,.property-primary-photo', 
						'escape' => false,
					));
				}

				$photo = $this->Rumahku->photo_thumbnail(array(
					'save_path' => $save_path, 
					'src' => $photoName, 
					'size' => 'm',
				), array(
					'title'=> $photoTitle, 
					'alt'=> $photoTitle, 
					'class' => 'default-thumbnail',
				));
				// $photoDeleteLink = $this->Html->link($this->Rumahku->icon('times'), array(
				// 	'controller' => 'ajax', 
				// 	'action' => 'property_photo_delete', 
				// 	$photoId,
				// 	$photoSessionId,
				// 	'admin' => false,
				// ), array(
				// 	'class' => 'btn btn-danger btn-xs ajax-link',
				// 	'data-alert' => __('Anda yakin ingin menghapus foto ini ?'),
				// 	'data-type' => 'media-delete',
				// 	'escape' => false,
				// ));

				$photoAction = $this->Form->input('PropertyMedias.category_media_id.'.$idx, array(
					'label' => false,
					'div' => array(
						'class' => 'form-group',
					),
					'title' => __('Label Foto'),
					'class' => 'form-control disable-drag label-image',
					'required' => false,
					'empty' => __('Pilih Label'),
					'options' => $categoryMedias,
					'data-size' => 'modal-md', 
				//	'data-wrapper-write' => '.content-upload-photo', 
					'data-url' => $this->Html->url(array(
						'controller' => 'ajax',
						'action' => 'property_photo_title',
						$photoSessionId,
						'draft' => $draft_id,
						'admin' => false,
					)),
					'value' => $photoCategoryId,
				));

				if(!$primary){
					$photoAction .= $this->Html->tag('div', $this->Form->input('PropertyMedias.options_id.'.$idx, array(
						'type' => 'checkbox',
						'label' => array(
							'text' => __('Pilih Foto'),
							'data-show' => '.fly-button-media',
						),
						'div' => false,
						'required' => false,
	                    'hiddenField' => false,
						'value' => $photoId,
						'class' => 'check-option',
					)), array(
						'class' => 'bottom cb-checkmark disable-drag',
					));
					$addClass = '';
				} else {
					$addClass = 'disable-drag';

				//	untuk primary photo kasih wrapper
					$photo = $this->Html->tag('div', $photo, array(
						'class' => 'property-primary-photo', 
					));
				}

				$content .= '<li class="template-download fade col-sm-4 ajax-parent in '.$addClass.'" rel="'.$photoId.'">
					<div class="item">
						<div class="preview relative">
							'.$photo.'
							<div class="primary-file">
								'.$contentPrimary.'
							</div>
						</div>
						<div class="action cb-custom">
							'.$photoAction.'
						</div>
					</div>
	    		</li>';

				$idx++;
			}
		}

		echo '<form id="fileupload" action="'.Router::url('/', true).$url.'" method="POST" enctype="multipart/form-data">
	        <ul class="files row drag" data-toggle="modal-gallery" data-target="#modal-gallery" data-url="'.$orderUrl.'">'.$content.'</ul>
	        <div class="fileupload-buttonbar">
	            <div class="span7 tacenter action-upload">
	                <div class="fileinput-button">
	                    <i class="icon-plus icon-white"></i>
	                    <a href="#" class="btn uploads '.$label_class.'">'.$label_input.'</a>
	                    <input type="file" name="data[files][]" multiple>
	                </div>
	            </div>
	        </div>
	        <div class="fileupload-loading"></div>
	    </form>
		<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd">
		    <div class="modal-header">
		        <a class="close" data-dismiss="modal">&times;</a>
		        <h3 class="modal-title"></h3>
		    </div>
		    <div class="modal-body"><div class="modal-image"></div></div>
		    <div class="modal-footer">
		        <a class="btn modal-download" target="_blank">
		            <i class="icon-download"></i>
		            <span>Download</span>
		        </a>
		        <a class="modal-play modal-slideshow" data-slideshow="5000">
		            <i class="icon-play icon-white"></i>
		            <span>Slideshow</span>
		        </a>
		        <a class="btn btn-info modal-prev">
		            <i class="icon-arrow-left icon-white"></i>
		            <span>Previous</span>
		        </a>
		        <a class="btn btn-primary modal-next">
		            <span>Next</span>
		            <i class="icon-arrow-right icon-white"></i>
		        </a>
		    </div>
		</div>
		';
	}

	public function loadCrm( $url, $data = false, $save_path = false, $options = false ) {
		$this->_loadCrmScripts();
		$this->_loadCrmTemplate( $url, $data, $save_path, $options);
	}

	private function _loadCrmScripts()
	{
		$msgDelete = __('Anda yakin ingin menghapus dokumen ini ?');

		echo '<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-upload fade">
		        {% if (file.error) { %}
		            <div class="error-full alert">{%=file.message%}</div>
		        {% } else if (o.files.valid && !i) { %}
		            <div>
		                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
		            </div>
		        {% } %}
		    </div>
		{% } %}
		</script>
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <li class="template-download ajax-parent" rel="{%=file.id%}">
		        {% if (file.error) { %}
		            <div class="error-full alert" colspan="2">{%=file.message%}</div>
		        {% } else { %}
		            <div class="preview relative">
						{%=file.name%}
					</div>
					<div class="action">
						<a class="document-delete ajax-link" href="{%=file.delete_url%}" data-remove=".ajax-parent[rel=\'{%=file.id%}\']" data-alert="Anda yakin ingin menghapus dokumen ini?">
				            <i class="rv4-bold-cross"></i>
				        </a>
					</div>
		        {% } %}
		    </li>
		{% } %}
		</script>';

	}
	
	private function _loadCrmTemplate( $url = null, $data = false, $save_path = false, $options = false )
	{
		$content = '';
		$activity_id = $this->Rumahku->filterEmptyField($options, 'activity_id');
		$label_input = $this->Rumahku->filterEmptyField($options, 'label', false, __('Tambah Foto'), false);
		$label_class = $this->Rumahku->filterEmptyField($options, 'label_class');

		if( !empty($data) ) {
			$idx = 0;

			foreach ($data as $key => $value) {
				$fileId = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'id');
				$fileSessionId = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'session_id');
				$fileName = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'name');

				$urlLink = $this->Html->link($this->Rumahku->icon('rv4-bold-cross'), array(
					'controller' => 'ajax',
					'action' => 'document_delete',
					$fileId,
					$fileSessionId,
					'admin' => true,
				), array(
					'escape' => false,
					'class' => 'document-delete ajax-link',
					'data-alert' => __('Anda yakin ingin menghapus dokumen ini?'),
					'data-remove' => '.ajax-parent[rel=\''.$fileId.'\']',
				));

				$content .= '<li class="template-download ajax-parent">
					<div class="preview relative">
						'.$fileName.'
					</div>
					<div class="action">
						'.$urlLink.'
					</div>
	    		</li>';

				$idx++;
			}
		}

		echo '<form id="fileupload" class="crm-files" action="'.Router::url('/', true).$url.'" method="POST" enctype="multipart/form-data">
	        <div class="fileupload-buttonbar">
	            <div class="span7 tacenter action-upload">
	                <div class="fileinput-button">
	                    <i class="icon-plus icon-white"></i>
	                    <a href="#" class="btn uploads '.$label_class.'">'.$label_input.'</a>
	                    <input type="file" name="data[files][]" multiple>
	                </div>
	            </div>
	        </div>
	        <div class="fileupload-loading"></div>
	        <ul class="files mt15" data-toggle="modal-gallery" data-target="#modal-gallery">'.$content.'</ul>
	    </form>
		<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd">
		    <div class="modal-header">
		        <a class="close" data-dismiss="modal">&times;</a>
		        <h3 class="modal-title"></h3>
		    </div>
		    <div class="modal-body"><div class="modal-image"></div></div>
		    <div class="modal-footer">
		        <a class="btn modal-download" target="_blank">
		            <i class="icon-download"></i>
		            <span>Download</span>
		        </a>
		        <a class="modal-play modal-slideshow" data-slideshow="5000">
		            <i class="icon-play icon-white"></i>
		            <span>Slideshow</span>
		        </a>
		        <a class="btn btn-info modal-prev">
		            <i class="icon-arrow-left icon-white"></i>
		            <span>Previous</span>
		        </a>
		        <a class="btn btn-primary modal-next">
		            <span>Next</span>
		            <i class="icon-arrow-right icon-white"></i>
		        </a>
		    </div>
		</div>
		';
	}

	/**
	*	Load the scripts needed.
	* 	@access private
	* 	@return void
	*/
	public function loadContact( $url, $data = false, $save_path = false, $options = false ) {
		$this->_loadContactScripts($data);
		$this->_loadContactTemplate($url, $data, $save_path, $options);
	}

	private function _loadContactScripts($data)
	{
		echo '<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-upload fade col-sm-12">
		        <div class="preview relative"><span class="fade"></span></div>
		        {% if (file.error) { %}
		            <div class="error-full alert" colspan="2">{%=file.message%}</div>
		        {% } else if (o.files.valid && !i) { %}
		            <div>
		                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
		            </div>
		        {% } %}
		    </div>
		{% } %}
		</script>
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    	<div class="item">
			        {% if (file.error) { %}
			            <div class="error-full alert" colspan="2">{%=file.message%}</div>
			        {% } else { %}
			            <div class="preview relative">
							<img style="width:100%" src="{%=file.thumbnail_url%}">
						</div>
			        {% } %}
				</div>
				<input type="hidden" class="temp-form-field" name="data[Contact][photo]" value="{%=file.path%}" />
		{% } %}
		</script>';
	}

	private function _loadContactTemplate( $url = null, $data = false, $save_path = false, $options = false )
	{
		$label_input = $this->Rumahku->filterEmptyField($options, 'label', false, __('Tambah Foto'), false);
		$label_class = $this->Rumahku->filterEmptyField($options, 'label_class');

		echo '<form id="single-fileupload" action="'.$url.'" method="POST" enctype="multipart/form-data">
	        <div class="fileupload-buttonbar hide">
	            <div class="span7 tacenter action-upload">
	                <div class="fileinput-button">
	                    <i class="icon-plus icon-white"></i>
	                    <a href="#" class="btn uploads '.$label_class.'">'.$label_input.'</a>
	                    <input type="file" name="data[files][]" multiple>
	                </div>
	            </div>
	        </div>
	        <div class="fileupload-loading"></div>
	        <ul class="files row row-centered" data-toggle="modal-gallery" data-target="#modal-gallery"></ul>
	    </form>
		';
	}

	public function loadCustom( $url = null, $options = false )
	{
		if( !empty($url) && is_array($url) ) {
			$url = $this->Html->url($url);
		}

		$this->_loadScriptsloadCustom($url, $options);
		$this->_loadTemplateloadCustom( $url, $options );
		
	}

	private function _loadScriptsloadCustom($data, $options = false, $template = false){
		echo $this->_View->element('script', array(
			'options' => $options,
		), array(
			'plugin' => 'FileUpload',
		));
	}
	
	private function _loadTemplateloadCustom( $url = null, $options = false ){
		echo $this->_View->element('template', array(
			'url' 		=> $url, 
			'options' 	=> $options,
		), array(
			'plugin' => 'FileUpload',
		));
	}

	/**
	*	Load external JS files needed.
	* 	@access private
	* 	@return void
	*/
	private function _loadExternalJsFiles()
	{
		echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/vendor/jquery.ui.widget.js"></script>
		<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
		<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
		<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
		<script src="http://blueimp.github.com/cdn/js/bootstrap.min.js"></script>
		<script src="http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.iframe-transport.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.fileupload.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.fileupload-fp.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.fileupload-ui.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/locale.js"></script>
		<script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/main.js"></script>';	
	}

}
?>