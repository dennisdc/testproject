<?php

class DocnummerController extends Zend_Controller_Action
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
       	$this->view->title = "Overzicht Documentnummers";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$doc = new Model_DbTable_Docnummer();
    	$result = $doc->getDocnummerList();
    	$countresult = $doc->getDocnummerCount();
    	$this->view->docnummer = $result;
    	$this->view->docnummercount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Documentnummer toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Docnummer();
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
    			$naam = $form->getValue('doc_oms');
    			// customer cannot already exist in database
    			$doc = new Model_DbTable_Docnummer();
    			
    			$founddoc = $doc->getDocnummerByName($naam);
    			if ($founddoc->doc_id != ""){
    				$messages[] = 'Documentnaam reeds in gebruik.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'doc_oms' => $this->_request->getPost('doc_oms'),
    					'doc_nr' => $this->_request->getPost('doc_nr')
    				);
    				// commit to db
    				$doc = new Model_DbTable_Docnummer();
    				$doc->insert($data);
    				$this->flash("Documentnummer bewaard.", '/docnummer');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Documentnummer aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Docnummer();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('doc_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$doc_oms = $form->getValue('doc_oms');
    			$doc_nr = $form->getValue('doc_nr');

    			$udoc = new Model_DbTable_Docnummer();
    			$udoc->updateDocnummerById($id, $doc_oms, $doc_nr);
    			$this->flash("Documentnummer aangepast.", '/docnummer');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('doc_id', 0);
    		if ($id > 0){
    			$udoc = new Model_DbTable_Docnummer();
    			$form->populate($udoc->getDocnummerById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('doc_id');
    	$doc = new Model_DbTable_Docnummer();
    	$doc->deleteDocnummerbyID($id);
    	$this->flash("Documentnummer verwijderd.", '/docnummer');
    }
}