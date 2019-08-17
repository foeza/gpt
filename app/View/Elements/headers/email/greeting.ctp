<?php 
		$name = $this->Rumahku->filterEmptyField($params, 'name');

		$with_greet = isset($params['with_greet']) ? $params['with_greet'] : true;

		if($with_greet){
?>
<tr>
	<td align="left" valign="top">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tbody>
				<tr>
					<td>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr>
									<td align="left" valign="top">
										<?php 
												if(!empty($name)) {
													$greeting = sprintf(__('Hai %s'), $name);
												} else {
													$greeting = __('Hai');
												}

												echo $this->Html->tag('h2', $greeting, array(
													'style' => 'margin: 20px 0 0; font-size: 21px; padding:0px;',
												));
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<?php
		}
?>