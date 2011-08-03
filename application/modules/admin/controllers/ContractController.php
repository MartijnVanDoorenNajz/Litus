<?php

namespace Admin;

use \Admin\Form\Contract\Index;
use \Admin\Form\Contract\View;

use \Zend\Registry;

class ContractController extends \Litus\Controller\Action
{

    public function init()
    {
        parent::init();
    }

    private $_id;

    private $_type;

    private function _init()
    {
        $this->_id = '0';
        if($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if(isset($postData['id']))
                $this->_id = $postData['id'];

            if(isset($postData['type']))
                $this->_type = $postData['type'];
        } else {
            $this->_id = $this->getRequest()->getParam('id','0');
            $this->_type = $this->getRequest()->getParam('type','contract');
        }

        if($this->_id == '0')
            throw new \InvalidArgumentException("Need to give a pdf id to download.");

        $this->view->pdfId = $this->_id;
    }

    private function _filterArray($input)
    {
        $result = array();
        foreach ($input as $i)
            if(($i != '.') && ($i != '..'))
                $result[] = $i;
        return $result;
    }

    private function _getRootDirectory()
    {
        return Registry::get('litus.resourcesDirectory') . '/pdf/br';
    }

    public function indexAction()
    {
        $ids = scandir($this->_getRootDirectory());
        if(!$ids)
            throw new \RuntimeExceptin();

        $this->view->form = new Index($this->_filterArray($ids));
    }

    public function viewAction()
    {
        $this->_init();

        $types = scandir($this->_getRootDirectory() . '/' . $this->_id);
        if(!$types) {
            throw new \RuntimeException("An unexpected error occurred.");
        } else {
            $this->view->form = new View($this->_id, $this->_filterArray($types));
        }
    }

    public function downloadAction()
    {
        $this->_init();

        $this->view->body = file_get_contents($this->_getRootDirectory() . '/' . $this->_id . '/' . $this->_type);

        $this->view->filename = $this->_type;
    }
}
