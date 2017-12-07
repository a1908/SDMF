<?php

	class Params{
		
		public static function get($key){
			$sql = "select `value` from ".PARAMS_TABLE." where `key` = '$key'";
			$stmt = App::$db->query($sql);
			$rec = $stmt->fetch();
			return $rec?$rec->value:null;
		}
		
		public static function set($key, $value){
			$sql = "update ".PARAMS_TABLE." set `value`=? where `key` = '$key'";
			$stmt = App::$db->prepare($sql);
			$stmt->execute(array($value));
		}

		public static function inc($key){
			$sql = "lock tables ".PARAMS_TABLE." write";
			App::$db->query($sql);
			$value = self::get($key);
			self::set($key,$value+1);
			$sql = "unlock tables";
			App::$db->query($sql);
			return $value;
		}


		
		public static function dump(){
			$sql = "select `key`,`value` from ".PARAMS_TABLE;
			$stmt =  App::$db->query($sql);
			$params = $stmt->fetchAll();
			var_dump($params);
		}
	}