<?php

	class Menu{
		private $activePage;		
		private $menu;
		private $langPath;
		private $language;
		private $web_prefix;
		private $base_link;
		private $item_tag = array("item","dropdown","popover","section","include","divider");
		private $item_tag_class = array(
				"item"=>"",
				"dropdown"=>"dropdown",
				"popover"=>"dropdown mouseover",
				"section"=>"",
				"include"=>"",
				"divider"=>"divider",
			);
			

		
		public function __construct($activePage,$menu, $lang=null){
			
			$this->activePage = $activePage;
			$this->langPath =  $lang ? $lang."/" : "";
			$this->language = App::getRouter()->getLanguage();
			$this->web_prefix = Zone::getWebPrefix();
			$this->base_link = WEB_ROOT.$this->web_prefix;
			
			if( defined("SITEMAP_XML") ){
				$this->menu = App::getXmldefs()->xpath("menus/menu[@id='$menu']")[0];
			}else{
				throw new Exception ("No XML definition for menu construction!");
			}
			
			if(!$this->menu){
				throw new Exception("Menu '$menu' is not found!");
			}
		}


		public function listItems(){
			$items = array();
			
			if(isset($this->menu['acl'])){
				if(!Session::checkACL($this->menu['acl'])){
					return $items;
				}
			}
			
			foreach($this->menu->children() as $menu_item){
				if(isset($menu_item['acl'])){
					if(!Session::checkACL($menu_item['acl'])){
						continue;
					}
				}
				$items[] = $menu_item;
			}
			return $items;
		}
		
		public function html($class=null,$item_id=null){

			if(isset($this->menu['acl'])){
				if(!Session::checkACL($this->menu['acl'])){
					return "";
				}
			}
			
			if( is_null($item_id) ){
				$html = "<ul ".($class===null?"class='nav navbar-nav'":$class).">\n";
				$html .= $this->getMenuItems($this->menu);
				$html .= "</ul>\n";
			}else{
				$item = $this->menu->xpath("*[@id='$item_id']");
				if( $item )
					$html = $this->getMenuItem($item[0],$class);
				else
					$html = "";
			}
			
			return $html;
		}
		
		private function getMenuItems($menu,$class=null){
			$html = "";

			$items = $menu->xpath(implode("|",$this->item_tag));

			foreach($items as $menu_item){
				$html .= $this->getMenuItem($menu_item,$class);
			}
			return $html;
		}

		private function getMenuItem($menu_item,$class=null){
			if(isset($menu_item['acl'])){
				if(!Session::checkACL($menu_item['acl'])){
					return "";
				}
			}

			$html = "";
			$tag = $menu_item->getName();

			$language = $this->language;
			$base_link = $this->base_link;
	
			$ca = array();
			if($this->item_tag_class[$tag]){
				$ca[] = $this->item_tag_class[$tag];
			}
			if($class){
				$ca[] = $class;
			}
			if(strcmp($menu_item['link'],$this->activePage)==0){
				$ca[] = "active";
			}
						
			$cs = $ca?" class='".implode(" ",$ca)."'":"";
			
			switch($tag){
				case "item":
					$html .= "<li$cs><a href='$base_link/{$this->langPath}{$menu_item['link']}'>{$menu_item->caption->$language}</a></li>\n";
				break;
				
				case "dropdown":
					$html .= "<li$cs><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>{$menu_item->caption->$language}  <span class='glyphicon glyphicon-triangle-bottom dropdown-sign'></span></a>\n";
					$html .= "<ul class='dropdown-menu'>\n";
					$html .= $this->getMenuItems($menu_item);
					$html .= "</ul>\n</li>\n";
				break;
				
				case "popover":
					$submenu_link = isset($menu_item['link'])?$menu_item['link']:"#";
				
					$html .= "<li$cs'><a href='$submenu_link' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>{$menu_item->caption->$language}</a>\n";
					$html .= "<ul class='dropdown-menu'>\n";
					$html .= $this->getMenuItems($menu_item);
					$html .= "</ul>\n</li>\n";
				break;
				
				case "section":
					$html .= $this->getMenuItems($menu_item);
				break;
				
				case "include":
					$filePath = MENUS_PATH.$menu_item['file'];
					if( file_exists($filePath)){
						ob_start();
						require($filePath);
						$html .= ob_get_clean();	
					}
				break;
				
				case "divider":
					$html .= "<li role='separator'$cs></li>\n";
				break;
			}
			
			return $html;

		}


		
		public function getLink($id){
			$item = $this->menu->xpath("*[@id='$id']");
			if( $item )
				return WEB_ROOT."/".$item[0]['link'];
			else
				return null;
		}
				
		public function getCaption($id){
			$item = $this->menu->xpath("*[@id='$id']");
			if( $item )
				return $item[0]->caption->{$this->language};
			else
				return null;
		}		
	}