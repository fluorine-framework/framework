<?php
	
	namespace Component;
	use Component\File;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Request {


		public static $header;
		protected static $ssl = null;

		public static function init() {

			
			try {
				File::get(COMPONENTS .'request/headers.'. EXT);
			}
			catch(Exception $e) { die($e->getMessage()); }

			static::$header = Request\Header::newInstance();

		}

		public static function Ajax() {

			if(static::$header->get('X-Requested-With') == 'XMLHttpRequest') return true;
			return false;
		}

		public static function isSecure() {

			if( static::$ssl === null) {

				static::$ssl = false;
				if (!empty($_SERVER['HTTPS']) 
					&& $_SERVER['HTTPS'] !== 'off' 
					|| $_SERVER['SERVER_PORT'] == 443)
				{
					static::$ssl = true;
				}
				if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) 
					&& $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' 
					|| !empty($_SERVER['HTTP_X_FORWARDED_SSL']) 
					&& $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
				{
				    static::$ssl = true;
				}
			}

			return static::$ssl;
		}

	}