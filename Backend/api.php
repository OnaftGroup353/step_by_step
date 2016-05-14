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
			"createManual" 					=> "manuals.php",
			"getManuals" 					=> "manuals.php",
			"getManualsByUserId" 			=> "manuals.php",
			"deleteManualById" 				=> "manuals.php",
			"getManualById" 				=> "manuals.php",
			"updateManual" 					=> "manuals.php"
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
		
		public function getSessionData($token)
		{
			$query="SELECT id as `user_id`, `scope_id`, `session` as `token` FROM users WHERE `session`='$token'";
			$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
			if ($r->num_rows > 0)
			{
				$res = $r->fetch_assoc();
				$session = array("user_id" => $res["user_id"], "token" => $res["token"], "scope_id" => $res["scope_id"]);
				return $session;
			}
			$this->send_error(205);
		}
	}

	$api = new API;
	$api->processApi();
?>