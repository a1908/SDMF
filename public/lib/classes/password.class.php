<?php
/*-----------
	class password
		
*/	
	
	class Password {
		
		static function salt(){
			$strong = true;
			return bin2hex(openssl_random_pseudo_bytes(16,$strong));
		}
		
		
		static function passToStore($pwd,$salt){
			return openssl_digest($salt.$pwd, "SHA512");
		}
		
		static function generate(){
			$pwd = bin2hex(openssl_random_pseudo_bytes(4));
			return $pwd;
		}
	}
