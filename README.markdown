

## Installation

cd your_app_name/plugins/
git clone git@github.com:loadsys/CakePHP-RSS-Datasource.git rss

## USAGE

<?php
//  app/config/database.php
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
?>


<?php
//  app/models/feed.php
class Feed extends AppModel {
	public $name = 'Feed';
	public $useDbConfig = 'feedSource';
}
?>

<?php
//  app/controllers/feeds_controller.php
class FeedsController extends AppController {
	public index() {
		$feeds = $this->Feed->find('all');
		$this->set(compact('feeds'));
	}
}
?>

## Reference

Original blog post: http://blog.loadsys.com/2009/06/19/cakephp-rss-feed-datasource/