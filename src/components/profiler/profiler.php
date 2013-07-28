<?php
	
	namespace Component\Profiler;
	use Component\File;

	class Log {

		public static $profiler, $log_query;
		private static $queries, $checkpoints, $memory;

		public static function init() {

			static::$profiler 	= \Config::$profiler;
			static::$log_query	= \Config::$profiler_query;
		}

		public static function query($query_string, $start) {

			$time = number_format( (microtime(true) - $start) * 1000, 2);
			static::$queries[] = new Log\Query($query_string, $time);
		}

		public static function checkpoint($msg, $type='null') {

			static::$checkpoints[] = new Log\Checkpoint($msg, $type);
		}

		public static function getTime() {

			return microtime(true);
		}

		public static function time($note, $startTime) {

			$msg = number_format( (microtime(true) - $startTime) * 1000, 2) .' ms';
			static::checkpoint($msg, $note);
		}



		public static function render() {

			static::$memory = new \stdClass;
			static::$memory->time  = number_format((microtime(true) - START_TIME) * 1000, 2) .' ms';
			static::$memory->usage = File::get_file_size(memory_get_usage(true));
			
			try {
				$output = File::get(COMPONENTS .'profiler/profiler.html', true);
			}
			catch(Execption $e) {
				die( $e->getMessage() );
			}
			
			$_queries		= '';
			$_checkpoints 	= '';
			
			if(static::$log_query) {
				// Log queries
				if(!empty(static::$queries)) {
					foreach(static::$queries as $query) {
						$_queries .= '<section><aside><span>'. $query->time .' ms.</span></aside><article>'. $query->query .'</article><div class="fluorine_profiler_clearfix"></div></section>';
					}
				} else {
					$_queries .= '<section class="fluorine_profiler_tab_none"><i>None</i></section>';
				}
			} else {
				$_queries .= '<section class="fluorine_profiler_tab_none"><i>Not logged</i></section>';
			}

			if(!empty(static::$checkpoints)) {
				foreach(static::$checkpoints as $cpoint) {
					$_checkpoints .= '<section><aside><span>'. $cpoint->type .'</span></aside><article>'. $cpoint->msg .'</article><div class="fluorine_profiler_clearfix"></div></section>';
				}
			} else {
				$_checkpoints .= '<section class="fluorine_profiler_tab_none"><i>None</i></section>';
			}

			$replacing = array(
				'{queries}'		=> $_queries,
				'{checkpoints}'	=> $_checkpoints,
				'{memUsage}'	=> static::$memory->usage,
				'{totalTime}'	=> static::$memory->time
			);
			$output = strtr($output, $replacing);

			return $output;

		}


	}
