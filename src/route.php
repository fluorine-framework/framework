<?php
	

	class Route {

		public static function get($route, $action) {

			Router::register('GET', $route, $action);
		}

		public static function post($route, $action) {

			Router::register('POST', $route, $action);
		}

		public static function reg($route, $action, $method=null) {

			$_method = (is_null($method)) ? 'ANY' : $method;

			Router::register($_method, $route, $action);
		}

		public static function register($method) {

			Router::register_method($method);
		}

		public static function controller($controller) {

			Router::register_controller($controller);
		}
	}