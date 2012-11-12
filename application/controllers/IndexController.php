<?php

class IndexController extends Zend_Controller_Action
{

	protected $_redirector;
	protected $_flashMessenger;

	protected function setMessages()
	{
		$this->view->messages = join("",$this->_flashMessenger->getMessages());
	}
	
    public function postDispatch()
	{
		$this->setMessages();
		parent::postDispatch();
	}
	
	protected function flash($message,$to)
	{
		$this->_flashMessenger->addMessage($message);
		$this->_redirector->gotoUrl($to);
	}
	
    public function init()
    {
		$this->_flashMessenger 	= $this->_helper->getHelper('FlashMessenger');
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$telecomNamespace = new Zend_Session_Namespace('Zend_Auth');
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect('/user/login');
		}
		else{
			$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
			$username = $ddcNamespace->username;
			$usrmodel = new Model_DbTable_User();
			$user = $usrmodel->getUserByIdJoined($ddcNamespace->userid);
			$this->view->headeruser = $user;
		}  
    }

    public function indexAction()
    {
        $this->_redirect('/user/login');
    }
    
    public function dashboardAction()
    {
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect('/user/login');
		}
		$this->view->datum = date("d-m-Y G:i");
    	$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
    	$this->view->username = $ddcNamespace->username;
    	$this->view->userid = $ddcNamespace->userid;
    	$this->view->title = "Startscherm";
    	$this->view->headTitle($this->view->title, 'PREPEND');
  		// onbetaalde aankoopfacturen
  		$af = new Model_DbTable_Af();
  		$onbetaaldaf = $af->getAfUnpaidCount();
  		$onbetaaldlateaf = $af->getAfUnpaidLateCount();
  		$this->view->onbetaaldaf = $onbetaaldaf;
  		$this->view->onbetaaldlateaf = $onbetaaldlateaf;
  		
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('index');        
    	$this->_helper->actionStack($request);
    	
    }
    
    public function leftcontentAction(){
    	
    }
}