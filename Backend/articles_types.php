<?php
require_once("rest.inc.php");
require_once("db_connection.php");

class API extends REST
{
    public $data = "";
	
	public $db_conn = null;

    public function __construct()
    {
        parent::__construct();
        $this->dbConnect();
    }

    private function dbConnect(){
        $this->db_conn = getDBConnection();
    }
	
    public function processApi(){
		
		if($this->get_request_method() == "GET"){
            if ($_REQUEST['req'])
			{
				$req = json_decode($_REQUEST['req']);
				if ($req->id)
					$this->get_article_type_id($req->id);
			}
			else
				$this->get_article_type();
			
        }
		$this->response('',404, "text");
		/*
		if($this->get_request_method() == "POST"){
			
            if ($_REQUEST['req'])
			{
				$req = json_decode($_REQUEST['req']);
				if ($req->name)
					$this->insert_article_type($req);
				else
					$this->response('',404, "text");
			}
			$this->response('',404, "text");
        }
		*/
    }
	
	private function get_article_type()
	{
		$query="SELECT distinct id, name FROM article_types";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if($r->num_rows > 0) {
			$rows = array();
			while($res = $r->fetch_assoc()) 
				$rows[] = $res;
			$this->response(json_encode($rows), 200, "json");
		}
		$this->response('No Content!',204, "text");
	}
	
	private function get_article_type_id($id)
	{
		$id = intval($id);
		if ($id >= 0)
		{
			$query="SELECT distinct id, name FROM article_types where id=$id";
			$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
			if($r->num_rows > 0) {
				$result = $r->fetch_assoc();
				$this->response(json_encode($result), 200, "json");
			}
		}
		$this->response('Not Found!',404, "text");
	}
	
	private function insert_article_type($req)
	{
		$query="INSERT INTO article_types (name) VALUES ('$req->name')";
		$r = $this->db_conn->query($query) or die($this->db_conn->error." ".__LINE__);
		if ($r)
		{
			$res = array('id' => $this->db_con->insert_id, 'name' => $req->name);
			$this->response(json_encode($res), 200, "json");
		}
		$this->response('Internal Server Error',500, "text");
	}

}

	$api = new API;
	$api->processApi();
?>