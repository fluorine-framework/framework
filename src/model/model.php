<?php
	
		
	namespace Model;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/

	abstract class Model extends \Database\Connector {

		public function __construct() {

			parent::__construct();

			require_once(SYS .'model/interfaces/table.'. EXT);
			require_once(SYS .'model/interfaces/migration.'. EXT);

			foreach(glob(APP .'database/tables/*.'. EXT) as $file) {
				require_once($file);	
			}

			if( method_exists($this, 'init')) {
				call_user_func( array($this, 'init'));
			}
			
		}
	}
	