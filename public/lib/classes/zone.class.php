<?php

	class Zone{
		
		protected static $zone_name;
		protected static $layout;
		protected static $web_prefix;
		protected static $login_path;
		protected static $access_type;
		protected static $mixed_roles;
		protected static $roles;
		protected static $open_access;
		protected static $credentials_model;
		protected static $credentials_verification;
		

		public static function getZoneName(){
			return self::$zone_name;
		}
		
		public static function getLayout(){
			return self::$layout;
		}
		
		public static function getWebPrefix(){
			return self::$web_prefix;
		}
		
		public static function getLoginPath(){
			if(self::$login_path){
				return self::$web_prefix."/".App::getRouter()->getLanguagePath("%/").self::$login_path;
			}else{
				return null;
			}
		}
		
		public static function openAccess(){
			return self::$open_access;
		}
		
		public static function getAccessType(){
			return self::$access_type;
		}
		
		public static function getMixedRoles(){
			return self::$mixed_roles;
		}
		
		public static function getRoles(){
			return self::$roles;
		}
		
		public static function set($zone_name){
			$zone = Config::get("zones")[$zone_name];
			self::$zone_name = $zone_name;
			self::$layout = $zone["layout"];
			self::$web_prefix = $zone["web_prefix"];
			self::$login_path = $zone["login_path"];
			self::$access_type = $zone["access_type"];
			self::$mixed_roles = $zone["mixed_roles"];
			if(isset($zone["open_access"])){
				self::$open_access = $zone["open_access"];
			}else{
				self::$open_access = array();
			}
			$roles_path = CONFIG_PATH.self::$layout.DS.ROLES_XML.".xml";
			if(file_exists($roles_path)){
				$roles_xml = simplexml_load_file($roles_path);
				self::$roles = $roles_xml;
			}
			if(isset($zone["credentials_model"])){
				self::$credentials_model = $zone["credentials_model"];
				self::$credentials_verification = $zone["credentials_verification"];
			}			
		}
		
		public static function verifyUserCredentials($id){
			if(empty(self::$credentials_model)){
				return true;
			}
			$model = new self::$credentials_model;
			return($model -> {self::$credentials_verification} ($id));
		}
		
		public static function dump(){
			echo "<pre>";
			echo "Zone class print out:\n";
			echo "Name:\t".self::$zone_name."\n";
			echo "Layout:\t".self::$layout."\n";
			echo "WebPrefix:\t".self::$web_prefix."\n";
			echo "LoginPath:\t".self::$login_path."\n";
			echo "AccessType:\t".self::$access_type."\n";
			echo "Open Access:\t";
			var_dump(self::$open_access);
			echo "\nMixedRoles:\t".(self::$mixed_roles?"true":"false")."\n";
			echo "Roles:\n";//print_r(self::$roles)."\n";
			foreach(self::$roles->role as $r){
				echo $r['id'].":".$r->title->{App::getRouter()->getLanguage()}.":".$r->descr->{App::getRouter()->getLanguage()}."\n";
			}
			echo "\n---------------------------------------------------------\n";
			echo "</pre>";
		}
	}