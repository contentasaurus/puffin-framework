<?php

use \puffin\session as session;
use \puffin\app as app;

############################################

define('SERVER_ROOT', dirname(__FILE__));

define('APP_PATH', SERVER_ROOT.'/app');
define('CONTROLLER_PATH', APP_PATH.'/controllers');
define('MODEL_PATH', APP_PATH.'/models');
define('PLUGIN_PATH', APP_PATH.'/plugins');
define('TEST_PATH', APP_PATH.'/tests');
define('VIEW_PATH', APP_PATH.'/views');
define('PARTIAL_PATH', VIEW_PATH.'/partials');
define('LAYOUT_PATH', VIEW_PATH.'/layouts');
define('SCRIPT_PATH', VIEW_PATH.'/scripts');
define('PUBLIC_PATH', SERVER_ROOT.'/public');
define('VENDOR_PATH', SERVER_ROOT.'/vendor');
define('SYSTEM_PATH', VENDOR_PATH.'/puffin');

define('MUSTACHE_EXT', '.html');
define('ERROR_REPORTING', true);

############################################

require VENDOR_PATH . '/autoload.php';

############################################

if (ERROR_REPORTING) {
	ini_set("display_errors", "1");
	error_reporting(E_ERROR);
}

############################################

session::start();
view::init('mustache');

$app = app::init_router();
require 'app/app.php';
app::route();

echo app::render();

exit;
