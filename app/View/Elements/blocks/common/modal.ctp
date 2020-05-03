<?php 
		$prefix = Configure::read('App.prefix');
?>
<div id="myModal" class="modal fade">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<a href="" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true"><i class="rv4-bold-cross"></i></span></a>
				<h4 id="openModalLabel" class="modal-title">Modal Title</h4>
			</div>
			<div class="modal-body">
				<div class="modal-subheader">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, in.</p>
				</div>
				<div class="content">
					<form action="">
						<div class="form-group special">
							<input type="text" id="form1">
							<label for="form1">Input Text Label</label>
						</div>
						<div class="form-group at-modal">
							<div class="row">
								<div class="col-sm-2">
									<label for="form2">Label #1</label>
								</div>
								<div class="col-sm-10">
									<input type="text" id="form2">
								</div>
							</div>
						</div>
						<div class="form-group at-modal">
							<div class="row">
								<div class="col-sm-2">
									<label for="form3">Label #2</label>
								</div>
								<div class="col-sm-10">
									<input type="text" id="form3">
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<a href="" class="btn default">Batal</a>
					<a href="" class="btn blue">Lanjut</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
		// if( $prefix == 'admin' ) {
  //       	echo $this->element('blocks/common/modals/tour');
  //       }

  //       echo $this->element('blocks/common/modal/modal_booking');
?>