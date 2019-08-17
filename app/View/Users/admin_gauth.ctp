<?php

	$result		= empty($result) ? array() : $result;
	$redirect	= Common::hashEmptyField($result, 'redirect');

?>
<script type="text/javascript">
	var objParent = window.opener;
	objParent.document.location = '<?php echo($redirect); ?>';
	window.close();
</script>