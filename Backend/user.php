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
	global $api;
	if (isset($api->_request->token))
		if ($api->_request->token != null)
			loginToken();
	if (isset($api->_request->password))
		loginEmail();
	if (isset($api->_request->uid))
		loginSocial();
	$api->send_error(100);
}

function loginSocial()
{
	global $api;
	if (!isset($api->_request->first_name, $api->_request->last_name, $api->_request->email, $api->_request->uid, $api->_request->network))
		$api->send_error(101);
	$first_name = $api->checkString($api->_request->first_name);
	$last_name = $api->checkString($api->_request->last_name);
	$email = $api->_request->email;
	$uid = $api->_request->uid;
	$network = $api->_request->network;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error(201);
	if (strrpos($email, "'"))
		$api->send_error(201);
	if (strrpos($uid, "'"))
		$api->send_error(206);
	$network_id = 0;
    if ($network == "vkontakte")
        $network_id = 2;
    if ($network == "facebook")
        $network_id = 3;
    if ($network == "google")
        $network_id = 4;
	if ($network_id == 0)
		$api->send_error(106);
	$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE `social_network_id`='$uid' AND `social_network_type`='$network_id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if ($r->num_rows > 0)
	{
		$res = $r->fetch_assoc();
		$length = 20;
		$time = microtime(true);
		$id = $res["id"];
		$token = bin2hex(openssl_random_pseudo_bytes($length)).md5($time).md5($id);
		$query="UPDATE users SET `session`='$token' where `social_network_id`='$uid' AND `social_network_type`='$network_id'";
		$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
		$response = array("token" => $token, "id" => $id, "scope" => $res["scope_name"]);
		$api->response(json_encode($response), 200, "json");
	}
	$query="SELECT id FROM users WHERE email='$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if($r->num_rows == 0) 
	{
		$password = bin2hex(openssl_random_pseudo_bytes(20));
		$query="INSERT INTO users (`email`, `password`, `first_name`, `last_name`, `social_network_id`, `social_network_type`, `ismailconfirmed`) VALUES ('$email', '$password', '$first_name', '$last_name', '$uid', '$network_id', '1')";
		$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
		if ($r)
		{
			$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE `social_network_id`='$uid' AND `social_network_type`='$network_id'";
			$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
			if ($r->num_rows != 0)
			{
				$res = $r->fetch_assoc();
				$length = 20;
				$time = microtime(true);
				$id = $res["id"];
				$token = bin2hex(openssl_random_pseudo_bytes($length)).md5($time).md5($id);
				$query="UPDATE users SET `session`='$token' where `social_network_id`='$uid' AND `social_network_type`='$network_id'";
				$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
				$response = array("token" => $token, "id" => $id, "scope" => $res["scope_name"]);
				$api->response(json_encode($response), 200, "json");
			}
		}
		$api->send_error(100);
	}
	else
	{
		$query="UPDATE users SET `first_name`='$first_name', `last_name`='$last_name', `social_network_id`='$uid', `social_network_type`='$network_id', `ismailconfirmed`='1' WHERE `email`='$email'";
		$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
		if (!$r)
			$api->send_error(100);
		$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE `social_network_id`='$uid' AND `social_network_type`='$network_id'";
		$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
		if ($r->num_rows != 0)
		{
			$res = $r->fetch_assoc();
			$length = 20;
			$time = microtime(true);
			$id = $res["id"];
			$token = bin2hex(openssl_random_pseudo_bytes($length)).md5($time).md5($id);
			$query="UPDATE users SET `session`='$token' where `social_network_id`='$uid' AND `social_network_type`='$network_id'";
			$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
			$response = array("token" => $token, "id" => $id, "scope" => $res["scope_name"]);
			$api->response(json_encode($response), 200, "json");
		}
		$api->send_error(100);
	}
}

function loginEmail()
{
	global $api;
	if (!isset($api->_request->email, $api->_request->password))
		$api->send_error(101);
	$email = $api->_request->email;
	$password = $api->_request->password;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error(201);
	if (strrpos($email, "'"))
		$api->send_error(201);
	if (checkEmailInDatabase($email, $api))
		$api->send_error(201);
	if (strrpos($password, "'"))
		$api->send_error(204);
	$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE email='$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if($r->num_rows == 0)
		$api->send_error(201);
	$res = $r->fetch_assoc();
	if (!password_verify($password, $res["password"]))
		$api->send_error(204);
	$length = 20;
	$time = microtime(true);
	$id = $res["id"];
	$token = bin2hex(openssl_random_pseudo_bytes($length)).md5($time).md5($id);
	$query="UPDATE users SET `session`='$token' where `email`='$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	$response = array("token" => $token, "id" => $id, "scope" => $res["scope_name"]);
	$api->response(json_encode($response), 200, "json");
}

function loginToken()
{
	global $api;
	$token = $api->_request->token;
	if (strrpos($token, "'"))
		$api->send_error(205);
	$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE session='$token'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows == 0) 
		$api->send_error(205);
	$res = $r->fetch_assoc();
	$response = array("token" => $token, "id" => $res["id"], "scope" => $res["scope_name"]);
	$api->response(json_encode($response), 200, "json");
}

function logout()
{
	global $api;
	if (!isset($api->_request->token))
		$api->send_error(101);
	if ($api->_request->token == null)
		$api->send_error(205);
	$token = $api->_request->token;
	if (strrpos($token, "'"))
		$api->send_error(205);
	$query="UPDATE users SET session=NULL WHERE `session`='$token'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	$api->response("OK", 200, "text");
}

/*! \fn registrationCheckEmail()
	\brief
		public method
	
		Check available email when registering new user
	
		<b>Request</b>
	
		{
			"email": "email"
		}
		
		<b>Response</b>
		
		{
			"aviable": "true"
		}
*/
function registrationCheckEmail() 
{
	global $api;
	if (!isset($api->_request->email))
		$api->send_error(101);
	$email = $api->_request->email;
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		send_error(201);
	if (strrpos($api->_request->email, "'"))
		send_error(201);
	$res = array('aviable' => checkEmailInDatabase($email));
	$this->response(json_encode($res), 200, "json");
}

/*! \fn getUserInfo()
	\brief
		public method
	
		<b>Request</b>
	
		{
			"id": "123"
		}
		
		<b>Response</b>
		
		Return when looking a user profile
		
		{
			"id": "123",    
			"scope_id": "1",
			"scope_name": "User",
			"first_name": "first_name",
			"middle_name": "middle_name",
			"last_name": "last_name",
			"interest": "interest",
			"position": "position",
			"social_network_id": "123411231231",
			"social_network_type": "social_network_type",
			"banned": "0"
		}
		
		<b>OR</b>
		
		Return in personal cabinet
		
		{
			"id": "123",  
			"email": "email",
			"scope_id": "1",
			"scope_name": "User",
			"first_name": "first_name",
			"middle_name": "middle_name",
			"last_name": "last_name",
			"interest": "interest",
			"position": "position",
			"social_network_id": "123411231231",
			"social_network_type": "social_network_type",
			"banned": "0"
		}
		
*/
function getUserInfo()
{	
	global $api;
	if (!isset($api->_request->id))
		$api->send_error(101);
	$id = intval($api->_request->id);
	if ($id >= 0)
	{
		if (isset($api->_request->token))
			if ($api->_request->token != null)
				if (strrpos($api->_request->token, "'") == -1)
					$session = $api->getSessionData($api->_request->token);
		if (isset($session))
			if ($session["user_id"] == $id)
				$query="SELECT u.id, u.email, u.scope_id, s.name as scope_name, u.first_name, u.middle_name, u.last_name, u.interest, u.position, u.social_network_id, u.social_network_type, u.banned FROM users as u inner join scope as s on u.scope_id=s.id  WHERE u.id=$id";
		if (!isset($query))
			$query="SELECT u.id, u.scope_id, s.name as scope_name, u.first_name, u.middle_name, u.last_name, u.interest, u.position, u.social_network_id, u.social_network_type, u.banned FROM users as u inner join scope as s on u.scope_id=s.id  WHERE u.id=$id";
		$r = $api->db_conn->query($query) or die($api->db_conn->error);
		if($r->num_rows > 0) 
		{
			$res = $r->fetch_assoc();
			$api->response(json_encode($res), 200, "json");
		}
	}
	$api->send_error(104);
}

/*! \fn insertUser()
	\brief
		public method
	
		<b>Request</b>
	
		{
			"email": "mail@example.com",
			"password": "password"
		}
		
		<b>Response</b>
		
		{
			"id": "123"
		}
		
*/
function insertUser()
{
	global $api;
	if (!isset($api->_request->email, $api->_request->password))
		$api->send_error(101);
	$email = $api->_request->email;
	$password = $api->_request->password;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error(201);
	if (strrpos($email, "'"))
		$api->send_error(201);
	if (strrpos($password, "'"))
		$api->send_error(204);
	if (!checkEmailInDatabase($email))
		$api->send_error(202);
	$password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 11));
	$length = 20;
	$time = microtime(true);
	$confirmationCode = bin2hex(openssl_random_pseudo_bytes($length)).md5($time);
	$query="INSERT INTO users (`email`, `password`, `mailconfimationcode`) VALUES ('$email', '$password', '$confirmationCode')";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
	{
		send_mail($email, $confirmationCode);
		$res = array('id' => $api->db_conn->insert_id);
		$api->response(json_encode($res), 200, "json");
	}
	$api->send_error(100);
}

/*! \fn updateUser()
	\brief
		public method
	
		<b>Request</b>
		
		User changes password
		
		{
			"password": "password"
		}
		
		Moderator banning user
		
		{
			"id": "1", 
			"banned": "0"
		}
		
		User changes email
		
		{
			"email": "email"
		}
		
		Administrator changes user scope
		
		{
			"id": "1", 
			"scope_id": "1"
		}
		
		User changes his personal information
		
		{
			"first_name": "first_name", 
			"middle_name": "middle_name", 
			"last_name": "last_name", 
			"interest": "interest", 
			"position": "position", 
			"social_network_id": "social_network_id", 
			"social_network_type": "1"
		}
		
		<b>Response</b>
		
		OK
		
*/
function updateUser()
{
	global $api;
	if (!isset($api->_request->token))
		$api->send_error(205);
	if ($api->_request->token == null)
		$api->send_error(205);
	if (isset($api->_request->password))
		update_user_password();
	if (isset($api->_request->banned))
		update_user_ban();
	if (isset($api->_request->email))
		update_user_email();
	if (isset($api->_request->scope_id))
		update_user_scope();
	update_user();
}

/*! \fn update_user_password()
	\brief changes user password
	
		private method		
*/
function update_user_password()
{
	/*
		{
			"password": "password"
		}
	*/
	global $api;
	if (!isset($api->_request->password))
		$api->send_error(101);
	$password = $api->_request->password;
	if (strrpos($password, "'"))
		$api->send_error(204);
	$session = $api->getSessionData($api->_request->token);
	$id = $session["user_id"];
	$query="UPDATE users SET `password`='$password' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error(100);
}

/*! \fn update_user_ban()
	\brief ban/unban user
	
		private method		
*/
function update_user_ban()
{
	/*
		{
			"id": "1", 
			"banned": "0"
		}
	*/
	global $api;
	if (!isset($api->_request->id, $api->_request->banned))
		$api->send_error(101);
	$session = $api->getSessionData($api->_request->token);
	if ($session["scope_id"] < 2)
		$api->send_error(105);
	$id = intval($api->_request->id);
	$banned = intval($api->_request->banned);
	if ($banned != 0 && $banned != 1)
		$api->send_error(101);
	$query="SELECT scope_id FROM users WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows == 0) 
		$api->send_error(203);
	$res = $r->fetch_assoc();
	$scope_id = $res["scope_id"];
	if ($session["scope_id"] < $scope_id)
		$api->send_error(105);
	$query="UPDATE users SET `banned`='$banned' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error(100);
}

/*! \fn update_user_scope()
	\brief change user scope
	
		private method		
*/
function update_user_scope()
{
	/*
		{
			"id": "1", 
			"scope_id": "1"
		}
	*/
	global $api;
	if (!isset($api->_request->id, $api->_request->scope_id))
		$api->send_error(101);
	$session = $api->getSessionData($api->_request->token);
	if ($session["scope_id"] < 3)
		$api->send_error(105);
	$id = intval($api->_request->id);
	$scope_id = intval($api->_request->scope_id);
	$query="UPDATE users SET `scope_id`='$scope_id' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error(100);
}

/*! \fn update_user_email()
	\brief change user email
	
		private method		
*/
function update_user_email()
{
	/*
		{
			"email": "email"
		}
	*/
	global $api;
	$session = $api->getSessionData($api->_request->token);
	if (!isset($api->_request->email))
		$api->send_error(101);
	$email = $api->_request->email;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error(201);
	if (strrpos($email, "'"))
		$api->send_error(201);
	if (!checkEmail($email))
		$api->send_error(202);
	$id = $session["user_id"];
	$length = 20;
	$time = microtime(true);
	$confirmationCode = bin2hex(openssl_random_pseudo_bytes($length)).md5($time);
	$query="UPDATE users SET `email`='$email', `mailconfimationcode`='$confirmationCode',`ismailconfirmed`='0'  WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
	{
		send_mail($email, $confirmationCode);
		$api->response("OK", 200, "text");
	}
	$api->send_error(100);
}

function confirmEmail()
{
	/*
		{
			"confirmationCode": "123qwerty123"
		}
	*/
	global $api;
	if (trim(json_encode($api->_request)) == "\"\"")
		$api->send_error(101);
	$api->_request = json_decode($api->_request);
	if (!isset($api->_request->confirmationCode))
		$api->send_error(101);
	$confirmationCode = $api->_request->confirmationCode;
	if (strrpos($confirmationCode, "'"))
		$api->send_error(200);
	$query="SELECT count(*) as COU FROM users WHERE `mailconfimationcode`='$confirmationCode'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if (!$r)
		$api->send_error(100);
	$res = $r->fetch_assoc();
	if ($res["COU"] == 0)
		$api->send_error(200);
	$query="UPDATE users SET `ismailconfirmed`='1'  WHERE `mailconfimationcode`='$confirmationCode'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error(100);
}

/*! \fn update_user()
	\brief change user information
	
		private method		
*/
function update_user()
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
	$session = $api->getSessionData($api->_request->token);
	if (!isset($api->_request->first_name, $api->_request->middle_name, $api->_request->last_name, 
				$api->_request->interest, $api->_request->position, $api->_request->social_network_id, 
				$api->_request->social_network_type))
		$api->send_error(101);
	$id = intval($session["user_id"]);
	$social_network_type = intval($api->_request->social_network_type);
	$first_name = $api->checkString($api->_request->first_name);
	$middle_name = $api->checkString($api->_request->middle_name);
	$last_name = $api->checkString($api->_request->last_name);
	$interest = $api->checkString($api->_request->interest);
	$position = $api->checkString($api->_request->position);
	$social_network_id = $api->checkString($api->_request->social_network_id);
	$query="UPDATE users SET `first_name`='$first_name', `middle_name`='$middle_name', `last_name`='$last_name', `interest`='$interest', `position`='$position', `social_network_id`='$social_network_id', `social_network_type`='$social_network_type' WHERE id='$id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error(100);
}

function send_mail($mailTO, $confirmationCode)
{
	require_once "mail.php";
}

?>