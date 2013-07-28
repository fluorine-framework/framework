<?php
	

	class Session {

		public function set($name, $value=null) {

			if(is_string($name)) {
				if(empty($value)) return false;

				$_SESSION[$name] = $value;
				return true;
			}

			if(is_array($name)) {
				$data = $name;
				$name  = (!empty($value)) ? $value : '';

				if(empty($name)) {
					// Register all keys in the array.
					foreach($data as $k => $v) {
						Session::set($k, $v);
					}
					return true;
				} else {
					// If we have a name defined, register the array itself as a session.
					$_SESSION[$name] = $data;
					return true;
				}
			}

		}

		public function get($key, $name=null) {

			if(empty($name)) {
				return $_SESSION[$key];
			} else {
				return $_SESSION[$name][$key];
			}
		}

		public function delete($name) {
			unset($_SESSION[$name]);
		}

	}
	