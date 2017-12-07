<?php
	// class to handle many to many links
	
	class MultipleForeignKeys{
		protected $link_table;
		protected $internal_key;
		protected $external_key;
		protected $data_source;
		
		public function __construct($link_table, $internal_key, $external_key, $data_source){
			$this->link_table = $link_table;
			$this->internal_key = $internal_key;
			$this->external_key = $external_key;
			$this->data_source = new DataSource($data_source);
		}

		public function insert($internal_key, $external_keys){
			$sql = "insert into {$this->link_table} ({$this->internal_key},{$this->external_key}) values ";
			
			$values = array();
			foreach($external_keys as $ex){
				$values[] = "($internal_key,$ex)";
			}			
			
			$sql .= implode(",",$values);
			App::$db->query($sql);
		}
		
		public function clearAll($internal_key){
			$sql = "delete from {$this->link_table} where $this->internal_key='$internal_key'";
			App::$db->query($sql);
		}

		public function getExternalKeys($internal_key){

			$sql = "select ".$this->external_key." as `key` from ".$this->link_table." where ".$this->internal_key."='$internal_key'";							
			$stmt = App::$db->query($sql);
			$keys = $stmt->fetchAll();
			
			$keys_array = array();
			foreach($keys as $key){
				$keys_array[] = "'".$key->key."'";
			}
			return $keys_array;
		}
		
		public function getKeyValuePairs($keys=null){
			return $this->data_source->getKeyValuePairs($keys);
		}


	} 
	