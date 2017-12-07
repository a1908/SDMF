<?php
	
	class View{
		
		protected $page_vars;
		protected $path;
		protected $router;
		protected $layout;
		
		protected static function getDefaultViewPath(){
			$router = App::getRouter();
			$layout = Zone::getLayout();
			if(!$router){
				throw new Exception("Router does not exists!");
			}
			$controller_dir = $router->getController();
			$method = $router->getMethod();
			$view_language_path = VIEWS_PATH.$layout.DS.$controller_dir.DS.$method.$router->getLanguagePath(".%").".html.php";
			if( file_exists($view_language_path) ){
				return $view_language_path;
			}
			
			return VIEWS_PATH.$layout.DS.$controller_dir.DS.$method.".html.php";
			
		}

		public function set($var,$val){
			$this->page_vars[$var] = $val;
		}
		
		public function __construct( $page_vars = array(), $path = NULL ){

			if( is_null($path) ){
				// $path = default path
				$path = self::getDefaultViewPath();

			}else{
				if(substr($path,0,strlen(TEMPLATES_DIR))==TEMPLATES_DIR){
					$layout = TEMPLATES_DIR.DS;
				}else{
					$layout = Zone::getLayout().DS;
				}
				$language_path =  VIEWS_PATH.$layout.$path.App::getRouter()->getLanguagePath(".%").".html.php";
				if( file_exists($language_path) ){
					$path = $language_path;
				}else{
					$path =  VIEWS_PATH.$layout.$path.".html.php";
				}
				
			}
			if( !file_exists($path) ){
				// no method, no default view - page is not found!
				Page::setTitle(__("error-page-not-found-page-title"));
				$path = VIEWS_PATH.DS.Zone::getLayout().DS.DEFAULT_CONTROLLER.DS."404.html.php";
			}
			
			$this->path = $path;
			$this->page_vars = $page_vars;
			
		}
		
		public function render(){
			extract($this->page_vars);
			ob_start();
			require($this->path);
			return ob_get_clean();			
		}
	}