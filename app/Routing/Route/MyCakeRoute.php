<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

//App::uses('CakeRoute', 'Routing/Route');

/**
 * NC Plugin用Routes
 *
 * <pre>
 * 		blocks(active-blocks)/(block_id)/(plugin)/(controller)/XXXX -> コントローラがあれば、そのコントローラを呼ぶ、なければ
 * 		アクション名と判断する（(plugin)/(pluginController)/(action)）。
 * </pre>
 *
 * @copyright     Copyright 2012, NetCommons Project
 * @package       Routing.Route
 * @author        Noriko Arai,Ryuji Masukawa
 * @since         v 3.0.0.0
 * @license       http://www.netcommons.org/license.txt  NetCommons License
 */
class MyCakeRoute extends CakeRoute {
	/**
	 * Checks to see if the given URL can be parsed by this route.
	 * If the route can be parsed an array of parameters will be returned; if not
	 * false will be returned. String urls are parsed if they match a routes regular expression.
	 *
	 * @param string $url The url to attempt to parse.
	 * @return mixed Boolean false on failure, otherwise an array or parameters
	 */
	public function parse($url) {
		$route = parent::parse($url);
		if (!$route) {
			return false;
		}
		//if(isset($route['plugin']) && !isset($route['controller'])) {
		//	$route['controller'] = $route['plugin'];
		//}
		if(isset($route['plugin'])) {
			$block_type = Configure::read(NC_SYSTEM_KEY.'.block_type');
			if(!isset($block_type) && isset($route['block_type']) && $route['block_type'] == 'blocks') {
				return false;
			}
		}
		// Add Start Ryuji.M
		// Conrtollerがあるかどうか判断し、なければ、Actionとする。
		// コントローラ名は常にpluginName + controllerにて判断する。
		// 例：active-blocks/(block_id)/announcement/edit
		//     -> Plugin/AnnouncementEditController.phpがあれば、AnnouncementEditController->indexメソッドへ
		//     -> なければ、Plugin/AnnouncementController.php->editメソッドへ
		if(isset($route['controller'])) {
			$pluginName = Inflector::camelize($route['plugin']);
			if($route['plugin'] == $route['controller']) {
				$controller = Inflector::camelize($route['controller']);
			} else {
				$controller = $pluginName . Inflector::camelize($route['controller']);
			}
			$file_path = App::pluginPath($pluginName) . 'Controller' . DS .$controller.'Controller.php';
			if (!file_exists($file_path)) {
				$buf_action = isset($route['action']) ? $route['action'] : null;
				$route['action'] = $route['controller'];
				$route['controller'] = $route['plugin'];
				if(isset($buf_action)) {
					if(isset($route['_args_'])) {
						$route['_args_'] = $buf_action .'/'. $route['_args_'];
					} else {
						$route['_args_'] = $buf_action;
					}
				}
			} else {
				$route['controller'] = $controller;
			}
		}
		// Add End Ryuji.M
		return $route;
	}

	/**
	 * Reverse route plugin shortcut urls. If the plugin and controller
	 * are not the same the match is an auto fail.
	 *
	 * @param array $url Array of parameters to convert to a string.
	 * @return mixed either false or a string url.
	 */
	public function match($url) {
		if(!isset($url['permalink'])) {
			$permalink = Configure::read(NC_SYSTEM_KEY.'.permalink');
			if($permalink) {
				$url['permalink'] = $permalink;
			} else {
				$url['permalink'] = '';
			}
		}
		if(isset($url['controller'])) {
			$controller_arr = explode('_', $url['controller'], 2);
			if($controller_arr[0] == $url['plugin']) {
				if(isset($controller_arr[1])) {
					$url['controller'] = $controller_arr[1];
				} else {
					$url['controller'] = '';
				}
			}
		}

		if(isset($url['plugin']) && !isset($url['block_type'])) {
			$block_type = Configure::read(NC_SYSTEM_KEY.'.block_type');
			$url['block_type'] = isset($block_type) ? $block_type : 'active-blocks';
		}

		if(isset($url['action']) && $url['action'] == 'index') {
			$url['action'] = '';
		}

		//if (isset($url['controller']) && isset($url['plugin']) && $url['plugin'] != $url['controller']) {
		//	return false;
		//}
		$this->defaults['controller'] = $url['controller'];

		$result = parent::match($url);
		unset($this->defaults['controller']);

		return $result;
	}

/**
 * Finds URL for specified action.
 *
 * Returns an URL pointing to a combination of controller and action. Param
 * $url can be:
 *
 * - Empty - the method will find address to actual controller/action.
 * - '/' - the method will find base URL of application.
 * - A combination of controller/action - the method will find url for it.
 *
 * There are a few 'special' parameters that can change the final URL string that is generated
 *
 * - `base` - Set to false to remove the base path from the generated url. If your application
 *   is not in the root directory, this can be used to generate urls that are 'cake relative'.
 *   cake relative urls are required when using requestAction.
 * - `?` - Takes an array of query string parameters
 * - `#` - Allows you to set url hash fragments.
 * - `full_base` - If true the `FULL_BASE_URL` constant will be prepended to generated urls.
 *
 * @param string|array $url Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
 *   or an array specifying any of the following: 'controller', 'action',
 *   and/or 'plugin', in addition to named arguments (keyed array elements),
 *   and standard URL arguments (indexed array elements)
 * @param bool|array $full If (bool) true, the full base URL will be prepended to the result.
 *   If an array accepts the following keys
 *    - escape - used when making urls embedded in html escapes query string '&'
 *    - full - if true the full base URL will be prepended.
 * @return string Full translated URL with base path.
 */
	public static function url($url = null, $full = false) {
		//
		// block_typeをページ表示中ならば、block になるように修正
		//
		if (empty($url)) {
			$block_type = Configure::read(NC_SYSTEM_KEY.'.block_type');
			if(isset($block_type) && $block_type == 'blocks') {
				$request = self::$_requests[count(self::$_requests) - 1];
				$here = $request->here;
				$here = preg_replace('/(.*)\/active-blocks\/([0-9]*)/i', '$1/blocks/$2', $here);

				$url = isset($here) ? $here : '/';
			}

			if ($full && defined('FULL_BASE_URL')) {
				$url = FULL_BASE_URL . $url;
			}
		}
		return parent::url($url, $full);
	}
}
