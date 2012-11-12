<?php

class ToolsController extends Zend_Controller_Action
{
	protected $user;

    public function init()
    {
        /* Initialize action controller here */
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect('/user/login');
		}
		else{
			$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
			$username = $ddcNamespace->username;
			$usrmodel = new Model_DbTable_User();
			$this->user = $usrmodel->getUserByIdJoined($ddcNamespace->userid);
			$this->view->headeruser = $this->user;
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
    	$this->view->user = $this->user;
    }
}

