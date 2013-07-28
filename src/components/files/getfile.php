<?php
	
	namespace Component;


	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/

	class File {


		public static function get($path, $executed = false) {

			if(!file_exists($path)) {
				throw new \Exception('File not found. '. $path .' was not found.');
				return false;
			}

			if($executed === false) {
				require_once($path);
			} else {
				return file_get_contents($path);
			}
			
		}

		public static function exists($path, $throw=true) {

			if(!file_exists($path)) {
				
				if($throw) throw new \Exception('<b>File not found.</b><br />The file "'. $path .'" was not found.');
				return false;
			}
			return true;
		}

		public static function get_file_size($size) {

			$units = array('Bytes', 'KiB', 'MiB', 'GiB');
			$_size = $size / pow(1024, ($i = floor(log($size, 1024))));
			
			return round($_size, $i, 2) .' '. $units[$i];
		}

	}


	
	