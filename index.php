<?php

// Load Fat Free Framework
$f3 = require('vendor/bcosca/fatfree/base.php');
$f3->config('app/config.cfg');

// Set Parameters
$f3->set('AUTOLOAD','app/');
$f3->set('UI','templates/');
$f3->set('UPLOADS','tmp/uploads/');
$f3->set('VERSION','1.0');
$f3->set('CACHE', true); // Required For Sessions
new Session();

// Initialize Database & RedBeanPHP ORM
require('vendor/gabordemooij/redbean/rb-p533.php');
R::setup('mysql:host=127.0.0.1;dbname=tools','root','root');
R::freeze(true);
define('REDBEAN_MODEL_PREFIX', '\\Models\\');

require 'app/helper.php';
include "app/routes.php";

$f3->run();
