<?php

	class Upload{
		
		static function uploadFiles($type, $upload, $destination){
			if( substr($destination,-1) != DS ){
				$destination .= DS;
			}
			
			if( file_exists($destination) ){
				if( !is_dir($destination) ){
					Session::setFlash(__("File-Upload-Error-Destination-folder-exists-but-not-a-folder"));
					return;
				}
			}else{
				mkdir($destination);
			}
			
			$error = false;
			if( is_array($upload["name"]) ){
				// array of files is uploaded
				foreach( $upload["name"] as $k=>$v){
					// check files for correct images types etc.
					if( $v ){
						$file_data["name"] = $upload["name"][$k]; 
						$file_data["type"] = $upload["type"][$k]; 
						$file_data["tmp_name"] = $upload["tmp_name"][$k]; 
						$file_data["error"] = $upload["error"][$k]; 
						$file_data["size"] = $upload["size"][$k]; 
						
						if( self::checkUpload($type, $file_data) ){
							move_uploaded_file($upload["tmp_name"][$k], $destination.$v);
						}else{
							$error = true;
						}
					}
				}	
					
			}else{
				if( $upload["name"] ){
					if( $result = self::checkUpload($type, $upload) ){
						move_uploaded_file($upload["tmp_name"], $destination.$upload["name"]);
					}else{
						$error = true;
					}
				}
			}
			return !$error;
		}

		static function checkUpload($type, $file_data ){

			if( $file_data["error"] != 0){
				Session::setFlash(htmlspecialchars($file_data["name"]).":".__("File-Upload-Error:").$file_data["error"] );
				return false;
			}

			$check_result = true;
			$allowed_types = Config::get("allowed-upload-types")[$type];
			
			// check file type		
			if( strpos($allowed_types,$file_data["type"]) === false ){
				Session::setFlash(htmlspecialchars($file_data["name"]).":".__("File-Upload-Error:").__("File-Upload-Error-Unsupported-file-type"));
				$check_result = false;
			}
			
			$path = pathinfo($file_data["name"]);

			//check file extension
			if( strpos(Config::get("allowed-upload-ext")[$type],strtolower($path["extension"])) === false ){
				Session::setFlash(htmlspecialchars($file_data["name"]).":".__("File-Upload-Error:").__("File-Upload-Error-Unsupported-file-extension"));
				$check_result = false;
			}
			
			switch( $type ){
				case IMAGE_UPLOAD:
					// check image
					if( $imageinfo = getimagesize($file_data['tmp_name']) ){
						if( strpos($allowed_types, $imageinfo["mime"] ) !== false ){
							$content = file_get_contents($file_data['tmp_name']);
							$pattern = "/php\s|\<passthru|\<shell_exe|\<my_delimdelimUploaded|<\myshellexec\>|\<PHPShell\>|\<FilesMan\>|\<perl\>|\<java\>|\<var\>|\<function\>|\<submit\>|\<base64|\<eval/";
							if( preg_match($pattern, $content, $matches) ){
								// malicious code suspected
								ob_start();
								var_dump($matches);
								$s = ob_get_clean();
								Session::setFlash(htmlspecialchars($file_data["name"]).":".__("File-Upload-Error:").__("File-Upload-Error-Malicious-code-suspected")."<pre>$s</pre>");
								
								//replace it
								$clean_content = preg_replace($pattern,"---",$content);
								file_put_contents($file_data['tmp_name'], $clean_content);
								
								$check_result = true;
							}
						}else{
							// mime type is not allowed
							Session::setFlash(htmlspecialchars($file_data["name"]).":".__("File-Upload-Error:").__("File-Upload-Error-Unsupported-MIME-type"));
							$check_result = false;
						}
					}else{
						// not an image
						Session::setFlash(htmlspecialchars($file_data["name"]).":".__("File-Upload-Error:").__("File-Upload-Error-Image-required"));
						$check_result = false;
					}
				break;
				
				case DOC_UPLOAD:
				break;

				
			}
			return $check_result;
		}
	}