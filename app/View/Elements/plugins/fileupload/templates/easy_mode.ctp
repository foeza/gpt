<?php

	$savePath	= Configure::read('__Site.property_photo_folder');
	$options	= empty($options) ? array() : $options;
	$record		= empty($record) ? array() : $record;
	$record     = Common::hashEmptyField($options, 'record', $record);
	$photo		= Common::hashEmptyField($record, 'Property.photo');

?>
<form id="single-fileupload" action="<?php echo $url; ?>" method="POST" enctype="multipart/form-data">
	<div class="user-thumb relative tacenter">
		<?php 

			echo $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
				'save_path'	=> $savePath, 
				'src'		=> $photo, 
				'size'		=> 'm',
			), array(
				'alt' => 'property-thumbnail',
				'class' => 'info-upload-photo',
			)), array(
				'class' => 'files',
			));

			echo($this->Html->tag('div', '&nbsp;', array(
				'class' => 'change-photo pick-file',
			)));

		?>
	</div>
    <div class="fileupload-buttonbar">
        <div class="span7 tacenter action-upload">
            <div class="fileinput-button">
                <i class="icon-plus icon-white"></i>
                <a href="#" class="btn uploads"><?php echo $this->Rumahku->icon('rv4-cam-2'); ?></a>
                <input type="file" name="data[files][]">
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