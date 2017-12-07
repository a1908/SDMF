<?php
	
	class ListViewStatus{
		protected $text_filter;
		protected $fields_filter;
		protected $items_on_page;
		protected $active_page;
		
		
		public function __construct($text_filter = null, $fields_filter = null){
			$this->text_filter = $text_filter;
			$this->fields_filter = $fields_filter;
			$this->active_page = 1;
			$this->items_on_page = key(Config::get("items_on_page"));
		}
		
		public function getActivePage(){
			return $this->active_page;
		}
		
		public function setActivePage($p){
			$this->active_page = $p;
		}
		
		
		public function getTextFilter(){
			return $this->text_filter;
		}
		
		public function setTextFilter($text_filter){
			$this->active_page = 1;
			$this->text_filter = $text_filter;
		}

		public function getFieldsFilter(){
			return $this->fields_filter;
		}		

		public function setFieldsFilter($fields_filter){
			$this->active_page = 1;
			$this->fields_filter = $fields_filter;
		}		
				
		public function clearFilters(){
			$this->active_page = 1;
			$this->text_filter = null;
			foreach($this->fields_filter as $k=>$v){
				$this->fields_filter[$k] = null;
			}
		}		
		
		public function unsetStatus($name){
			Session::delete($name);	
		}
		
		public function getItemsOnPage(){
			return (int)$this->items_on_page;
		}

		public function setItemsOnPage($items_on_page){
			$this->active_page = 1;
			$this->items_on_page = $items_on_page;
		}
		
		
		
	}