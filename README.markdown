

## Installation

cd your_app_name/plugins/
git clone git@github.com:loadsys/CakePHP-RSS-Datasource.git rss

## Usage

Currently, you must create a different datasource variable for each feed that will be used.

The values shown below under the Optional comment will be set to the values you see there if they are left out. The required options must exist.

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

Inside of each model that will consume an RSS feed, change the $useDbConfig property to the appropriate feed property from the database.php file.

	<?php
	//  app/models/feed.php
	class Feed extends AppModel {
		public $name = 'Feed';
		public $useDbConfig = 'feedSource';
	}
	?>

Then in your controller that uses the model, simply use the $this->Model->find('all');

	<?php
	//  app/controllers/feeds_controller.php
	class FeedsController extends AppController {
		public $uses = array('Feed');
		public index() {
			$feeds = $this->Feed->find('all');
			$this->set(compact('feeds'));
		}
	}
	?>

## Reference

Original blog post: http://blog.loadsys.com/2009/06/19/cakephp-rss-feed-datasource/