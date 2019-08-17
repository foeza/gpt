<?php
        $separator = !empty($separator) ? $separator : '-';
        $ext = $this->Rumahku->_callGetExt($filepath);
        $content_type = $this->Rumahku->_getContentType($ext);
        $filename = empty($basename) ? String::uuid() : $this->Rumahku->toSlug($basename, $separator);

        header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Type: '.$content_type);
        header("Content-disposition: attachment; filename=\"" . $filename.".".$ext . "\""); 
	header('Content-Transfer-Encoding: binary');
        readfile($filepath);
        exit();
?>
