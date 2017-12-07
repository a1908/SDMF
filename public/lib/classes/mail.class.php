<?php
	class Mail{
		protected $mail_to;
		protected $mail_from;
		protected $content_type;
		protected $timestamp;

		public function __construct($mail_to=null, $mail_from=null, $content_type=null, $timestamp=null){
				if( !$mail_to ){
					$mail_to = Config::get('mail_to');
				}
				$this->mail_to = $mail_to;
			
				if( !$mail_from ){
					$mail_from = Config::get('mail_from');
				}
				$this->mail_from = $mail_from;
				
				if( !$content_type ){
					$this->content_type = "text/html";
				}else{
					$this->content_type = "text/plain";
				}
				
				if($timestamp){
					$this->timestamp = true;
				}else{
					$this->timestamp = false;
				}
		}
		
		// sends mail
		// 0 - mail sent OK
		// -1 - error
		// content type: 2 options text/html; text/plain
		public function send($message, $subject=null){
				
			if( !$subject ){
				$s = Config::get("default_mail_subject");
				$subject = $s[App::getRouter()->getLanguage()];
			}	
			
			if($this->timestamp){
				$timestamp = date("F jS Y, h:iA.", time());
			}				
			
			$headers = "From: {$this->mail_from}\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";
			$headers .= "Content-Type: {$this->content_type}; charset=utf-8\r\n";
			
			if($this->content_type=="text/html"){
				$message = "<html><body>$message";
				if($this->timestamp){
					$message .= "<hr>$timestamp";
				}
				$message .= "</body></html>";
			}else{
				if($this->timestamp){
					$message .= "\n".$timestamp;
				}
			}

			if (mail($this->mail_to, $subject, $message, $headers)) {
				return 0;
			} else {
				return -1;
			}
		}		

		// converts form data to message text
		// captions - field names;
		// values - field values
		// data_types - type of data: text, textarea
		public function formToMessage($captions,$values,$data_types){
			
			$message = "";
			
			if($this->content_type == "text/html"){
				$new_line = "<br>";
				foreach($values as $k=>$v){
					$values[$k] = htmlspecialchars($v);
				}
			}else{
				$new_line = "\n";
			}
			
			foreach($captions as $k=>$caption){
				
				$value = $values[$k];
				if( $this->content_type=="text/html"){
					$caption = "<strong>$caption</strong>";
					
					if( $data_types[$k] == "textarea" ){
						$value = nl2br($value);
					}
				}
				
				$message .= $new_line.$caption.$new_line.$value.$new_line;
			}
			
			return $message;
		}
		
	}