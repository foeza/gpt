<?php
		if( !empty($_canonical) ) {

			if( empty($_canonical_url) ) {
				$_canonical_url = FULL_BASE_URL.$this->here;
			} else {
				$_canonical_url = $this->Html->url($_canonical_url, true);
			}

			printf('<link rel="canonical" href="%s" />' . PHP_EOL, $_canonical_url);
		}
?>