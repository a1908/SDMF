<?php
	
	class Lang{
		
		protected static $data;
		
		public static function load($lang_code){
			$lang_file_path = LANG_PATH.$lang_code.".php";
			if( file_exists($lang_file_path) ){
				self::$data = include($lang_file_path);
			}else{
				throw new Exception("Unknown language code: '$lan_code'");
			}
		}
		
		public static function get($key, $default_value = ""){
			return isset(self::$data[$key]) ? self::$data[$key] : ($default_value ? $default_value : $key);
		}
	}