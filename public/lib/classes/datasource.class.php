<?php
	
	class DataSource{
		
		protected $default;
		protected $key;
		protected $value;
		protected $data;
		protected $SQL_base;
		protected $xmldef;
		
		// $keys = list of comma separated values
		public function __construct($id, $keys=null){
			
			$xmldefs = App::getDataXmlDefs();
			$lang = App::getRouter()->getLanguage();
			
			
			if( $id ){
				$ds_node = $xmldefs->xpath("//data_source[@id='$id']");
			}else{
				throw new Exception("No id provided for data source");
			}
			
			if( $ds_node ){
				$this->xmldef = $ds_node[0];
			}else{
				throw new Exception("Data source '$id' does not exist");
			}

			$this->default = $this->xmldef->default->$lang;
			$this->key = (string)$this->xmldef->key['name'];
			$this->value = (string)$this->xmldef->value['name'];
			
			$sql = "select ".$this->xmldef->key." as ".$this->key.", ".$this->xmldef->value." as ".$this->value." from ".$this->xmldef->table_ref;
			$this->SQL_base = $sql;
			if($keys){			
				$sql .= " where {$this->xmldef->key} in ($keys)";
			}
			$stmt = App::$db->query($sql);
			$this->data = $stmt->fetchAll();
		}

		public function getKeyName(){
			return $this->key;
		}

		public function getValueName(){
			return $this->value;
		}

		// returns value of the $key from data_source
		// if $key == null returns first from values selected by __constructor
		public function getValue($key=null){
			if( $key ){
				$sql = $this->SQL_base." where {$this->xmldef->key}='$key'";
				$stmt = App::$db->query($sql);
				$rec = $stmt->fetch();
			}else{
				$rec = $this->data[0];
			}
			return $rec->{$this->value};
		}
		
		//returns array of key - value pairs
		// if $keys==null return array selected by __constructor
		public function getValues($keys=null){
			
			if( $keys ){
				$sql = $this->SQL_base." where {$this->xmldef->key} in ($keys)";
				$stmt = App::$db->query($sql);
				$set = $stmt->fetch();
			}else{
				$set = $this->data;
			}
			
			foreach($set as $rec){
				$result[] = $rec->{$this->value};
			}
			return $result;
		}
		
		// similar to getValues but returns accosiative array with keys
		public function getKeyValuePairs($keys=null){
			if( $keys ){
				$sql = $this->SQL_base." where {$this->xmldef->key} in ($keys)";
				$stmt = App::$db->query($sql);
				$set = $stmt->fetchAll();
			}else{
				$set = $this->data;
			}
			foreach($set as $rec){
				$result[$rec->{$this->key}] = $rec->{$this->value};
			}			
			return $result;
		}
		
		public function selectHTML($name, $selected=null, $attr=null){
?>
		<select name="<?=$name?>"<?=$attr?" ".$attr:""?>>			
			<option value="" disabled<?=$selected?"":" selected"?>><?=$this->default?></option>
<?php
			$k = $this->key;
			$v = $this->value;
			foreach($this->data as $o){
?>
			<option value="<?=$o->$k?>"<?=($selected==$o->$k)?" selected":""?>><?=$o->$v?></option>
<?php
			}
?>
		</select>
<?php						
		}

		public function multipleHTML($name, $selected=null, $attr=null, $size=null){
?>
		<select name="<?=$name?>[]"<?=$attr?" ".$attr:""?> multiple<?=$size?" size='$size'":""?>>			
<?php
			$k = $this->key;
			$v = $this->value;
			foreach($this->data as $o){
?>
			<option value="<?=$o->$k?>"<?=$selected?(in_array($o->$k,$selected)?" selected":""):""?>><?=$o->$v?></option>
<?php
			}
?>
		</select>
<?php						
		}

		public function optionsListSingle($selected=null){
			$k = $this->key;
			$v = $this->value;
			foreach($this->data as $o){
?>
			<option value="<?=$o->$k?>"<?=($selected==$o->$k)?" selected":""?>><?=$o->$v?></option>
<?php
			}
?>
		</select>
<?php						
		}

		public function optionsListMultiple($selected=null){
			$k = $this->key;
			$v = $this->value;
			foreach($this->data as $o){
?>
			<option value="<?=$o->$k?>"<?=$selected?(in_array($o->$k,$selected)?" selected":""):""?>><?=$o->$v?></option>
<?php
			}
?>
		</select>
<?php						
		}

	}