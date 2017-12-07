<?php
	
	class Controller{
		protected $data;
		protected $model;
		protected $params;
		
		
		public function getData(){
			return $this->data;
		}
		
		public function getModel(){
			return $this->model;
		}
		
		public function getParams(){
			return $this->params;
		}
		
		public function __construct($data=array()){
			$this->data = $data;
			$this->params = App::getRouter()->getParams();
		}
		
		public function getViewPath($controller=null,$method=null){
			$router = App::getRouter();
			$layout = Zone::getLayout();
			if(!$controller){
				$controller_dir = $router->getController();
			}
			if(!$method){
				$method = $router->getMethod();
			}
			
			$current_lang = $router->getLanguagePath();
			if( $current_lang ){
				$view_language_path = VIEWS_PATH.$layout.DS.$controller_dir.DS.$method.".".$current_lang.".html.php";
				if( file_exists($view_language_path) ){
					return $view_language_path;
				}
			}
			
			return VIEWS_PATH.$layout.DS.$controller_dir.DS.$method.".html.php";			
		}
				
	}