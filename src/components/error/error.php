<?php
	
	namespace Component;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Error {

		public static function register($code) {

			if( \Config::$status == '1' ) {

				$error = array();
				
				$error = require_once(COMPONENTS .'error/errorcodes.php');
				$custom = require_once(APP .'errors.php');

				$error = array_merge($error, $custom);
				//trigger_error($error[$code], E_USER_ERROR);
				throw new \Exception($error[$code]);

			} else {

				Response::$header->set('500', '');

			}

		}
		
	}










