<?php
	
	class Session{
				
		// setFlash is called in controllers
		public static function setFlash($message){
			self::set("flash_message",$message);
		}
		
		// hasFlash is used in templates
		public static function hasFlash(){
			return !is_null(self::get("flash_message"));
		}
		
		public static function flash(){
			echo self::get("flash_message");
			self::delete("flash_message");
		}
		
		public static function set($key, $value){
			$_SESSION[$key] = $value;
		}
		
		public static function get($key){
			if( isset($_SESSION[$key]) ){
				return $_SESSION[$key];
			}
			return null;
		}
		
		public static function delete($key){
			if( isset($_SESSION[$key]) ){
				unset($_SESSION[$key]);
			}
		}
		
		public static function start(){
			session_start();
			if( !self::get("access_token") ){
				self::setAccessToken();
			}
		}
		
		public static function destroy(){
			session_destroy();
		}

		public static function login($login, $password, $site_zone){
			$u = new User;
			
			$authorised = $u->verifyUser($login, $password, $site_zone);

			Session::set("authorised",$authorised);

			if($authorised){
				$user_data = $u->getData();
				Session::set("login",$user_data['login']);
				Session::set("user_role",explode(",", $user_data['user_role']));
				Session::set("user_name",$user_data['name']." ".$user_data['surname']);
				Session::set("user_id",$user_data['id']);
				Session::set("user_layout",$user_data["site_zone"]);
			}else{
				Session::setFlash(__("login-error-invalid-credentials"));
			}
			
			return($authorised);
		}
		
		public static function logout(){
			Session::destroy();
			Router::redirect(WEB_ROOT.Zone::getWebPrefix().App::getRouter()->getLanguagePath("/%"));			
		}
		
		
		public static function updateUserCredentials(){
			$user_id = self::get("user_id");

			if(!Zone::verifyUserCredentials($user_id)){
				self::logout();
			}
		}

		public static function checkACL($acl){

			if(!Session::get("authorised")){
				return false;
			}
	
			if(!$acl){
				return true;
			}
			
			if(!is_array($acl)){
				$acl = explode(",",$acl);
			}
						
			if(in_array("all", $acl)){
				return true;
			}
			return array_intersect($acl, Session::get("user_role"));		
		}
		
		// set access token 
		public static function setAccessToken(){

			$access_token = array("token"=>md5(uniqid(rand(), true)),"ttl"=>2);
			self::set("access_token", $access_token );
			return $access_token;			
		}
		
		public static function getAccessToken(){
			return self::get("access_token");
		}
				
		public function deleteAccessToken(){
			self::delete("access_token");
		}

		
		public static function dump(){
			echo "<pre>";
			var_dump($_SESSION);
			echo "</pre>";
		}
		
	}