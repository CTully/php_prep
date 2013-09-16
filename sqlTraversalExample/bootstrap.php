<?php
/**
* @ignore
*/
ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);
//defined('HEADER_GUARD') or exit;

/**
* Callback function that handles the shutdown
* of our mini-application function will include
* an application garbage collection such as destroying
* the data base cconnection
*/



//@acess public
//@pramas void
//#return void


function shutdown(){

global $mysqli;

if($mysqli instanceof mysqli){
	$mysqli->close();
}

else{
$mysqli = null;
}

}


/* Register shutdown callback function */
register_shutdown_function('shutdown');



//Load the configuration array deatails.
$config = require_once APP_ROOT_PATH . 'config.php';
$config = is_array($config) ? $config: null;

if(empty($config)){
trigger_error('Application configuration details appear to be invalid.', E_USER_ERROR);

}




//iniatlize the database connection
$mysqli = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

if($mysqli->connect_error){
	trigger_error('Could not connect to application database. please see config settings.', E_USER_ERROR);
	}
	
	//Delete the database password from $config
    //cleanup sensitive config details	
	unset($config['db_pass']);

?>
