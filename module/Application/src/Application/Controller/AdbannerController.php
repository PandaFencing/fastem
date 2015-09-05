<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AdbannerController extends AbstractActionController
{
    private $redirector = null;
    private $flashMessenger = null;

    public function indexAction()
    {
        $qFilter = $this->params()->fromQuery('filter');
        if (!empty($qFilter)) {
            $sCode = array(
                'declined' => -2,
                'pending' => -1,
                'normal' => 0,
                'expired' => 1
            );
            if (in_array($qFilter, $sCode)) {
                $status = $sCode[$qFilter];
            }
        }
        $db = rpcache::get('db');
        $sql = 'SELECT a.*, b.name AS adzone_name FROM adbanner AS a LEFT JOIN adzone AS b ON a.zoneid=b.id ';
        if (isset($status)) {
            $sql .= " WHERE a.status = $status; ";
        } else {
            $sql .= ";";
        }
        $adbanner = $db->fetchAll($sql);
        $this->view->adbanner = $adbanner;
        $this->view->messages = $this->flashMessenger->getMessages();
        $this->view->qFilter = $qFilter;
        $this->view->urlArr = array('action' => 'index', 'controller' => 'adbanner', 'module' => 'default');
    }

    public function addAction()
    {
        $sql = "SELECT * FROM adzone;";
        $db = Zend_Registry::get('db');
        $adzone = $db->fetchAll($sql);
        $this->view->adzone = $adzone;
        $this->view->messages = $this->flashMessenger->getMessages();
    }

    public function saveaddAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->redirector->gotoSimple('index');
        }

        $name = $this->params()->fromQuery('name');
        if (empty($name)) {
            $this->flashMessenger->addMessage('广告名不能为空');
            $this->redirector->gotoSimple('add');
        }
        $image = $this->params()->fromQuery('image');
        if (empty($image)) {
            $this->flashMessenger->addMessage('广告图片不能为空');
            $this->redirector->gotoSimple('add');
        }
        $url = $this->params()->fromQuery('url');
        if (empty($url)) {
            $this->flashMessenger->addMessage('广告目标链接不能为空');
            $this->redirector->gotoSimple('add');
        }
        $zoneid = intval($this->params()->fromQuery('zoneid'));
        if (empty($zoneid) || !is_numeric($zoneid)) {
            $this->flashMessenger->addMessage('广告位没有选择');
            $this->redirector->gotoSimple('add');
        }
        $sql = " SELECT width, height FROM adzone WHERE id='" . intval($zoneid) . "';";
        $db = Zend_Registry::get('db');
        $arr = $db->fetchRow($sql);
        $width = $arr['width'];
        $height = $arr['height'];
        $tracid = $this->params()->fromQuery('tracid');
        $uptime = strtotime($this->params()->fromQuery('uptime'));
        $downtime = strtotime($this->params()->fromQuery('downtime'));
        $status = intval($this->params()->fromQuery('status'));
        $adtype = intval($this->params()->fromQuery('adtype'));
        $data = array(
            'name' => $name,
            'image' => $image,
            'url' => $url,
            'tracid' => $tracid,
            'zoneid' => $zoneid,
            'uptime' => $uptime,
            'downtime' => $downtime,
            'status' => $status,
            'width' => $width,
            'height' => $height,
            'adtype' => $adtype
        );
        $db = Zend_Registry::get('db');
        $db->insert('adbanner', $data);
        $this->flashMessenger->addMessage('成功增加一条广告');
        $this->redirector->gotoSimple('index');
    }

    public function editAction()
    {
        $id = intval($this->params()->fromQuery('id'));
        if (empty($id)) {
            $this->flashMessenger->addMessage('没有提供id');
            $this->redirector->gotoSimple('index');
        }
        $db = Zend_Registry::get('db');
        $adbanner = $db->fetchRow("SELECT * FROM adbanner WHERE id = '" . $id . "';");
        if (false == $adbanner) {
            $this->flashMessenger->addMessage('找不到要编辑的广告');
            $this->redirector->gotoSimple('index');
        }
        $adzone = $db->fetchAll("SELECT * FROM adzone;");

        $this->view->adbanner = $adbanner;
        $this->view->adzone = $adzone;
    }

    public function saveeditAction()
    {

        if (!$this->getRequest()->isPost()) {
            $this->redirector->gotoSimple('index');
        }

        $id = intval($this->params()->fromQuery('id'));
        if (empty($id)) {
            $this->flashMessenger->addMessage('没有提供id');
            $this->redirector->gotoSimple('index');
        }
        $name = $this->params()->fromQuery('name');
        if (empty($name)) {
            $this->flashMessenger->addMessage('广告名不能为空');
            $this->redirector->gotoSimple('index');
        }
        $image = $this->params()->fromQuery('image');
        if (empty($image)) {
            $this->flashMessenger->addMessage('广告图片不能为空');
            $this->redirector->gotoSimple('index');
        }
        $url = $this->params()->fromQuery('url');
        if (empty($url)) {
            $this->flashMessenger->addMessage('广告目标链接不能为空');
            $this->redirector->gotoSimple('index');
        }
        $zoneid = intval($this->params()->fromQuery('zoneid'));
        if (empty($zoneid) || !is_numeric($zoneid)) {
            $this->flashMessenger->addMessage('广告位没有选择');
            $this->redirector->gotoSimple('index');
        }
        $sql = " SELECT width, height FROM adzone WHERE id='" . intval($zoneid) . "';";
        $db = Zend_Registry::get('db');
        $arr = $db->fetchRow($sql);
        $width = $arr['width'];
        $height = $arr['height'];
        $tracid = $this->params()->fromQuery('tracid');
        $uptime = strtotime($this->params()->fromQuery('uptime'));
        $downtime = strtotime($this->params()->fromQuery('downtime'));
        $status = intval($this->params()->fromQuery('status'));
        $adtype = intval($this->params()->fromQuery('adtype'));
        $data = array(
            'name' => $name,
            'image' => $image,
            'url' => $url,
            'tracid' => $tracid,
            'zoneid' => $zoneid,
            'uptime' => $uptime,
            'downtime' => $downtime,
            'status' => $status,
            'width' => $width,
            'height' => $height,
            'adtype' => $adtype
        );
        $db = Zend_Registry::get('db');
        $db->update('adbanner', $data, 'id = ' . $id);
        $this->flashMessenger->addMessage('编辑广告成功');
        $this->redirector->gotoSimple('index');
    }

    public function deleteAction()
    {
        $id = intval($this->params()->fromQuery('id'));
        if ($id == 0) {
            $this->flashMessenger->addMessage('ID 非法');
            $this->redirector->gotoSimple('index');
        } else {
            $db = Zend_Registry::get('db');
            $n = $db->delete('adbanner', 'id = ' . $id);
            $this->flashMessenger->addMessage('成功删除一条广告位');
            $this->redirector->gotoSimple('index');
        }
    }
}

