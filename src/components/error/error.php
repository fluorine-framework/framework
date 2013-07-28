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

			$error = array();
			
			$error = require_once(COMPONENTS .'error/errorcodes.php');
			$custom = require_once(APP .'errors.php');

			$error = array_merge($error, $custom);
			trigger_error($error[$code], E_USER_ERROR);

		}

		public static function handler($code, $msg, $file, $line) {

			if (error_reporting() === 0) return false;
			$e = new \ErrorException($msg, $code, 0, $file, $line);
			static::exception($e, true);

		}

		public static function exception(\Exception $e, $trace=true) {

			$msg 	= $e->getMessage();
			$code 	= $e->getCode();
			$file 	= $e->getFile();
			$line 	= $e->getLine();
			$trace	= $e->getTraceAsString();
			$type 	= static::getType($code);

			//if( $type == 'Notice') return false;

			if($trace === false) $trace = '';

			$html = file_get_contents(COMPONENTS .'error/template.html');

			$search  = array('|{msg}|', '|{code}|', '|{type}|', '|{file}|', '|{line}|', '|{trace}|');
			$replace = array($msg, $code, $type, $file, $line, $trace);

			$data = preg_replace($search, $replace, $html);

			#echo "CODE: ". $code ."\n<br />\n";
			/*
			 * If status is on production, we don't wanna show an exception error so
			 * we can give an Http Error 500.
			 */
			if( \Config::$status == '0') {

				if( $code == '500' ) {
					Event::fire('http_500', array($e));
					exit;
				}

			}

			echo $data;
			exit;

		}

		public static function fatal() {

			$error = error_get_last();

			if(!is_null($error)) {
				extract($error);
				static::exception(new \ErrorException($message, $type, 0, $file, $line), false);
			}
		}


		private static function getType($code) {

			switch ($code) {
	            case E_USER_ERROR:
	                $type = 'Fatal Error';
	            break;
	            case E_USER_WARNING:
	            case E_WARNING:
	                $type = 'Warning';
	            break;
	            case E_USER_NOTICE:
	            case E_NOTICE:
	            case @E_STRICT:
	                $type = 'Notice';
	            break;
	            case @E_RECOVERABLE_ERROR:
	                $type = 'Catchable';
	            break;
	            default:
	                $type = 'Unknown Error';
	            break;
            }
            return $type;
		}
		
	}










