<?php
	
	namespace Component\Nitrogen\Query;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	class Builder {

		public $query;
		protected $query_parts;

		private $bindings = array();

		private $fields;
		private $table;
		private $joins;
		private $wheres;
		private $order;
		private $limit;
		private $create;
		private $update;

		public function __construct($query_parts) {

			$this->query_parts = $query_parts;

			$this->fields();
			$this->insert();
			$this->update();
			$this->table();
			$this->joins();
			$this->wheres();


			$this->build();
			return $this->query;
		}

		private function bind($val) {
			$this->bindings[] = $val;
		}

		protected function fields() {

			if(empty($this->query_parts['fields'])) return true;

			if($this->query_parts['fields'] == '*') {
				$this->fields = '*';
				return true;
			}

			foreach($this->query_parts['fields'] as $key => $field) {

				$field = $this->wrap( $field );
				$this->query_parts['fields'][$key] = $field;
			}
			$this->fields = implode(', ', $this->query_parts['fields']);
		}

		protected function insert() {

			if(empty($this->query_parts['create'])) return true;

			$fields = array(); $values = array();

			foreach($this->query_parts['create'] as $f => $v) {
				$fields[] = $f;
				$values[] = $v;
			}

			foreach($fields as $key => $field) {
				$fields[$key] = $this->wrap( $field );
			}
			foreach($values as $key => $value) {
				$values[$key] = '?';
				$this->bind($value);
			}

			$_fields = implode(', ', $fields);
			$_values = implode(', ', $values);

			$this->create = '('. $_fields .') VALUES ('. $_values .')';
		}

		protected function update() {

			if(empty($this->query_parts['update'])) return true;

			$data = array();

			foreach($this->query_parts['update'] as $f => $v) {

				$data[] = $this->wrap($f) ." = '". $v ."'";
			}

			$this->update = implode(', ', $data);

			if(substr($this->update, -2) == ', ') {
				$this->update = substr($this->update, 0, -2);
			}
		}

		protected function table() {

			$this->table = $this->wrap( $this->query_parts['table'] );
			if(isset($this->query_parts['table_as'])) {
				$this->table .= ' AS '. $this->query_parts['table_as'];
			}
		}

		protected function joins() {

			if(empty($this->query_parts['joins'])) return true;

			$joins = $this->query_parts['joins'];
			$_joins = '';
			foreach($joins as $join) {

				$join[0] = $this->wrap( $join[0] );
				$join[1] = $this->wrap( $join[1] );
				$join[3] = $this->wrap( $join[3] );
				
				$table = ($join[5] !== null) ? $join[0].' AS '. $join[5] : $join[0];
				$_joins .= ' '. strtoupper($join[4]) .' JOIN '. $table .' ON '. $join[1] .' '. $join[2] .' '. $join[3];

			}
			$this->joins = $_joins;
		}

		protected function wheres() {


			if(empty($this->query_parts['wheres'])) return true;

			$_wheres = array();

			foreach($this->query_parts['wheres'] as $key => $where) {
				
				$where[0] = $this->wrap( $where[0] );
				$_wheres[$key] = $where[0] .' '. $where[1] . ' ? '. $where[3] .' ';
				$this->bind($where[2]);
			}

			$last = count($_wheres)-1;
			$lastWhere = $_wheres[$last];

			$lastWhere = preg_replace('/AND |OR /', '', $lastWhere);

			$_wheres[$last] = $lastWhere;

			$this->wheres = implode('', $_wheres);
		}





		protected function build() {

			// Variables that only needs to be set when query type is select.
			$where = '';
			$order = '';
			$speciel = '';

			switch($this->query_parts['type']) {
				case 'create':
					$start = 'INSERT INTO ';
					$table = $this->table;
					$speciel = ' '. $this->create;
					$where = '';
					break;
				case 'read':
					$start = 'SELECT ';
					$table = $this->fields .' FROM '. $this->table;
					if(!empty($this->joins)) $speciel = $this->joins;
					if(!empty($this->wheres)) $where = ' WHERE '. $this->wheres;
					//$order = ' ORDER BY '. $this->
					break;
				case 'update':
					$start = 'UPDATE ';
					$table = $this->table;
					$speciel = ' SET '.$this->update;
					$where = ' WHERE '. $this->wheres;
					break;
				case 'delete':
					$start = 'DELETE FROM ';
					$table = $this->table;
					$speciel = '';
					$where = ' WHERE '. $this->wheres;
					break;
			}

			$this->query = $start . $table . $speciel . $where;

		}

		public function getBindings() {
			return $this->bindings;
		}

		public function replace_with_bingings($query) {

			$bindings = explode('?', $query);
			$last = count($bindings);
			$_query = '';
			foreach($bindings as $k => $bind) {
				$trim = trim($bind);
				if(empty($trim)) break;

				$bind .= ($k != $last-1) ? '?' : '';
				$bind = (isset($this->bindings[$k])) ? str_replace('?', "'". $this->bindings[$k] ."'", $bind) : $bind;
				$_query .= $bind;
			}

			return $_query;
		}

		private function wrap($var) {
			if( strpos($var, '.') === false ) {
				return '`'. $var .'`';
			} else {

				$_var = explode('.', $var);
				$t = '`'. $_var[0]. '`';
				$field = (strpos($_var[1], '*') === false) ? '`'. $_var[1]. '`' : $_var[1];

				return $t .'.'. $field;
			}
		}

		public function __toString() {

			return $this->query;
		}
	}
