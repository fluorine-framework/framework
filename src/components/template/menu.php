<?php
	

	/*************************
	 *                       *
	 *   (C)Copyright 2013   *
	 *     Niels Fyhring     *
	 *                       *
	 *************************/
	
	namespace Template;

	class Menu {

		private static $menus, $items;


		public static function init() {

		}


		public static function register($name, $parentName=false, $class=false, $open=false) {

			static::$menus[$name] = array($name, $parentName, $class, $open);

			static::$items[$name] = array();
		}


		public static function addItem($menu, $title, $url, $subMenuName=false, $attr=false) {

			static::$items[$menu][] = array($title, $url, $subMenuName, $attr);
		}


		public static function getMenu($menu) {

			return static::$menus[$menu];
		}

		public static function getMenuWithParent($parent) {

			$data = array();
			foreach(static::$menus as $menu) {

				if($menu[1] == $parent) $data[] = $menu;
			}

			return $data;
		}


		public static function getItems($menu) {

			return static::$items[$menu];
		}


		public static function getData() {

			echo "<pre>";
			print_r(static::$menus);
			print_r(static::$items);
			echo "</pre>";

		}
	}