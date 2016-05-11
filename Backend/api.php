<?php
 	require_once("rest.inc.php");
	require_once("db_connection.php");

	class API extends REST 
	{
		public $data = "";
		public $db_conn = null;
		
		public $methods = array(
			"login" 						=> "user.php",
			"logout" 						=> "user.php",
			"registrationCheckEmail" 		=> "user.php",
			"getUserInfo" 					=> "user.php",
			"insertUser" 					=> "user.php",
			"updateUser" 					=> "user.php",
			"confirmEmail" 					=> "user.php",
			"getArticleTypes" 				=> "articles_types.php",
			"getArticleById" 				=> "articles.php",
			"getAllArticles" 				=> "articles.php",
			"createArticle" 				=> "articles.php",
			"createManual" 					=> "manuals.php",
			"getManuals" 					=> "manuals.php",
			"getManualsByUserId" 			=> "manuals.php",
			"deleteManualById" 				=> "manuals.php",
			"getManualById" 				=> "manuals.php"
		);

		public function __construct()
		{
			parent::__construct();
			$this->dbConnect();
		}
		
		private function dbConnect(){
			$this->db_conn = getDBConnection();
		}

		public function get_method_location($name)
		{
			foreach ($this->methods as $k => $v)
				if ($k == $name)
					return $v;
			return null;
		}	

		public function processApi()
		{
			//$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			$func = trim(str_replace("/","",$_REQUEST['x']));
			$location = $this->get_method_location($func);
			if($location != null){	
				require_once($location);
				$func();
			}
			$this->send_error(104);
		}
		
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}

	$api = new API;
	$api->processApi();
?>