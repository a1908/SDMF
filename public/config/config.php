<?php
	Config::set("CompanyName", array(
		"ru" => "название компании",
		"en" => "company name",
		"it" => "italiano madonna bella",
	));
	
	Config::set("CompanyAddress", array(
		"ru" => "Город, улица, дом №",
		"en" => "City, street, building number",
		"it" => "italiano addresse",
	));

	Config::set("CompanySlogan", array(
		"ru" => "слоган",
		"en" => "slogan",
		"it" => "slogan in italian",
	));
	
	Config::set("CompanyPhone","8 123 456 7890");
	
	Config::set("CompanyEmail","info@companydomain.tld");
	Config::set("global title", array(
		"ru" => "заголовок для страниц сайта",
		"en" => "global page title",
		"it" => "global page title",
	));
	
	Config::set("languages",array("ru", "en", "it"));
	
	Config::set("zones",array(
		"default" => array(
			"layout"=>"default",
			"web_prefix" => "",
			"login_path"=>PROTECTED_URL."/home/login",
			"access_type"=>PUBLIC_ACCESS,
			"mixed_roles" => false,
		),
		PROTECTED_URL => array(
			"layout"=>"protected",
			"web_prefix" => "/".PROTECTED_URL,
			"login_path"=>"home/login",
			"access_type"=>PROTECTED_ACCESS,
			"mixed_roles" => true,
		),
	));
	
	Config::set("display_date_format", array(
		"ru" => "d.m.Y",
		"en" => "d/m/Y",
		"it" => "d.m.Y", 
	));

	Config::set("datepicker_locale", array(
		"ru" => "ru",
		"en" => "en-gb",
		"it" => "it", 
	));
	
	Config::set("default_zone_name","default");
	Config::set("default_language","ru");
	
	Config::set("default_controller",DEFAULT_CONTROLLER);
	Config::set("default_method",DEFAULT_METHOD);
	
	Config::set("mail_to", MAIL_TO);
	Config::set("mail_from","automated.mail@companydomain.tld");
	Config::set("default_mail_subject", array(
		"ru" => "Сообщение с сайта ".$_SERVER["HTTP_HOST"],
		"en" => "Site message ".$_SERVER["HTTP_HOST"],
		"it" => "Messaggio dal sito ".$_SERVER["HTTP_HOST"],
	));
	
	if( DB_USE ){
		Config::set("db_host",DB_HOST);
		Config::set("db_name",DB_NAME);
		Config::set("db_user",DB_USER);
		Config::set("db_user_password",DB_USER_PASSWORD);
	}
	

	Config::set("allowed-upload-ext", array(
		IMAGE_UPLOAD => "gif, jpeg, jpg, png",
		DOC_UPLOAD => "pdf, gif, jpeg, jpg, png",
	));
		
	Config::set("items_on_page",array(12=>12, 36=>36, 48=>48, ""=>"Items-on-page-All"));
	
		
