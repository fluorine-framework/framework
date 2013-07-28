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

	}