<?php
	
	
	
	class Config {
		
		public static $template;
		public static $data;

		public static $path;
		public static $profiler;
		public static $profiler_query;
		public static $status;
		
		public static function init() {
			
			$config = array('db' => array());
			require_once(APP .'config.php');

			static::$data = new stdClass;

			static::$data->title = $config['default']['title'];
			static::$template = $config['default']['template'];

			// Database config options
			define('DB_HOST', $config['db']['host']);
			define('DB_NAME', $config['db']['name']);
			define('DB_USER', $config['db']['user']);
			define('DB_PASS', base64_decode($config['db']['pass']));

			// Path to remove from uri
			static::$path = $config['path'];

			static::$profiler = $config['profiler']['log'];
			static::$profiler_query = $config['profiler']['query'];

			static::$status = $config['status'];
			
		}
		
	}