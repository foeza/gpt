<?php 
		$_breadcrumb = isset($_breadcrumb)?$_breadcrumb:true;
    	$pageCount = $exportBtn = false;

?>
<div class="hidden-print">
<?php 
		if( !empty($module_title) ) {
			if( !empty($_breadcrumb) ) {
	        	$pageCount = $this->Paginator->counter(array('format' => '%count%'));

				if( !empty($pageCount) ) {
			        $pageCount = $this->Rumahku->getFormatPrice($pageCount);
			        $pageCount = $this->Html->tag('label', sprintf('%s %s Data', $this->Rumahku->icon('rv4-list'), $pageCount), array(
			            'class' => 'datatable-count'
			        ));
			    } else {
			    	$pageCount = false;
			    }
			} else {
		    	$pageCount = false;
		    }

		    if(!empty($export)){
		    	if( !empty($export['url']) ) {
			    	$exportBtn = $this->Rumahku->generateButtonExport($export);
			    } else if( is_array($export) ) {
			    	foreach ($export as $key => $ex) {
			    		$exportBtn .= $this->Rumahku->generateButtonExport($ex);
			    	}
			    }
		    }
		    
			echo $this->Html->tag('div', $this->Html->tag('h2', $module_title).$pageCount.$exportBtn, array(
				'id' => 'crumbtitle',
				'class' => 'clear',
			));
		}
?>
</div>