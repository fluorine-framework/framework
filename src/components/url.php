<?php
	

	class URL {

		protected static $ssl = false;
		protected static $protocol;
		protected static $request_uri;
		protected static $query_string;

		public static function init() {

			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
				static::$ssl = true;
			}
			static::$protocol = (static::$ssl === true) ? 'https' : 'http';

			$req = $_SERVER['REQUEST_URI'];
			if( strpos($req, '?') !== false) {

				$_query = explode('?', $req);

				static::$request_uri = $_query[0];
				static::$query_string = $_query[1];
			} else {

				static::$request_uri = $req;
				static::$query_string = null;
			}

		}

		public static function https() {

			return static::$protocol;
		}

		public static function base() {
			
			return static::$protocol .'://'. $_SERVER['HTTP_HOST'];
		}

		public static function full() {

			return static::base() . static::$request_uri;
		}

		public static function uri() {

			return ltrim(static::$request_uri, '/');
		}

		public static function segment($num=0) {

			$data = explode('/', static::uri() );
			return (isset($data[$num])) ? $data[$num] : null;
		}

		public static function queryString() {

			return static::$query_string;
		}

		public static function get($key) {

			return $_GET[$key];
		}
	}