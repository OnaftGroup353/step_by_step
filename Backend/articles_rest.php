<?php
require_once("rest.inc.php");

class API extends REST
{

    public $data = "";

    const DB_SERVER = "127.0.0.1";
    const DB_USER = "root";
    const DB_PASSWORD = "";
    const DB = "article_database";

    private $db = NULL;
    private $mysqli = NULL;

    public function __construct()
    {
        parent::__construct();                // Init parent contructor
        $this->dbConnect();                    // Initiate Database connection
    }

    private function dbConnect(){
        $this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
    }

    /*
     * Dynmically call the method based on the query string
     */
    public function processApi(){
        $func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
        if((int)method_exists($this,$func) > 0)
            $this->$func();
        else
            $this->response('',404); // If the method not exist with in this class "Page not found".
    }

    private function article_type(){
        if($this->get_request_method() != "GET"){
            $this->response('',406);
        }
        $id = (int)$this->_request['id'];
        if($id > 0){
            $query="SELECT distinct id, name FROM article_types at where at.id=$id";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            if($r->num_rows > 0) {
                $result = $r->fetch_assoc();
                $this->response($this->json($result), 200); // send user details
            }
        }
        $this->response('',204);	// If no records "No Content" status
    }

}

?>