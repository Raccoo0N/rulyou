<?php
// 
//
// 
	class User extends Singleton { 
		private $dbo; 
//	
//===================================
		public function __construct( $d=array() ){
			$this->dbo = DBO::getInstance();
		}
//		
//-----------------------------------------------------
		public static function getInstance( $c=null, $name="", $params=array() ){
            return parent::getInstance( $c ? $c : __CLASS__ );
        }

//
//===================================
        protected function get_fields( $d=array() ){
        	$data = array(); 

        	$full_name = isset( $d['full_name'] ) ? App::text( $d['full_name'] ) : 
							( isset( $_REQUEST['full_name'] ) ? App::text( $_REQUEST['full_name'] ) : "" );
			if( $full_name ){
				$data['full_name'] = $full_name; 
			} 

			$role = isset( $d['role'] ) ? App::uid( $d['role'] ) : 	
		    				( isset( $_REQUEST['role'] ) ? App::uid( $_REQUEST['role'] ) : "" ); 
		    if( $role ){
		    	$data['role'] = $role; 
		    }

		    $efficiency = isset( $d['efficiency'] ) ? (int)$d['efficiency'] : 
		    				( isset( $_REQUEST['efficiency'] ) ? (int)$_REQUEST['efficiency'] : 0 ); 
		    if( $efficiency ){
		    	$data['efficiency'] = $efficiency; 
		    }

		    //$status = isset( $d['status'] ) ? (int)$d['status'] : 
		    //				( isset( $_REQUEST['status'] ) ? (int)$_REQUEST['status'] : 0 );
		    //if( $status ){
		    //	$data['status'] = $status; 
		    //}

		    return $data;
        }
//
//===================================
		public function create( $d=array() ){ 
			$data = $this->get_fields( $d ); 

			if( !isset( $data['full_name'] ) ){
				return array(
					'success'=> false, 
					'result'=> array(
						'error'=> "Incorrect parameter full_name"
					) 
				);
			}
			if( $data ){
		    	$ins = $this->dbo->ins(
		    		TABLE_USERS, 
		    		$data
		    	); 

		    	return array(
		    		'success'=> $ins ? true : false,  
		    		'result'=> $ins ? array(
		    							'id'=> $ins 
		    						) :
		    						array(
		    							'error'=> "DB Error"
		    						)
		    	); 
		    }
		    else {
		    	return array(
		    		'success'=>false, 
		    		'result'=> array(
		    			'error'=> "Wrong data format"
		    		)
		    	);
		    }
		}
//
//===================================
		public function get( $d=array() ){ 
			 $id = ACTION ? (int)ACTION : 0; 
			 $inner = isset( $d['inner'] ) ? (int)$d['inner'] : 0; 

			 if( $inner ){ 
			 	return $id ? 
			 			$this->dbo->get("SELECT * FROM `". TABLE_USERS ."` WHERE `id`='". $id ."'") : 
			 			array(
			 				'error'=> "Wrong id" 
			 			);
			} 

			$condition = array(); 

			if( $id ){
				$condition[] = "`id`='". $id ."'"; 
			}

			$full_name = isset( $d['full_name'] ) ? App::text( $d['full_name'] ) : 
							( isset( $_REQUEST['full_name'] ) ? App::text( $_REQUEST['full_name'] ) : '' );
			if( $full_name ){
				$condition[] = "`full_name`='". $full_name ."'";
			}

			$role = isset( $d['role'] ) ? App::uid( $d['role'] ) : 
							( isset( $_REQUEST['role'] ) ? App::uid( $_REQUEST['role'] ) : '' );
			if( $role ){
				$condition[] = "`role`='". $role ."'"; 
			}

			$efficiency = isset( $d['efficiency'] ) ? (int)$d['efficiency'] : 
							( isset( $_REQUEST['efficiency'] ) ? (int)$_REQUEST['efficiency'] : 0 ); 
			if( $efficiency ){
				$condition[] = "`efficiency`='". $efficiency ."'";
			} 

			//$status = isset( $d['status'] ) ? (int)$d['status'] : 
			//				( isset( $_REQUEST['status'] ) ? (int)$_REQUEST['status'] : 0 ); 
			//if( $status ){
			//	$condition['status'] = $status; 
			//} 
			//else {
			//	$condition[] = "`status` IN (1,2)";
			//}

			$Q = "SELECT * 
					FROM `". TABLE_USERS ."` 
					". ( $condition ? "WHERE ". implode(" AND ", $condition ) : "" )." 
					ORDER BY `id` ASC";

			$users = $this->dbo->load( $Q );

			return array(
				'success'=> $users ? true : false, 
				'result'=> $users ? 
							$users : 
							array(
								'error'=> "Users not found"
							) 
			);

		}
//
//===================================
		public function update( $d=array() ){
			$id = ACTION ? (int)ACTION : 0;
			$data = array(); 

			if( $id ){
				$data = $this->get_fields( $d );

  				if( $data ){
  					$upd = $this->dbo->upd(
  						TABLE_USERS, 
  						$data, 
  						array(
  							'id'=> $id 
  						)
  					);

  					// TODO check DBO->upd() 
  					//
  					//if( $upd ){
  						return array(
  							'success'=> true,
  							'result'=> $this->get(
								array(
									'inner'=> true, 
									'id'=>$id
								) 
							)	
						);
  					//} 
  					//else {
  					//	return array(
  					//		'success'=> false, 
  					//		'result'=> array(
  					//			'error'=> "DB Error"
  					//		)
  					//	);
  					//}
  				} 
  				else {
  					return array(
  						'success'=> false, 
  						'result'=> array(
  							'error'=> "Nothing to change"
  						)
  					);
  				}
			}
			else {
				return array(
					'success'=>false,
					'result'=> array(
						'error'=> "User not found"
					) 
				);
			}
		}
//
//=================================== 
		public function delete( $d=array() ){ 
			$id = ACTION ? (int)ACTION : 0; 

			$Q = "DELETE 
					FROM `users` 
					WHERE `id`='". $id ."'"; 
			$del = $this->dbo->query( $Q );

			if( $del ){
				$return = array(
					'success'=> true, 
				); 
			}
			else { 
				$return = array(
					'success'=> false, 
					'result'=> array(
						'error'=> "DB Error"
					) 
				);
			}

			/*
			$upd = $this->dbo->upd(
				TABLE_USERS, 
				array(
					'status'=> 5
				),
				$id ? 
					array(
						'id'=> $id 
					) : 
					"`id`>0"
			);

			$return['success'] = $upd ? true : false; 

			if( $upd && $id ){ 
				$return['result'] = $this->get(
					array(
						'inner'=> true, 
						'id'=>$id
					)
				);

			}
			*/

			return $return;
		}
//	
//=================================== 

	}
//
//
//
