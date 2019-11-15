<div id="modal-booking" class="modal fade credentialID">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-image-supp">
				<?php
						// echo $this->Html->image('https://images.unsplash.com/photo-1476891626313-2cecb3820a69?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=0851277fe9818394dcfcfd67c5c7a4b0&auto=format&fit=crop&w=608&q=80');
				?>
			</div>

			<div class="modal-form-wrapper">
				<div class="modal-header">
					<?php
							echo $this->Html->tag('button', '&times;', array(
								'type' => "button",
								'class' => "close",
								'data-dismiss' => "modal",
								'aria-hidden' => "true"
							));

							echo $this->Html->tag('h4', __('Masukkan data diri Anda.'), array(
								'class' => "modal-title",
							));
					?>
				</div>
				
				<div class="modal-body">
						
				</div>
			</div>
        </div>
    </div>
</div>