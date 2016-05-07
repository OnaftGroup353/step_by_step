<?php
class SmtpMail
{
    public $smtp_username; //Почта
    public $smtp_password; //Пароль
    public $smtp_host; //Хост
    public $smtp_port; //Порт
    public function __construct($smtp_username, $smtp_password, $smtp_host, $smtp_port = 25)
	{
		$this->smtp_username = $smtp_username;
		$this->smtp_password = $smtp_password;
		$this->smtp_host = $smtp_host;
		$this->smtp_port = $smtp_port;
    }
    function send($mailTo, $subject, $message, $headers)
	{
		$contentMail = "To: <".$mailTo.">\r\n";
		$contentMail .= "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: ".$headers."\r\n";
        $contentMail .= "Subject:".$subject."\r\n\r\n\r\n";
        $contentMail .= $message . "\r\n";
        try
		{
            if(!$socket = fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30))
                throw new Exception($errorNumber.".".$errorDescription);
            if (!$this->_parseServer($socket, "220"))
                throw new Exception('Connection error');
            fputs($socket, "EHLO ".$_SERVER['SERVER_NAME']."\r\n");
            if (!$this->_parseServer($socket, "250"))
			{
                fclose($socket);
                throw new Exception('Error of command sending: HELO');
            }
            fputs($socket, "auth login\r\n");
            if (!$this->_parseServer($socket, "334"))
			{
                fclose($socket);
                throw new Exception('Autorization error');
            }
			fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            if (!$this->_parseServer($socket, "334"))
			{
                fclose($socket);
                throw new Exception('Autorization error');
            }
            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            if (!$this->_parseServer($socket, "235"))
			{
                fclose($socket);
                throw new Exception('Autorization error');
            }
			fputs($socket, "MAIL FROM: <".$this->smtp_username.">\r\n");
            if (!$this->_parseServer($socket, "250"))
			{
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }
            fputs($socket, "RCPT TO: <" . $mailTo . ">\r\n");     
            if (!$this->_parseServer($socket, "250"))
			{
                fclose($socket);
                throw new Exception('Error of command sending: RCPT TO');
            }
            fputs($socket, "DATA\r\n");     
            if (!$this->_parseServer($socket, "354"))
			{
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
            }
            fputs($socket, $contentMail."\r\n.\r\n");
            if (!$this->_parseServer($socket, "250"))
			{
                fclose($socket);
                throw new Exception("E-mail didn't sent");
            }
            fputs($socket, "QUIT\r\n");
            fclose($socket);
        }
		catch (Exception $e)
		{
            return  $e->getMessage();
        }
        return true;
    }
	private function _parseServer($socket, $response)
	{
        while (@substr($responseServer, 3, 1) != ' ')
            if (!($responseServer = fgets($socket, 256)))
                return false;
        if (!(substr($responseServer, 0, 3) == $response))
            return false;
        return true; 
    }
}