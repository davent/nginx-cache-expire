nginx-cache-expire
==================

WordPress Module to gracefully expire Nginx's file cache

The main point of this plugin is to expire Nginx's file-based cache, used in the Proxy and FastCGI module,
in a graceful way in order to utilise Nginx's ability to serve stale cache files while the cache is
re-populated and therefore avoid thundering herd issues.

Note: This is very much a work-in-progress/proof-of-concept!



TODO
====

 * Proper/some error reporting and logging

 * Add more Events triggers to the Options page

