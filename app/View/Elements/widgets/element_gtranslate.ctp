<?php
		// reference code language [en,zh-CN,de,ja,es,fr,it,ko,nl,pt,ru]
		// here: https://cloud.google.com/translate/docs/languages
?>
<div class="wrapper-google-translate">
	<div class="container">
		<div class="ul-wrapper-translate list-inline ct-topbar__list">
			<div id="google_translate_element"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement(
  	{
  		pageLanguage: 'id',
  		includedLanguages: 'en,zh-CN,de,ko',
  		layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  	}, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
