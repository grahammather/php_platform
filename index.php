<?php 
ini_set('display_errors', '1');
error_reporting(E_ALL);

require 'fatfree-2.0.3/lib/base.php';
require 'lib/rb.php';

define("BASEDIR", __DIR__);
F3::set('AUTOLOAD', 'lib/; controllers/; models/; routes/; src/');

// AXON
//F3::set('DB', new DB(Cfg::instance()->db_dsn, Cfg::instance()->db_username, Cfg::instance()->db_password));

// REDBEANPHP
R::setup(Cfg::instance()->db_dsn, Cfg::instance()->db_username, Cfg::instance()->db_password);
R::debug(Cfg::instance()->debug_sql);
// R::freeze( true ); //will freeze redbeanphp

/**
 * how home looks when you're either logged in or not logged in
 */
F3::route('GET|POST /', 'HomeRouteHandler->display');

/**
 * test
 */
F3::route('GET|POST /test', 'TestRouteHandler->display');

/**
 * purge memcache
 */
F3::route('GET|POST /purgecache', 'PurgeCacheRouteHandler->display');

/**
 * return the current time
 */
F3::route('GET /time',
function () {
    $now = new DateTime(); 
    echo $now->format("M j, Y H:i:s O")."\n"; 
}
);

// --------------------------------

/**
 * javascript constants that are generated by php
 */
F3::route('GET /const.js',
function () {
    F3::set('app_url', Cfg::instance()->app_url);
    F3::set('app_id', Cfg::instance()->fb_app_id);
    F3::set('master_url', Cfg::instance()->master_url);
    F3::set('v', Cfg::instance()->v);
    F3::set('gateway_url', Cfg::instance()->master_url.'gateway');
    
    echo F3::render('templates/const.js.php');
}
);

/**
 * javascript functions that are generated by php
 */
F3::route('GET /func.js',
function () {
    $params = array(
        'redirect_uri' => Cfg::instance()->app_url,
        'scope' => 'publish_actions,email'
    );
    
    $loginUrl = FB::instance()->facebook->getLoginUrl($params);
    
    F3::set('loginUrl', $loginUrl);
    echo F3::render('templates/func.js.php');
}
);

/**
 * javascript variables for this page load
 */
F3::route('GET /vars.js', 'VarsRouteHandler->display');

/**
 * minifier
 */
F3::route('GET /min',
function () {
    Web::minify($_GET['base'], explode (',', $_GET['files']));
}
, 3600);

F3::run();
