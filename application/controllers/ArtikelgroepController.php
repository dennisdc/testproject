<?php

class ArtikelgroepController extends Zend_Controller_Action
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
       	$this->view->title = "Overzicht artikelgroepen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$artgroep = new Model_DbTable_Artikelgroep();
    	$result = $artgroep->getArtikelgroepList();
    	$countresult = $artgroep->getArtikelgroepCount();
    	$this->view->artikelgroep = $result;
    	$this->view->artikelgroepcount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Artikelgroep toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Artikelgroep();
    	$form->submit->setLabel('Add');
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
    			$naam = $form->getValue('artgroep_oms');
    			// artgroep cannot already exist in database
    			$artgroep = new Model_DbTable_Artikelgroep();
    			
    			$foundgroep = $artgroep->getArtikelgroepByName($naam);
    			if ($foundgroep->artgroep_id != ""){
    				$messages[] = 'Naam artikelgroep reeds in gebruik.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'artgroep_oms' => $this->_request->getPost('artgroep_oms')
    				);
    				// commit to db
    				$groep = new Model_DbTable_Artikelgroep();
    				$groep->insert($data);
    				$this->flash("Artikelgroep bewaard.", '/artikelgroep');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Artikelgroep aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Artikelgroep();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('artgroep_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$naam = $form->getValue('artgroep_oms');

    			$ugroep = new Model_DbTable_Artikelgroep();
    			$ugroep->updateArtikelgroepById($id, $naam);
    			$this->flash("Artikelgroep aangepast.", '/artikelgroep');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('artgroep_id', 0);
    		if ($id > 0){
    			$ugroep = new Model_DbTable_Artikelgroep();
    			$form->populate($ugroep->getArtikelgroepById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('artgroep_id');
    	$groep = new Model_DbTable_Artikelgroep();
    	$groep->deleteArtikelgroepbyID($id);
    	$this->flash("Artikelgroep verwijderd.", '/artikelgroep');
    }
}