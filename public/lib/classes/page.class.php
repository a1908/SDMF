<?php
	
	class Page{
		protected static $acl = array();
		protected static $link;
		protected static $title;
		protected static $keywords;
		protected static $description;
		protected static $template;
		protected static $xmldef;
		
		public static function setTitle($value=null){
			self::$title = $value;
		}
		
		public static function setKeywords($value){
			self::$keywords = $value;
		}
		
		public static function setDescription($value){
			self::$description = $value;
		}

		public static function getTitle(){
			$title = Config::get("global title")[App::getRouter()->getLanguage()];
			if(self::$title){
				$title .= " - ".self::$title;
			}
			return $title;
		}
		
		public static function getKeywords(){
			return self::$keywords;
		}
		
		public static function getDescription(){
			return self::$description;
		}
		
		public static function getTemplate(){
			return self::$template;
		}
		
		public static function getACL(){
			return self::$acl;
		}
		
		public static function getLink(){
			return self::$link;
		}
		
		public static function getXmlDef(){
			return self::$xmldef;
		}
	
		public static function initPage($page_data){
			$lang = App::getRouter()->getLanguage();
			if(isset($page_data['acl'])){
				self::$acl = explode(",", $page_data['acl']);
			}
			self::$link = $page_data["id"];
			self::$title = $page_data->title->$lang;
			self::$keywords = $page_data->keywords->$lang;
			self::$description = $page_data->description->$lang;
			self::$template = $page_data->template;
			self::$xmldef = $page_data;
		}
		
		public static function dump(){
			echo "<pre>";
			echo "Page class print out:\n";
			echo "link:\t".self::$link."\n";
			echo "title:\t".self::$title."\n";
			echo "keywords:\t".self::$keywords."\n";
			echo "description:\t".self::$description."\n";
			echo "template:\t".self::$template."\n";
			echo "xml def:\n";
			var_dump(self::$xmldef);
			
			if(self::$acl){				
			echo "acl:\n";print_r(self::$acl)."\n";
			}else{
				echo "No acl control: free for all\n";
			}
			echo "</pre>";
		}
	}