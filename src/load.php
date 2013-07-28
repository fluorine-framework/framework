<?php
	
	use Component\File;
	
	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Load {
		
		
		
		public static function model($className) {
			
			// If class name of the requested model is empty, shout it for the programmer!
			if(empty($className)) die("Failed to load model with no name!");

			try {
				File::get(APP .'model/'.strtolower($className).'.php');
			}
			catch(Exception $e) {
				die('Error while trying to load '. $className .' model.<br />'. $e->getMessage() );
			}

			$name = 'Model\\'.$className;
			return new $name;
			
		} // End of method model
		
		
	}