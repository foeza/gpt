<?php
		$params = !empty($params)?$params:false;

		echo '<script id="template-upload" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-upload fade">
		        {% if (file.error) { %}
		            <div class="error-full alert" colspan="2">{%=file.message%}</div>
		        {% } else if (o.files.valid && !i) { %}
		            <div>
		                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
		            </div>
		            <div class="start">{% if (!o.options.autoUpload) { %}
		                <button class="btn btn-primary">
		                    <i class="icon-upload icon-white"></i>
		                    <span>{%=locale.fileupload.start%}</span>
		                </button>
		            {% } %}</div>
		        {% } %}
		    </div>
		{% } %}
		</script>
		<script id="template-download" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		    <div class="template-download">
				<div class="item">
			        {% if (file.error) { %}
			            <div class="error-full alert" colspan="2">{%=file.message%}</div>
			        {% } else { %}
			        	<img src="{%=file.thumbnail_url%}">
			        	<input name="data[Property][photo_id]" type="hidden" value="{%=file.id%}">
			        	<input name="data[Property][photo]" type="hidden" value="{%=file.thumbnail_url%}">
			        {% } %}
				</div>
		    </div>
		{% } %}
		</script>';
?>