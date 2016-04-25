<?php
function checkEmailInDatabase($email) // true if user with $email not exist
{
	global $api;
	$query="SELECT count(*) as cou FROM users WHERE email LIKE '$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	$res = $r->fetch_assoc();
	return $res["cou"] == 0;
}

function login()
{
	/*
	
		{
			"token": "token"
		}
		
		||
	
		{
			"email": "email",
			"password": "password"
		}
		
	*/
	global $api;			
	$api->_request = json_decode($api->_request);
	if (isset($api->_request->token))
	{
		$token = $api->_request->token;
		if (strrpos($token, "'"))
			$api->send_error("Invalid session!", 400);
		$query="SELECT id, email, session, scope_id FROM users WHERE session='$token'";
		$r = $api->db_conn->query($query) or die($api->db_conn->error);
		if($r->num_rows == 0) 
			$api->response("Invalid session", 400, "text");
		$res = $r->fetch_assoc();
		session_start();
		$_SESSION["user_id"] = $res["id"];	
		$_SESSION["token"] = $token;	
		$_SESSION["scope_id"] = $res["scope_id"];
		$api->response(json_encode($res), 200, "json");
	}
	if (!isset($api->_request->email, $api->_request->password))
		$api->send_error("Bad Request!", 400);
	$email = $api->_request->email;
	$password = $api->_request->password;
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error("Invalid email!", 400);
	if (strrpos($email, "'"))
		$api->send_error("Invalid email!", 400);
	if (checkEmailInDatabase($email, $api))
		$api->send_error("Invalid email!", 400);
	if (strrpos($password, "'"))
		$api->send_error("Invalid password!", 400);
	$query="SELECT id, email, password, scope_id FROM users WHERE email='$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if($r->num_rows == 0) 
		$api->send_error("Incorrect email!", 400);
	$res = $r->fetch_assoc();
	if ($res["password"] != $password)
		$api->send_error("Incorrect password!", 400);
	session_start();
	$length = 20;
	$time = microtime(true);
	$id = $res["id"];
	$token = bin2hex(openssl_random_pseudo_bytes($length)).md5($time).md5($id);
	$_SESSION["user_id"] = $id;
	$_SESSION["token"] = $token;
	$_SESSION["scope_id"] = $res["scope_id"];
	$query="UPDATE users SET `session`='$token' where `email`='$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	$response = array("token" => $token);
	$api->response(json_encode($response), 200, "json");
}

function logout()
{
	/*
		{
			"token": "token"
		}
	*/
	global $api;			
	$api->_request = json_decode($api->_request);
	session_start();
	if (!isset($_SESSION["token"]))
		$api->send_error("Invalid session!", 400);
	if (!isset($api->_request->token))
		$api->send_error("Invalid session!", 400);
	$token = $api->_request->token;
	if (strrpos($token, "'"))
		$api->send_error("Invalid session!", 400);	
	if ($_SESSION["token"] != $token)
		$api->send_error("Invalid session!", 400);
	$id = $_SESSION["user_id"];
	$query="UPDATE users SET session=NULL WHERE id='$id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	session_destroy();
	$api->response("OK", 200, "json");
}

function registrationCheckEmail() 
{
	global $api;
	$api->_request = json_decode($api->_request);
	
	if (!isset($api->_request->email))
		$api->send_error("Bad Request!", 400);
	$email = $api->_request->email;
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		send_error("Invalid email!", 400);
	if (strrpos($req->email, "'"))
		send_error("Invalid email!", 400);
	$res = array('aviable' => checkEmailInDatabase($email));
	$this->response(json_encode($res), 200, "json");
	/*
		{
			"aviable": "true"
		}
		
		"true" is email is aviable
		"false" if email is not aviable
	*/
}

function getUserInfo()
{	
	/*
		{
			"id": "123"
		}
	*/

	global $api;
	$api->_request = json_decode($api->_request);
	if (!isset($api->_request->id))
		$api->send_error("Bad Request!", 400);
	$id = intval($api->_request->id);
	if ($id >= 0)
	{
		session_start();
		if (isset($_SESSION["user_id"]))
			if ($_SESSION["user_id"] == $id)
				$query="SELECT u.id, u.email, u.scope_id, s.name as scope_name, u.first_name, u.middle_name, u.last_name, u.interest, u.position, u.social_network_id, u.social_network_type, u.banned FROM users as u inner join scope as s on u.scope_id=s.id  WHERE u.id=$id";
		if (!isset($query))
			$query="SELECT u.id, u.scope_id, s.name as scope_name, u.first_name, u.middle_name, u.last_name, u.interest, u.position, u.social_network_id, u.social_network_type, u.banned FROM users as u inner join scope as s on u.scope_id=s.id  WHERE u.id=$id";
		$r = $api->db_conn->query($query) or die($api->db_conn->error);
		if($r->num_rows > 0) {
			$res = $r->fetch_assoc();
			$api->response(json_encode($res), 200, "json");
		}
	}
	send_error("Invalid user!", 204);
}

function insertUser()
{
	/*
		{
			"email": "mail@example.com",
			"password": "password"
		}
	*/
	
	global $api;
	$api->_request = json_decode($api->_request);
	
	if (!isset($api->_request->email, $api->_request->password))
		send_error("Bad Request!", 400);
	$email = $api->_request->email;
	$password = $api->_request->password;
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		send_error("Invalid email!", 400);
	if (strrpos($email, "'"))
		send_error("Invalid email!", 400);
	if (strrpos($password, "'"))
		send_error("Invalid password!", 400);
	if (!checkEmail($email))
		send_error("This email already in use!", 400);
	
	/*
	??????????????????????????????????????????????????????????????????????????????????????????????????????????????
	SEND MAIL???
	??????????????????????????????????????????????????????????????????????????????????????????????????????????????
	*/
	
	$query="INSERT INTO users (`email`, `password`) VALUES ('$email', '$password')";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
	{
		$res = array('id' => $api->db_con->insert_id);
		$api->response(json_encode($res), 200, "json");
	}
	$api->response('Internal Server Error', 500, "text");
}

function updateUser()
{
	global $api;
	$api->_request = json_decode($api->_request);
	
	/*
		{
			"password": "password"
		}
	*/
	if (isset($api->_reques->password))
		update_user_password();
	
	/*
		{
			"id": "1", 
			"banned": "0"
		}
	*/
	if (isset($api->_reques->banned))
		update_user_ban();
	
	/*
		{
			"email": "email"
		}
	*/
	if (isset($api->_reques->email))
		update_user_email();
	
	/*
		{
			"id": "1", 
			"scope_id": "1"
		}
	*/	
	if (isset($api->_reques->scope_id))
		update_user_scope();
	
	
	/*
		{
			"first_name": "first_name", 
			"middle_name": "middle_name", 
			"last_name": "last_name", 
			"interest": "interest", 
			"position": "position", 
			"social_network_id": "social_network_id", 
			"social_network_type": "1"
		}
	*/
	update_user();
}

function update_user_password()
{
	/*
		{
			"password": "password"
		}
	*/
	global $api;
	if (!isset($api->_request->password))
		$api->send_error("Bad Request!", 400);
	$password = $api->_request->password;
	if (strrpos($password, "'"))
		$api->send_error("Invalid password!", 400);
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	$id = $_SESSION["user_id"];
	$query="UPDATE users SET `password`='$password' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->response('Internal Server Error',400, "text");
}

function update_user_ban()
{
	/*
		{
			"id": "1", 
			"banned": "0"
		}
	*/
	global $api;
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	if ($_SESSION["scope_id"] < 2)
		$api->send_error("Permission denied!", 400);
	if (!isset($api->_request->id, $api->_request->banned))
		$api->send_error("Bad Request!", 400);
	$id = intval($api->_request->id);
	$banned = intval($api->_request->banned);
	if ($banned != 0 || $banned != 1)
		$api->send_error("Bad Request!", 400);
	$query="UPDATE users SET `banned`='$banned' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->response('Internal Server Error',400, "text");
}

function update_user_scope()
{
	/*
		{
			"id": "1", 
			"scope_id": "1"
		}
	*/
	global $api;
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	if ($_SESSION["scope_id"] < 3)
		$api->send_error("Permission denied!", 400);
	if (!isset($api->_request->id, $api->_request->scope_id))
		$api->send_error("Bad Request!", 400);
	$id = intval($api->_request->id);
	$scope_id = intval($api->_request->scope_id);
	$query="UPDATE users SET `scope_id`='$scope_id' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->response('Internal Server Error',400, "text");
}

function update_user_email()
{
	/*
		{
			"email": "email"
		}
	*/
	global $api;
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	if (!isset($api->_request->email))
		$api->send_error("Bad Request!", 400);
	$email = $api->_request->email;
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		send_error("Invalid email!", 400);
	if (strrpos($email, "'"))
		send_error("Invalid email!", 400);
	if (!checkEmail($email))
		send_error("This email already in use!", 400);
	$id = $_SESSION["user_id"];
	
	/*
	??????????????????????????????????????????????????????????????????????????????????????????????????????????????
	SEND MAIL???
	??????????????????????????????????????????????????????????????????????????????????????????????????????????????
	*/
	
	$query="UPDATE users SET `email`='$email' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->response('Internal Server Error',400, "text");
}

private function update_user($req)
{
	/*
		{
			"first_name": "first_name", 
			"middle_name": "middle_name", 
			"last_name": "last_name", 
			"interest": "interest", 
			"position": "position", 
			"social_network_id": "social_network_id", 
			"social_network_type": "1"
		}
	*/
	global $api;
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	
	if (!isset($api->_request->first_name, $api->_request->middle_name, $api->_request->last_name, 
				$api->_request->interest, $api->_request->position, $api->_request->social_network_id, 
				$api->_request->social_network_type))
		$api->send_error("Bad Request!", 400);
	$id = intval($_SESSION["user_id"]);
	$social_network_type = intval($api->_request->social_network_type);
	$first_name = $this->checkString($api->_request->first_name);
	$middle_name = $this->checkString($api->_request->middle_name);
	$last_name = $this->checkString($api->_request->last_name);
	$interest = $this->checkString($api->_request->interest);
	$position = $this->checkString($api->_request->position);
	$social_network_id = $this->checkString($api->_request->social_network_id);
	$query="UPDATE users SET `first_name`='$first_name', `middle_name`='$middle_name', `last_name`='$last_name', `interest`='$interest', `position`='$position', `social_network_id`='$social_network_id' WHERE id='$id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->response('Internal Server Error',400, "text");
}