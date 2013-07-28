<?php
	
	
	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	
	class Router {

		protected static $routes = array(
			'GET' 	=> array(),
			'POST'	=> array(),
			'ANY'	=> array()
		);

		protected static $methods = array('GET', 'POST', 'ANY');


		/**
		 * Register a route
		 * @param 	string 	$method
		 * @param 	string 	$route
		 * @param 	mixed 	$action
		 * @return 	void
		 * 
		 * Errors that can be thrown here.
		 * Code 		Desc
		 * Http_405		Method not allowed
		 * R_100		Route already exists
		 */
		public static function register($method, $route, $action) {

			// If the requested method isn't allowed, throw an error.
			if(!in_array($method, static::$methods)) return Error::register('http_405');

			/*
			 * If the route already exists in the requested method, register the 
			 * error, but ignore it. This allowed us to ask if there's been any 
			 * registered errors, so it doesn't execute something it shouldn't.
			 */
			if(array_key_exists($route, static::$routes[$method])) Error::register('router_100');
			if(array_key_exists($route, static::$routes['ANY'])) Error::register('router_100');

			static::$routes[$method][$route] = $action;

		}

		/**
		 * Returns either the full array of routes or all routes for
		 * a specific method.
		 * @param 	string 	$type 	optional
		 * @return 	array
		 */
		public static function getRoutes($type=null) {

			return ($type===null) ? static::$routes : static::$routes[$type];
		}

		/**
		 * Looks for a route in the assigned method.
		 * @param 	string 	$method
		 * @param 	string 	$route
		 * @return 	mixed
		 */
		public static function find($method, $route) {

			$data		= array();
			$closure	= false;

			if(!in_array($method, static::$methods)) return false;

			if( array_key_exists($route, static::$routes[$method])
			 || array_key_exists($route, static::$routes['ANY'])) {

			 	$_method = (array_key_exists($route, static::$routes['ANY'])) ? 'ANY' : $method;

				$func = static::$routes[$_method][$route];

				if( $func instanceof Closure ) $closure = true;

				$data['func'] = $func;
				$data['closure'] = $closure;

				return $data;

			} 
			
			return false;

		}

		/**
		 * Register a new method to be allowed.
		 * @param 	string 	$method
		 * @return 	boolean	
		 */
		public static function register_method($method) {

			if(in_array($method, static::$methods)) return false;
			if(array_key_exists($method, static::$routes)) return false;

			static::$methods[] = $method;
			static::$routes[$method] = array();

			return true;
		}


		/**
		 * Register a controller and all it's methods.
		 * @param 	string 	$controller
		 * @return 	void
		 */
		public static function register_controller($controller) {

			if( empty($controller) ) return false;

			if( !file_exists('controller/'. $controller .'.php') ) return false;
			require_once('controller/'. $controller .'.php');

			$controller_name = $controller .'_Controller';

			if( !class_exists( $controller_name )) return false;


			$methods = get_class_methods( $controller_name );


			foreach($methods as $method) {

				if(strpos($method, '_') === false) break;

				$_m 		= explode('_', $method);
				$r_method 	= strtoupper($_m[0]);
				$name 		= $_m[1];

				if(empty($r_method) || empty($name)) break;
				if($r_method == 'ACTION') $r_method = 'ANY';

				$_name = '/'. strtolower($name);
				if($_name == '/index') $_name = '';

				$route = strtolower($controller) . $_name;
				$action = strtolower($controller) .'@'. $name;

				static::register($r_method, $route, $action);
			}

		}


	}



