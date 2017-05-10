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

$params=parse_ini_file('./tools_conf.ini');

// Initialize Database & RedBeanPHP ORM
require('vendor/gabordemooij/redbean/rb-p533.php');
R::setup('mysql:host=localhost;dbname='.$params['db_name'],$params['db_user'],$params['db_pwd']);
R::freeze(true);
define('REDBEAN_MODEL_PREFIX', '\\Models\\');
$schema = R::getDuplicationManager()->getSchema();
R::getDuplicationManager()->setTables($schema);

require 'app/helper.php';
include "app/routes.php";
ini_set('memory_limit', '512M');

$f3->run();
