<?php
define('BASE_PATH', realpath(dirname(__DIR__)));
chdir(dirname(__DIR__));
require 'init_autoloader.php';
Zend\Mvc\Application::init(require 'config/application.config.php')->run();




