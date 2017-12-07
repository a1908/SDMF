<?php
	define("ROOT",dirname(dirname(dirname(__DIR__))));
	define("GLOBAL_ROOT",dirname(ROOT));

	// environment
	define("ENVIRONMENT",basename(ROOT));
	define("DEVELOPMENT_ENVIRONMENT","dev");
	define("TEST_ENVIRONMENT","test");
	define("PRODUCTION_ENVIRONMENT","public");

	// set protocol
	if(isset($_SERVER["HTTPS"])){
		if($_SERVER["HTTPS"]=="on"){
			define("PROTOCOL", "https://");
		}else{
			define("PROTOCOL", "http://");
		}
	}else{
		define("PROTOCOL", "http://");
	}

	if(ENVIRONMENT == PRODUCTION_ENVIRONMENT){
		define("WEB_ROOT", PROTOCOL.$_SERVER["HTTP_HOST"].str_replace($_SERVER["DOCUMENT_ROOT"],"",GLOBAL_ROOT));
	}else{
		define("WEB_ROOT", PROTOCOL.$_SERVER["HTTP_HOST"].str_replace($_SERVER["DOCUMENT_ROOT"],"",ROOT));
	}

	// standard folder names
	define("IMG_DIR","img");			// site images folder name
	define("DATA_DIR","z-data");		// site data folder name - include in .htaccess
	define("WWW_DIR","www");			// site www root - include in .htaccess
	define("TEMPLATES_DIR","templates");// templates name in views folder
	
	// web paths to be used in html files 
	define("SCRIPTS_PATH", WEB_ROOT."/scripts/");	// web path to web accesible scripts 
	define("JS_PATH", WEB_ROOT."/js/");				// web path to javascripts
	define("IMG_PATH", WEB_ROOT."/".IMG_DIR."/");	// web path to images
	define("CSS_PATH", WEB_ROOT."/css/");			// web path to css files
	define("DATA_PATH", WEB_ROOT."/".DATA_DIR."/");	// web path to data folder - i.e. user data, user uploaded files, etc.

	define("DS", DIRECTORY_SEPARATOR);

	// local paths to be ised in php scripts
	define("CONFIG_PATH",ROOT.DS."config".DS);
	define("CONTROLLERS_PATH",ROOT.DS."mvc".DS."controllers".DS);
	define("MODELS_PATH",ROOT.DS."mvc".DS."models".DS);
	define("VIEWS_PATH",ROOT.DS."mvc".DS."views".DS);
	define("TEMPLATES_PATH",VIEWS_PATH.TEMPLATES_DIR.DS.TEMPLATES_DIR);
	define("LANG_PATH",ROOT.DS."lang".DS);
	
	define("WEB_ROOT_LOCAL_PATH",ROOT.DS.WWW_DIR.DS);
	define("IMG_LOCAL_PATH", WEB_ROOT_LOCAL_PATH .IMG_DIR.DS);
	define("DATA_LOCAL_PATH", ROOT.DS.DATA_DIR.DS);
	
	define("LIB_PATH",ROOT.DS."lib".DS);
	define("CLASSES_PATH",LIB_PATH."classes".DS);
	define("INCLUDE_PATH",LIB_PATH."includes".DS);
	define("INTERNAL_SCRIPTS_PATH",INCLUDE_PATH."scripts".DS);

	define("FORMS_PATH",VIEWS_PATH.TEMPLATES_DIR.DS."includes".DS."forms".DS);
	define("MENUS_PATH",VIEWS_PATH.TEMPLATES_DIR.DS."includes".DS."menus".DS);
	define("PAGE_BLOCKS_PATH",VIEWS_PATH.TEMPLATES_DIR.DS."includes".DS."page_blocks".DS);
	define("SNIPPETS_PATH",VIEWS_PATH.TEMPLATES_DIR.DS."includes".DS."snippets".DS);
	
	// default controller and method
	define("DEFAULT_CONTROLLER", "home");	// controller to be used if no controller is found in URL
	define("DEFAULT_METHOD", "index");		// nethod to be used if no method is found in URL
			
	// char to convert parameters list into array and back
	define("LIST_SEPARATOR",",");

