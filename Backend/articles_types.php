<?php
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