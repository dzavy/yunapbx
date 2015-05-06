<?php
session_start();

setlocale(LC_ALL, "ro_RO");
bindtextdomain ("messages", dirname(__FILE__)."/../locale");
textdomain ("messages");

include_once(dirname(__FILE__).'/smarty_utils.inc.php');


function myprint($var){ //functie pt teste
	print ("<pre>");
	print_r ($var);
	print ("</pre>");	
}

function admin_run($generator_function, $template="", $need_auth=true) {
	if ($need_auth) {
		if (empty($_SESSION['_USER'])) {
			header('Location: Login.php'); 
			die();
			return;
		}
	}
	$Output = $generator_function();

	if ($template != "") {
		$smarty = smarty_init(dirname(__FILE__).'/../admin/templates');
		$smarty->assign('Output', $Output);
		echo $smarty->fetch($template);
	} else {
		echo $Output;
	}
}

function admin_logout() {
	unset($_SESSION['_USER']);
}

//preiau valoarea unei constante din tabela settings
function pbx_var_get($name) {
        global $mysqli;
	$query = "
		SELECT
			Value
		FROM
			Settings
		WHERE
			Name = '".$mysqli->real_escape_string($name)."'
	";

	$result = $mysqli->query($query) or die(__LINE__.__FILE__);
	$row = $result->fetch_object();
	return $row->Value;
}

//atribui o valoare unei constante din tabela settings
function pbx_var_set($name, $value) {
    global $mysqli;
		$query = "DELETE FROM Settings WHERE Name='".$mysqli->real_escape_string($name)."' LIMIT 1";
		$mysqli->query($query) or die(__LINE__.__FILE__);

		$query = "
				INSERT INTO	Settings (Name,Value)
				VALUES ('".$mysqli->real_escape_string($name)."','".$mysqli->real_escape_string($value)."')
		";
		$mysqli->query($query) or die(__LINE__.__FILE__);
}

//adaugat mihai - obtine IP extern afisat in $url
function get_web_page( $url ){
    $options = array( 'http' => array(
        'user_agent'    => 'nelu',      // who am i
        'max_redirects' => 10,          // stop after 10 redirects
        'timeout'       => 120,         // timeout on response
    ) );
    $context = stream_context_create( $options );
    $page    = @file_get_contents( $url, false, $context );
 
    $result  = array( );
    if ( $page != false )
        return $page;
    else if ( !isset( $http_response_header ) )
        return null;    // Bad url, timeout    
}

function ftp_test($server, $user, $password, $path){
	$errors = array();
	// set up basic connection
	$conn_id = ftp_connect($server); 

	// login with username and password
	$login_result = ftp_login($conn_id, $user, $password); 

	// check connection
	if (!$conn_id)  { 
			$errors["Connect"] = true;
			return $errors;			
	}
	
	if (!$login_result){ 
			$errors["Login"] = true;
			return $errors;			
	}
	
	if ($path){
		if (!ftp_chdir($conn_id, $path)){
			$errors["Path"] = true;
			return $errors;			
		}
	}

	$file = '../test.txt';
	$fp = fopen($file, 'r');
	
	// try to upload $file
	if (!ftp_fput($conn_id, $file, $fp, FTP_ASCII)) {
		$errors["Upload"] = true;
		return $errors;			
	}	
	
	if (!ftp_delete($conn_id, $file)) {
		$errors["Delete"] = true;
		return $errors;				
	}			
	// close the FTP stream 
	ftp_close($conn_id);
	return $errors;
}
