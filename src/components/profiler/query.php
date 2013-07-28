<?php
	
	namespace Component\Profiler\Log;

	class Query {

		public $query, $time;

		public function __construct($query, $time) {

			$this->query = $query;
			$this->time  = $time;

		}

	}
