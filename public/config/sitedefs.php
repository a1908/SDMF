<?php
/* the file contains definitions of site specific constants */

	define("DB_USE",false);					// determine DB usage
	
	define("SITEMAP_XML","sitemap");	// comment out if sitemap.xml is not used
	define("ROLES_XML","roles");		// comment out if roles are not used
	define("DATA_DEFS_XML","data_defs");	//data definitions
	
	define("MULTILINGUAL",true);		// true if there are language versions of XML nodes in XML files false otherwise
	
	// type of access for routes
	define("PUBLIC_ACCESS","public");
	define("PROTECTED_ACCESS","protected");	

	// URL which leads to PROTECTED part of the site
	// change for something unusual - not admin 
	define("PROTECTED_URL", "protected_url");  

	// login fields names
	define("LOGIN_FIELD","login");
	define("PASSWORD_FIELD", "efg");
	
	// upload types
	define("IMAGE_UPLOAD","image");
	define("DOC_UPLOAD","doc");
	
	define("DB_HOST","localhost");  


// environmental definitions
// public - production (one directory)
// test - test (one directory)
// dev, dev.* and test.* - development environment (as per .htaccess in site global root)

	switch(ENVIRONMENT){
		case PRODUCTION_ENVIRONMENT:
		//production
			error_reporting(0);
			define("DB_NAME","");
			define("DB_USER","");
			define("DB_USER_PASSWORD","");
			define("MAIL_TO","");				// email for forms submission
		break;
		
		case TEST_ENVIRONMENT:
		//test
			define("DB_NAME","");
			define("DB_USER","");
			define("DB_USER_PASSWORD","");
			define("MAIL_TO","");				// email for forms submission
		break;

		case DEVELOPMENT_ENVIRONMENT:
		default:
		//development
			error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
			ini_set("display_errors","1");
			define("DB_NAME","");
			define("DB_USER","");
			define("DB_USER_PASSWORD","");
			define("MAIL_TO",""); // email for forms submission
		break;
	}
	
	// email for technical support
	define("SUPPORT_EMAIL","");
	
	// table to use for site parameters
	define("PARAMS_TABLE","_params");
	
	// sign to use for no value
	define("NO_VALUE_SIGN","-");
	
	/* add more site specific constants here */
