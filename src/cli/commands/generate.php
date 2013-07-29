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
		private $name, $tablename;


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


			$override = $restful = false;

			// If the option to restful is set, set the base controller to the rest controller.
			if(array_search('--restful', $opt) !== false) $restful = true;
			if(array_search('--override', $opt) !== false) $override = true;


			$this->template 		= ($restful)  ? 'templates/gen_controller_rest.txt'
														: 'templates/gen_controller.txt';
			$this->path 			= APP .'controller/'. $arg[0] .'.'. EXT;
			

			if( File::exists($this->path, false) === true && $override === false) {
				// If the controller file already exists, warn the user.
				// If, however, the user wants to override, tell the user
				// to add the flag "--override" to the command.
				throw new CLIException('Controller already exists. Add the flag "--override" to allow overriding files.');
			}

			// Add the keys and values that is going to be replaced.
			$this->replacements 	= array(
				'name'		=> ucfirst($arg[0])
			);

			return $this->makeFile($this->template, $this->path);
		}


		/**
		 * Generates model file. Options can be set so it also
		 * generates a table file, and a migration file.
		 * @param 	$arg 	Arguments
		 * @param 	$opt 	Options
		 */
		public function model($arg, $opt) {

			if(!isset($arg[0])) {
				throw new CLIException('To generate a model, you must provide a name.');
			}

			$override 	= false;
			$table 		= false;
			$migration 	= false;
			if(array_search('--override', $opt) !== false) 	$override 	= true;
			if(array_search('--table', $opt) !== false) 	$table 		= true;
			if(array_search('--migration', $opt) !== false) $migration 	= true;

			$this->template 		= ($table)  ? 'templates/gen_model_table.txt'
												: 'templates/gen_model.txt';
			$this->path 	 		= APP .'model/'. $arg[0] .'.'. EXT;

			if( File::exists($this->path, false) === true && $override === false) {
				// If the model file already exists, warn the user.
				// If, however, the user wants to override, tell the user
				// to add the flag "--override" to the command.
				throw new CLIException('Model already exists. Add the flag "--override" to allow overriding files.');
			}

			$name = (substr($arg[0], -1) == 's') ? substr($arg[0], 0, -1) : $arg[0];

			// Add the keys and values that is going to be replaced.
			$this->replacements 	= array(
				'class_name'	=> ucfirst($arg[0]),
				'name'			=> ucfirst($name)
			);


			// When generating models, we offer the shortcut to also add the
			// table and migration file rigth away, and to make this happend
			// we check if $table and or $migration is set to true, and then 
			// we run their command. Otherwise we just make the model file.
			if(!$table && !$migration) return $this->makeFile($this->template, $this->path);

			// We return the value of makefile into a variable so we can get
			// the returned value from all commands.
			$return = $this->makeFile($this->template, $this->path);

			if($table) {
				$return .= $this->table($arg, $opt);
			}
			if($migration) {
				$return .= $this->migration($arg, $opt);
			}

			return $return;
		}


		/**
		 * Generates table file. Options can be set so it also
		 * generates a migration file.
		 * @param 	$arg 	Arguments
		 * @param 	$opt 	Options
		 */
		public function table($arg, $opt) {

			if(!isset($arg[0])) {
				throw new CLIException('To generate a model, you must provide a name.');
			}

			$override 		= false;
			$migration 		= false;

			if(array_search('--override', $opt) !== false) 	$override 	= true;
			if(array_search('--migration', $opt) !== false) $migration 	= true;

			$this->template 		= 'templates/gen_table.txt';
			$this->path 			= APP .'database/tables/'. $arg[0] .'.'. EXT;

			if( File::exists($this->path, false) === true && $override === false) {
				// If the table file already exists, warn the user.
				// If, however, the user wants to override, tell the user
				// to add the flag "--override" to the command.
				throw new CLIException('Table already exists. Add the flag "--override" to allow overriding files.');
			}

			if(!isset($this->tablename) || !isset($this->name)) $this->askForTableName($arg[0]);
			
			// Add the keys and values that is going to be replaced.
			$this->replacements 	= array(
				'name'		=> ucfirst($this->name),
				'tablename' => $this->tablename
			);

			if(!$migration) return $this->makeFile($this->template, $this->path);

			$return  = $this->makeFile($this->template, $this->path);
			$return .= $this->migration($arg, $opt);

			return $return;
		}


		/**
		 * Generates migration file. Options can be set so it also
		 * generates a table file.
		 * @param 	$arg 	Arguments
		 * @param 	$opt 	Options
		 */
		public function migration($arg, $opt) {

			if(!isset($arg[0])) {
				throw new CLIException('To generate a model, you must provide a name.');
			}

			$override 		= false;
			$table 			= false;

			if(array_search('--override', $opt) !== false) 	$override 	= true;
			if(array_search('--table', $opt) !== false) 	$table  	= true;

			$this->template 		= 'templates/gen_migration.txt';
			$this->path 			= APP .'database/migrations/'. $arg[0] .'.'. EXT;

			if( File::exists($this->path, false) === true && $override === false) {
				// If the migration file already exists, warn the user.
				// If, however, the user wants to override, tell the user
				// to add the flag "--override" to the command.
				throw new CLIException('Migration file already exists. Add the flag "--override" to allow overriding files.');
			}

			if(!isset($this->tablename) || !isset($this->name)) $this->askForTableName($arg[0], false);

			// Add the keys and values that is going to be replaced.
			$this->replacements 	= array(
				'name'		=> ucfirst($this->name),
				'tablename' => $this->tablename
			);

			if(!$table) return $this->makeFile($this->template, $this->path);

			$return  = $this->makeFile($this->template, $this->path);
			$return .= $this->table($arg, $opt);

			return $return;
		}


		/**
		 * Generates a view.
		 */
		public function view($arg, $opt) {

			if(!isset($arg[0])) {
				throw new CLIException('To generate a model, you must provide a name.');
			}

			$override 		= false;

			if(array_search('--override', $opt) !== false) 	$override 	= true;

			$this->template 		= 'templates/gen_view.txt';
			$this->path 			= APP .'view/'. $arg[0] .'.'. EXT;

			if( File::exists($this->path, false) === true && $override === false) {
				// If the view file already exists, warn the user.
				// If, however, the user wants to override, tell the user
				// to add the flag "--override" to the command.
				throw new CLIException('View file already exists. Add the flag "--override" to allow overriding files.');
			}

			$this->replacements = array();

			return $this->makeFile($this->template, $this->path);
		}


		/**
		 * A simple method to ask the user for a tablename.
		 * @param 	$default 		string
		 * @param 	$grammaWithS 	boolean 	optional
		 * @return 	void
		 */
		private function askForTableName($default, $grammaWithS=true) {

			// If the tablename isn't the same as the filename, use userinput.
			echo PHP_EOL ."Please enter the table name [\"". $default ."\"]: ";
			$input = trim(fread(STDIN, 50));

			if($grammaWithS)  $this->name = (substr($default, -1) == 's') ? substr($default, 0, -1) : $default;
			if(!$grammaWithS) $this->name = $default;

			$this->tablename = (empty($input)) ? $default : $input;

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

			// If path contains slash, check if folder exists otherwise 
			// create the needed folders.
			if(strpos($path, '/') !== false) {
				$folders 	= explode('/', $path);
				$count 		= count($folders);
				$_path 		= $folders[0];
				unset($folders[$count-1]);

				for($i=0; $i <= $count; $i++) {
					if(!is_dir($_path)) {
						mkdir($_path, 0777);
					}
					if(isset($folders[$i+1])) $_path .= '/'. $folders[$i+1];
					
				}
			}

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