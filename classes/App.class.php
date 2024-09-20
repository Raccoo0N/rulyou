<?php
// 
// 
	class App {
// 
//====================================================================
		public static function test( $d=array() ){
			if( isset( $d['email'] ) ){ 
				return preg_match( "/^\w+[A-Za-z0-9\_\.\-]*@(((([a-z0-9]{2,})|([a-z0-9][-][a-z0-9]+))[\.][a-z0-9])|([a-z0-9]+[-]?))+[a-z0-9]+\.([a-z]{2,7})$/iu", $d['email'] ); 
			}
			if( isset( $d['phone'] ) ){
				return preg_match( '/^\+7\(\d{3}\)\d{7}$/iu', $d['phone'] );
			} 
			if( isset( $d['url'] ) ){

			}
			return false;
		}
// 
//====================================================================
		public static function filter( $d=array() ){
			if( isset( $d['email'] ) ){
				return filter_var( $d['email'], FILTER_VALIDATE_EMAIL );
			} 
			if( isset( $d['domain'] ) ){
				return filter_var( $d['domain'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME );
			} 
			if( isset( $d['ip'] ) ){
				return filter_var( $d['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
			} 
			if( isset( $d['regexp'] ) ){
				return filter_var( $d['regextp'], FILTER_VALIDATE_REGEXP, FILTER_FLAG_IPV4 );
			}
			return false; 
		}
// 
//====================================================================
		public static function hash( $text="" ){
			$t = preg_replace( '/[^a-zA-Z0-9]/i', '', $t );
			return $t;
		}
// 
//====================================================================
		public static function randomstring( $length=8 ){
			$symbols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.@';
			$text = "";
			$max = $strlen( $symbols );
			for( $i=0; $i<$length; $i++ ){ 
				$text .= substr( $symbols, rand(0,$max), 1 ); 
			}
			return $text;
		}
// 
//====================================================================
		public static function createCookie( $name, $value='', $maxage=0, $path='', $domain='', $secure=false, $HTTPOnly=false ){ 
			$ob = ini_get('output_buffering'); 
			if( headers_sent() && (bool) $ob === false || strtolower($ob) == 'off' ){ 
				return false; 
			}
			if( !empty($domain) ){ 
				if ( strtolower( substr($domain, 0, 4) ) == 'www.' ){ 
					$domain = substr($domain, 4); 
				}
				if ( substr($domain, 0, 1) != '.' ){
					$domain = '.'. $domain;
				}
				$port = strpos($domain, ':');
				if ( $port !== false ){ 
					$domain = substr($domain, 0, $port); 
				}
			}
			header('Set-Cookie: '. rawurlencode($name) .'='. rawurlencode($value) 
										.(empty($domain) ? '' : '; Domain='.$domain) 
										.(empty($maxage) ? '' : '; Max-Age='.$maxage) 
										.(empty($path) ? '' : '; Path='.$path) 
										.(!$secure ? '' : '; Secure') 
										.(!$HTTPOnly ? '' : '; HttpOnly'), false); 
			return true; 
		}
// 
//====================================================================
		public static function uid( $t="" ){ 
			if( is_array( $t ) ){ 
				var_dump( $t ); 
				return ""; 
			}
			else {
				$text = trim( $t );
				$text = substr( $text, 0, 32 );
				$text = preg_replace( '/[^A-Za-z0-9-_\,\.]/i', "", $text );
				$text = preg_replace('/\s*--\s*/', "", $text );
				return $text; 
			}
		}
// 
//====================================================================
		private static function _prepare( $value ){
			$value = strval($value);
			$value = stripslashes($value);
			$value = str_ireplace(array("\0", "\a", "\b", "\v", "\e", "\f"), ' ', $value);
			$value = htmlspecialchars_decode($value, ENT_QUOTES);	
			return $value;
		}
// 
//====================================================================
		public static function text( $value, $default = '' ){
			$value = self::_prepare($value);
			$value = str_ireplace(array("\t"), ' ', $value);			
			$value = preg_replace(array(
				'@<\!--.*?-->@s',
				'@\/\*(.*?)\*\/@sm',
				'@<([\?\%]) .*? \\1>@sx',
				'@<\!\[CDATA\[.*?\]\]>@sx',
				'@<\!\[.*?\]>.*?<\!\[.*?\]>@sx',	
				'@\s--.*@',
				'@<script[^>]*?>.*?</script>@si',
				'@<style[^>]*?>.*?</style>@siU', 
				'@<[\/\!]*?[^<>]*?>@si',			
			), ' ', $value);		
			$value = strip_tags($value); 		
			$value = str_replace(array('/*', '*/', ' --', '#__'), ' ', $value); 
			$value = preg_replace('/[ ]+/', ' ', $value);			
			$value = trim($value);
			$value = htmlspecialchars($value, ENT_QUOTES);	
			return (strlen($value) == 0) ? $default : $value;
		}
//
//====================================================================

// 
//====================================================================
		public static function format( $s=0 ){
			return preg_replace( '/(\d)(?=(\d{3})+(?!\d))/i', "$1 ", $s );
		}
// 
//====================================================================
		public static function bablo( $s=0 ){
			return preg_replace( '/(\d)(?=(\d{3})+(?!\d))/i', "$1 ", sprintf( "%01.2f", $s ) );
		}
// 
//====================================================================

//
//====================================================================

//
//====================================================================

//
//====================================================================

//
//====================================================================
		public static function no_zeros( $text="" ){
			return rtrim( 
				rtrim( 
					preg_replace( '/\,/', '.', trim( $text ) ), "0" 
				), "." 
			);
		}
//
//====================================================================
		public static function css( $name="" ){
			$fileName = BASE_DIR ."static/css/". $name .".css";
			if( file_exists( $fileName ) ){ include_once $fileName; }
		}
//
//====================================================================
		public static function js( $name="" ){
			$fileName = BASE_DIR ."static/js/". $name .".js";
			if( file_exists( $fileName ) ){ include_once $fileName; }
		} 
//
//====================================================================//
		public static function telega( $d=array() ){ 
			$chat_id = isset( $d['chat_id'] ) ? App::uid( $d['chat_id'] ) : ""; 
			$text = isset( $d['text'] ) ? App::text( $d['text'] ) : "";
			$bot = ""; 
			if( $chat_id && $text ){ 
				$ch = curl_init( 'https://api.telegram.org/bot'. $bot .'/sendMessage');
		        curl_setopt( $ch, CURLOPT_POST, TRUE );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, array('chat_id'=>$chat_id, 'text'=>$text ) );
				$result = curl_exec($ch); 
				curl_close($ch);
				return $result; 
		    }
		    return false; 
		}
//
//====================================================================

//
//====================================================================

//
//====================================================================

	}
?>