<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
	public function init() {
		$this->_helper->layout->disableLayout();
	}

	public function indexAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$cookie = new Zend_Http_Cookie('fastem_inadmin', "", $_SERVER['SERVER_NAME'], time()-72000, '/');
		$this->_helper->redirector->gotoSimple('index','index','index');
	}

}
