<?php

	require('../lib/NginxCacheExpire.inc.php');

	$ngx_cache = new NginxCacheExpire('/data/cache/nginx', '1:2');
	if($ngx_cache->uri( $argv[1] )) {

		echo "Was going to expire: " . date('r', $ngx_cache->get_expire_time()) . "\n";
		$ngx_cache->expire();
		echo "Was going to expire: " . date('r', $ngx_cache->get_expire_time()) . "\n";

	}

?>
