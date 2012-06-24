<?php
/*
Plugin Name: Nginx Cache Expire
Plugin URI: http://github.com/davent/nginx-cache-expire/ 
Description: Expires Nginx's file cache when content is changed/updated.
Version: 0.0.1
Author: Dave Avent
Author URI: http://lumux.co.uk/
License: GPL Version 2 http://www.gnu.org/licenses/gpl-2.0.html
*/

define('NCE_DIR', WP_CONTENT_DIR . '/plugins/nginx-cache-expire');

define('NCE_LIB_DIR', NCE_DIR . '/lib');

require(NCE_LIB_DIR . '/NginxCacheExpire.inc.php');

include(NCE_DIR . '/options.php');

class WPNginxCacheExpire {

	// URLs we wish to expire
	protected $expire_urls = array();

	var $ngx_cache;
    
	public function __construct() {

		// manages plugin activation and deactivation
                register_activation_hook( __FILE__, array(&$this, 'activate') );
                register_deactivation_hook( __FILE__, array(&$this, 'deactivate') );
		
		// Instansiate NginxCacheExpire with hard-coded $cache_dir and $cache_levels for now
		$this->ngx_cache = new NginxCacheExpire(get_option('nce_cache_dir'), get_option('nce_cache_level'));

		foreach ($this->registered_events() as $event) {

			add_action($event, array($this, 'add_to_expire_list'));

		}

		add_action('shutdown', array($this, 'expire_posts'));
	}

	static function activate() {

	}

	static function deactivate() {
	}

	// Return the WordPress Events which should trigger a cache expiring
	private function registered_events () {

		$triggers = array();

		foreach( get_option( 'nce_triggers' ) as $trigger => $enabled ) {

			if( $enabled == "1" ) {

				$triggers[] = $trigger;

			}

		}

		return $triggers;

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

			// If content has changed we should really update /sitemap.xml to keep the search engines happy
			$this->expire(site_url() . '/sitemap.xml');

		}        

	}

	// Actually expire the URL from Nginx
	protected function expire( $url ) {

        	if($this->ngx_cache->uri( $url )) {

                	if( $this->ngx_cache->expire() ) {


			} else {

				error_log( "Could not expire " . $url );

			}

        	} else {

			error_log( "Could not expire " . $url );

		}

	}

}

$nginx_cache_expire = new WPNginxCacheExpire();

?>
