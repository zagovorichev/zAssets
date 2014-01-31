<?php defined('SYSPATH') or die('No direct script access.');

return array(
    // On dev don't use (see full script code)
    'active' => Kohana::$environment==Kohana::PRODUCTION,
    'path' => '/static/assets/', // must be writeable
    'host' => 'http://site.com/assets/',
    'css_compress' => '/usr/local/lib/node_modules/sqwish/bin/sqwish',
    'js_compress' => '/usr/local/lib/node_modules/uglify-js/bin/uglifyjs',

    'css_path' => '/static/css/',
    'js_path' => '/static/js/',

    //TODO сделать через гирмана
    'compress_queue' => '/var/zassets/compress.queue',
);
