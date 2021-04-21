<?php
	namespace PDLoader;
	
	class Recaptcha extends Loader{	
		
		public function init($url=false){
			echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>'."\n";
			echo "<script>".trim(preg_replace('/\s+/', ' ',file_get_contents(__DIR__.'/defaults.js')))."</script>\n";

			if( !$url ){
				if( $this->config() && isset($this->config()['url'] ) ){
					$url = $this->config()['url'];
				}else{
					$url = $this->url();
				}
			}

			$assets = [];

			foreach (glob(__DIR__.'/dist/*') as $c) {
				$f = explode('.',$c);
				$raw = str_replace(__DIR__,$url.'loader/modules/Recaptcha',$c);
				if( $f[count($f)-1] == 'js' ){
				   $assets[] = [ 'type' => 'js', 'src' => $raw ];
				}
			}


			echo "<script async defer>var recaptchaassets = ".json_encode($assets).";".trim(preg_replace('/\s+/', ' ',file_get_contents(__DIR__.'/loader.js'))).";</script>\n";
			
		}

		public function loadRecaptchaBox($sitekey=false,$recaptcha_success=false,$recaptcha_expired=false,$recaptcha_error=false){

			$box = file_get_contents(__DIR__.'/recaptcha-box.temp');

			$sitekey = ( $sitekey ) ? $sitekey : $this->config(false)['site_key'];

			if( !$recaptcha_success ){
				$recaptcha_success = 'recapsuccess';
			}
			if( !$recaptcha_expired ){
				$recaptcha_expired = 'recapexpired';
			}

			if( !$recaptcha_error ){
				$recaptcha_error = 'recaperror';
			}

			echo '<div class="g-recaptcha recaptcha-module" data-callbacksuccess="'.$recaptcha_success.'" data-sitekey="'.$sitekey.'" data-callback="recaptsuccess" data-expired-callback="recaptexpired" data-error-callback="recapterror" data-callbackexpired="'.$recaptcha_expired.'" data-callbackerror="'.$recaptcha_error.'"></div>';
			
		}

		public function onBuild(){
			if( file_exists(__DIR__.'/package.json') ){
				unlink(__DIR__.'/package.json');
			}
			if( file_exists(__DIR__.'/package-lock.json') ){
				unlink(__DIR__.'/package-lock.json');
			}
			if( file_exists(__DIR__.'/dist/index.html') ){
				unlink(__DIR__.'/dist/index.html');
			}
			if( file_exists(__DIR__.'/index.html') ){
				unlink(__DIR__.'/index.html');
			}
			if( file_exists(__DIR__.'/.gitignore') ){
				unlink(__DIR__.'/.gitignore');
			}
			if( file_exists(__DIR__.'/src') ){
				$this->deleteFolder(__DIR__.'/src');
			}
			if( file_exists(__DIR__.'/build') ){
				$this->deleteFolder(__DIR__.'/.phpintel');
			}
			if( file_exists(__DIR__.'/.phpintel') ){
				$this->deleteFolder(__DIR__.'/.phpintel');
			}
			if( file_exists(__DIR__.'/.cache') ){
				$this->deleteFolder(__DIR__.'/.cache');
			}
			if( file_exists(__DIR__.'/.git') ){
				$this->deleteFolder(__DIR__.'/.git');
			}

		}

		public function recaptcha_verify($response=false,$site_secret=false){

			if( !$response ){
				echo json_encode([ 'success' => false, 'msg' => 'Response key not found' ]);
				return;
			}


			if( !$site_secret ){
				if( $this->config() && isset($this->config()['site_secret'] ) ){
					$site_secret = $this->config()['site_secret'];
				}else{
					echo json_encode([ 'success' => false, 'msg' => 'Site secret not found' ]);
					return;
				}
			}

			$curl = curl_init(); 
			curl_setopt($curl,CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify?secret=".$site_secret."&response=".$response );
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_HEADER, false); 
			$result=curl_exec($curl);
			curl_close($curl);
			
			return json_encode($result);
		}

		private function deleteFolder($dir){
			if(file_exists($dir)){
				$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
				$files = new \RecursiveIteratorIterator($it,
				             \RecursiveIteratorIterator::CHILD_FIRST);

				foreach($files as $file) {
					chmod($file->getRealPath(),0755);
				    if ($file->isDir()){
				        rmdir($file->getRealPath());
				    } else {
				        unlink($file->getRealPath());
				    }
				}
				rmdir($dir);
			}
			
		}

	}