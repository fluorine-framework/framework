<?php
	
	use Component\Event;
	use Component\Error;
	use Component\Request;
	use Component\Response;
	use Component\Profiler\Log;
	
	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	require_once(COMPONENTS . 'error/error.php');


	set_error_handler(function($code, $msg, $file, $line) {
		Error::handler($code, $msg, $file, $line);
	});
	set_exception_handler(function($exception) {
		Error::exception($exception);
	});
	register_shutdown_function(function() {
		Error::fatal();
	});










	require_once(SYS . 'bootstrap.php');

	
	require_once(COMPONENTS . 'request/request.php');
	require_once(COMPONENTS . 'response/response.php');
	require_once(COMPONENTS . 'event/event.php');

	require_once(SYS . 'router.php');
	require_once(SYS . 'route.php');
	require_once(SYS . 'config.php');
	require_once(SYS . 'autoload.php');

	// Requiring the controller and a some extended 
	// versions, including the REST controller.
	require_once(SYS . 'controller/controller.php');
	require_once(SYS . 'controller/base_controller.php');
	require_once(SYS . 'controller/rest_controller.php');


	require_once(SYS . 'view.php');
	require_once(SYS . 'json.php');
	
	// Defined routes
	require_once(APP .'route.php');

	// Components
	require_once(COMPONENTS . 'url.php');
	require_once(COMPONENTS . 'files/getfile.php');
	
	class App {
		
		
		public static function boot() {
			
			Config::init();
			Request::init();

			require_once(SYS .'model/database/connector.php');
			require_once(COMPONENTS . 'database/nitrogen/nitrogen.php');
			require_once(COMPONENTS . 'database/nitrogen/builder.php');
			require_once(SYS . 'model/model.php');
			
			$boot = new Bootstrap( Config::$path );
			URL::init();

			Autoload::load();

			Log::init();
			Template\Menu::init();

			$boot->getPage();
			$boot->getContent();

			if( Config::$profiler === true ) {
				echo Log::render();
			}

		}
		
		
	}