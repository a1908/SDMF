<?php

	require_once(dirname(__DIR__)."/lib/includes/scripts/init.php");

	Session::start();

	$url = isset($_GET['url'])?$_GET['url']:"";
	
	try{
			
		App::run($url);

	}catch( Exception $e){
		echo $e->getMessage();
		echo "<pre>";
		var_dump($e);
	}
