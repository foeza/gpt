<?php
		$value = !empty($value) ? $value : false;

		$options = !empty($options) ? $options : array();
		$attributes = !empty($attributes) ? $attributes : array();

		$cnt_pending = Common::hashEmptyField($value, 'cnt_pending', 0);	
		$cnt_rejected = Common::hashEmptyField($value, 'cnt_rejected', 0);	
		$cnt_process = Common::hashEmptyField($value, 'cnt_process', 0);
		$cnt_approved = Common::hashEmptyField($value, 'cnt_approved', 0);
		$cnt_approved_credit = Common::hashEmptyField($value, 'cnt_approved_credit', 0);
		$cnt_completed = Common::hashEmptyField($value, 'cnt_completed', 0);

		$noRow = empty($noRow) ? 'row' : '';

		$link_artikel = array(
			'controller' => 'blogs',
			'action' => 'index',
			'admin' => true,
		);

		$link_banner = array(
			'controller' => 'pages',
			'action' => 'slides',
			'admin' => true,
		);

?>
<div class="<?php echo $noRow; ?>">
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_completed, array(
				'class' => 'label',
			));
			
			$report_view = $this->Html->tag( 'h3', __('Artikel'), array(
				'class' => 'color-green'
			));
			echo $this->Html->link( $report_view, $link_artikel, array(
				'escape' => false,
			));
		?>
	</div>
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_approved_credit, array(
				'class' => 'label',
			));
			
			$report_view = $this->Html->tag( 'h3', __('Banner'), array(
				'class' => 'color-purple'
			));
			echo $this->Html->link( $report_view, $link_banner, array(
				'escape' => false,
			));
		?>
	</div>
</div>