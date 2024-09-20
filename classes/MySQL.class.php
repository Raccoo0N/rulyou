<?php
//
//	public function __construct( )												// конструктор (неплохо, правда?)
//	public function connect( )													// подключение к базе 
//	public function log( $msg = "", $sta=false )								// вывести ошибки
//	public static function clear_string( $text="", $length=256 )				// чистка текста 
//	public static function tobase( $text="" )									// для сохранения в базу 
//	public static function frombase( $text="" ) 								// после получения из базы 
//	public static function query( $query )										// выполнить запрос 
//	public static function exec( $query )										// выполнить запрос с COLLATE 
//	public static function load( $query="", $row=false )						// получить vfcсив строк( хэш(поумолчанию)/массив )
//	public static function get( $query="", $row=false )							// получить одну строку 
//	public static function field( $table="", $field="", $where="" ) 			// получить значение поля
//	public static function cnt( $table="", $where="" )							// получить количество строк
//	public static function upd( $table, $values, $condition, $limit, $add )		// обновить строку
//	public static function ins( $table, $values, $exist )						// добавить строку 
//	DEPRECATED 
//	public function select( $query ) 											// 
//	public static function row( $query="" )
//	public static function fetch( $query="", $row=false )						// получить строку( хэш(поумолчанию)/массив )
//	public static function es( $text )											// удалить инъекции
//	public function escape( $data )												// удалить инъекции
//	public function count( $table="", $where="" )								// получить количество строк
//
//
	class MySQL {
// переменные
//----------------------------------------------------
		private static $instance;
		public $sqlid;		//ID sql-connection
		public $result;		//ID result sql queries
		public $status;		//Status of connect
		public $error;		//Description of last error
		public $db_user;
        public $db_password;
        public $db_name;
        public $db_server;
// конструктор
//----------------------------------------------------
		public function __construct(  ){
			$this->db_server    = DBHOST;
			$this->db_user      = DBUSER;
			$this->db_password  = DBPASSWD;
			$this->db_name      = DBNAME;//
			$this->connect();

		}
// подключение
//----------------------------------------------------
		public function connect( $server="", $user="", $password="", $base="" ){
			$this->sqlid = mysqli_connect( "p:". ( $server ? $server : $this->db_server ), ( $user ? $user : $this->db_user ), ( $password ? $password : $this->db_password ), ( $base ? $base : $this->db_name ) ); 
			//mysqli_pconnect
			if( !$this->sqlid ){ 
				$this->log('No DB connection: ', 1); 
				return false; 
			}
			if( !mysqli_select_db( $this->db_name ) ){ 
				$this->log('No such base: ', 1); 
				return false; 
			}
			$Q = "SET NAMES 'utf8' COLLATE 'utf8_general_ci'";
			mysqli_query( $this->sqlid, $Q );// ){ $this->log('Cant set names: '); }
            $this->status = true;
			return $this->sqlid;
		} 
// выводим ошибки
//----------------------------------------------------
		public function log( $msg="", $sta=false ){
			$this->error = ( $msg ? $msg : "" ) . mysqli_error();
			if( $sta ){ $this->status = false; }
			$fp = @fopen( BASE_DIR ."logs/db/". date("Y-m-d") .".log", "a"); 
			if( $fp ){
				@fwrite( $fp, date("H:i:s") ." ===========================\n". var_export( $this->error, 1 ) ."\n"); 
				@fcloase( $fp ); 
			} 
			return $this->status;
		}
// подготовка текста 
//----------------------------------------------------
		public static function clear_string( $t="", $length=256 ){
			$text = trim( $t );
			//$text = preg_replace( '/[^A-Za-zА-Яа-я0-9ЁёЙй\-\_\.\,\?\!\@\#\=\%\s\"\'\(\)]/im', "", $text );
			$text = preg_replace('/\s*--\s*/', "", $text );
			$text = preg_replace('/[\>]/', "&gt;", $text );
			$text = preg_replace('/[\<]/', "&lt;", $text );
			$text = preg_replace('/[\$]/', "&#36;", $text ); 
			$text = preg_replace('/[\r\n]/', '<br/>', $text ); 
			$text = preg_replace('/[\'\"]/', '&apos;', $text);
			$text = substr( $text, 0, $length ); 
			//$text = htmlentities( $text );
			return $text;
		} 
// 
		public static function tobase( $t="", $len=1024 ){ 
			$text = $t; // urldecode( $t );
			$text = MySQL::clear_string( $text, $len ); 
			// $text = urlencode( $text ); 
			//var_dump( $text );
			return $text; 
		}
		public static function frombase( $t="" ){ 
			$text = $t; 
			$text = preg_replace('/(\<br\/\>)/', '\n', $text );
			// $text = urldecode( $text );
			return $text; 
		}
// запрос
//----------------------------------------------------
		public static function query( $q ){
			if( $q ){ 
				$q = preg_replace('/[\r\n\t]/', ' ', $q );
				$res = mysqli_query( $this->sqlid, $q );
				return $res ? $res : ( TRACE_MYSQL ? $q : false );
			} 
			return false; 
        }

		public static function exec( $q ){
			if( $q ){
				$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
				mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" ); 
				$q = preg_replace('/[\r\n\t]/', ' ', $q );
				$res = mysqli_query( $db, $q );
				return $res ? $res : ( TRACE_MYSQL ? $q : false );
			} 
			return false; 
        }
// загрузка
//----------------------------------------------------
		public static function load( $q="", $row=false ){
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" ); 
			$q = preg_replace('/[\r\n\t]/', ' ', $q );
			$r = mysqli_query( $db, $q );
			if( !$r ){ return false;  }
			else { 
				$ara = array();
				while ( $v = ( $row ? mysqli_fetch_row( $r ) : mysqli_fetch_assoc( $r ) ) ){ 
					array_push( $ara, $v ); 
				}	
				return $ara ? $ara : ( TRACE_MYSQL ? $q : false );
			}
		}
// одна строка
//----------------------------------------------------
		public static function get( $q="", $row=false ){
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" ); 
			$q = preg_replace('/[\r\n\t]/', ' ', $q );
			$result = mysqli_query( $db, $q );
			return $result ? ( $row ? mysqli_fetch_row( $result ) : mysqli_fetch_assoc( $result ) ) : ( TRACE_MYSQL ? $q : false ); 
		}
// значение
//----------------------------------------------------
		public static function field( $t="", $field="", $w="" ){
			if( !$t || !$field || !$w ){return 0; }
			$cond = "";
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
			
			if( is_array( $w ) && count( $w ) ){
				$tmp = array();
				foreach( $w as $k=>$v ){
					$v = in_array( $v, array("NOW()", "CURRENT_TIMESTAMP") ) ? $v : "'". $v ."'";
					array_push( $tmp, ( "`". $k ."`=". $v ) );
				}
				$cond = implode(" AND ", $tmp);
			}
			else { $cond = $w; }

			$Q = "SELECT `". $field ."` 
					FROM `". $t ."` 
					WHERE ". $cond;
			$res = mysqli_query( $db, $Q ); 
			if( !$res ){ return 0; }
			else { 
				$f = mysqli_fetch_assoc( $res );
				return isset( $f[ $field ] ) ? $f[ $field ] : 0;
			}
		}
// подсчет
//----------------------------------------------------
		public static function cnt( $t="", $w="" ){ 
			if( !$t || !$w ){ return 0; }
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
			$cond = "";
			if( is_array( $w ) && count( $w ) ){ 
				$tmp = array(); 
				foreach( $w as $k=>$v ){
					$v = in_array( $v, array("NOW()", "CURRENT_TIMESTAMP") ) ? $v : "'". $v ."'";
					array_push( $tmp, ( "`". $k ."`=". $v ) );
				}
				$cond = implode(" AND ", $tmp);
			}
			else { $cond = $w; }
			$Q = "SELECT COUNT(*) AS 'cnt' FROM `". $t ."` WHERE ". $cond; 
			$result = mysqli_query( $db, $Q );
			if( !$result ){ return 0; }
			else { 
				$ara = mysqli_fetch_assoc( $result );	
				return isset( $ara['cnt'] ) ? $ara['cnt'] : 0;
			}
		}
//
//----------------------------------------------------
		public static function upd( $table="", $values=array(), $condition="", $limit=0, $add=false ){
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME ); //mysqli_select_db( DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" ); 
			if( is_array( $condition ) ){ 
				$tmp = array(); 
				foreach( $condition as $k=>$v ){
					$v = in_array( $v, array("NOW()", "CURRENT_TIMESTAMP") ) ? $v : "'". $v ."'";
					array_push( $tmp, ( "`". $k ."`=". $v ) );
				} 
				$cond = implode(" AND ", $tmp);
			} 
			else { $cond = $condition; }
			// 
			$result = mysqli_query( $db, "SELECT * FROM ". $table ." WHERE ". $cond .";" ); // 
			$keys = array_keys( $values );
			$vals = array_values( $values );
			$sets = ''; 
			// INSERT  
			if( !$result ){
				if( !$add ){ return false; }
				else {
					for( $i=0; $i<count( $keys ); $i++ ){
						if( $sets ){ $sets .= ','; }
						if( in_array( $vals[ $i ], array( 'NOW()', 'CURRENT_TIMESTAMP' ) ) ){ $sets .= "`". $keys[$i] ."`=". $vals[$i]; }
						else { $sets .= "`". $keys[$i] ."`='". $vals[$i] ."'"; }
					}
				}
			} 
			// UPDATE 
			else {
				for( $i=0; $i<count( $keys ); $i++ ){
					if( $sets ){ $sets .=','; }
					if( in_array( $vals[ $i ], array( 'NOW()', 'CURRENT_TIMESTAMP' ) ) ){ $sets .= "`". $keys[$i] ."`=". $vals[$i]; }
					else { $sets .= "`". $keys[$i] ."`='". $vals[$i] ."'"; }
				}
			}
			$Q = "UPDATE `". $table ."` SET ". $sets ." WHERE ". $cond ." LIMIT ". ( $limit ? $limit : 1 ) .";";
			$res = mysqli_query( $db, $Q );
			return $res ? $res : ( TRACE_MYSQL ? $Q : false );
		}
//
//----------------------------------------------------
		public static function ins( $table="", $values=array(), $exist=false ){
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME ); //mysqli_select_db( DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
			$keys = array_keys( $values );
			$vals = array_values( $values );
			$f1 = ''; 
			$f2 = '';
			for( $i=0; $i<count( $keys );$i++ ){
				if( $f1 ){ 
					$f1 .= ','; 
					$f2 .= ','; 
				}
				$f1 .= '`'. $keys[$i] .'`';
				//if( preg_match( "/SELECT/ui", $vals[$i] ) ){ 
				$f2 .= in_array( $vals[$i], array( 'NOW()', 'CURRENT_TIMESTAMP' ) ) ? $vals[$i] : "'". $vals[$i] ."'"; 
			}
			$Q = ( $exist ? "REPLACE" : "INSERT") ." INTO `". $table ."` ". "(". $f1 .") VALUES (". $f2 .")";
			$res = mysqli_query( $db, $Q ); 
			$id = $res ? mysqli_insert_id( $db ) : 0; 
			//var_dump( $id );
			//$id = $id ? $id : mysqli_fetch_assoc( mysqli_query( $db, "SELECT LAST_INSERT_ID();" ) );
			//$id = $id ? $id[0] : 0;
			return $res ? $id : ( TRACE_MYSQL ? $Q : 0 ); 
			// 
		}
//
//----------------------------------------------------

//
//
//
//
// DEPRECATED 
//
//
//
//
// загрузка
//----------------------------------------------------
        public function select( $Q ){
        	$r = mysqli_query( $this->sqlid, $q );
        	if( !$r ){ 
				return false;  
			}
			else { 
				$ara = array();
				while ( $v = ( $row ? mysqli_fetch_row( $r ) : mysqli_fetch_assoc( $r ) ) ){ 
					array_push( $ara, $v ); 
				}	
				return $ara;
			}
        } 
// одна строка
//----------------------------------------------------
		public function row( $q, $row = false ){
			$result = mysqli_query( $this->sqlid, $q );
			if( !$result ){ 
				return false; 
			}
			else { 
				return $row ? mysqli_fetch_row( $result ) : mysqli_fetch_assoc( $result );
			}
		} 
// подсчет
//----------------------------------------------------
		public function count( $t="", $w="" ){ 
			if( !$t || !$w ){ return 0; } 
			mysqli_query( $this->sqlid, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
			$cond = "";
			if( is_array( $w ) && count( $w ) ){
				$tmp = array();
				foreach( $w as $k=>$v ){
					$v = in_array( $v, array("NOW()", "CURRENT_TIMESTAMP") ) ? $v : "'". $v ."'";
					array_push( $tmp, ( "`". $k ."`=". $v ) );
				}
				$cond = implode(" AND ", $tmp);
			}
			else { $cond = $w; }
			$Q = "SELECT COUNT(*) AS cnt FROM `". $t ."` WHERE ". $cond;
			$result = mysqli_query( $this->sqlid, $Q );
			if( !$result ){ return 0; }
			else { 
				$ara = mysqli_fetch_assoc( $result );	
				return isset( $ara['cnt'] ) ? $ara['cnt'] : 0;
			}
		}
// загрузка
//----------------------------------------------------
		public static function fetch( $d=array() ){
			$fields = "";
			if( isset( $d['fields'] ) && is_array( $d['fields'] ) && count( $d['fields'] ) ){
				foreach( $d['fields'] as $k ){
					$fields .= ( $fields ? ", " : "" ) ."`". $k ."`";
				}
			}
			else {
				$fields = "*";
			}

			$table = isset( $d['table'] ) ? App::guard( $d['table'] ) : "";
			if( !$table ){
				return false;
			}

			$where = "";
			if( isset( $d['where'] )  && is_array( $d['where'] ) && count( $d['where'] ) ){
				foreach( $d['where'] as $k=>$v ){
					$where .= ( $where ? " AND " : "" ) ."`". $k ."`='". $v ."'";
				}
			}
			else {
				return false;
			}

			$order = isset( $d['order'] ) ? ( " ORDER BY `". App::guard( $d['order'] ) ."`" ) : "";
			$dir = ( $order && isset( $d['dir'] ) && in_array( $d['dir'], array('ASC','DESC') ) ) ? $d['dir'] : "";

			$offset = isset( $d['offset'] ) ? ( (int)$d['offset'] .", " ) : "";
			$limit = isset( $d['limit'] ) ? (int)$d['limit'] : 10;

			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
			
			$Q = " SELECT ". $fields ." FROM `". $table ."` 
					WHERE ". $where ." ". $order ." ". $dir ." 
					LIMIT ". $offset . $limit;
			$res = mysqli_query( $db, $Q );

			if( $res ){
				$return = array();
				while ( $v = mysqli_fetch_assoc( $res ) ){
					array_push( $return, $v ); 
				}
				return $return;
			}
			return false;
		}
// удаление спецсимволов
//----------------------------------------------------
		public function esc( $text="" ){
			return mysqli_real_escape_string( $this->sqlid, $text );
		}
// удаление спецсимволов
//----------------------------------------------------
		public static function es( $text="" ){
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
			return mysqli_real_escape_string( $db, $text );//mysqli_escape_string( $text );
		}
// удаление спецсимволов
//----------------------------------------------------
		public static function full_escape( $data="" ){
            $data = mysqli_real_escape_string( $this->sqlid, $data );
            $data = get_magic_quotes_gpc() ? stripslashes( $data ) : $data;
			return $data;
		}
// удаление спецсимволов
//----------------------------------------------------
		public static function escape( $data="" ){
			$db = mysqli_connect( DBHOST, DBUSER, DBPASSWD, DBNAME );
			mysqli_query( $db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
            $data = mysqli_real_escape_string( $db, $data );
            $data = get_magic_quotes_gpc() ? stripslashes( $data ) : $data;
			return $data;
		}

//
//----------------------------------------------------











	}// class Mysql
