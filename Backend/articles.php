<?php
/**
 * Created by PhpStorm.
 * User: Aleksandr
 * Date: 25.04.2016
 * Time: 22:55
 */

    function createArticle() {
        global $api;
        $api -> _request = json_decode($api->_request);
        $caption = $api->_request->caption;
        $article_type_id = $api->_request->article_type_id;
        $content = $api->_request->content;
        $update_date = $api->_request->update_date;
        $previous_version_article_id = $api->_request->previous_version_article_id;
        $isdeleted = $api->_request->isdeleted;
        $query="INSERT INTO users (
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
            $res = array('id' => $api->db_con->insert_id);
            $api->response(json_encode($res), 200, "json");
        }
        $api->response('Internal Server Error', 500, "text");
    }

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

    function getArticleById($id)
    {
        global $api;
        $id = intval($id);
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