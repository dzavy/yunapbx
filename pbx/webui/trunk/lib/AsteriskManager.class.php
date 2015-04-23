<?php
define("AERR_CANNOT_CONNECT", 1);
define("AERR_INVALID_SOCKET", 2);
define("AERR_AUTHENTICATION_FAIL", 3);

require_once("AsteriskManagerMessage.class.php");

class AsteriskManager {
	private static $GetConfig_Cache;
	private $Server;
	private $Port;
	private $Socket;

	function __construct($Server = "127.0.0.1", $Port = "5038") {
		$this->Server = $Server;
		$this->Port   = $Port;
	}

	function __destruct() {
		if ($this->Socket) {
			fclose($this->Socket);
		}
	}

	function Connect($User, $Pass) {
		$this->Socket = fsockopen($this->Server, $this->Port, $errno, $errstr);
		if (!$this->Socket) {
			throw new Exception($errstr, constant("AERR_CANNOT_CONNECT"));
		}

		stream_set_timeout($this->Socket, 1);

		$message = new AsteriskManagerMessage();
		$message->SetKey("Action"  , "Login");
		$message->SetKey("UserName", $User);
		$message->SetKey("Secret"  , $Pass);
		$message->SetKey("Events"  , "off");

		$response = $this->Send($message);

		if ($response->GetKey("Response") != "Success") {
			throw new Exception("Authentication failed", constant("AERR_AUTHENTICATION_FAIL"));
		}
	}

	public function Send(AsteriskManagerMessage $query) {
		if ($this->Socket == null) {
			throw new Exception("Couldn't send query to Asterisk Manager. Null Socket.", constant("AERR_INVALID_SOCKET"));
		}

		//echo "<pre>{$query->ToString()}</pre>";
		fputs($this->Socket, $query->ToString());

		do {
			$chunk = fgets($this->Socket);
			$info  = stream_get_meta_data($this->Socket);
			$resp .= $chunk;
		} while ($chunk != "\r\n" && $info != "timed_out");

		return new AsteriskManagerMessage($resp);
	}
	
	public function Run($clicmd) {
		if ($this->Socket == null) {
			throw new Exception("Couldn't send query to Asterisk Manager. Null Socket.", constant("AERR_INVALID_SOCKET"));
		}
		
		$query = new AsteriskManagerMessage();
		$query->SetKey('Action' , 'Command');
		$query->SetKey('Command', $clicmd);
		
		fputs($this->Socket, $query->ToString());
		
		$chunk_no = 0;
		do {
			$chunk = fgets($this->Socket);
			$info  = stream_get_meta_data($this->Socket);
			if ($chunk_no > 2) {
				if ($chunk != "--END COMMAND--\r\n")
					$resp .= $chunk;
			}
			$chunk_no++;
		} while ($chunk != "\r\n" && $info != "timed_out");
		
		return  $resp;
	}
}
?>
