<?php

	/*! \fn getArticleTypes()
		\brief
			public method
		
			<b>Request</b>
			
			<b>Empty</b> request to get all article types.
			
			OR
							
			{
				"id": "1"
			}
			
			<b>Response</b>
			
			[{
				"id": "123",
				"name": "name"
			}, 
			{
				"id": "123",
				"name": "name"
			}]
			
			OR
			
			{
				"id": "123",
				"name": "name"
			}
		
	*/
	function getArticleTypes()
	{
		global $api;
		if (count($api->_request) == 0)
			get_article_types();
		$api->_request = json_decode($api->_request);
		if (isset($api->_request->id))
			get_article_type_id($api->_request->id);
		$api->response('Page Not Found!',404, "text");
	}
	
	/*! \fn get_article_types()
		\brief returns all article types
	
			private method		
	*/
	function get_article_types()
	{
		global $api;
		$query="SELECT distinct id, name FROM article_types";
		$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
		if($r->num_rows > 0) {
			$rows = array();
			while($res = $r->fetch_assoc()) 
				$rows[] = $res;
			$api->response(json_encode($rows), 200, "json");
		}
		$api->response('No Content!',204, "text");
	}
	
	/*! \fn get_article_type_id($id)
		\brief returns article type by id
		
			private method	
		\param $id article type id		
	*/
	function get_article_type_id($id)
	{
		global $api;
		$id = intval($id);
		if ($id >= 0)
		{
			$query="SELECT distinct id, name FROM article_types where id=$id";
			$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
			if($r->num_rows > 0) {
				$result = $r->fetch_assoc();
				$api->response(json_encode($result), 200, "json");
			}
		}
		$api->response('Not Found!',404, "text");
	}
?>