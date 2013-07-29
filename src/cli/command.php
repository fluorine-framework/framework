<?php
	

	namespace CLI;

	abstract class Command {

		abstract public function help();

		public function __call($method, $args) {

			echo "Method not found. ". PHP_EOL;
			return $this->help();	
		}

	}
