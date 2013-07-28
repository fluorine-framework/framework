<?php
	
	namespace Component\Profiler\Log;

	class Checkpoint {

		public $msg, $type;

		public function __construct($msg, $type) {

			$this->msg = $msg;
			$this->type = $type;
		}
		
	}