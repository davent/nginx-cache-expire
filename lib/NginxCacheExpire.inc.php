<?php

	class NginxCacheExpire {

		var $uri;

		var $cache_dir;

		var $cache_levels;

		var $cache_key_string;

		var $expirable = false;
	
		public function __construct($dir, $levels) {

			$this->set_cache_dir( $dir );

			$this->set_cache_levels( $levels );

		}

		public function uri( $uri ) {

			$this->uri = $uri;

			if ( !is_readable( $this->get_cache_file() ) || !file_exists( $this->get_cache_file() ) ) {

				error_log( "File either does not exist ot is not readable!" );
				return false;

			}

			if ( is_writable( $this->get_cache_file() ) ) {

				$this->expirable = true;

			}

			return true;

		}

		private function set_cache_dir( $cache_dir ) {

			$this->cache_dir = $cache_dir;

		}

		private function set_cache_levels( $levels ) {

			$this->cache_levels = $levels;

		}

		private function get_cache_file() {

			$cache_dir = $this->cache_dir;

			$parsed_uri = parse_url( $this->uri );
			
			if( !isset($parsed_uri['path']) ) {

				$parsed_uri['path'] = "/";

			}

			if( !isset($parsed_uri['query']) ) {

				$parsed_uri['query'] = "";

			}

			$this->cache_key_string = $parsed_uri['scheme'] . 'GET' . $parsed_uri['host'] . $parsed_uri['path'] . $parsed_uri['query'];

			$cache_key = md5( $this->cache_key_string );
			$cache_key_array = str_split( $cache_key );

			foreach ( preg_split("/:/",$this->cache_levels) as $level ) {
				$cache_dir .= '/' . substr(implode($cache_key_array), (0 - $level) );
				array_pop( $cache_key_array );
			}

			return ( $cache_dir . '/' . $cache_key );

		}

		public function get_expire_time() {

			$cache_file = $this->get_cache_file();

			$fp = fopen($cache_file, "rb");
			$data = fread($fp, 8);
			$valid = unpack("l", $data);     
			$data = fread($fp, 8);
			$last_mod = unpack("l", $data);     

			fclose( $fp );

			$expiry = $valid[1] + $last_mod[1];

			return $expiry;

		}

		public function expire() {

			if ( $this->expirable ) {

				$cache_file = $this->get_cache_file();
			
				$fp = fopen($cache_file, "rb+");     

				if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock

					fseek( $fp, 0);
					fwrite ( $fp , pack("l", time()));
					fseek( $fp, 8);
					fwrite ( $fp , pack("l", array("-1")));
    					fflush($fp);            // flush output before releasing the lock
    					flock($fp, LOCK_UN);    // release the lock

				} else {
			
					error_log( "Cannot get flick lock!" );
					return false;

				}

				fclose( $fp );
			
				return true;

			} else {

				error_log( "Cannot expire this file!" );
				return false;

			}

		}

	}

?>
