<?php
	
	use Component\Url;
	use Component\File;
	use Component\Error;
	use Rain\TPL;

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/

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

			$this->uri 		= Url::uri();
			$this->method 	= Url::method();

			$this->uri = Url::reduceUri($path);

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
		 * Finds the route and gets the closure or controller
		 * @return 	boolean 	doesn't answer anything, just to quit the method from running.
		 * Errors that can be thrown here.
		 * Code 		Desc
		 * C_100		Controller file not found.
		 * C_101		Controller classname not valid.
		 * C_102		Methodname not valid. 
		 */
		public function getPage() {


			$exp = explode('/', Url::uri() );
			$i = count($exp);
			$_data = null;

			while($i) {
				
				$s = implode('/', $exp);

				if( Url::uri() == '') $s = '/';

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

				list($controller, $method) = explode('@', $data['func']);


				if( !is_file(APP.'controller/'. $controller .'.php')) return Error::register('controller_100');
				require_once(APP .'controller/'. $controller .'.php');

				$controller_name = $controller. '_Controller';
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

			$config = array(
				'base_url'		=> null,
				'tpl_dir'		=> APP .'template/'. $tplDir .'/',
				'cache_dir'		=> APP .'tmp/',
				'debug'			=> true,
				'php_enabled'	=> true,
				'tpl_ext'		=> 'php'
			);

			Tpl::configure($config);


			$tpl = new Tpl;
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
