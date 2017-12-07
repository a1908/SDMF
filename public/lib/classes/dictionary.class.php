<?php
//----------------------------------

	class Dictionary{
		
		public static function getValues($term){
			$sql = "select `key`, `value` from _dic where term='$term' and lang='".App::getRouter()->getLanguage()."'";
			$stmt = App::$db->query($sql);
			$values = null;
			while( $rec = $stmt->fetch() ){
				$values[$rec->key] = $rec->value;
			}
			return $values;
		}
	}