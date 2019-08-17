<?php
		echo '<option value="">'.$title.'</option>'."\n";
		
		if(!empty($output)) {
			foreach($output as $k => $v) {
				echo '<option value="'.$k.'">'.$this->Rumahku->safeTagPrint($v).'</option>'."\n";
			}
		}
?>
