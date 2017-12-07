<?php
	class DB{
		public $connection;
		
		public function __construct($host, $name, $user, $password){
			$options = [
			    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
			    //PDO::ATTR_EMULATE_PREPARES   => false, !!! NOT SURE AS YET WHAT IT DOES
			];			
			$this->connection = new PDO('mysql:dbname='.$name.';host='.$host.";charset=utf8", $user, $password, $options);
		}
		
		public function query($sql){
			if(!$this->connection)
				return false;
				
			$stmt = $this->connection->query($sql);
			
			$result = $stmt->fetchAll();
			
			return $result;
		}
	}