<?php

class ToolsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect('/user/login');
		}
    }

    public function indexAction()
    {
        $this->view->title = "Administration";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    }
    
    public function leftcontentAction()
    {
    	// generate leftmenu action admin
    }
}

