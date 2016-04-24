<?php
require_once("rest.inc.php");
require_once("db_connection.php");

class API extends REST
{
    public $data = "";
	public $db_conn = null;

    public function __construct()
    {
        parent::__construct();
        $this->dbConnect();
    }

    private function dbConnect(){
        $this->db_conn = getDBConnection();
    }

    public function processApi(){
		if($this->get_request_method() == "GET"){
            if ($this->_request['req'])
			{
				$req = json_decode($this->_request['req']);
				if ($req->email)
					$this->checkEmail($req->email);
				if ($req->id)
					$this->getUserInfo($req->id);
			}
        }
		if($this->get_request_method() == "POST"){
            if ($this->_request['req'])
			{
				$req = json_decode($$this->_request['req']);
				$this->insert_user($req);
			}
        }
		if($this->get_request_method() == "PUT"){
            if ($this->_request['req'])
			{
				$req = json_decode($$this->_request['req']);
				if ($req->password)
					$this->update_user_password($req);
				if ($req->banned)
					$this->update_user_ban($req);
				if ($req->email)
					$this->update_user_email($req);
				if ($req->scope_id)
					$this->update_user_scope($req);
				$this->update_user($req);
			}
        }
		$this->response('',404, "text");
    }
	
	private function checkString($param)
	{
		while (strrpos($param, "'"))
		{
			$param = preg_replace("/\'/", "«", $param, 1);
			$param = preg_replace("/\'/", "»", $param, 1);
		}
		return $param;
	}
	
	private function checkEmail($email)
	{
		$query="SELECT count(*) as cou FROM users WHERE email LIKE '$email'";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		$res = $r->fetch_assoc();
		return $res["cou"] == 0;
	}
	
	private function getCheckEmail($email)
	{
		$email = $this->checkString($email);
		$res = array('res' => checkEmail($email));
		$this->response(json_encode($res), 200, "json");
	}
	
	private function getUserInfo($id)
	{	
		$id = intval($id);
		if ($id >= 0)
		{
			session_start();
			if ($_SESSION["USER_ID"])
				if ($_SESSION["USER_ID"] == $id)
					$query="SELECT u.id, u.email, u.scope_id, s.name as scope_name, u.first_name, u.middle_name, u.last_name, u.interest, u.position, u.social_network_id, u.social_network_type, u.banned FROM users as u inner join scope as s on u.scope_id=s.id  WHERE u.id=$id";
			if (!$query)
				$query="SELECT u.id, u.scope_id, s.name as scope_name, u.first_name, u.middle_name, u.last_name, u.interest, u.position, u.social_network_id, u.social_network_type, u.banned FROM users as u inner join scope as s on u.scope_id=s.id  WHERE u.id=$id";
			$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
			if($r->num_rows > 0) {
				$res = $r->fetch_assoc();
				$this->response(json_encode($res), 200, "json");
			}
		}
		send_error("Invalid user!", 204);
	}
	
	private function insert_user($req)
	{
		if (!$req->email)
			send_error("Bad Request!", 400);
		if (!$req->password)
			send_error("Bad Request!", 400);
		if(filter_var($req->email, FILTER_VALIDATE_EMAIL)) 	
			send_error("Invalid email!", 400);
		if (!checkEmail($req->email))
			send_error("This email already in use!", 400);
		if (strrpos($req->email, "'"))
			send_error("Invalid email!", 400);
		if (strrpos($req->password, "'"))
			send_error("Invalid password!", 400);
		$email = $req->email;
		$password = $req->password;
		
		/*
		??????????????????????????????????????????????????????????????????????????????????????????????????????????????
		SEND MAIL???
		??????????????????????????????????????????????????????????????????????????????????????????????????????????????
		*/
		
		$query="INSERT INTO users (`email`, `password`) VALUES ('$email', '$password')";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
		{
			$res = array('id' => $this->db_con->insert_id);
			$this->response(json_encode($res), 200, "json");
		}
		$this->response('Internal Server Error',500, "text");
	}
	
	/*
		req = {
			"id": "1", 
			"email": "email", 
			"password": "password", 
			"scope_id": "1", 
			"first_name": "first_name", 
			"middle_name": "middle_name", 
			"last_name": "last_name", 
			"interest": "interest", 
			"position": "position", 
			"social_network_id": "social_network_id", 
			"social_network_type": "1", 
			"banned": "0"
		}
	*/
		
	private function update_user_password($req)
	{
		/*
		req = {
			"password": "password"
		}
		*/
		if (!$req->password)
			send_error("Bad Request!", 400);
		if (strrpos($req->password, "'"))
			send_error("Invalid password!", 400);
		session_start();
		if (!$_SESSION["USER_ID"])
			send_error("Not authorized!", 400);
		$password = $req->password;
		$id = $_SESSION["USER_ID"];
		$query="UPDATE users SET `password`='$password' WHERE id=$id";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
			$this->response("OK", 200, "text");
		$this->response('Internal Server Error',400, "text");
	}
	
	private function update_user_ban($req)
	{
		/*
		req = {
			"id": "1", 
			"banned": "0"
		}
		*/
		if (!$req->id)
			send_error("Bad Request!", 400);
		if (!$req->banned)
			send_error("Bad Request!", 400);
		$banned = intval($req->banned);
		if ($banned != 0 || $banned != 1)
			send_error("Bad Request!", 400);
		session_start();
		if (!$_SESSION["USER_ID"])
			send_error("Not authorized!", 400);
		if ($_SESSION["scope_id"] < 2)
			send_error("Permission denied!", 400);
		$id = $req->id;
		$query="UPDATE users SET `banned`='$banned' WHERE id=$id";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
			$this->response("OK", 200, "text");
		$this->response('Internal Server Error',400, "text");
	}
	
	private function update_user_scope($req)
	{
		/*
		req = {
			"id": "1", 
			"scope_id": "1"
		}
		*/
		if (!$req->id)
			send_error("Bad Request!", 400);
		if (!$req->scope_id)
			send_error("Bad Request!", 400);
		$scope_id = intval($req->scope_id);
		session_start();
		if (!$_SESSION["USER_ID"])
			send_error("Not authorized!", 400);
		if ($_SESSION["scope_id"] != 3)
			send_error("Permission denied!", 400);
		$id = $req->id;
		$query="UPDATE users SET `scope_id`='$scope_id' WHERE id=$id";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
			$this->response("OK", 200, "text");
		$this->response('Internal Server Error',400, "text");
	}
	
	private function update_user_email($req)
	{
		/*
		req = {
			"email": "email"
		}
		*/
		session_start();
		if (!$_SESSION["USER_ID"])
			send_error("Not authorized!", 400);
		if (!$req->email)
			send_error("Bad Request!", 400);
		$email = $req->email;
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	
			send_error("Invalid email!", 400);
		if (!checkEmail($email))
			send_error("This email already in use!", 400);
		if (strrpos($email, "'"))
			send_error("Invalid email!", 400);
		$id = $_SESSION["USER_ID"];
		
		/*
		??????????????????????????????????????????????????????????????????????????????????????????????????????????????
		SEND MAIL???
		??????????????????????????????????????????????????????????????????????????????????????????????????????????????
		*/
		
		$query="UPDATE users SET `email`='$email' WHERE id=$id";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
			$this->response("OK", 200, "text");
		$this->response('Internal Server Error',400, "text");
	}
	
	private function update_user($req)
	{
		/*
		req = {
			"first_name": "first_name", 
			"middle_name": "middle_name", 
			"last_name": "last_name", 
			"interest": "interest", 
			"position": "position", 
			"social_network_id": "social_network_id", 
			"social_network_type": "1"
		}
		*/
		session_start();
		if (!$_SESSION["USER_ID"])
			send_error("Not authorized!", 400);
		if (!$req->first_name)
			send_error("Bad Request!", 400);
		if (!$req->middle_name)
			send_error("Bad Request!", 400);
		if (!$req->last_name)
			send_error("Bad Request!", 400);
		if (!$req->interest)
			send_error("Bad Request!", 400);
		if (!$req->position)
			send_error("Bad Request!", 400);
		if (!$req->social_network_id)
			send_error("Bad Request!", 400);
		if (!$req->social_network_type)
			send_error("Bad Request!", 400);
		$id = $_SESSION["USER_ID"];
		$social_network_type = intval($req->social_network_type);
		$first_name = $this->checkString($req->first_name);
		$middle_name = $this->checkString($req->middle_name);
		$last_name = $this->checkString($req->last_name);
		$interest = $this->checkString($req->interest);
		$position = $this->checkString($req->position);
		$social_network_id = $this->checkString($req->social_network_id);
		$query="UPDATE users SET `first_name`='$first_name', `middle_name`='$middle_name', `last_name`='$last_name', `interest`='$interest', `position`='$position', `social_network_id`='$social_network_id' WHERE id='$id'";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
			$this->response("OK", 200, "text");
		$this->response('Internal Server Error',500, "text");
	}
}
	$api = new API;
	$api->processApi();
?>