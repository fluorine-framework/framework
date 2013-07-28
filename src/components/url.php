<?php
	
	namespace Component;

	class Url {

		// Bool. Determine whether the request is secure or not.
		protected static $ssl = false;

		// String. Contains the request protocol. (http or https)
		protected static $protocol;

		// String. 
		protected static $request_uri;

		// String.
		protected static $request_method;

		// String
		protected static $query_string = '';


		/**
		 * Initialize the class members values.
		 * @return 	void
		 */
		public static function init() {

			static::$ssl 				= Request::isSecure();
			static::$protocol 			= (static::$ssl === true) ? 'https' : 'http';

			static::$request_uri 		= $req = $_SERVER['REQUEST_URI'];
			static::$request_method 	= $_SERVER['REQUEST_METHOD'];

			if( strpos($req, '?') !== false) {
				list(static::$request_uri, static::$query_string) = explode('?', $req);
			}
		}


		/**
		 * Removes some of the path in the uri. Used if the system is
		 * not running on the top level of a domain.
		 * @param 	string 	$path
		 * @return 	void
		 */
		public static function reduceUri($path) {

			if(empty($path)) return;

			$split	= explode($path, static::$request_uri);
			static::$request_uri = $split[1];
		}


		/**
		 * Returns the protocol.
		 * @return 	string
		 */
		public static function https() {

			return static::$protocol;
		}


		/**
		 * Returns the base url
		 * @return 	string
		 */
		public static function base() {
			
			return static::$protocol .'://'. $_SERVER['HTTP_HOST'];
		}


		/**
		 * Returns the url after the base. Aka the request uri.
		 * @return 	string
		 */
		public static function uri() {

			return ltrim(static::$request_uri, '/');
		}


		/**
		 * Returns the request method. (GET, POST, PUT etc.)
		 */
		public static function method() {

			return static::$request_method;
		}


		/**
		 * Returns the full url.
		 * @return 	string
		 */
		public static function full() {

			return static::base() . static::$request_uri;
		}


		/**
		 * Retuns a specific segment of the uri.
		 * If the requested segment doesn't exists it
		 * returns null.
		 * @param 	int 	$num 	default = 0
		 * @return 	mixed 
		 */
		public static function segment($num=0) {

			$data = explode('/', static::uri() );
			return (isset($data[$num])) ? $data[$num] : null;
		}


		/**
		 * Returns the query string.
		 * The query string starts after ? 
		 * @return 	string
		 */
		public static function queryString() {

			return static::$query_string;
		}
		
	}
