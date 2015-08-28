<?php

namespace Application;


use Zend\Db\Adapter\Adapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;


class Module
{
	public function onBootstrap (MvcEvent $e)
	{
		$m = $e->getApplication()->getEventManager();
		$l = new ModuleRouteListener();
		$l->attach($m);
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	public function getServiceConfig()
	{
		return [
			'factories' => [
				'db' => function ($sm) {
					$config = $sm->get('config');
					return new Adapter($config['db']);
				}
			]
		];
	}

	public function getAutoloaderConfig()
	{
		return [
			'Zend\Loader\StandardAutoloader' => [
				'namespaces' => [
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
				]
			]
		];
	}

}
