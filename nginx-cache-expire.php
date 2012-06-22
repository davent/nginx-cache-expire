<?php
/*
Plugin Name: Nginx Cache Expire
Plugin URI: http://github.com/davent/nginx-cache-expire/ 
Description: Expires Nginx's file cache when content is changed/updated.
Version: 0.0.1
Author: Dave Avent
Author URI: http:/lumux.co.uk/
License: Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0
*/

class NginxCacheExpire {

	// URLs we wish to expire
	protected $expire_urls = array();

	// Wordpress events which should trigger a cache expiry 
	protected $registered_events = array('publish_post', 'edit_post', 'deleted_post');
    
	public function __construct() {

		foreach ($this->registered_events as $event) {

			add_action($event, array($this, 'add_to_expire_list'));

		}

		add_action('shutdown', array($this, 'expire_posts'));
	}

	// Add a URL to the list to be expired
	public function add_to_expire_list( $postId ) {

		array_push($this->expire_urls, get_permalink($postId));

	}

	// Iterate through the list of URLs to be expired and expire them
	public function expire_posts() {

		$expire_urls = array_unique($this->expire_urls);

		foreach($expire_urls as $url) {

			$this->expire($url);
		}
        
		if (!empty($expire_urls)) {
 
			// Clear the homepage too to take in to account updated posts
			$this->expire(home_url());

		}        

	}

	// Actually expire the URL from Nginx
	protected function expire( $url ) {

		file_put_contents('/tmp/nginx-cache-expire.log', $url . "\n", FILE_APPEND | LOCK_EX);

	}

}

$nginx_cache_expire = new NginxCacheExpire();

