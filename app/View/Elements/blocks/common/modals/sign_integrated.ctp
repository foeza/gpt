<?php
		$linkRegister = array(
			'controller' => 'users',
			'action' => 'register_integration',
			'admin' => true,
		);
?>
<div data-role="modal-sign-integrated">
	<div id="wrapper-modal-sign-integrated" class="sign-integrated">

		<div class="item">
			<div class="row">
				<div class="col-md-6 col-sm-12">
					<div class="wrapper-content">
						<img src="/img/tours/zoom-contact.png" class="img-default">
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
	    			<div class="tacenter wrapper-content">
	    				<div class="head-title">
			    			<h2 class="mb20">Integrasikan properti Anda dengan Rumah 123</h2>
			    			<div class="desc mb10">
			    				<p>Untuk informasi & bantuan silakan hubungi:</p>
			    				<?php 
			    						echo $this->Html->tag('p', __('%s (WA)', Configure::read('__Site.company_profile.phone2')));
			    						echo $this->Html->tag('p', Configure::read('__Site.company_profile.email'));
			    				?>
		    				</div>
		    				<?php
		    						echo $this->Html->link(__('Daftar sekarang'), $linkRegister, array(
		    							'class' => 'btn blue',
		    						));
		    				?>
		    			</div>
	      			</div>
				</div>
			</div>
		</div>
  	</div>
</div>