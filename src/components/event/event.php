<?php
	
	namespace Component;


	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Event {

		private static $events = array();


		public static function listen($code, $callback, $override=false) {

			if(static::has($code)) {
				static::$events[$code] = $callback;
			} else {
				if($override === true) {
					static::$events[$code] = $callback;
				}
			}
		}

		public static function fire($event, $parameters=array()) {

			if(static::has($event)) {
				call_user_func_array(static::$events[$event], $parameters);
			}
		}

		public static function has($event) {
			
			if(array_key_exists($event, static::$events)) {
				return true;
			}
			return false;
		}

	}
