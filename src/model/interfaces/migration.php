<?php
	
	namespace Database\Migration;

	interface Migration {

		public function create();
		public function remove();

	}