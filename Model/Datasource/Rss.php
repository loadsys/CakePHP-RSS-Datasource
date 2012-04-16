<?php
/**
 * RSS Feed Datasource
 *
 * Helps reading RSS feeds in CakePHP as if it were a model.
 *
 * PHP versions 4 and 5
 *
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2009, Loadsys Consulting, Inc. (http://www.loadsys.com)
 * @version       $1.0$
 * @modifiedby    $LastChangedBy: Joey Trapp (Loadsys) $
 * @lastmodified  $Date: 2010-10-11$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::uses('Xml',  'Utility');

class Rss extends DataSource {

	/**
	 * Default configuration options
	 * 
	 * @var array
	 * @access public
	 */
	public $_baseConfig = array(
		'feedUrl' => false,
		'encoding' => 'UTF-8',
		'cacheTime' => '+1 day',
		'version' => '2.0',
	);
	
	public $cacheSources = false;
		
	/**
	 * Should modify this method to ping or check url to see if it returns a valid
	 * response.
	 *
	 * @return bool
	 * @access public
	 */
	public function isConnected() {
		return true;
	}

	/**
	 * read function.
	 * 
	 * @access public
	 * @param object &$model
	 * @param array $queryData
	 * @return array
	 */
	public function read(&$model, $queryData = array()) {
		if (isset($model->feedUrl) && !empty($model->feedUrl)) {
			$this->config['feedUrl'] = $model->feedUrl;
		}
		$data = $this->__readData();

		$channel = Set::extract($data, 'rss.channel');
		if ( isset($channel['item']) ) {
			unset($channel['item']);
		}

		$items = Set::extract($data, 'rss.channel.item');

		if ( $items ) {
			$items = $this->__filterItems($items, $queryData['conditions']);

			$items = $this->__sortItems($model, $items, $queryData['order']);

			//used for pagination
			$items = $this->__getPage($items, $queryData);
	
			//return item count
			if ( Set::extract($queryData, 'fields') == '__count' ) {
				return array(array($model->alias => array('count' => count($items))));
			}
		} else {
			if ( Set::extract($queryData, 'fields') == '__count' ) {
				return array(array($model->alias => array('count' => count($items))));
			}
		}

		$result = array();
		if (is_array($items)) {
			foreach($items as $item) {
				$item['channel'] = $channel;
				$result[] = array($model->alias => $item);
			}
		}
		return $result;
	}

	/**
	 * name function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
	public function name($name) {
		return $name;
	}	

	/**
	 * __readData function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __readData() {
		$config = $this->config;
		$feedUrl = $config['feedUrl'];
		$cacheTime = $config['cacheTime'];

		$cachePath = 'rss_'.md5($feedUrl);
		Cache::set(array('duration' => $cacheTime));
		$data = Cache::read($cachePath);

		if ($data === false) {
			$data = Set::reverse(
				Xml::build(
					$this->config['feedUrl'],
					array(
						'version' => $this->config['version'],
						'encoding' => $this->config['encoding']
					)
				)
			);

			Cache::set(array('duration' => $cacheTime));
			Cache::write($cachePath, serialize($data));
		}
		else {
			$data = unserialize($data);
		}

		return $data;
	}
	
	/**
	 * __filterItems function.
	 * 
	 * @access public
	 * @param mixed $items
	 * @param mixed $conditions
	 * @return void
	 */
	public function __filterItems($items = null, $conditions = null) {
		if (empty($items) || empty($conditions)) {
			return $items;
		}

		$filteredItems = array();        

		foreach ($items as $item) {
			foreach ($conditions as $field => $condition) {
				$itemPassedFilters = $this->__passesCondition($item[$field], $condition);
			}
			
			if ($itemPassedFilters==true) {
                				array_push($filteredItems, $item);
			}
        		}
		return $filteredItems;
	}

	/**
	 * __passesCondition function.
	 * 
	 * @access public
	 * @param mixed $field
	 * @param mixed $condition
	 * @return void
	 */
	public function __passesCondition($field, $condition) {
        		return preg_match($condition, $field);
	}

	/**
	 * __getPage function.
	 * 
	 * @access public
	 * @param mixed $items
	 * @param array $queryData
	 * @return void
	 */
	public function __getPage($items = null, $queryData = array()) {
		if ( empty($queryData['limit']) ) {
			return $items;
		}

		$limit = $queryData['limit'];
		$page = $queryData['page'];

		$offset = $limit * ($page-1);

		return array_slice($items, $offset, $limit);
	}

	/**
	 * __sortItems function.
	 * 
	 * @access public
	 * @param mixed &$model
	 * @param mixed $items
	 * @param mixed $order
	 * @return void
	 */
	public function __sortItems(&$model, $items, $order) {
		if ( empty($order) || empty($order[0]) ) {
			return $items;
		}

		$sorting = array();
		foreach( $order as $orderItem ) {
			if ( is_string($orderItem) ) {
				$field = $orderItem;
				$direction = 'asc';
			}
			else {
				foreach( $orderItem as $field => $direction ) {
					continue;
				}
			}

			$field = str_replace($model->alias.'.', '', $field);

			$values =  Set::extract($items, '{n}.'.$field);
			if ( in_array($field, array('lastBuildDate', 'pubDate')) ) {
				foreach($values as $i => $value) {
					$values[$i] = strtotime($value);
				}
			}
			$sorting[] =& $values;
			
			switch(strtolower($direction)) {
				case 'asc':
					$direction = SORT_ASC;
					break;
				case 'desc':
					$direction = SORT_DESC;
					break;	
				default:
					trigger_error('Invalid sorting direction '. strtolower($direction));
			}
			$sorting[] =& $direction; 
		}
		
		$sorting[] =& $items;
		$sorting[] =& $direction;
		call_user_func_array('array_multisort', $sorting);
	
		return $items;
	}

	/**
	 * calculate function.
	 * 
	 * @access public
	 * @param mixed &$model
	 * @param mixed $func
	 * @param array $params
	 * @return void
	 */
	public function calculate(&$model, $func, $params = array()) {
		return '__'.$func;
	}
	
	/**
	 * This datasource does not support creating rss feeds
	 * 
	 * @access public
	 * @return void
	 */
	public function create() {
		return false;
	}
	
	/**
	 * This datasource does not support updating rss feeds
	 * 
	 * @access public
	 * @return void
	 */
	public function update() {
		return false;
	}
	
	/**
	 * This datasource does not support deleting rss feeds
	 * 
	 * @access public
	 * @return void
	 */
	public function delete() {
		return false;
	}
}