<?php

class VfController extends Zend_Controller_Action
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
		else{
			$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
			$username = $ddcNamespace->username;
			$usrmodel = new Model_DbTable_User();
			$user = $usrmodel->getUserByIdJoined($ddcNamespace->userid);
			$this->view->headeruser = $user;
		}  
	}

	protected function flash($message,$to)
	{
		$this->_flashMessenger->addMessage($message);
		$this->_redirector->gotoUrl($to);
	}

    public function indexAction()
    {
       	$this->view->title = "Verkoop";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
    public function offerteAction(){
    	   	
    	$this->view->title = "Offerte";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$this->view->doc = "Offerte";
    	
    	
    	$kl_id = $this->_getParam('kl_id', 0);
    	$art_id = $this->getRequest()->getParam('art_id');
    	$art_aantal = $this->getRequest()->getParam('art_aantal');

    	$offNamespace = new Zend_Session_Namespace('offerte');
    	
    	if ($kl_id > 0){
    		$offNamespace->klant = $kl_id;
    		$klant = new Model_DbTable_Klant();
    		$result = $klant->getKlantById($kl_id);
    		$this->view->klant = $result;
    	}
    	if($art_id > 0){
    		$offNamespace->artikels[$art_id] += $art_aantal;
    	}
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    	// add offerte hier
    }
    
    public function zoekklantAction(){
    	$this->view->title = "Zoeken klant";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	// bepaal document
    	$doc = $this->_getParam('doc', 0);
    	
    	$klant = new Model_DbTable_Klant();
    	$result = $klant->getKlantList();
    	$countresult = $klant->getKlantCount();
    	$this->view->klant = $result;
    	$this->view->usercount = $countresult;
    	$this->view->doc = $doc;

    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
}

