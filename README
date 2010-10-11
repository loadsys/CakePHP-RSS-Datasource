See blog post http://blog.loadsys.com/2009/06/19/cakephp-rss-feed-datasource/

`/config/database.php
<?php
class DATABASE_CONFIG {

	public $feedSource = array(
	
		/** Required **/
		'datasource' => 'rss.rss',
		'database' => false,
		'feedUrl' => 'http://feedurl',
		
		/** Optional **/
		'encoding' => 'UTF-8',
		'cacheTime' => '+1 day',
	);
}
?>`

`/models/feed.php
<?php
class Feed extends AppModel {
	public $name = 'Feed';
	public $useDbConfig = 'feedSource';
}
?>`


`/controllers/feeds_controller.php
<?php
class FeedsController extends AppController {
	public index() {
		$feeds = $this->Feed->find('all');
		$this->set(compact('feeds'));
	}
}
?>`