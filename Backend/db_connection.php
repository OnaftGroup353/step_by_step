<?php
	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB = "manual_database";

	function getDBConnection()
	{
		$db_conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB);
		
		return $db_conn;
	}
?>