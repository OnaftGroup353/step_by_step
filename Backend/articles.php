<?php

	/*! \fn createArticle()
		\brief
			creates new article
		
			public method
		
			<b>Request</b>
			
			{
				"caption": "caption",
				"article_type_id": "1",
				"content": "content",
				"update_date": "update_date",
				"previous_version_article_id": "122",
				"isdeleted": "0"
			}
			
			<b>Response</b>
			
			{
				"id": "123"
			}
		
	*/
    function createArticle() 
	{
        global $api;
		if (count($api->_request) == 0)
			$api->send_error("Bad Request!", 400);
        $api -> _request = json_decode($api->_request);		
        $caption = $api->_request->caption;
        $article_type_id = $api->_request->article_type_id;
        $content = $api->_request->content;
        $update_date = $api->_request->update_date;
        $previous_version_article_id = $api->_request->previous_version_article_id;
        $isdeleted = $api->_request->isdeleted;
        $query="INSERT INTO articles (
            'caption', 
            'article_type_id', 
            'content', 
            'update_date', 
            'previous_version_article_id', 
            'isdeleted') 
            VALUES ('$caption', 
            '$article_type_id',
            '$content',
            '$update_date',
            '$previous_version_article_id',
            '$isdeleted',
            )";
        $r = $api->db_conn->query($query) or die($api->db_conn->error);
        if ($r)
        {
            $res = array('id' => $api->db_conn->insert_id);
            $api->response(json_encode($res), 200, "json");
        }
        $api->response('Internal Server Error', 500, "text");
    }

	/*! \fn getAllArticles()
		\brief
			returns all articles
		
			public method
		
			<b>Request</b>
			
			Empty
			
			<b>Response</b>
			
			[{
				"id": "123",
				"caption": "caption",
				"article_type_id": "1",
				"content": "content",
				"update_date": "update_date",
				"previous_version_article_id": "122",
				"isdeleted": "0"
			},
			{
				"id": "123",
				"caption": "caption",
				"article_type_id": "1",
				"content": "content",
				"update_date": "update_date",
				"previous_version_article_id": "122",
				"isdeleted": "0"
			}]
		
	*/
    function getAllArticles()
    {
        global $api;
        $query = "SELECT distinct id, 
            caption, 
            article_type_id, 
            content, 
            update_date, 
            previous_version_article_id, 
            isdeleted
            FROM articles";
        $r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
        if($r->num_rows > 0) {
            $rows = array();
            while($res = $r->fetch_assoc())
                $rows[] = $res;
            $api->response(json_encode($rows), 200, "json");
        }
        $api->response('No Content!',204, "text");
    }

	/*! \fn getArticleById()
		\brief
			returns article by id
		
			public method
		
			<b>Request</b>
			
			{
				"id": "1"
			}
			
			<b>Response</b>
			
			{
				"id": "123",
				"caption": "caption",
				"article_type_id": "1",
				"content": "content",
				"update_date": "update_date",
				"previous_version_article_id": "122",
				"isdeleted": "0"
			}
		
	*/
    function getArticleById()
    {
        global $api;
		if (count($api->_request) == 0)
			$api->send_error("Bad Request!", 400);
		$api -> _request = json_decode($api->_request);
		if (!isset($api->_request->id))
			$api->send_error("Bad request!", 400);
        $id = intval($api->_request->id);
        if($id >= 0)
        {
            $query = "SELECT distinct id, 
            caption, 
            article_type_id, 
            content, 
            update_date, 
            previous_version_article_id, 
            isdeleted
            FROM articles
            WHERE id=$id";
            $r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
            if($r->num_rows > 0)
            {
                $result = $r->fetch_assoc();
                $api->response(json_encode($result), 200, "json");
            }
            $api->response('Not Found', 404, "text");
        }
    }

?>