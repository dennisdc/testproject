<?php

class OfferteController extends Zend_Controller_Action
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
	
	public function init()
	{
		$this->_flashMessenger 	= $this->_helper->getHelper('FlashMessenger');
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$this->config = Zend_Registry::get('config');
		require_once('Zend/Db.php');
        $this->db = Zend_Db::factory($this->config->resources->db);
        require_once('Zend/Db/Table/Abstract.php');
        Zend_Db_Table_Abstract::setDefaultAdapter($this->db);
        Zend_Registry::set('db', $this->db);
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect('/user/login');
		}  
	}

	protected function flash($message,$to)
	{
		$this->_flashMessenger->addMessage($message);
		$this->_redirector->gotoUrl($to);
	}

    public function indexAction()
    {
       	$this->view->title = "Offerte";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
    public function listunapprovedAction(){
    	   	
    	$this->view->title = "Offertes";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$off = new Model_DbTable_Offerte();
    	$unapproved = $off->getOfferteUnapproved();
    	$this->view->unapproved = $unapproved;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	        ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }    
}