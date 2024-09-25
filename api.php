<?php 
	$post = $_REQUEST; 
	$input = json_decode( file_get_contents('php://input'), 1, 1024 ); 
	$post = $input; 
	$return = array('error'=>1, 'msg'=>"UNAUTHORIZED"); 
	$method = strtoupper($_SERVER['REQUEST_METHOD']);

	$DBO = DBO::getInstance();  
	$USER = User::getInstance(); 

//
// VARIABLES
//
	function cmp_function($a, $b, $c){ return ($a[$c] > $b[$c]); } // ASC  uasort($array, 'cmp_function');
	function cmp_function_desc($a, $b, $c){ return ($a[$c] < $b[$c]); } // DESC  uasort($array, 'cmp_function_desc');

	switch( CONTROLLER ){ 
//?
//? USER ----------------------------
//?
		case "create": 
			switch( $method ){
				case "POST": 
					$return = $USER->create( $post );
					break; 
				default: 
					$return = array('success'=>false, 'result'=>array('error'=>"Method not allowed") );
					break; 
			}
			break; 
		case "get": 
			switch( $method ){
				case "GET": 
					$return = $USER->get( $post );
					break; 
				default: 
					$return = array('success'=>false, 'result'=>array('error'=>"Method not allowed"));
					break; 
			} 
			break; 
		case "update": 
			switch( $method ){
				case "PATCH": 
					$return = $USER->update( $post );
					break; 
				default: 
					$return = array('success'=>false, 'result'=>array('error'=>"Method not allowed"));
					break; 
			} 
			break; 
		case "delete": 
			switch( $method ){
				case "DELETE": 
					$return = $USER->delete( $post );
					break; 
				default: 
					$return = array('success'=>false, 'result'=>array('error'=>"Method not allowed"));
					break; 
			} 
			break; 
		default:
			$return = array('success'=>false, 'result'=>array('error'=>"Controller not found", 'controller'=>CONTROLLER, 'method'=> $method));
			break; 
	}

	echo json_encode( $return );
	exit(); 



