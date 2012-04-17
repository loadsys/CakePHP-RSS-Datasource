## Installation

	cd your_app_name/Plugin/
	git clone git@github.com:loadsys/CakePHP-RSS-Datasource.git Rss

You may optionally want to add it as a submodule instead of the clone command above.

	git submodule add git@github.com:loadsys/CakePHP-RSS-Datasource.git Rss

Also, add the following line to your bootstrap.php.

	CakePlugin::load('Rss');

## Usage

The values shown below under the Optional comment will be set to the values you see there if they are left out. The required options must exist. If you wish, you can make different database config property for each different feed you would like to work with. The feedUrl is an optional parameter that will be used by default if it is not set in the model.

	<?php
	//  app/config/database.php
	class DATABASE_CONFIG {
		public $feedSource = array(

			/** Required **/
			'datasource' => 'Rss.Rss',
			'database' => false,

			/** Optional **/
			'feedUrl' => 'http://feedurl',
			'encoding' => 'UTF-8',
			'cacheTime' => '+1 day',
		);
	}
	?>

Inside of each model that will consume an RSS feed, change the $useDbConfig property to the appropriate feed property from the database.php file. If you are instead using a single database config property, you would set the feed url in the model. The public $feedUrl is read before a read, and takes priority over the feedUrl set in the database config property.

	<?php
	//  app/models/feed.php
	class Feed extends AppModel {
		public $name = 'Feed';
		public $useDbConfig = 'feedSource';
		/** Optional **/
		public $feedUrl = 'http://feedUrl';
	}
	?>

Then in your controller that uses the model, simply use the $this->Model->find('all');

	<?php
	//  app/controllers/feeds_controller.php
	class FeedsController extends AppController {
		public $uses = array('Feed');
		public function index() {
			$feeds = $this->Feed->find('all');
			$this->set(compact('feeds'));
		}
	}
	?>

## Reference

Original blog post: http://blog.loadsys.com/2009/06/19/cakephp-rss-feed-datasource/