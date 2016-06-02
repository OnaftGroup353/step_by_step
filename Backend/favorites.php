<?php

function addFavorite()
{
	global $api;
	if (!isset($api->_request->token))
		$api->send_error(101);
	if ($api->_request->token == null)
		$api->send_error(101);
    if (!isset($api->_request->manual_id))
        $api->send_error(101);
    $manual_id = $api->_request->manual_id;
    $session = $api->getSessionData($api->_request->token);
	$user_id = $session->user_id;
	$query="INSERT INTO favorite_manuals (`article_id`, `user_id`) VALUES ('$manual_id', '$user_id')";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if ($r)
		$api->send_error(000);
	$api->send_error(100);
}

function deleteFavorite()
{
	global $api;
	if (!isset($api->_request->token))
		$api->send_error(101);
	if ($api->_request->token == null)
		$api->send_error(101);
    if (!isset($api->_request->manual_id))
        $api->send_error(101);
    $manual_id = $api->_request->manual_id;
    $session = $api->getSessionData($api->_request->token);
	$user_id = $session->user_id;
	$query="DELETE FROM `favorite_manuals` WHERE `article_id`='$manual_id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if ($r)
		$api->send_error(000);
	$api->send_error(100);
}

function getMyFavorites()
{
	global $api;
	if (!isset($api->_request->token))
		$api->send_error(101);
	if ($api->_request->token == null)
		$api->send_error(101);
    $session = $api->getSessionData($api->_request->token);
	$user_id = $session->user_id;
	$query="SELECT fav.`article_id`, a.`caption`, a.`update_date` as `date`
	FROM (`favorite_manuals` as fav INNER JOINT `manual_articles` as ma ON fav.`article_id`=ma.`id`)
									INNER JOINT `articles` as a ON ma.`article_id`=a.`id`
	WHERE `user_id`='$user_id'";
	$r = $api->db_conn->query($query) or die($api->db_conn->error." ".__LINE__);
	if ($r)
	{
		if ($r->num_rows > 0)
		{
			while($res = $r->fetch_assoc())
			{
				$rrr = (object) array("id" => $res["article_id"], "title" => $res["caption"], "date" => $res["date"]);
				$response[] = $rrr;
			}
			$api->response(json_encode($response), 200, "json");
		}
	}
	$api->send_error(103);
}

?>