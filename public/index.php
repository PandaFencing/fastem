<?php
chdir(dirname(__DIR__));
define('BASE_PATH', getcwd());
include 'vendor/autoload.php';
Zend\Mvc\Application::init(require 'config/application.config.php')->run();




