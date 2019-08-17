<?php

	$debugMode	= Configure::read('debug');
	$error		= empty($error) ? null : $error;
	$code		= empty($code) ? '' : $code;
	$message	= empty($message) ? '' : $message;

	if($error){
		$code		= empty($code) ? $error->getCode() : $code;
		$message	= empty($message) ? $error->getMessage() : $message;
	}

	if($debugMode){
		echo($this->Html->tag('h4', __('%s<br>File %s<br> Line %s', $message, h($error->getFile()), h($error->getLine())), array(
			'class' => 'error-handler-title', 
		)));

		echo($this->Html->tag('p', __('Ups, Sepertinya terjadi kesalahan. %s', $code ? sprintf('(%s)', $code) : null), array(
			'class' => 'error-handler-message', 
		)));

		if(empty($error->queryString) === false){
			$queryError = Common::formatSql($error->queryString);

			echo($this->Html->tag('div', $queryError, array(
				'class' => 'sql-formatter-placeholder', 
			)));

		//	versi google formatter (ini ga otomatis enter, dikasih oneliner display nya onliner semua)
		//	echo($this->Html->tag('pre', $this->Html->tag('code', $error->queryString, array(
		//		'class' => 'lang-sql', 
		//	)), array(
		//		'class' => 'prettyprint', 
		//	)));
		}

		echo($this->element('exception_stack_trace'));
	}
	else{

		?>
		<div class="container-fluid error-handler-wrapper">
			<div class="row">
				<div class="col-sm-12">
					<?php

						$isError400 = substr($code, 0, 1)  == 4;

						if($isError400){
							$image = '404.gif';
							$title = 'Ups, halaman yang Anda minta tidak ditemukan.';
						}
						else{
							$image = 'error_page.png';
							$title = 'Ups, Sepertinya terjadi kesalahan.';
						}

						echo($this->Html->image($image, array(
							'alt'	=> '404',
							'class'	=> 'error-handler-image', 
							'width'	=> '100%', 
						)));

						echo($this->Html->tag('h4', __($title), array(
							'class' => 'error-handler-title', 
						)));

					//	if(empty($isError400) && $message){
					//		echo($this->Html->tag('p', __($message), array(
					//			'class' => 'error-handler-message', 
					//		)));
					//	}

						$supportEmail = Configure::read('__Site.company_profile.email');

						if($supportEmail){
							$supportEmail = $this->Html->link($supportEmail, sprintf('mailto:%s', $supportEmail));

							echo($this->Html->tag('p', __('Untuk bantuan lebih lanjut, silakan hubungi kami melalui %s.', $supportEmail), array(
								'class' => 'error-handler-message', 
							)));
						}

					?>
				</div>
			</div>
		</div>
		<?php

	}

?>