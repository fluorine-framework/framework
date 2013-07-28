<?php
	
	namespace Component;
	use Component\File;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Response {


		public static $header;

		public static function init() {

			
			try {
				File::get(COMPONENTS .'request/headers.'. EXT);
			}
			catch(Exception $e) { die($e->getMessage()); }

			static::$header = Request\Header::newInstance();

			

		}

		public static function error($code) {

			static::$header->set($code, '');

			$file = null;
			try {
				$file = File::get(APP .'view/errors/'. $code .'.php', true);
			}
			catch(Exception $e) { die($e->getMessage()); }

			echo $file;
			exit;

		}


	}