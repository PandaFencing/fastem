<?php

namespace Application;



class Module
{
	public function onBootstrap (MvcEvent $e)
	{
		$m = $e->getApplication()->getEventManager();
		$l = new ModuleRouteListener();
		$l->attach($m);
	}
	public function _initConfig()
	{
		Zend_Registry::set('config', new Zend_Config($this->getOptions()));
	}

	protected function _initDatabases()
	{
		$this->bootstrap('db');
		$db = $this->getResource('db');
		Zend_Registry::set('db', $db);
	}
	protected function _initLayout()
	{
		Zend_Layout::startMvc();
	}
}
