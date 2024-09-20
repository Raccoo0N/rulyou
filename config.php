<?php
//
//
	if( !defined('BASE_DIR') ){
		define('BASE_DIR', dirname(__FILE__)."/");
	} 
	//
	// DATABASE
	//
	require_once BASE_DIR ."db_config.php"; 

	define('SAVESERVER', false);
	define('ONREPAIR', false);
	define('SYSTIME', time() );
	define('SITE_URL', isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : "" ); 
	define('BASE_SITE', SITE_URL ); 
	define('BASE_TITLE', ( isset( $_SESSION['settings']['base_title'] ) ? $_SESSION['settings']['base_title'] : '' ) ); 
	define('VER', "LCSTv.0.1b" );
	define('MAX_WHILE', 256);
	define('MAX_PHOTOSIZE', 3145728);
	define('SALT', "WBD_"); 
	define('PAGE_AUTHOR', "LCST");

	define('TRACE_MYSQL', false );
	define('DEBUG_MODE', false ); 
	//
	// NAVIGATION
	//
	if( !defined('CLASS_DIR') ){ define('CLASS_DIR', BASE_DIR ."classes/"); }
	define('TPL_DIR', BASE_DIR ."templates/"); 
	define('TPL_EXT', ".tpl.php");
	define('INC_DIR', BASE_DIR ."controllers/"); 
	define('INC_EXT', ".inc.php"); 
	define('UPLOADS_DIR', BASE_DIR ."userdata/");
	//
	// SETTINGS
	//
	define('ROBOT', isset( $_SESSION['ROBOT'] ) ? false : true );
	define('AUTH', isset( $_SESSION['AUTH'] ) && $_SESSION['AUTH'] ? true : false ); 
	define('UID', ( isset( $_SESSION['UID'] )  ? $_SESSION['UID'] : ( isset( $_SESSION['USER']['uid'] ) ? $_SESSION['USER']['uid'] : false ) ) );  

	if( UID ){
		$USER = User::getInstance(); 
		$_SESSION['USER'] = $USER->get( array('uid'=>UID) );
	}
	//
	//
	if( isset( $_SESSION['ADMIN'] ) ){ define('ADMIN', $_SESSION['ADMIN'] ); } 
	else {
		if( isset( $_SESSION['USER']['role'] ) && $_SESSION['USER']['role'] == 4 ){ 
			$_SESSION['ADMIN'] = true;
			define('ADMIN', true ); 
		} 
		else { define('ADMIN', false ); }
	}  
	//
	// 
	define('LOREM', "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");
	








