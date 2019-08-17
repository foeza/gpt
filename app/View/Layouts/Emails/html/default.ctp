<?php 

	$params		= empty($params) ? null : $params;
	$subject	= $this->Rumahku->filterEmptyField($params, 'subject');
	$withGreet	= isset($params['with_greet']) ? $params['with_greet'] : true;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<meta name="viewport" content="width:device-width">
		<meta property="og:title" content="<?php echo($subject);?>">
		<title><?php echo($subject);?></title>
		<style type="text/css">
			body {
				margin: 0; 
				padding: 0;
			}
		</style>
	</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="font-family: Helvetica, Sans-Serif; background: #FFFFFF;">
		<tbody>
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: auto; padding: 30px 0; font-size: 14px;" >
						<tbody>
							<?php echo($this->element('headers/email/header')); ?>
							<tr>
								<td>
									<table style="background: #F5F5F5;" width="100%" cellspacing="0" cellpadding="0" border="0">
										<tbody>
											<tr>
												<td>
													<table style="padding: 15px 30px 30px; margin: auto; " width="600px;" cellspacing="0" cellpadding="0" border="0">
														<tbody>
															<?php
																	$name = $this->Rumahku->filterEmptyField($params, 'name', false, false, true, 'ucwords');

																	if(!empty($withGreet) && !empty($name)):
															?>
															<tr>
																<td style="padding: 30px 0 20px; text-align: center;">
																	<?php
																			$greet	= __('Hai, %s!', $name);

																			echo($this->Html->tag('p', trim($greet), array(
																				'style' => 'margin: 0; font-size: 18px; color: #434343;', 
																			)));

																	?>
																</td>
															</tr>
															<?php endif; ?>

															<tr>
																<td><?php echo($content_for_layout); ?></td>
															</tr>
														<tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<?php echo($this->element('footers/email/footer')); ?>
				</td>
			</tr>
		</tbody>
	</table>

</body>
</html>
<?php

	$debug = $this->Rumahku->filterEmptyField($params, 'debug');
	if($debug == 'view'){
		exit();
	}

?>