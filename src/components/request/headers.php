<?php
	
	namespace Component\Request;


	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Header {

		private static $headers;
		private static $instance = false;

		public static function newInstance() {

			if(!static::$instance) {
				static::$instance = new Header;
				self::$headers = apache_request_headers();
			}
			return static::$instance;

		}

		public function set($name, $value) {

			if($name == '500') {
				header('HTTP/1.1 500 Internal Server Error', true, 500);
				trigger_error('ISE function called.', E_USER_ERROR);
				//header("HTTP/1.0 404 Not Found", true);
			} else {
				header($name .': '. $value);
			}
		}

		public function get($name) {

			if(isset($this->headers[$name])) {
				return $this->headers[$name];
			}
			return null;

		}

	}