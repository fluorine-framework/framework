<?php
	
	namespace CLI;
	use Component\File;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	require_once(SYS .'cli/cliexception.php');
	require_once(SYS .'cli/command.php');
	require_once(SYS .'components/files/getfile.php');

	/*
	 * Command pattern:
	 * $ php hydrogen generate:controller home --restful
	 * run as PHP | use file hydrogen | class "generate" | method "controller" | args | options
	 */

	class Hydrogen {

		public static $command, $method, $arguments, $options;

		public static function input($arguments) {

			$_argv = array();
			$_argv['command']	= null;
			$_argv['args'] 		= array();
			$_argv['options'] 	= array();

			$i = 0;
			foreach($arguments as $k => $v) {
				if( substr($v, 0, 2) == '--') {
					$_argv['options'][] = $v;
				} else {

					if($i == 1) $_argv['command'] = $v;
					if($i > 1) $_argv['args'][] = $v;
				}

				$i++;
			}

			$command = $_argv['command'];
			$method  = 'help';

			if(strpos($command, ':') !== false) {
				list($command, $method) = explode(':', $command);
			}
			static::$command = $command;
			static::$method  = $method;
			static::$arguments 	= $_argv['args'];
			static::$options 	= $_argv['options'];


			return $_argv;
		}

		public static function run_command() {

			$command 	= static::$command;
			$method 	= static::$method;

			// Let's see if the command file exists.
			$path = SYS .'cli/commands/'. $command .'.'. EXT;
			if( ! File::exists($path)) {

				throw new CLIException('Unable to load file.');
			}
			
			// Let's include the file.
			require_once($path);

			// Let's check if the class exists.
			$_command = "CLI\\Command\\". ucfirst($command);
			if(!class_exists( $_command)) {

				throw new CLIException('Class '. $_command .' doesn\'t exists.');
			}

			// Let's create a new instance of the command object.
			$task = new $_command;

			// Let's check if the method is callable.
			if(!is_callable(array($task, $method))) {

				throw new CLIException('Unable to call '. $method .' in '. $command);
			}

			// Let's call the method and by that finally run the command.
			$output = $task->$method(static::$arguments, static::$options);

			// If there was a returned value print it.
			if(!empty($output)) echo $output . PHP_EOL;
		}


	}
