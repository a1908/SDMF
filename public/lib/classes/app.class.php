<?php
	class App{
		
		protected static $router;
		protected static $web_root;
		protected static $xmldefs;
		protected static $xml_data_defs;
		public static $db;
		
		public static function getRouter(){
			return self::$router;
		}
		
		public static function getWebRoot(){
			return self::$web_root;
		}
				
		public static function getXmldefs(){
			return self::$xmldefs;
		}
		
		public static function getDataXmlDefs(){
			return self::$xml_data_defs;
		}
				
		public static function run($url){
			
			// get route info
			self::$router = new Router($url);
			self::$web_root = self::$router->getWebRoot();
			$controller = self::$router->getController();
			$controller_class = ucfirst($controller)."Controller";
			$controller_method = strtolower(self::$router->getMethod());
			$page_link = addslashes( $controller."/".$controller_method );
			$layout = Zone::getLayout();
			$web_prefix = Zone::getWebPrefix().self::$router->getLanguagePath("/%");
			$language_path_dot = self::$router->getLanguagePath(".%");
			
			// load language
			Lang::load(self::$router->getLanguage());

			// get db connection
			// connect to Database if DB_HOST is set
			if(DB_USE){
				$db = new DB(Config::get("db_host"),Config::get("db_name"),Config::get("db_user"),Config::get("db_user_password"));
				self::$db = $db->connection;
			}
			
			// get page definitions
			if( defined("SITEMAP_XML") ){
				// sitemap XML is used for pages data, menues and forms
				$sitemap_path = CONFIG_PATH.$layout.DS.SITEMAP_XML.".xml";

				if( file_exists( $sitemap_path ) ){
					// get the page data from the site map
					self::$xmldefs = simplexml_load_file($sitemap_path);
					$page_data = self::$xmldefs->xpath("//page[link='$page_link']");
					if($page_data){
						Page::initPage($page_data[0]);
					}
				}
				//get data definitions XML
				self::$xml_data_defs = simplexml_load_file(CONFIG_PATH.DATA_DEFS_XML.".xml");
			}

			// check credentials
			$authorised = Session::get("authorised") && Session::get("user_layout")==Zone::getLayout();

			if( $authorised ){
				// update user credentials
				Session::updateUserCredentials();
			}


			$page_acl = Page::getACL();
			$access_denied = !Session::checkACL($page_acl);

			if( Zone::getAccessType() == PROTECTED_ACCESS ){
				// Zone is protected - authorisation required
				// unless it is a page with open access (login form, password restore, registration, etc).
				if( !in_array($page_link,Zone::openAccess()) ){
					// page is protected - check authorisation			
					if( $authorised ){
						// user logged in - checking page ACL
						if( $page_acl && $access_denied ){
							// page has limited access and user has no access rights
							Session::setFlash(__("error-access-denied"));
							Router::redirect(WEB_ROOT."/".DEFAULT_CONTROLLER."/404");
						}
					}else{
						// user is not logged in - redirect to login page
						if( self::$router->getUrl() ){
							// attempt to open inside page without authorisation - display warning message
							// and send it to login controller as a parameter
							Session::setFlash(__("message-login-required"));
							Router::redirect(WEB_ROOT.Zone::getLoginPath()."?next_page=".urlencode(self::$router->getUrl()));
						}else{
							// just show login page
							Router::redirect(WEB_ROOT.Zone::getLoginPath());
						}
						
					}
				}
			}else{
				// Zone has public access - check page acl
				if( $page_acl ){
					// Page is protected - check authorisation and rights
					if( $authorised ){
						// user logged in - checking access rights
						if( $access_denied){
							Session::setFlash(__("error-access-denied"));
							Router::redirect(WEB_ROOT."/".DEFAULT_CONTROLLER."/404?");
						}
					}else{
						// user is not logged in - if login page exists go to it otherwise go to main page
						$login_page = Zone::getLoginPath(); 
						if( $login_page ){
							Session::setFlash(__("message-login-required"));
							Router::redirect(WEB_ROOT.Zone::getLoginPath()."?next_page=".urlencode(self::$router->getUrl()));
						}else{
							Router::redirect(WEB_ROOT);	
						}
					}
				}				
			}

			// call up controller
			$controller_object = new $controller_class;

			if( method_exists($controller_object, $controller_method) ){
				// controller may return a view path if not use default from the view object - derived from controller name VIEWS/zone/controller
				$view_path = $controller_object->$controller_method();
				$view_object = new View($controller_object->getData(), $view_path);
								
			}else{
				// no method => no data from controller are required just render the view
				// if it exists in the directory VIEWS/route/controller
				// if not => then page is not found 
				$view_object = new View();				
			}
						
			//render view
			$page_content["content"] = $view_object->render();

			if( !($template = Page::getTemplate()) ){
				$template = $layout;
			}


			$template_path = TEMPLATES_DIR.DS.$template;
			$view_object = new View($page_content,$template_path);
			
			echo $view_object->render();
		}
	}