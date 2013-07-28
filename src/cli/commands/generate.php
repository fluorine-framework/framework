<?php
	
	namespace CLI\Command;

	use CLI\CLIException;
	use CLI\Command;
	use Component\File;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/

	class Generate extends Command {

		// The $template path is relative to the current file only.
		// $path is the path to the final file, relative to ROOT.
		// $replacements is an array, with keys as replace keys, and
		// values as replace values.
		protected $template, $path, $replacements;



		/**
		 * This method is a requirement for a command to work.
		 * The method is supposed to help the user understand
		 * each command. 
		 * This method returns a nice looking overview of the
		 * generate command.
		 *
		 * @return 	string 	Returns the nice looking overview.
		 */
		public function help() {

			$help  = PHP_EOL .'Generator Help'. PHP_EOL;
			$help .= '-----------------------------------'. PHP_EOL;
			
			$help .= 'Methods:'. PHP_EOL;
			$help .= ' - Controller'. PHP_EOL .' - Model'. PHP_EOL .' - Table'. PHP_EOL;

			$help .= PHP_EOL .'Arguments:'. PHP_EOL;
			$help .= ' - Name'. PHP_EOL;

			$help .= PHP_EOL .'Options/Flags:'. PHP_EOL;
			$help .= ' - restful'. PHP_EOL;
			$help .= ' - override'. PHP_EOL;

			$help .= PHP_EOL .'Usage:'. PHP_EOL;
			$help .= '$ php hydrogen generate:method argument --option'. PHP_EOL;

			return $help;

		}


		/**
		 * Generates controller file.
		 * @param 	$arg 	Arguments
		 * @param 	$opt 	Options/Flags
		 * @return 	void
		 */
		public function controller($arg, $opt) {

			if(!isset($arg[0])) {
				throw new CLIException('To generate a controller, you must provide a name.');
			}


			$this->template 		= 'templates/gen_controller.txt';
			$this->path 			= APP .'controller/'. $arg[0] .'.'. EXT;

			$baseController 		= 'Base';
			$override 				= false;

			// If the option to restful is set, set the base controller to the rest controller.
			if(array_search('--restful', $opt) !== false) $baseController = 'Rest';
			if(array_search('--override', $opt) !== false) $override = true;

			if( File::exists($this->path, false) === true && $override === false) {

				// If the controller file already exists, warn the user.
				// If, however, the user wants to override, tell the user
				// to add the flag "--override" to the command.
				throw new CLIException('Controller already exists. Add the flag "--override" to allow overriding files.');
			}

			// Add the keys and values that is going to be replaced.
			$this->replacements 	= array(
				'name'		=> ucfirst($arg[0]),
				'base'		=> $baseController
			);

			return $this->makeFile($this->template, $this->path);
		}

		/**
		 * Gets the template file, modify it and put it in position.
		 * @param 	$template 	The path to the template file.
		 * @param 	$path 		The path to the final file.
		 * @return 	void
		 */
		private function makeFile($template, $path) {

			try {
				$file = File::get(SYS .'cli/commands/'. $template, true);
			}
			catch(\Exception $e) { die('Unable to get template file.'); }

			// Run replace sequence.
			$data = $this->replace($file);

			file_put_contents($path, $data);
		}


		/**
		 * Replaces all keys from the Generate::$replacements array.
		 * @param 	$template 	Code dump of a controller "dummy"
		 * @return 	$template 	Modified code dump.
		 */
		private function replace($template) {

			foreach($this->replacements as $key => $value) {
				$template = str_replace('{{'. $key .'}}', $value, $template);
			}

			return $template;
		}

	}