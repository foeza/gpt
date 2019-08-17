<?php
        echo $this->element('blocks/common/forms/action_custom', array(
			'_float_class' => 'floleft',
    	));

        $text_progress = '-';
        if( $progress_status == 0 ) {
        	$text_progress = __('Laporan tidak diproses dikarenakan data tidak ditemukan');
        } else if( $progress_status == 1 ) {
        	$text_progress = __('Laporan sedang di antrikan');
        } else if( $progress_status == 2 ) {
        	$text_progress = __('Laporan sedang di proses');
        } else if( $progress_status == 3 ) {
        	$text_progress = __('Berhasil di generate');
        }
?>

<div id="big-wrapper-write">
	<div class="row mt20">
		<div class="col-sm-9">
			<?php
					if( !empty($periods) ) {
						echo $this->Html->tag('p', sprintf('Periode : %s', $periods), array(
							'class' => 'mb10',
						));
					}

					// if( !in_array($report_type_id, array(2,3)) ) {
						echo $this->element('blocks/properties/report/report_include_exclude', array(
							'title' => __('Filter Umum'),
							'filter_values' => $general_filter,
						));
					// }
					
					echo $this->element('blocks/properties/report/report_include_exclude', array(
						'title' => __('Include Filter'),
						'filter_values' => $include,
					));
					echo $this->element('blocks/properties/report/report_include_exclude', array(
						'title' => __('Exclude Filter'),
						'filter_values' => $exclude,
					));
			?>
		</div>
		<div class="col-sm-3">
			<?php
					echo $this->Html->tag('h2', __('Total Data'), array(
						'class' => 'mb10',
					));

					echo $this->Html->tag('h3', $cnt, array(
						'class' => 'mb10'
					));

					echo $this->Html->tag('h2', __('Status Laporan'), array(
						'class' => 'mb10',
					));

					echo $this->Html->tag('h4', $text_progress);

					if( $progress_status == 3 ) {
						echo $this->Html->link(__('Unduh'), array(
	      					'controller' => 'settings',
	      					'action' => 'download',
	      					'report',
	      					$id,
	      					'path',
	      					'admin' => true,
	  					), array(
	  						'title' => __('Download'),
	  					));
					}
			?>
		</div>
	</div>
</div>