<?php
	
	namespace Component;

	use Component\Nitrogen\Query;
	use Component\Profiler\Log;
	

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	

	class Nitrogen extends \Database\Connector {

		protected $hiddenFields;
		protected $query;
		protected $query_string;
		protected $query_parts = array();

		protected $prepared_query;
		protected $real_query;


		public function select($fields = '*') {

			$this->query_parts['fields'] = $fields;
			return $this;
		}

		public function insert($data) {

			$this->query_parts['create']	= $data;
			$this->query_parts['type']		= 'create';
			
			return $this->_query();
		}

		public function update($data) {

			$this->query_parts['update']	= $data;
			$this->query_parts['type']		= 'update';

			return $this->_query();
		}

		public function delete() {

			$this->query_parts['type']		= 'delete';

			return $this->_query();
		}

		public function find($id) {

			return $this->select()->where_id_is($id);
		}

		public function from($table, $as=null) {

			$this->query_parts['table'] = $table;
			$this->query_parts['table_as'] = $as;
			return $this;
		}

		public function join($table, $field1, $operator, $field2, $type='INNER', $as=null) {

			$this->query_parts['joins'][] = array(
				$table, $field1, $operator, $field2, $type, $as
			);
			return $this;
		}

		public function where($field, $operator, $value, $connector = 'AND') {

			$this->query_parts['wheres'][] = array(
				$field, $operator, $value, $connector
			);
			return $this;
		}

		public function order_by($field, $order) {

			$this->query_parts['order_by'] = array($field, $order);
			return $this;
		}

		public function sql($query, $type='read') {
			$this->query_string = $query;
			$this->___query($type);
		}
		

		public function fetch($fields = false) {

			$this->_fetch($fields);
			$stmt = $this->_query();

			return $this->hideHiddenFields( $stmt->fetch() );
		}

		public function fetch_all($fields = false) {

			$this->_fetch($fields);
			$stmt = $this->_query();

			return $this->hideHiddenFields( $stmt->fetchAll() );
		}

		private function _fetch($fields) {

			if($fields !== false && !empty($fields)) $this->select($fields);
			$this->query_parts['type'] = 'read';

			$this->hiddenFields = $this->hidden();

		}

		private function _query() {

			if(!isset($this->query_parts['table']) && isset($this->table)) {
				$this->query_parts['table'] = $this->table;
			}

			$this->query_string = new Query\Builder($this->query_parts);
			$type = $this->query_parts['type'];
			$this->query_parts = array();

			return $this->___query($type, true);
		}

		private function ___query($type, $useBindings=false) {

			try {
				$stmt = $this->db->prepare($this->query_string);

				if($useBindings) $bindings = array_values($this->query_string->getBindings());

				$start = microtime(true);
				$exec = ($useBindings) ? $stmt->execute($bindings) : $stmt->execute();

				$qString = ($useBindings) ? $this->query_string->replace_with_bingings( $this->query_string ) : $this->query_string;

				if( Log::$profiler && Log::$log_query ) 
					Log::query($qString, $start);
			}
			catch(PDOException $e) {
				die( $e );
			}
			if($type == 'read') {
				return $stmt;
			}
			return $exec;

		}

		private function hideHiddenFields($rows) {

			$_rows = array();
			$hiddenFields = array_flip($this->hiddenFields);
			foreach($rows as $row => $fields) {
				
				$data = new \stdClass;
				foreach($fields as $field => $val) {
					if(!isset($hiddenFields[$field])) {
						$data->{$field} = $val; 
					}
				}
				$_rows[] = $data;
			}
			return $_rows;
		}



		// Extra methods

		public function table($table, $as=null) {
			return $this->from($table, $as);
		}

		public function where_id($value) {
			return $this->where_id_is($value);
		}

		public function where_id_is($value) {
			return $this->where('id', '=', $value);
		}

		public function where_slug_is($value) {
			return $this->where('slug', '=', $value);
		}

	}