<?php

class AfrekController extends Zend_Controller_Action
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
       	$this->view->title = "Overzicht aankooprekeningen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$afrek = new Model_DbTable_Afrek();
    	$result = $afrek->getAfrekList();
    	$countresult = $afrek->getAfrekCount();
    	$this->view->afrek = $result;
    	$this->view->afrekcount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Aankooprekening toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Afrek();
    	$form->submit->setLabel('OK');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
    	// form is processed
    	if ($this->getRequest()->isPost()){
    	    $formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$naam = $form->getValue('afrek_oms');
    			// afrek cannot already exist in database
    			$afrek = new Model_DbTable_Afrek();
    			
    			$foundafrek = $afrek->getAfrekByName($naam);
    			if ($foundafrek->afrek_id != ""){
    				$messages[] = 'Aankooprekening reeds in gebruik.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'afrek_oms' => $this->_request->getPost('afrek_oms')
    				);
    				// commit to db
    				$groep = new Model_DbTable_Afrek();
    				$groep->insert($data);
    				$this->flash("Aankooprekening bewaard.", '/afrek');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Aankooprekening aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Afrek();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('afrek_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$naam = $form->getValue('afrek_oms');

    			$uafrek = new Model_DbTable_Afrek();
    			$uafrek->updateAfrekById($id, $naam);
    			$this->flash("Aankooprekening aangepast.", '/afrek');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('afrek_id', 0);
    		if ($id > 0){
    			$uafrek = new Model_DbTable_Afrek();
    			$form->populate($uafrek->getAfrekById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('afrek_id');
    	$afrek = new Model_DbTable_Afrek();
    	$afrek->deleteAfrekbyID($id);
    	$this->flash("Aankooprekening verwijderd.", '/afrek');
    }
}

