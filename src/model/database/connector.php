<?php
	
		
	namespace Database;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	

	abstract class Connector {
		
		public $db;
		
		public function __construct() {

			try {
				$this->db = new \PDO( "mysql:host=". DB_HOST .";dbname=". DB_NAME, DB_USER, DB_PASS );  
			}
			catch(PDOException $e) {
				die( "DB ERROR: ". $e->getMessage() );
			}
			$this->db->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			$this->db->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ );
			
		}
		
	}