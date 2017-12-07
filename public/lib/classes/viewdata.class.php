<?php
	// class ViewData is used to filter data for a view
	// it holds filter values, page number, items on page, total rows count, and data
	class ViewData{
		protected $rows_count; // total count of records
		protected $filter;	// parameters to filter data + standard parameter 'items_on_page'
		protected $presentation; // parameters to present data + standard ones = page_number and view_type
		protected $data;	// data to show on the current page
		protected $meta_data; // array of fields=>labels
		protected $row_id; // data row identifier
		
		public function __construct($filter=array(),$presentation=array()){
			$this->filter = array_merge($filter,array("items_on_page"=>Config::get("items_on_page")[0])); 						// add standard filter name
			$this->presentation = array_merge($presentation,array("page_number"=>1,"view_type"=>Config::get("view_type")[0]));  // standard presentation parameters
		}
		
		public function updateParams($input){

			if($input){
				foreach($input as $f=>$v){
					if(array_key_exists($f,$this->presentation)){
						$this->presentation[$f] = $input[$f];						
					}
				}
				
				foreach($input as $f=>$v){
					if(array_key_exists($f,$this->filter)){
						$this->filter[$f] = $input[$f];
						$this->presentation["page_number"] = 1; // filter changed - set page_number to 1
					}				
				}
			}
		}
		
		public function setPageNumber($page_number){
			$this->presentation["page_number"] = $page_number;	
		}
		
		public function setItemsOnPage($items_on_page){
			$this->filter["items_on_page"] = $items_on_page;
		}
			
		public function setRowsCount($count){
			$this->rows_count = $count;
		}
		
		public function setData($data){
			$this->data = $data;
		}
		
		public function setMetaData($meta_data){
			$this->meta_data = $meta_data;
		}
		
		public function setRowId($row_id){
			$this->row_id = $row_id;
		}
	
		public function getPageNumber(){
			return $this->presentation["page_number"];
		}
	
		public function getItemsOnPage(){
			return $this->filter["items_on_page"];
		}
		
		public function getRowsCount(){
			return $this->rows_count;
		}
	
		public function getTotalPages(){
			if(!$this->filter["items_on_page"]) return 0;
			return ceil($this->rows_count/$this->filter["items_on_page"]);
		}
		
		public function getViewType(){
			return $this->presentation["view_type"];
		}
		
		public function getData(){
			return $this->data;
		}
	
		public function getFilter(){
			return $this->filter;
		}
		
		public function getPresentation(){
			return $this->presentation;
		}
		
		public function getDataFields(){
			return array_keys($this->meta_data);
		}
		
		public function getDataLabels(){
			return array_values($this->meta_data);
		}
		
		public function getMetaData(){
			return $this->meta_data;
		}
		
		public function getRowId(){
			return $this->row_id;
		}

	}