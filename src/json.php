<?php
	

	class Json {

		protected $data;

		/**
		 * Takes in an array or an object and turn it into a json array.
		 * @param 	mixed 	$data
		 */
		public function __construct($data) {

			$this->data = json_encode($data);

		}

		public function __toString() {
			return $this->data;
		}

	}