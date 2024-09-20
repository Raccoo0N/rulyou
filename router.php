<?php 
    $link = "";
    $router = isset( $_REQUEST['route'] ) && $_REQUEST['route'] ? $_REQUEST['route'] : $_SERVER['REQUEST_URI'];
	if( $router ){
		$link = explode('?', $router);
		$link = explode('/', preg_replace('/^\//', '', $link[0] ) );
	} 
	$body =  $link ? $link : array(""); 

	define('ISAPI', true );
	define('CONTROLLER', isset( $body[0] ) ? $body[0] : "" );
	define('ACTION', isset( $body[1] ) ? $body[1] : "" ); 
	define('ITEM_ID',  isset( $body[2] ) ? App::uid( $body[2] ) :  "" );
	define('ITEM_PARAM', isset( $body[3] ) ? App::uid( $body[3] ) : "" );
	define('ITEM_ADD', isset( $body[4] ) ? App::uid( $body[4] ) : "" );
	

	if( ISAPI ){ header("Content-Type: text/json; charset=utf-8"); } 
	else { header("Content-Type: text/html; charset=utf-8"); } 





