<?php
function getArticleTypeId($type)
{
	global $api;
	$query="SELECT id FROM article_types WHERE `name`='$type'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		if ($r->num_rows > 0)
		{
			$res = $r->fetch_assoc();
			return $res["id"];
		}
	return -1;
}

function createManual()
{
    global $api;
    if (trim(json_encode($api->_request)) == "\"\"")
        $api->send_error(101);
    $api->_request = json_decode($api->_request);
    if (!isset($api->_request->date, $api->_request->chapters, $api->_request->literatures
        , $api->_request->header, $api->_request->tableOfContents, $api->_request->metadata))
        $api->send_error(101);
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error(102);
    $date = $api->_request->date;
    $chapters = $api->_request->chapters;
    $literatures = $api->_request->literatures;
    $header = $api->_request->header;
    $tableOfContents = $api->_request->tableOfContents;
    $metadata = $api->_request->metadata;
	$manual_article = (object) array("caption" => $header->name, "article_type_id" => "1", "content" => "", "previous_version_article_id" => "NULL");
	$manual_article_id = createArticle($manual_article);
	if ($manual_article_id == -1)
		$api->send_error(100);
	$manual_article_article = (object) array("article_id" => $manual_article_id, "parent_article_id" => "NULL", "article_number" => "0", "iscurrent" => "1");
	$manual_article_article_id = createManualArticle($manual_article_article);
	if ($manual_article_article_id == -1)
		$api->send_error(100);
	$i = 1;
	foreach ($chapters as $v)
	{
		$chapter_article = (object) array("caption" => $v->name, "article_type_id" => "2", "content" => "", "previous_version_article_id" => "NULL");
		$chapter_article_id = createArticle($chapter_article);
		$chapter_article_article = (object) array("article_id" => $chapter_article_id, "parent_article_id" => $manual_article_article_id, "article_number" => $i, "iscurrent" => "1");
		$chapter_article_article_id = createManualArticle($chapter_article_article);
		for ($j = 1; isset($v->$j); $j++)
		{
			$type = getArticleTypeId($v->$j->type);
			if ($type == -1)
				continue;
			
			$chapter_article_n = (object) array("caption" => $v->$j->title, "article_type_id" => $type, "content" => $v->$j->data, "previous_version_article_id" => "NULL");
			$chapter_article_n_id = createArticle($chapter_article_n);

			$chapter_article_article_n = (object) array("article_id" => $chapter_article_n_id, "parent_article_id" => $chapter_article_article_id, "article_number" => $j, "iscurrent" => "1");
			$chapter_article_article_n_id = createManualArticle($chapter_article_article_n);
		}
		$i++;
	}
	$literatures_article_p = (object) array("caption" => "", "article_type_id" => "9", "content" => "", "previous_version_article_id" => "NULL");
	$literatures_article_p_id = createArticle($literatures_article_p);
	$literatures_article_p_article = (object) array("article_id" => $literatures_article_p_id, "parent_article_id" => $manual_article_article_id, "article_number" => $i, "iscurrent" => "1");
	$literatures_article_p_article_id = createManualArticle($literatures_article_p_article);
	$j = 1;
	foreach ($literatures as $v)
	{
		$literatures_article = (object) array("caption" => "", "article_type_id" => "8", "content" => $v, "previous_version_article_id" => "NULL");
		$literatures_article_id = createArticle($literatures_article);
		$literatures_article_article = (object) array("article_id" => $literatures_article_id, "parent_article_id" => $literatures_article_p_article_id, "article_number" => $j, "iscurrent" => "1");
		$literatures_article_article_id = createManualArticle($literatures_article_article);
		$j++;
	}
	$api->response("OK", 200, "text");
    /*
    {
		"tableOfContents": {},
		"metadata": {},
		"text": "code",
		"media": "picture"
	}
     */
}

function createArticle($article)
{
	global $api;
	$caption = $article->caption;
	$article_type_id = $article->article_type_id;
	$content = $article->content;
	$previous_version_article_id = $article->previous_version_article_id;
	$query="INSERT INTO articles (`caption`, `article_type_id`, `content`, `previous_version_article_id`) VALUES ('$caption', ".$article_type_id.", '$content', ".$previous_version_article_id.")";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	return $r ? $api->db_conn->insert_id : -1;
}

function createManualArticle($manual_article)
{
	global $api;
	$article_id = $manual_article->article_id;
	$parent_article_id = $manual_article->parent_article_id;
	$article_number = $manual_article->article_number;
	$iscurrent = $manual_article->iscurrent;
	$query="INSERT INTO manual_articles (`article_id`, `parent_article_id`, `article_number`, `iscurrent`) VALUES (".$article_id.", ".$parent_article_id.", ".$article_number.", ".$iscurrent.")";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	$res_id = $r ? $api->db_conn->insert_id : -1;
	$user_id = $_SESSION["user_id"];
	$query="INSERT INTO article_authors (`article_id`, `author_id`) VALUES (".$article_id.", ".$user_id.")";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	return $res_id;
}

function getManuals()
{
	global $api;
	$query="SELECT ma.`id`, a.`caption`, a.`update_date` as `date` FROM `manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id` WHERE (`parent_article_id` is NULL) AND (ma.`isdeleted`=0) ORDER BY a.`update_date` DESC";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows > 0) 
	{
		$rows = array();
		while($res = $r->fetch_assoc()) 
			$rows[] = $res;
		$api->response(json_encode($rows), 200, "json");
	}
	$api->send_error(103);
}

function getManualsByUserId()
{
	global $api;
    if (trim(json_encode($api->_request)) == "\"\"")
        $api->send_error(101);
    $api->_request = json_decode($api->_request);
    if (!isset($api->_request->user_id))
        $api->send_error(101);
	$user_id = $api->_request->user_id;
	$query="SELECT ma.`id`, a.`caption`, ma.`update_date` as `date` FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) INNER JOIN `article_authors` as aa ON aa.`article_id`=a.`id` WHERE (`parent_article_id` is NULL) AND (aa.`author_id`=".$user_id.") AND (ma.`isdeleted`=0) ORDER BY ma.`update_date` DESC";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows > 0) 
	{
		$rows = array();
		while($res = $r->fetch_assoc()) 
			$rows[] = $res;
		$api->response(json_encode($rows), 200, "json");
	}
	$api->send_error(103);
}

function deleteManualById()
{
	global $api;
    if (trim(json_encode($api->_request)) == "\"\"")
        $api->send_error(101);
    $api->_request = json_decode($api->_request);
    if (!isset($api->_request->id))
        $api->send_error(101);
	$id = $api->_request->id;
	$query="UPDATE `manual_articles` SET `isdeleted`='1' WHERE `id`=".$id;
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if ($r)
		$api->response("OK", 200, "text");
	$api->send_error(100);
}

function getManualById()
{
    global $api;
    if (trim(json_encode($api->_request)) == "\"\"")
        $api->send_error(101);
    $api->_request = json_decode($api->_request);
    if (!isset($api->_request->id))
        $api->send_error(101);
    $id = $api->_request->id;
	$query="SELECT UNIX_TIMESTAMP(`update_date`) as u_date FROM `manual_articles` WHERE `id`=".$id;
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	$row = $r->fetch_assoc();
	$date = $row["u_date"];
	$query="SELECT a.`caption` as caption FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) WHERE ma.`id`=".$id."";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	$row = $r->fetch_assoc();
	$header = (object) array("name" => $row["caption"]);
	$chapters = getChildArticles($id);
	$literatures = getLiterature($id);
    $tableOfContents = (object) array();
    $metadata = (object) array();
	$res = (object) array("id" => $id, "date" => $date, "chapters" => $chapters, "literatures" => $literatures, "tableOfContents" => $tableOfContents, "metadata" => $metadata);
	$api->response(json_encode($res), 200, "json");
}

function getChildArticles($id)
{
	global $api;
	$query="SELECT ma.`id`, a.`caption`, a.`content`, ma.`article_number`, a.`article_type_id`, at.`name` as `article_type` FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) INNER JOIN `article_types` as at ON at.`id`=a.`article_type_id` WHERE ma.`parent_article_id`=".$id." ORDER BY ma.`article_number` ASC";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows > 0) 
	{
		while($res = $r->fetch_assoc()) 
		{
			if ($res["article_type_id"] == 2)
			{
				$c_child = getChildArticles($res["id"]);
				$child = (object) array ("name" => $res["caption"], "id" => $res["id"]);
				$i = 1;
				if (gettype($c_child) == "array")
					foreach ($c_child as $k => $v)
					{
						$child->$i = $v;
						$i++;
					}
				$ress[] = $child;
			}
			else
			if ($res["article_type_id"] == 1)
			{
				//net
			}
			else
			if ($res["article_type_id"] == 8)
			{
				//net
			}
			else
			if ($res["article_type_id"] == 9)
			{
				//net
			}
			else
			{
				$child = (object) array("type" => $res["article_type"], "title" => $res["caption"], "data" => $res["content"], "id" => $res["id"]);
				$ress[] = $child;
			}
		}
	}
	return $ress;
}

function getLiterature($id)
{
	global $api;
	$query="SELECT ma.`id`, a.`caption`, a.`content`, ma.`article_number`, a.`article_type_id`, at.`name` as `article_type` FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) INNER JOIN `article_types` as at ON at.`id`=a.`article_type_id` WHERE ma.`parent_article_id`=".$id." ORDER BY ma.`article_number` ASC";
	$r = $api->db_conn->query($query) or die($api->db_conn->error);
	if($r->num_rows > 0) 
	{
		while($res = $r->fetch_assoc()) 
		{
			if ($res["article_type_id"] == 8)
			{
				$ress[] = (object) array("id" => $res["id"], "data" => $res["content"]);
			}
			if ($res["article_type_id"] == 9)
			{
				$c_child = getLiterature($res["id"]);
				$ress = $c_child;
			}
		}
	}
	return $ress;
}

function updateManual()
{
    global $api;
	$api->_request = json_decode($api->_request);
	/*
    if (trim(json_encode($api->_request)) == "\"\"")
        $api->send_error(101);
    $api->_request = json_decode($api->_request);
    if (!isset($api->_request->date, $api->_request->chapters, $api->_request->literatures
        , $api->_request->header, $api->_request->tableOfContents, $api->_request->metadata))
        $api->send_error(101);
	
	session_start();
	if (!isset($_SESSION["user_id"]))
		$api->send_error(102);
	*/
    $date = $api->_request->date;
	$id = $api->_request->id;
    $chapters = $api->_request->chapters;
    $literatures = $api->_request->literatures;
    $header = $api->_request->header;
    $tableOfContents = $api->_request->tableOfContents;
    $metadata = $api->_request->metadata;
	$article_tree = getArticleTree($id);
	
	$api->response(json_encode($article_tree), 200, "json");
	
	/*
	$manual_article = (object) array("caption" => $header->name, "article_type_id" => "1", "content" => "", "previous_version_article_id" => "NULL");	
	
	
	$manual_article_id = createArticle($manual_article);
	if ($manual_article_id == -1)
		$api->send_error(100);
	$manual_article_article = (object) array("article_id" => $manual_article_id, "parent_article_id" => "NULL", "article_number" => "0", "iscurrent" => "1");
	$manual_article_article_id = createManualArticle($manual_article_article);
	if ($manual_article_article_id == -1)
		$api->send_error(100);
	$i = 1;
	foreach ($chapters as $v)
	{
		$chapter_article = (object) array("caption" => $v->name, "article_type_id" => "2", "content" => "", "previous_version_article_id" => "NULL");
		$chapter_article_id = createArticle($chapter_article);
		$chapter_article_article = (object) array("article_id" => $chapter_article_id, "parent_article_id" => $manual_article_article_id, "article_number" => $i, "iscurrent" => "1");
		$chapter_article_article_id = createManualArticle($chapter_article_article);
		for ($j = 1; isset($v->$j); $j++)
		{
			$type = getArticleTypeId($v->$j->type);
			if ($type == -1)
				continue;
			
			$chapter_article_n = (object) array("caption" => $v->$j->title, "article_type_id" => $type, "content" => $v->$j->data, "previous_version_article_id" => "NULL");
			$chapter_article_n_id = createArticle($chapter_article_n);

			$chapter_article_article_n = (object) array("article_id" => $chapter_article_n_id, "parent_article_id" => $chapter_article_article_id, "article_number" => $j, "iscurrent" => "1");
			$chapter_article_article_n_id = createManualArticle($chapter_article_article_n);
		}
		$i++;
	}
	$literatures_article_p = (object) array("caption" => "", "article_type_id" => "9", "content" => "", "previous_version_article_id" => "NULL");
	$literatures_article_p_id = createArticle($literatures_article_p);
	$literatures_article_p_article = (object) array("article_id" => $literatures_article_p_id, "parent_article_id" => $manual_article_article_id, "article_number" => $i, "iscurrent" => "1");
	$literatures_article_p_article_id = createManualArticle($literatures_article_p_article);
	$j = 1;
	foreach ($literatures as $v)
	{
		$literatures_article = (object) array("caption" => "", "article_type_id" => "8", "content" => $v, "previous_version_article_id" => "NULL");
		$literatures_article_id = createArticle($literatures_article);
		$literatures_article_article = (object) array("article_id" => $literatures_article_id, "parent_article_id" => $literatures_article_p_article_id, "article_number" => $j, "iscurrent" => "1");
		$literatures_article_article_id = createManualArticle($literatures_article_article);
		$j++;
	}
	$api->response("OK", 200, "text");
	*/
    /*
    {
		"tableOfContents": {},
		"metadata": {},
		"text": "code",
		"media": "picture"
	}
     */
}

function getArticleAsArray($id)
{
	global $api;
	$query="SELECT ma.`id`, a.`caption`, a.`content`, ma.`article_number`, a.`article_type_id`, at.`name` as `article_type`, a.`previous_version_article_id`, ma.`parent_article_id`, ma.`iscurrent` FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) INNER JOIN `article_types` as at ON at.`id`=a.`article_type_id` WHERE ma.`article_id`=".$id."";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if ($r)
		if($r->num_rows > 0) 
		{
			$res = $r->fetch_assoc();
			return $res;
		}
	return NULL;
}

function getArticleTree($id)
{
	global $api;
	$query="SELECT ma.`id`, a.`caption`, a.`content`, ma.`article_number`, a.`article_type_id`, at.`name` as `article_type`, a.`previous_version_article_id`,
	ma.`parent_article_id`, ma.`iscurrent` FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) INNER JOIN `article_types` as at ON 
	at.`id`=a.`article_type_id` WHERE ma.`article_id`=".$id;
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if ($r)
		if($r->num_rows > 0) 
		{
			$res = $r->fetch_assoc();
			switch($res["article_type_id"])
			{
				case 1:
				case 2:
				case 8:
					$art = createArticleClass($res);
					$query="SELECT ma.`id`, a.`caption`, a.`content`, ma.`article_number`, a.`article_type_id`, at.`name` as `article_type`, a.`previous_version_article_id`, ma.`parent_article_id`, ma.`iscurrent` FROM (`manual_articles` as ma INNER JOIN `articles` as a ON ma.`article_id`=a.`id`) INNER JOIN `article_types` as at ON at.`id`=a.`article_type_id` WHERE ma.`parent_article_id`=".$id."";
					$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
					if ($r)
					if($r->num_rows > 0) 
					{
						while($res = $r->fetch_assoc()) 
						{
							$ch = getArticleTree($res["id"]);
							$art->child_articles[] = $ch;
						}
					}
					return $art;
					break;
				default:
					$art = createArticleClass($res);
					return $art;
					break;
			}
		}
	return NULL;
}

function createArticleClass($res)
{
	$r = new article;
	$r->id = $res["id"];
	$r->caption = $res["caption"];
	$r->content = $res["content"];
	$r->article_number = $res["article_number"];
	$r->article_type_id = $res["article_type_id"];
	$r->previous_version_article_id = $res["previous_version_article_id"];
	$r->parent_article_id = $res["parent_article_id"];
	$r->iscurrent = $res["iscurrent"];
	return $r;
}

class article
{
	public $id;
	public $caption;
	public $content;
	public $article_number;
	public $article_type_id;
	public $previous_version_article_id;
	public $parent_article_id;
	public $iscurrent;
	
	public $child_articles;
}

?>