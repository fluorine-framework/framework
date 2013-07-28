<?php

	namespace CLI;


	class CLIException extends \Exception {

		public function __toString() {

			// $output  = "Error in ". $this->file ." on line ". $this->line .".". PHP_EOL . PHP_EOL ."More information:";
			// $output .= PHP_EOL ."Code: ". $this->code . PHP_EOL ."Message: ". $this->message . PHP_EOL;

			$output = $this->message . PHP_EOL;

			return $output;
		}

	}