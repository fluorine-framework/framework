<?php
	
	use Component\File;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/


	class Autoload {

		public static $files = array();


		public static function register($path) {
			static::$files[] = $path;
		}

		public static function registerFromFile($paths) {

			foreach($paths as $path) static::register($path);
		}

		public static function reg_pkg($pkg) {

			try {
				File::exists($pkg .'/required.json');
			}
			catch(Exception $e) {
				die($e->getMessage());
			}

			$required = json_decode( File::get($pkg .'/required.json', true) );

			foreach($required as $file) {
				static::register($pkg .'/'. $file);
			}

		}

		public static function load() {

			static::loadFromAppFile();

			foreach(static::$files as $path) {

				try {
					File::get( $path );
				}
				catch(Exception $e) {
					die($e->getMessage() );
				}
			}

		}

		public static function loadFromAppFile() {

			$list = require_once(APP .'autoload.php');

			foreach($list as $el) {

				$is_pkg = true;

				if( strpos($el, '.') !== false) {
					$dots = explode('.', $el);
					$ext = end($dots);
					if($ext == EXT) {
						$is_pkg = false;
					}
				}

				if($is_pkg) {
					static::reg_pkg($el);
				} else {
					static::register($el);
				}
			}

		}

	}