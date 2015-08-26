<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AdzoneController extends AbstractActionController
{
	private $redirector = null;
	private $flashMessenger = null;
	public function init() {
		$this->redirector = $this->_helper->getHelper('Redirector');
		$this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->redirector->gotoSimple('index', 'login', 'auth');
		}
	}
	public function indexAction() {
		$db = Zend_Registry::get('db');
		$adzone = $db->fetchAll('SELECT * FROM adzone;');
		$this->view->adzone = $adzone;
		$this->view->messages = $this->flashMessenger->getMessages();
		$this->view->server_name = $_SERVER['SERVER_NAME'];
	}
	public function addAction() {
		$this->view->messages = $this->flashMessenger->getMessages();
	}
	public function saveaddAction() {
		if (!$this->getRequest()->isPost()) {
			$this->redirector->gotoSimple('index');
		}

		$name = $this->params()->fromQuery('name');
		if (empty($name)) {
			$this->flashMessenger->addMessage('广告位名不能为空');
			$this->redirector->gotoSimple('add');
		}
		$width = $this->params()->fromQuery('width');
		if (empty($width) || !is_numeric($width)) {
			$this->flashMessenger->addMessage('广告位宽度不能为空');
			$this->redirector->gotoSimple('add');
		}
		$height = $this->params()->fromQuery('height');
		if (empty($height) || !is_numeric($height)) {
			$this->flashMessenger->addMessage('广告位高度不能为空');
			$this->redirector->gotoSimple('add');
		}
		$description = $this->params()->fromQuery('description');
		$data = array(
			'name' => $name,
			'width' => $width,
			'height' => $height,
			'description' => $description
		);
		$db = Zend_Registry::get('db');
		$db->insert('adzone', $data);
		$this->flashMessenger->addMessage('成功增加一条广告位');
		$this->redirector->gotoSimple('index');
	}
	public function editAction() {
		$id = intval($this->params()->fromQuery('id'));
		if (empty($id)) {
			$this->flashMessenger->addMessage('没有指定要编辑的广告位');
			$this->redirector->gotoSimple('index');
		}
		$db = Zend_Registry::get('db');
		$adzone = $db->fetchRow('SELECT * FROM adzone WHERE id = ' . $id);
		if (false == $adzone) {
			$this->flashMessenger->addMessage('找不到要编辑的广告位');
			$this->redirector->gotoSimple('index');
		}
		$this->view->adzone = $adzone;
	}
	public function saveeditAction() {
	if (!$this->getRequest()->isPost()) {
			$this->redirector->gotoSimple('index');
		}
	    $id = $this->params()->fromQuery('id');
		if (empty($id)) {
			$this->flashMessenger->addMessage('对不起，没有指定要编辑的广告位');
			$this->redirector->gotoSimple('index');
		}

		$name = $this->params()->fromQuery('name');
		if (empty($name)) {
			$this->flashMessenger->addMessage('广告位名不能为空');
			$this->redirector->gotoSimple('add');
		}
		$width = $this->params()->fromQuery('width');
		if (empty($width) || !is_numeric($width)) {
			$this->flashMessenger->addMessage('广告位宽度不能为空');
			$this->redirector->gotoSimple('add');
		}
		$height = $this->params()->fromQuery('height');
		if (empty($height) || !is_numeric($height)) {
			$this->flashMessenger->addMessage('广告位高度不能为空');
			$this->redirector->gotoSimple('add');
		}
		$description = $this->params()->fromQuery('description');
		$data = array(
			'name' => $name,
			'width' => $width,
			'height' => $height,
			'description' => $description
		);
		$db = Zend_Registry::get('db');
		$db->update('adzone', $data, 'id = ' . $id);
		$this->flashMessenger->addMessage('成功保存编辑广告位');
		$this->redirector->gotoSimple('index');
	}
	public function deleteAction() {
		$id = intval($this->params()->fromQuery('id'));
		if ($id ==0 ) {
			$this->flashMessenger->addMessage('ID 非法');
			$this->redirector->gotoSimple('index');
		} else {
			$db = Zend_Registry::get('db');
			$n = $db->delete('adzone', 'id = ' . $id);
			$this->flashMessenger->addMessage('成功删除一条广告位');
			$this->redirector->gotoSimple('index');
		}
	}
}

