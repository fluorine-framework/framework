<?php
	
	
	use Component\File;
	use Component\Error;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/

	require_once(SYS .'raintpl/rain.tpl.class.php');

	class Bootstrap {

		protected $uri;
		protected $method;

		protected $segment;

		protected $view;
		protected $isView;
		protected $isJson;

		/**
		 * Assign the requested uri and method to the class members.
		 * @return 	void
		*/
		public function __construct($path) {

			$this->uri 		= $_SERVER['REQUEST_URI'];
			$this->method 	= $_SERVER['REQUEST_METHOD'];

			if( strpos($this->uri, '?') !== false ) {
				$_queryString = explode('?', $this->uri);
				$this->uri = $_queryString[0];
			}

			$this->removeUri($path);

			$_uri = explode('/', $this->uri);
			$uri = array();
			foreach($_uri as $k => $v) {

				if(empty($v)) {
					unset($_uri[$k]);
				} else {
					$uri[] = $v;
				}
			}
			if(empty($uri)) $uri[] = '/';

			$this->segment = $uri;

			$this->isView = true;
			$this->isJson = false;

		}

		/**
		 * Removes some of the path in the uri. Used if the system is not running
		 * on the top level of the domain.
		 * @param 	string 	$path
		 * @return 	void
		 */
		private function removeUri($path) {

			$_uri = explode($path, $this->uri);
			$this->uri = $_uri[1];

			define('URI', $this->uri);
		}

		/**
		 * Finds the route and gets the closure or controller
		 * @return 	boolean 	doesn't answer anything, just to quit the method from running.
		 * Errors that can be thrown here.
		 * Code 		Desc
		 * C_100		Controller file not found.
		 * C_101		Controller classname not valid.
		 * C_102		Methodname not valid. 
		 */
		public function getPage() {


			$exp = explode('/', URL::uri() );
			$i = count($exp);
			$_data = null;

			while($i) {
				
				$s = implode('/', $exp);

				if( URL::uri() == '') $s = '/';

				$_data = Router::find($this->method, $s);
				if($_data !== false) break;

				unset($exp[$i-1]);
				$i--;
			}

			// Try to find a route that contains a wildcard.
			// Router::find_with_wildcard();
			
			$data = $_data;
			

			if($data === false) {
				Error::register('http_404');
				return false;
			}

			if($data['closure'] == true) {
				
				$this->view = $data['func']();
				if( !$this->view instanceof View ) {
					$this->isView = false;
				}

			} else {

				// Getting the correct controller and method starts here.

				if( strpos($data['func'], '@') === false) {
					$this->content = $data['func'];
					return true;
				}

				$_d = explode('@', $data['func']);
				$controller = $_d[0];
				$method 	= $_d[1];

				$controller_name = $controller. '_Controller';

				if( !is_file(APP.'controller/'. $controller .'.php')) return Error::register('controller_100');

				require_once(APP .'controller/'. $controller .'.php');

				if( !class_exists($controller_name)) return Error::register('controller_101');

				$ct = new $controller_name();
				$restful = (isset($ct->restful)) ? $ct->restful : false;
				$r_method  = ($restful === true) ? $this->method .'_' : '';

				$method_name = $r_method . $method;
				if( !method_exists($controller_name, $method_name)) return Error::register('controller_102');

				$this->view = call_user_func(array($ct, $method_name));

				if( !$this->view instanceof View ) {
					$this->isView = false;
				}

				if( $this->view instanceof Json ) {
					$this->isJson = true;
				}

			}
		}

		/**
		 * Outputs the content by finding the correct template
		 * and view file, if needed.
		 */
		public function getContent() {

			$tplDir = ($this->isView) ? $this->view->tpl : Config::$template;

			raintpl::configure('base_url', null);
			raintpl::configure('tpl_dir', APP .'template/'. $tplDir .'/');
			raintpl::configure('cache_dir', APP .'tmp/');
			raintpl::configure('php_enabled', true);
			raintpl::configure('tpl_ext', 'php');


			$tpl = new RainTPL;
			$tpl->assign('isView', $this->isView);
			$tpl->assign('title', Config::$data->title);

			if($this->isView) {

				$vars = $this->view->getVars();

				if(!empty($vars)) {
					foreach($vars as $key => $value) {
						$tpl->assign($key, $value);
					}
				}

				$view = '../../'. APP .'view/'. $this->view->path;

			} else {

				$view = $this->view;

				if($this->isJson === true) {

					echo $view;
					exit;

				}
			}

			try {
				File::get('template/'. $tplDir .'/template.php');
				if(class_exists('Template\Model')) {
					$tpl_class = new Template\model;
					$tpl->assign('template', $tpl_class);
				}
			}
			// Ignore the error if the file doesn't exists.
			catch(Exception $e) {}


			$tpl->assign('view', $view);
			$tpl->draw('index');

		}



	}


















