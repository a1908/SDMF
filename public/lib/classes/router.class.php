<?php
	// Router parses URL
	// URL structure: /[route/][lang/][controller/method/]param1/.../paramN
	// routes, langs and defaults defined in config.php
	// NOTE: method must follow after controller to be parsed as a method
	// otherwise the default method of the default controller will be used 
	
	
	class Router{
		
		protected $url;
		protected $controller;
		protected $method;
		protected $params;
		
		protected $zone_name;		//site zone	name
		
		protected $active_link;		// controller."/".method
		protected $language_path;	// "" if $language is default, language code otherwise
		protected $language;		// language code
		protected $web_root;
		
		
		public function getUrl(){
			return $this->url;	
		}
		
		public function getLanguage(){
			return $this->language;	
		}

		public function getLanguagePath($str=null){
		// % in $str will be replaced with $lang if $lang != ""
		if( $this->language_path && $str ){
				return str_replace("%", $this->language_path, $str);	
			}
			return $this->language_path;
		}
		
		public function getWebRoot(){
			return $this->web_root;
		}
		
		public function getController(){
			return $this->controller;
		}
		
		public function getMethod(){
			return $this->method;	
		}
		
		public function getParams(){
			return $this->params;	
		}

		public function getActiveLink(){
			return $this->active_link;
		}
				
		public function __construct($url){

			$this->url = filter_var(rtrim($url,"/"),FILTER_SANITIZE_URL);
			$urlParsed = explode("/",$this->url);
						
			//Get defaults from config
			$zones = Config::get("zones");
			$this->zone_name = Config::get("default_zone_name");
			$default_language = Config::get("default_language");
			$this->language = $default_language;
			$this->language_path = "";
			$this->controller = Config::get("default_controller");
			$this->method = Config::get("default_method");
			$this->params = array();
			
			
			if( $url != "" ){
				//get zone
				$p = strtolower($urlParsed[0]);
				if( in_array($p, array_keys($zones)) ){
					$this->zone_name = $p;
					array_shift($urlParsed);
				}
				
				//set Zone 
				Zone::set($this->zone_name);

				//get language
				if ( $urlParsed ){
					$p = strtolower($urlParsed[0]);
					if(in_array($p, Config::get("languages")) ){
						if( $p != $default_language ){
							$this->language = $p;
							$this->language_path = $p;
						}
						array_shift($urlParsed);
					}
				}
				
				// get url as zone and language are off
				$this->url = implode("/", $urlParsed );
				
				// get controller				
				if( $urlParsed ){
					$p = str_replace("-","",strtolower($urlParsed[0])); // remove dashes from controller name
					
					//check if this could be controller
					if( file_exists(CONTROLLERS_PATH.DS.Zone::getLayout().DS.$p.".controller.php") ){
						$this->controller = $p;
						array_shift($urlParsed);
					}
				}
													
				// whatever goes after controller - that is a method
				if( $urlParsed ){
					$this->method = str_replace("-","",strtolower($urlParsed[0]));  // remove dashes from method name
					array_shift($urlParsed);
				}

				
				//get params
				$this->params = $urlParsed;
			}else{
				//set default zone
				Zone::set($this->zone_name);
			}	
			//add get to params
			if($_GET){
				$this->params = array_merge($this->params,$_GET);
			}
			
			$this->active_link = $this->controller."/".$this->method;
			$this->web_root = WEB_ROOT.Zone::getWebPrefix().($this->language_path?"/".$this->language_path:"");
		}
		
		public static function redirect($location){
		
			if(preg_match("/^https?:\/\//", $location)==0){
				$location = App::getWebRoot().$location;
			}

			header("Location: ".urldecode($location));
			die();
		}
		
	}