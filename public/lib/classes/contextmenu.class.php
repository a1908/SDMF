<?php

	class ContextMenu{
		protected $id;
		
		public function __construct(){
			$this->id = uniqid("context_menu_id_");
			$context_menu_id = $this->id;
			require MENUS_PATH."linked-table.context.menu.html.php";
		}
		
		public function getId(){
			return $this->id;
		}
		
	}