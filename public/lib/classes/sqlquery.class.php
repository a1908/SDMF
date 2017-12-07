<?php
	class SQLQuery{
		protected $sql_operation;
		protected $table;
		protected $sql_fields;
		protected $sql_statement;
		protected $where;
		protected $order_by;
		protected $group_by;
		protected $limit;
		
		
		public function __construct($sql, $fields=null, $table=null){
			$this->sql_operation = $sql;

			if($fields && is_null($table)){
				$this->sql_operation = $sql;
				$this->setFields($fields);
			}else if(is_null($fields) && $table){
				$this->sql_operation = $sql;
				$this->setTable($table);
			}else if($fields && $table){
				$this->sql_operation = $sql;
				$this->setFields($fields);
				$this->setTable($table);
			}
		}
		
		public function setFields($fields){
			if($fields){
				if(is_array($fields)){
					foreach($fields as $f=>$e){
						if( !is_numeric($f) ){
							$fields[$f] = "$e as '$f'";
						}
					}
					$this->sql_fields = implode(",",$fields);
				}else{
					$this->sql_fields = $fields;
				}
			}
		}
		
		public function setTable($table){
			if($table){
				$this->table = $table;
			}
		}

		public function getSQLQuery(){
			if( $this->sql_statement ) return $this->sql_statement;
			
			$sql = $this->sql_operation;
			
			if($this->sql_fields){
				$sql .= " ".$this->sql_fields;
			}
			
			if($this->table){
				$sql .= " from ".$this->table;
			}
			
			$sql .= $this->getWhere().$this->getSortOrder().$this->limit;
			
			$this->sql_statement = $sql;
			
			return $sql;
		}

		protected function getWhere(){
			if($this->where){
				return " where ".$this->where;
			}else{
				return "";
			}
		}
		
		public function getWhereCond(){
			return $this->where;
		}
		
		protected function getSortOrder(){
			if($this->order_by){
				return " order by ".$this->order_by;
			}else{
				return "";
			}
		}
		
		public function addWhere($cond,$concat=null){
			if($this->where){
				if(!$concat) $concat="and";
				$this->where .= " $concat ($cond)";
			}else{
				$this->where = "($cond)";
			}
		}

		public function addWhereList($concat, $field, $value, $op=null){
			$str = "";
			if(!$op) $op="in";
			if( is_array($value) ){
				$value = implode(",",$value);
			}
			$str = "$field $op ($value)";
			if($this->where){
				$this->where .= " $concat ($str)";
			}else{
				$this->where = "($str)";
			}
		}

		public function addWhereRange($concat, $field, $bound1, $op1, $bound2=null, $op2=null){
			$str = "$field $op1 '$bound1'";
			if($bound2){
				$str .= " and $field $op2 '$bound2'";
			}
			if($this->where){
				$this->where .= " $concat ($str)";
			}else{
				$this->where = "($str)";
			}
		}

		public function addWhereCond($type, $concat, $field, $value1, $op1 = null, $value2 = null, $op2 = null){
			if(is_null($value1) || $value1=="") return;
			$str = "";
			switch($type){
				case "value":
					$value1 = str_replace("'","\\'", $value1);
					if(!$op1) $op1="=";
					$str = "$field $op1 '$value1'";
				break;
				
				case "pattern":
					
					$value1 = str_replace("'","\\'", $value1);

					if(!is_array($field)){
						$field = [$field];
					}
					
					foreach($field as $f){
						$arr[] = "$f like '%$value1%'";
					}
					$str = implode(" or ", $arr);
				break;
				
				case "list":
					if(!$op1) $op1="in";
					if( is_array($value1) ){
						$value1 = implode(",",$value1);
					}
					$str = "$field $op1 ($value1)";
				break;
				
				case "range":
					$str = "$field $op1 '$value1'";
					if(!(is_null($value2) || $value2=="") ){
						$str .= " and $field $op2 '$value2'";
					}
				break;
			}		
			if($this->where){
				$this->where .= " $concat ($str)";
			}else{
				$this->where = "($str)";
			}
		}
		
		
		public function addSortOrder($field, $sort_order){
			$order_by = "";
			if(!$sort_order) return;
			if($this->order_by){
				$this->order_by .= ", $field $sort_order";
			}else{
				$this->order_by = "$field $sort_order";
			}
		}
		
		public function setOffsetLimit($offset,$limit){
				if(!$limit){
					return ;
				}
				if($offset){
					$this->limit = " limit $offset,$limit";
				}else{
					$this->limit = " limit $limit";
				}
		}
			
		
	}