<?php
	

	class Input {


		public function get($name) {
			return $_GET[$name];
		}

		public function post($name) {
			return $_POST[$name];
		}

		public function req($name) {
			return (isset($_GET[$name]) && !isset($_POST[$name])) ? $_GET[$name] : $_POST[$name];
		}

		public function gets() {
			return $_GET;
		}

		public function posts() {
			return $_POST;
		}

		public function save() {
			Session::set($_POST, 'flash');
		}

		public function old($name) {
			return Session::get($name, 'flash');
		}
		
		
	}
	