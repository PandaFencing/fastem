<?php
namespace Demo\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
	public function init() {
		$this->_helper->layout->disableLayout();
	}

	public function indexAction()
	{
		$db = Zend_Registry::get('db');
		$adz = $db->fetchAll("SELECT * FROM adzone;");
		$this->view->adzone = $adz;
	}
}


