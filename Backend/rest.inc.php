<?php
/* File : Rest.inc.php
 * Author : Arun Kumar Sekar
*/
class REST {

    public $_allow = array();
    public $json_content_type = "application/json";
    public $xml_content_type = "application/xml";
    public $_request = array();

    private $_method = "";
    private $_code = 200;

    public function __construct(){
        $this->inputs();
    }

    public function get_referer(){
        return $_SERVER['HTTP_REFERER'];
    }

    public function response($data,$status,$format){
        $this->_code = ($status)?$status:200;
        $this->set_headers($format);
        echo $data;
        exit;
    }
	
	private function get_error($code)
	{
        $errors = array(
			000 => 'null',
            100 => 'Internal Server Error',
            101 => 'Bad Request',
            102 => 'Not authorized',
			103 => 'No Content',
            104 => 'Not Found',
			105 => 'Permission denied',
			106 => 'Invalid social network',
			107 => 'Not Acceptable',
            200 => 'Invalid confirmation code',
            201 => 'Invalid email',
            202 => 'This email already in use',
            203 => 'Invalid user',
            204 => 'Invalid password',
            205 => 'Invalid session',
            206 => 'Invalid uid'
        );
        return array("error" => $code, "message" => $errors[$code]);
    }

    private function get_status_message(){
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($status[$this->_code])?$status[$this->_code]:$status[500];
    }

    public function get_request_method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    private function inputs()
    {	
		switch($this->get_request_method()){
            case "POST":
                //parse_str(file_get_contents("php://input"),$req);
				
				$req = array("0" => file_get_contents("php://input"));
                $req = $this->cleanInputs($req);
				foreach	($req as $k => $v)
					$this->_request = $v;
				if (trim(json_encode($this->_request)) == "\"\"")
					$this->send_error(101);
				$this->_request = json_decode($this->_request);
                break;
            default:
                $this->send_error(107);
                break;
        }
		/*
        switch($this->get_request_method()){
            case "POST":
                $this->_request = $this->cleanInputs($_POST);
                break;
            case "GET":
            case "DELETE":
                $this->_request = $this->cleanInputs($_GET);
                break;
            case "PUT":
                parse_str(file_get_contents("php://input"),$this->_request);
                $this->_request = $this->cleanInputs($this->_request);
                break;
            default:
                $this->response('',406);
                break;
        }
		*/
    }
	
	public static function checkString($param)
	{
		while (strrpos($param, "'"))
		{
			$param = preg_replace("/\'/", "«", $param, 1);
			$param = preg_replace("/\'/", "»", $param, 1);
		}
		return $param;
	}
	
	private function cleanInputs( $data ) {
		$clean_input = array();
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				$clean_input[$k] = $this->cleanInputs( $v );
			}
		} else {
			if ( get_magic_quotes_gpc() ) {
				$data = trim( stripslashes( $data ) );
			}
			$data = strip_tags( $data );
			$clean_input = trim( $data );
		}
		return $clean_input;
	}
	
	public function send_error($code)
	{
		$data = $this->get_error($code);
		$this->response(json_encode($data), 400, "text");
	}

    private function set_headers($format){
        header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		//header('Access-Control-Allow-Origin: *');
        if($format =='json')
        {
            header("Content-Type:".$this->json_content_type);
        }
        elseif($format =='xml')
        {
            header("Content-Type:".$this->xml_content_type);
        }
        else
        {
            header("Content-Type:text/plain");
        }
    }
}
?>