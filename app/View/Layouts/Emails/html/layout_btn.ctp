<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<meta name="viewport" content="width=device-width"/>
  	</head>

	<body style="background: #f0f0f0; font-family: Helvetica, Arial, sans-serif;">
		<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
			<tbody>
			  	<tr>
			    	<td>
			    		<?php
			    			echo $this->Html->image('/img/email/btn/head.jpg', array(
								'fullBase' => true,
								'style' => 'width:600px'
							))
			    		?>
			    	</td>
			  	</tr>
			</tbody>
		</table>

		<?php
  			echo $content_for_layout;
  		?>

		<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
		  	<tbody>
			  	<tr style="background: #f5f5f5;">
			    	<td align="center" style="padding: 15px 0">
			      		<?php
			    			echo $this->Html->image('/img/email/btn/prime-logo.png', array(
								'fullBase' => true,
							));
			    		?>
			    	</td>
			  	</tr>
			</tbody>
		</table>
	</body>
</html>
<?php 
	if( isset($params['debug']) && $params['debug'] == 'view' ){
		die();
	}
?>