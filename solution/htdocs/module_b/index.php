<?php

session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL',  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

require BASE_PATH . '/core/Autoloader.php';

$loader = new Core\Autoloader();
$loader->addNamespace('Core', BASE_PATH . '/core');
$loader->addNamespace('App',  BASE_PATH . '/app');
$loader->register();

$config = require BASE_PATH . '/config/app.php';

Core\View::setLayout($config['layout'] ?? null);

$app = new Core\Application($config);

require BASE_PATH . '/routes/web.php';

$app->run();
