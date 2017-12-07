<?php
	class HomeController extends Controller{
		
		
		public function __construct($data=array()){
			parent::__construct($data);
		}
		
		public function index(){

			App::getRouter()->redirect("/model/index");

		}
		
		
		public function login(){
			
			if($_POST){
				if( Session::login($_POST[LOGIN_FIELD], $_POST[PASSWORD_FIELD], Zone::getLayout())){
					$base_link = WEB_ROOT.Zone::getWebPrefix().App::getRouter()->getLanguagePath("/%");
					if( isset($_GET["next_page"]) ){
						Router::redirect($base_link."/".$_GET["next_page"]);
					}else{
						Router::redirect($base_link);
					}
				}
			}
		}

		
		public function logout(){
			Session::logout();
		}
		
		
		public function discount_rate(){
			$p = Params::get("site_margin");
			echo "discount = $p<br>";
			$d = 10000/100*$p;
			$d2 = $p + 10;
			$str = "hello".$p;
			echo "discounted price of 10000 will become $d and d2=$d2<br>";
			echo "str is '$str'<br>";
			echo "<h1>Change to 120</h1>";
			Params::set("site_margin",120);
			$p = Params::get("site_margin");
			echo "discount = $p<br>";
			$d = 10000/100*$p;
			$d2 = $p + 10;
			$str = "hello".$p;
			echo "discounted price of 10000 will become $d and d2=$d2<br>";
			echo "str is '$str'<br>";
			echo "<h1>back to to 25</h1>";
			Params::set("site_margin",25);
			$p = Params::get("site_margin");
			echo "discount = $p<br>";
			$d = 10000/100*$p;
			$d2 = $p + 10;
			$str = "hello".$p;
			echo "discounted price of 10000 will become $d and d2=$d2<br>";
			echo "str is '$str'<br>";
			die();
		}
		
		
	}
