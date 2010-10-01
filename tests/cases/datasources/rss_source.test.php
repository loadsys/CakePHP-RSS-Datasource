<?php
/**
 * Rss Datasource Test file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       datasources
 * @subpackage    datasources.tests.cases.models.datasources
 * @since         CakePHP Datasources v 0.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::import('Datasource', 'Datasources.RssSource');

// Add new db config
ConnectionManager::create('test_rss', array(
					'datasource' => 'RssDatasource.RssSource',
					'feedUrl' => 'http://loadsys1.com/rss_datasource_test.rss',
					'encoding' => 'UTF-8',
					)
);

/**
 * Rss Testing Model
 *
 */
class RssModel extends CakeTestModel {

/**
 * Name of Model
 *
 * @var string
 * @access public
 */
	var $name = 'RssModel';

/**
 * Database Configuration
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'test_rss';

/**
 * Set recursive
 *
 * @var integer
 * @access public
 */
	var $recursive = -1;
}

/**
 * Rss Datasource Test
 *
 */
class RssSourceTest extends CakeTestCase {

/**
 * Rss Source Instance
 *
 * @var RssSource
 * @access public
 */
	var $Model = null;

	var $channelAppend = array();
/**
 * Set up for Tests
 *
 * @return void
 * @access public
 */
	function setUp() {
		parent::setUp();
		$this->Model =& ClassRegistry::init('RssModel');
		$this->channelAppend = array(
	                                        'title' => 'Test Feed for CakePHP RSS Datasource Unit Test',
        	                                'link' => 'http://github.com/loadsys/CakePHP-RSS-Datasource',
                	                        'description' => 'Test RSS feed for data source test',
           	);

	}

/**
 * testFindAll
 *
 * @return void
 * @access public
 */
	function testFindAll() {
		$result = $this->Model->find('all');
		$expected = array(
			array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                        array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
		);
		$this->assertEqual($result, $expected);
	}

/**
 * testFindFields
 * DATASOURCE CURRENTLY DOESNT SUPPORT FIELDS ARRAY
 * @return void
 * @access public
 */
/* 
	function testFindFields() {
		$expected = array(
			array('RssModel' => array('title' => 'ATest1')),
			array('RssModel' => array('title' => 'BTest2')),
		);
		$result = $this->Model->find('all', array('fields' => array('title')));
		pr($result);
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('fields' => array('RssModel.title')));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('fields' => array('RssModel.title', 'Unknown.title')));
		$this->assertEqual($result, $expected);
	}
*/

/**
 * testFindLimit
 *
 * @return void
 * @access public
 */
	function testFindLimit() {
		$result = $this->Model->find('all', array('limit' => 1));
		$expected = array(
                        array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
		);
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('limit' => 1, 'page' => 2));
		$expected = array(
                        array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
		);
		$this->assertEqual($result, $expected);
	}

/**
 * testFindOrder
 *
 * @return void
 * @access public
 */
	function testFindOrder() {
		$result = $this->Model->find('all', array('order' => array('RssModel.title' => 'desc')));
		$expected = array(
                        array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                        array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
		);
		$this->assertEqual($result, $expected);

/* FIELDS ARRAY CURRENTLY NOT SUPPORTED
		
		$result = $this->Model->find('all', array('fields' => array('RssModel.title'), 'order' => 'RssModel.title DESC'));
		$expected = array(
                        array('RssModel' => array('title' => 'BTest2',)),
                        array('RssModel' => array('title' => 'ATest2',)),
		);
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('fields' => array('RssModel.title'), 'order' => 'RssModel.title desc', 'limit' => 1, 'page' => 2));
		$expected = array(
                        array('RssModel' => array('title' => 'ATest2',)),
		);
		$this->assertEqual($result, $expected);
*/
	}
/**
 * testFindConditions
 *
 * @return void
 * @access public
 */
	function testFindConditions() {
		$result = $this->Model->find('all', array('conditions' => array('RssModel.title' => 'ATest1')));
		$expected = array(array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title =' => 'ATest1')));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title = ATest1')));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title !=' => 'ATest1')));
		$expected = array(array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title != ATest1')));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title LIKE' => '%ATest%')));
                $expected = array(array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)));

		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title LIKE %ATest%')));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title' => array('ATest1', 'BTest2'))));
                $expected = array(
                        array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                        array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                );
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title IN (ATest1, BTest2)')));
		$this->assertEqual($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('RssModel.title' => 'ATest1', 'RssModel.description' => 'BTest2')));
		$expected = array();
		$this->assertIdentical($result, $expected);
	}

/**
 * testFindconditionsRecursive
 *
 * @return void
 * @access public
 */
	function testFindConditionsRecursive() {
		$result = $this->Model->find('all', array('conditions' => array('AND' => array('RssModel.title' => 'ATest1', 'RssModel.description' => 'BTest2'))));
		$expected = array();
		$this->assertIdentical($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('OR' => array('RssModel.title' => 'ATest1', 'RssModel.title' => 'BTest2'))));
                $expected = array(
                        array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                        array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                );
		$this->assertIdentical($result, $expected);

		$result = $this->Model->find('all', array('conditions' => array('NOT' => array('RssModel.title' => 'ATest1'))));
		$expected = array(
                        array('RssModel' => array('title' => 'BTest2', 'description' => 'BTest2', 'link' => 'http://www.test2.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
		);
		$this->assertIdentical($result, $expected);
	}

/**
 * testFindFirst
 *
 * @return void
 * @access public
 */
	function testFindFirst() {
		$result = $this->Model->find('first');
                $expected = array(
                        array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
		);
		$this->assertEqual($result, $expected);

/* FIELDS ARRAY NOT YET IMPLEMENTED 
		$result = $this->Model->find('first', array('fields' => array('title')));
		$expected = array('RssModel' => array('title' => 'ATest1'));
		$this->assertEqual($result, $expected);
*/
	}

/**
 * testFindCount
 *
 * @return void
 * @access public
 */
	function testFindCount() {
		$result = $this->Model->find('count');
		$this->assertEqual($result, 2);

		$result = $this->Model->find('count', array('limit' => 1));
		$this->assertEqual($result, 1);

		$result = $this->Model->find('count', array('limit' => 5));
		$this->assertEqual($result, 2);

		$result = $this->Model->find('count', array('limit' => 1, 'page' => 2));
		$this->assertEqual($result, 1);
	}

/**
 * testFindList
 *
 * @return void
 * @access public
 */
	function testFindList() {
                $this->Model->primaryKey = 'title';
		$result = $this->Model->find('list');
		$expected = array('ATest1' => 'ATest1', 'BTest2' => 'Btest2');
		$this->assertEqual($result, $expected);

		$this->Model->displayField = 'description';
		$result = $this->Model->find('list');
                $expected = array('ATest1' => 'ATest1', 'BTest2' => 'Btest2');
		$this->assertEqual($result, $expected);
	}

/**
 * testRead
 *
 * @return void
 * @access public
 */
	function testRead() {
		$result = $this->Model->read();
                $expected = array(
                        array('RssModel' => array('title' => 'ATest1', 'description' => 'ATest1', 'link' => 'http://www.test1.com', 'pubDate'=>'Tue, 7 Sep 2010 00:01:01 -0500', 'Channel' => $this->channelAppend)),
                );
		$this->assertEqual($result, $expected);

		$result = $this->Model->read(array('title'), 2);
		$expected = array('RssModel' => array('title' => 'ATest2'));
		$this->assertEqual($result, $expected);
	}
}


?>