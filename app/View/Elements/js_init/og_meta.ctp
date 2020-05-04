
<meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" name="viewport" />
<meta content="follow, index" name="robots" />
<link rel="canonical" href="<?php echo FULL_BASE_URL.$this->here; ?>" />
<?php 

		$company_name	= Common::hashEmptyField($dataCompany, 'UserCompany.name');
		$meta_title		= Common::hashEmptyField($_config, 'UserCompanyConfig.meta_title', $company_name);
		$meta_desc		= Common::hashEmptyField($_config, 'UserCompanyConfig.meta_description');

		$meta_title 	= !empty($title_for_layout)?$title_for_layout:$meta_title;

		$meta_add = '';
		$break 	  = "\n";

		$og_meta = empty($og_meta) ? array() : $og_meta;

		echo $this->Html->charset('UTF-8') . PHP_EOL;
		echo $this->Html->tag('title', $meta_title) . PHP_EOL;
		echo $this->Html->meta('description', $meta_desc) . PHP_EOL;

		if( !empty($og_meta) ) {
			$og_desc = Hash::get($og_meta, 'description');
			$og_desc = urldecode($og_desc);
			$og_desc = $this->Rumahku->safeTagPrint($og_desc);
			$og_desc = trim($this->Text->truncate($og_desc, 320, array(
				'ending'	=> '',
				'exact'		=> false, 
			)));
			$og_title 	= Hash::get($og_meta, 'title');
			$og_title 	= htmlspecialchars($og_title);

			if( !empty($og_meta['full_path']) ) {
				$og_image = $og_meta['full_path'];
			} else {
				$size = 'l';
				if(!empty($og_meta['size'])){
					$size = $og_meta['size'];
				}
				$options = array(
					'save_path' => $this->Rumahku->safeTagPrint($og_meta['path']), 
					'src'=> $this->Rumahku->safeTagPrint($og_meta['image']), 
                    'size' => $size,
					'url' => true,
					'user_path' => !empty($og_meta['User'])?true:false,
					'fullbase' => true
				);

				$og_image = $this->Rumahku->photo_thumbnail($options, false, $og_meta); 
			}

			$meta_add = __('<meta name="twitter:card" content="summary_large_image" />%s', $break);
			$meta_add .= __('<meta name="twitter:site" content="@gtpdotcom" />%s', $break);
			$meta_add .= __('<meta name="twitter:creator" content="@gtpdotcom" />%s', $break);
			$meta_add .= __('<meta name="twitter:title" content="%s" />%s', $og_title, $break);
			$meta_add .= __('<meta name="twitter:description" content="%s" />%s', $og_desc, $break);
			$meta_add .= __('<meta name="twitter:image" content="%s" />%s', $og_image, $break);

			$meta_add .= __('<meta property="og:type" content="article" />%s', $break);
			$meta_add .= __('<meta property="og:url" content="%s" />%s', FULL_BASE_URL.$this->here, $break);
			$meta_add .= __('<meta property="og:title" content="%s" />%s', $og_title, $break);
			$meta_add .= __('<meta property="og:description" content="%s" />%s', $og_desc, $break);
			$meta_add .= __('<meta property="og:image" content="%s"/>%s', $og_image, $break);

			if(!empty($_config['UserCompanyConfig']['facebook_appid'])){
				$app_id = $this->Rumahku->safeTagPrint($_config['UserCompanyConfig']['facebook_appid']);
				$meta_add .= __('<meta property="fb:app_id" content="%s" />', $app_id);
			}

			echo $meta_add;

		}

		$curr_url = $arr_url = $this->params->params['named'];
		
		$pass = $this->params->params['pass'];

		$curr_url['controller'] = $this->params->params['controller'];
		$curr_url['action'] 	= $this->params->params['action'];

		$page = $this->Rumahku->filterEmptyField($arr_url, 'page', false, 0);
		$show = $this->Rumahku->filterEmptyField($arr_url, 'show');

		$param_not_allowed = array('sort', 'direction');

		if(!empty($page)){
			unset($curr_url['page']);
		}

		if(!empty($show)){
			unset($curr_url['show']);
		}

		foreach ($curr_url as $key => $value) {
			if(in_array($key, $param_not_allowed)){
				unset($curr_url[$key]);
			}
		}

		if(empty($curr_url)){
			$curr_url = $this->here;
		}
		
		if(!empty($pass)){
			$curr_url = array_merge($curr_url, $pass);
		}

		$_curr_url = $this->Html->url($curr_url, true);

		$pageCount = $this->Paginator->counter(array('format' => '%count%'));
		
		if(!empty($pageCount)){
			if($this->Paginator->hasPrev()){
				$prev_url = $this->Paginator->prev('&nbsp;', array('only_url' => true));

				foreach ($prev_url as $key => $value) {
					if(in_array($key, $param_not_allowed)){
						unset($prev_url[$key]);
					}
				}
				
				echo '<link rel="previous" href="'.$this->Html->url($prev_url, true).'" />'.PHP_EOL;
			}

			if($this->Paginator->hasNext()){
				$next_url = $this->Paginator->next('&nbsp;', array('only_url' => true));

				foreach ($next_url as $key => $value) {
					if(in_array($key, $param_not_allowed)){
						unset($next_url[$key]);
					}
				}
				
				echo '<link rel="next" href="'.$this->Html->url($next_url, true).'" />';
			}
		}
?>