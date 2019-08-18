<?php 
		$meta_add = '';
		$metaInfo = '';

		$og_meta = empty($og_meta) ? array() : $og_meta;

		if( !empty($og_meta) ) {
			$meta_add = sprintf('<meta property="og:type" content="article" /><meta property="og:url" content="%s" />', FULL_BASE_URL.$this->here);
			$meta_add .= '<meta name="twitter:card" content="summary_large_image" />';
			$meta_add .= '<meta name="twitter:site" content="@primesystemid" />';
			$meta_add .= '<meta name="twitter:creator" content="@primesystemid" />';

			$metaTitle			= Hash::get($og_meta, 'title');
			$metaDescription	= Hash::get($og_meta, 'description');

			if($metaTitle){
				$meta_add .= sprintf('<meta property="og:title" content="%s" />', htmlspecialchars($metaTitle));
				$meta_add .= sprintf('<meta property="twitter:title" content="%s" />', htmlspecialchars($metaTitle));
				$metaInfo .= sprintf('<meta name="title" content="%s" />', htmlspecialchars($metaTitle));
			}
			
			if( !empty($og_meta['full_path']) ) {
				$mainImage = $og_meta['full_path'];
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

				$mainImage = $this->Rumahku->photo_thumbnail($options, false, $og_meta); 
			}

			$meta_add .= sprintf('<meta property="og:image" content="%s"/>', $mainImage);
			$meta_add .= sprintf('<meta property="twitter:image" content="%s"/>', $mainImage);

			if( $metaDescription) {
				$metaDescription = urldecode($metaDescription);
				$metaDescription = $this->Rumahku->safeTagPrint($metaDescription);
				$metaDescription = trim($this->Text->truncate($metaDescription, 320, array(
					'ending'	=> '',
					'exact'		=> false, 
				)));

				$meta_add .= sprintf('<meta property="og:description" content="%s" />', $metaDescription);
				$meta_add .= sprintf('<meta property="twitter:description" content="%s" />', $metaDescription);
				$metaInfo .= sprintf('<meta name="description" content="%s" />', $metaDescription);
			}

			$app_id = Configure::read('Facebook.appId');
			if(!empty($_config['UserCompanyConfig']['facebook_appid'])){
				$app_id = $this->Rumahku->safeTagPrint($_config['UserCompanyConfig']['facebook_appid']);
			}

			$meta_add .= sprintf('<meta property="fb:app_id" content="%s" />', $app_id);

			echo $meta_add;
			echo $metaInfo;
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
?>
<meta content="grosirpasartasik.com adalah situs jual beli berbagai busana dan atau keperluan fashion. Pusat Grosir Pasar Tasik. Toko Online Termurah dan Terpercaya. Cari barang grosiran? grosirpasartasik.com" name="description" />

<meta content="follow, index" name="robots" />
<link rel="canonical" href="<?php echo $_curr_url;?>" />
<?php
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