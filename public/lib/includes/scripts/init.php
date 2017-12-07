<?php
	function __autoload($className){

		$pathToClass = CLASSES_PATH.strtolower($className).".class.php";
		if(preg_match("/^.+Controller$/", $className)){
			// remove Controller (-10) from the end of the class name
			$pathToClass = CONTROLLERS_PATH.Zone::getLayout().DS.substr_replace(strtolower($className),"",-10).".controller.php";
		}elseif(preg_match("/^.+Model$/", $className)){
			// remove Model (-5) from the end of the class name
			$pathToClass = MODELS_PATH.substr_replace(strtolower($className), "", -5).".php";
		}

		require_once($pathToClass);
	}

	function __($key, $default_value=""){
		return Lang::get($key, $default_value);
	}

	require_once("defs.php");
	require_once(CONFIG_PATH."sitedefs.php");	
	require_once(CONFIG_PATH."config.php");
