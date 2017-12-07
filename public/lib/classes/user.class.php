<?php
/*-----------
	Authentication class
		
*/	
	
	class User extends Model{
		protected $user_data;
		
		
		public function __construct(){
			parent::__construct("_user");
		}
		
		public function getByID($id){
			return($this->getRecord("where id=:id",array(':id'=>$id)));
		}
		
		public function create($name, $surname, $login, $email, $password, $site_zone, $user_role){
			$strong = true;
			$salt = bin2hex(openssl_random_pseudo_bytes(16,$strong));
			$password = openssl_digest($salt.$password, "SHA512");
			
			$stmt = App::$db->prepare("insert into _user (name, surname, login, email, salt, password, site_zone, user_role) values (:name, :surname,  :login, :email, :salt, :password, :site_zone, :user_role)");
			$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->bindValue(':surname', $surname, PDO::PARAM_STR);
			$stmt->bindValue(':login', $login, PDO::PARAM_STR);
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->bindValue(':salt', $salt, PDO::PARAM_STR);
			$stmt->bindValue(':password', $password, PDO::PARAM_STR);
			$stmt->bindValue(':site_zone', $site_zone, PDO::PARAM_STR);
			$stmt->bindValue(':user_role', $user_role, PDO::PARAM_STR);
			$stmt->execute();
	
			$this->id = App::$db->lastInsertId();
			$this->active = 1;
			$this->name = $name;
			$this->surname = $surname;
			$this->login = $login;
			$this->email = $email;
			$this->site_zone = $site_zone;
			$this->user_role = $user_role;
			return $this->id;
		}
		
		public function update($update_info,$id=null){
			
			$update_current = false;
			if(is_null($id)){
				$id = $this->id;
				$update_current = true;
			}
			
			$sql = "update ".USER_TABLE." set ";
			foreach(array_keys($update_info) as $k){
				$sql .= $k."=:".$k;
			}
			
			$sql .= " where id=:id";
			
			$update_info["id"] = $id;
			$stmt = App::$db->prepare($sql);
			$stmt->execute($update_info);			
		}
		
		public function getByLogin($login, $site_zone){
			return($this->getRecord("where login=:login and site_zone=:site_zone",array(':login'=>$login, ':site_zone'=>$site_zone)));			
		}

		public function getByEmail($email, $site_zone){
			return($this->getRecord("where email=:email and site_zone=:site_zone",array(':email'=>$email, ':site_zone'=>$site_zone)));
		}

		public function isPasswordValid($password){

			$password = openssl_digest($this->data["salt"].$password, "SHA512");
			return ($password==$this->data["password"]);
			
		}

		public function verifyUser($login, $password, $site_zone){
			$verified = false;
			if( $this->getByLogin($login, $site_zone) ){
				$verified = $this->isPasswordValid($password);
			}else if( $this->getByEmail($login, $site_zone) ){
				$verified = $this->isPasswordValid($password);
			}else{
				return false;
			}
			$verified = $verified && $this->data["active"];
			return $verified;
		}

		
		public function listUsers($site_zone=null){
			$sql = "select * from ".DB_TABLE.($site_zone?" where site_zone='$site_zone'":"");
			$stmt = App::$db->query($sql);
			return $stmt->fetchAll();
		}
		
		public function listActiveUsers($site_zone=null){
			$sql = "select * from ".DB_TABLE." where active".($site_zone?" and site_zone='$site_zone'":"");
			$stmt = App::$db->query($sql);
			return $stmt->fetchAll();
		}
		
		public function listNonActiveUsers($site_zone=null){
			$sql = "select * from ".DB_TABLE." where not active".($site_zone?" and site_zone='$site_zone'":"");
			$stmt = App::$db->query($sql);
			return $stmt->fetchAll();
		}
		
		public function userCount($rec_def,$param=null){
			return($this->getRecordCount($rec_def, $param));
		}
		
		public static function passwordrestore($email){
			$sql = "select id, concat_ws(' ',name, surname) as name from _user where email=:email";
			$stmt = App::$db->prepare($sql);
			$stmt->execute(array(":email"=>$email));
			
			if( $rec = $stmt->fetch() ){
				// valid email provided - generate password, save it into the user record and send it to the email
				$password = Password::generate();
				$salt = Password::salt();
				$pass_to_save = Password::passToStore($password,$salt);

				$sql = "update _user set salt='$salt', password='$pass_to_save' where id='{$rec->id}'";
				App::$db->exec($sql);
				
				$mail = new Mail($email);
				$message = "<p>".$rec->name."!</p>";
				$message .= "<p>Ваш новый пароль: $password<br>";
				 
				return $mail->send($message,"восстановление пароля");
			}
		}

	}