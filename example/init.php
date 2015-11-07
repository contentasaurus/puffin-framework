<?php

use \puffin\session as session;
use \puffin\app as app;

############################################

if( !defined(SERVER_ROOT) ){ define('SERVER_ROOT', dirname($_SERVER['DOCUMENT_ROOT'])); }
if( !defined(APP_PATH) ){ define('APP_PATH', SERVER_ROOT.'/app'); }
if( !defined(VENDOR_PATH) ){ define('VENDOR_PATH', SERVER_ROOT.'/vendor'); }
if( !defined(CONTROLLER_PATH) ){ define('CONTROLLER_PATH', APP_PATH.'/controllers'); }
if( !defined(MODEL_PATH) ){ define('MODEL_PATH', APP_PATH.'/models'); }
if( !defined(PLUGIN_PATH) ){ define('PLUGIN_PATH', APP_PATH.'/plugins'); }
if( !defined(VIEW_PATH) ){ define('VIEW_PATH', APP_PATH.'/views'); }
if( !defined(PARTIAL_PATH) ){ define('PARTIAL_PATH', VIEW_PATH.'/partials'); }
if( !defined(LAYOUT_PATH) ){ define('LAYOUT_PATH', VIEW_PATH.'/layouts'); }
if( !defined(SCRIPT_PATH) ){ define('SCRIPT_PATH', VIEW_PATH.'/scripts'); }
if( !defined(PUBLIC_PATH) ){ define('PUBLIC_PATH', SERVER_ROOT.'/public'); }
if( !defined(VENDOR_PATH) ){ define('VENDOR_PATH', SERVER_ROOT.'/vendor'); }
if( !defined(SYSTEM_PATH) ){ define('SYSTEM_PATH', VENDOR_PATH.'/puffin'); }

if( !defined(MUSTACHE_EXT) ){ define('MUSTACHE_EXT', '.html'); }


############################################

require VENDOR_PATH . '/autoload.php';

############################################

session::start();
Mustache_Autoloader::register();
$puffin = app::init();
$app = app::router();

require SERVER_ROOT.'/app/app.php';

app::route();
echo app::render();
