<?php
		$value = !empty($value) ? $value : false;

		// $controller = Common::hashEmptyField($options, 'controller');
		// $action = Common::hashEmptyField($options, 'action');

		$options = !empty($options) ? $options : array();
		$attributes = !empty($attributes) ? $attributes : array();

		$cnt_pending = Common::hashEmptyField($value, 'cnt_pending', 0);	
		$cnt_rejected = Common::hashEmptyField($value, 'cnt_rejected', 0);	
		$cnt_process = Common::hashEmptyField($value, 'cnt_process', 0);
		$cnt_approved = Common::hashEmptyField($value, 'cnt_approved', 0);
		$cnt_approved_credit = Common::hashEmptyField($value, 'cnt_approved_credit', 0);
		$cnt_completed = Common::hashEmptyField($value, 'cnt_completed', 0);

		$noRow = empty($noRow) ? 'row' : '';
?>
<div class="<?php echo $noRow; ?>">
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_completed, array(
				'class' => 'label',
			));
			
			$report_view = $this->Html->tag( 'h3', __('Completed'), array(
				'class' => 'color-green'
			));
			echo $this->Html->link( $report_view, array_merge($options, array(
				'status' => 'completed',
				'admin' => true,
			)), array_merge($attributes, array(
				'escape' => false,
			)));
		?>
	</div>
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_approved_credit, array(
				'class' => 'label',
			));
			
			$report_view = $this->Html->tag( 'h3', __('Perjanjian Kredit'), array(
				'class' => 'color-purple'
			));
			echo $this->Html->link( $report_view, array_merge($options, array(
				'status' => 'approved_credit',
				'admin' => true,
			)), array_merge($attributes, array(
				'escape' => false,
			)));
		?>
	</div>
</div>
<div class="<?php echo $noRow; ?>">
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_approved, array(
				'class' => 'label',
			));
			$report_view = $this->Html->tag( 'h3', __('Appraisal'), array(
				'class' => 'color-blue'
			));
			echo $this->Html->link( $report_view, array_merge($options, array(
				'status' => 'approved',
				'admin' => true,
			)), array_merge($attributes, array(
				'escape' => false,
			)));
		?>
	</div>
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_process, array(
				'class' => 'label',
			));
			$report_view = $this->Html->tag( 'h3', __('Proses'), array(
				'class' => 'color-orange'
			));
			echo $this->Html->link( $report_view, array_merge($options, array(
				'status' => 'process',
				'admin' => true,
			)), array_merge($attributes, array(
				'escape' => false,
			)));
		?>
	</div>
</div>
<div class="<?php echo $noRow; ?>">
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_rejected, array(
				'class' => 'label',
			));
			$report_view = $this->Html->tag( 'h3', __('Ditolak/Cancel'), array(
				'class' => 'color-red'
			));
			echo $this->Html->link( $report_view, array_merge($options, array(
				'status' => 'rejected',
				'admin' => true,
			)), array_merge($attributes, array(
				'escape' => false,
			)));
		?>
	</div>
	<div class="col-sm-12 col-lg-6 tacenter report-dashboard-kpr quick-data">
		<?php
			echo $this->Html->tag('label', $cnt_pending, array(
				'class' => 'label',
			));
			$report_view = $this->Html->tag( 'h3', __('Pending'), array(
				'class' => 'color-grey'
			));
			echo $this->Html->link( $report_view, array_merge($options, array(
				'status' => 'pending',
				'admin' => true,
			)), array_merge($attributes, array(
				'escape' => false,
			)));
		?>
	</div>
</div>