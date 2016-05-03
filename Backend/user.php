<?php


/*! \fn checkEmailInDatabase($email)
	\brief checks user with specified email in database
	
			private method
	\param $email user email
	\return true if user with $email not exist
*/
function checkEmailInDatabase($email) // true if user with $email not exist
{
	global $api;
	$query="SELECT count(*) as cou FROM users WHERE email LIKE '$email'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	$res = $r->fetch_assoc();
	return $res["cou"] == 0;
}

/*! \fn login()
	\brief
		public method
	
		<b>Request</b>
		
		To log in with token:
		
		{
			"token": "token"
		}
		
		To log in with email:
	
		{
			"email": "email",
			"password": "password"
		}
		
		<b>Response</b>
		
		{
			"token": "token",
			"id": "id",
			"scope": "scope_name"
		}
*/
function login()
{
	global $api;		
	if (count($api->_request) == 0)
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$api->_request = json_decode($api->_request);
	if (isset($api->_request->token))
	{
		$token = $api->_request->token;
		if (strrpos($token, "'"))
			$api->send_error("Invalid session!", 400);
		$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE session='$token'";
		$r = $api->db_conn->query($query) or die($api->db_conn->error);
		if($r->num_rows == 0) 
			$api->send_error("Invalid session", 400);
		$res = $r->fetch_assoc();
		session_start();
		$_SESSION["user_id"] = $res["id"];	
		$_SESSION["token"] = $token;	
		$_SESSION["scope_id"] = $res["scope_id"];
		$response = array("token" => $token, "id" => $_SESSION["user_id"], "scope" => $res["scope_name"]);
		$api->response(json_encode($response), 200, "json");
	}
	if (!isset($api->_request->email, $api->_request->password))
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$email = $api->_request->email;
	$password = $api->_request->password;
	//if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
	//	$api->send_error("Invalid email!", 400);
	if (strrpos($email, "'"))
		$api->send_error("Invalid email!", 400);
	if (checkEmailInDatabase($email, $api))
		$api->send_error("Invalid email!", 400);
	if (strrpos($password, "'"))
		$api->send_error("Invalid password!", 400);
	$query="SELECT u.id, u.email, u.password, u.scope_id, s.name as scope_name FROM users as u inner join scope as s on u.scope_id=s.id WHERE email='$email'";
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
	$response = array("token" => $token, "id" => $_SESSION["user_id"], "scope" => $res["scope_name"]);
	$api->response(json_encode($response), 200, "json");
}

/*! \fn logout()
	\brief
		public method
	
		<b>Request</b>
		
		{
			"token": "token"
		}
		
		<b>Response</b>
		
		OK
*/
function logout()
{
	global $api;		
	if (count($api->_request) == 0)
		$api->send_error("Bad Request!", 400);
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
	if (count($api->_request) == 0)
		$api->send_error("Bad Request!", 400);
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
	if (count($api->_request) == 0)
		$api->send_error("Bad Request!", 400);
	$api->_request = json_decode($api->_request);
	if (!isset($api->_request->id))
		$api->send_error("Bad Request!", 400);
	$id = intval($api->_request->id);
	if ($id >= 0)
	{
		session_start();
	$api->send_error($_SESSION["user_id"], 200);
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
	$api->send_error("Invalid user!", 204);
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
	if (count($api->_request) == 0)
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$api->_request = json_decode($api->_request);
	if (!isset($api->_request->email, $api->_request->password))
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$email = $api->_request->email;
	$password = $api->_request->password;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error("Invalid email!", 400);
	if (strrpos($email, "'"))
		$api->send_error("Invalid email!", 400);
	if (strrpos($password, "'"))
		$api->send_error("Invalid password!", 400);
	if (!checkEmailInDatabase($email))
		$api->send_error("This email already in use!", 400);
	
	/*
	??????????????????????????????????????????????????????????????????????????????????????????????????????????????
	SEND MAIL???
	??????????????????????????????????????????????????????????????????????????????????????????????????????????????
	*/
	
	$query="INSERT INTO users (`email`, `password`) VALUES ('$email', '$password')";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
	{
		$res = array('id' => $api->db_conn->insert_id);
		$api->response(json_encode($res), 200, "json");
	}
	$api->send_error('Internal Server Error', 500);
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
	if (count($api->_request) == 0)
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$api->_request = json_decode($api->_request);
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
	$api->send_error('Internal Server Error',500);
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
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	if ($_SESSION["scope_id"] < 2)
		$api->send_error("Permission denied!", 400);
	if (!isset($api->_request->id, $api->_request->banned))
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$id = intval($api->_request->id);
	$banned = intval($api->_request->banned);
	if ($banned != 0 && $banned != 1)
		$api->send_error("Bad Request!"." ".__LINE__, 400);
	$query="SELECT scope_id FROM users WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows == 0) 
		$api->send_error("Invalid user!", 400);
	$res = $r->fetch_assoc();
	$scope_id = $res["scope_id"];
	if ($_SESSION["scope_id"] < $scope_id)
		$api->send_error("Permission denied!", 400);
	$query="UPDATE users SET `banned`='$banned' WHERE id=$id";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error('Internal Server Error',500);
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
	$api->send_error('Internal Server Error',500);
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
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!", 400);
	if (!isset($api->_request->email))
		$api->send_error("Bad Request!", 400);
	$email = $api->_request->email;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 	
		$api->send_error("Invalid email!", 400);
	if (strrpos($email, "'"))
		$api->send_error("Invalid email!", 400);
	if (!checkEmail($email))
		$api->send_error("This email already in use!", 400);
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
	$api->send_error('Internal Server Error',500);
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
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error("Not authorized!"." ".__LINE__, 400);
	
	if (!isset($api->_request->first_name, $api->_request->middle_name, $api->_request->last_name, 
				$api->_request->interest, $api->_request->position, $api->_request->social_network_id, 
				$api->_request->social_network_type))
		$api->send_error("Bad Request!", 400);
	$id = intval($_SESSION["user_id"]);
	$social_network_type = intval($api->_request->social_network_type);
	$first_name = $api->checkString($api->_request->first_name);
	$middle_name = $api->checkString($api->_request->middle_name);
	$last_name = $api->checkString($api->_request->last_name);
	$interest = $api->checkString($api->_request->interest);
	$position = $api->checkString($api->_request->position);
	$social_network_id = $api->checkString($api->_request->social_network_id);
	$query="UPDATE users SET `first_name`='$first_name', `middle_name`='$middle_name', `last_name`='$last_name', `interest`='$interest', `position`='$position', `social_network_id`='$social_network_id' WHERE id='$id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error('Internal Server Error',500);
}

?>