<?php 
	error_reporting( E_ALL | E_STRICT );
	ini_set( 'display_errors', 'On' );
	ini_set("max_execution_time", 30); 
	ini_set('session.gc_maxlifetime', 43200);
	ini_set('session.cookie_lifetime', 43200);
	session_set_cookie_params(0);
	ini_set("session.use_cookies", 1 ); 
	ini_set("session.use_trans_sid", "off"); 
	if( session_id() == '' ){ session_start(); }
	ini_set('memory_limit', '128M');
	ini_set("file_uploads", 1);
	ini_set("upload_tmp_dir", "/tmp");
	ini_set("upload_max_filesize", "10M");
	ini_set("max_file_uploads", 3);

	header("Access-Control-Allow-Origin:*");
    //header("Access-Control-Allow-Credentials=true");
    header("Access-Control-Allow-Methods:GET,POST,PATCH,DELETE");

	if( !defined('BASE_DIR') ){
		define('BASE_DIR', dirname(__FILE__)."/");
	}
	if( !defined('CLASS_DIR') ){ 
		define('CLASS_DIR', BASE_DIR ."classes/"); 
	}
	//
	function cls( $class ){ 
		$filePath = CLASS_DIR . str_replace('_',"/",$class) .'.class.php'; 
		$res = require_once( $filePath );
		if( !$res ){ echo '<h1>include error: '. $class .'</h1>'; }
	}
	spl_autoload_register( 'cls' );

	include_once BASE_DIR ."config.php"; 
	  
	//include_once BASE_DIR ."errorhandler.php"; 

	$input = json_decode( file_get_contents('php://input'), 1, 1024 ); 










	