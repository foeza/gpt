<title>
	<?php 
			if( !empty($title_for_layout) ){
				echo $title_for_layout;
			}else{
				if(!empty($web_config['meta_title'])){
					echo $this->Rumahku->safeTagPrint($web_config['meta_title']);
				}
			}
	?>
</title>
<?php
		if(!empty($web_config['meta_title'])){
			echo $this->Html->meta('title', $this->Rumahku->safeTagPrint($web_config['meta_title']));
		}

		if(!empty($web_config['meta_description'])){
			echo $this->Html->meta('description', $this->Rumahku->safeTagPrint($web_config['meta_description']));	
		}
		
		if(!empty($web_config['meta_keyword'])){
			echo $this->Html->meta('keywords', $this->Rumahku->safeTagPrint($web_config['meta_keyword']));
		}
?>