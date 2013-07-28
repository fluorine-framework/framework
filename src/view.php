<?php
	

	class View {

		public $path, $tpl;
		protected $name;
		private $vars;

		/**
		 * Construct a new instance of a view.
		 * @param 	string 	$name
		 * @param 	string 	$tpl 	optional
		 * @return 	void
		 */
		public function __construct($name, $tpl = false) {

			$this->name = $name;
			$this->tpl 	= (!$tpl) ? Config::$template : $tpl;

			$this->path = $name;
		}

		/**
		 * Assign a value to a name in the current view.
		 * @param 	string 	$name
		 * @param 	mixed	$value
		 * @return 	void
		 */
		public function assign($name, $value) {

			$this->vars[$name] = $value;
		}

		/**
		 * Returns the var array.
		 * @return 	array 	View::vars
		 */
		public function getVars() {
			return $this->vars;
		}


	}