<?php

class LeveranController extends Zend_Controller_Action
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
       	$this->view->title = "Overzicht leveranciers";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$leveran = new Model_DbTable_Leveran();
    	$result = $leveran->getLeveranList();
    	$countresult = $leveran->getLeveranCount();
    	$this->view->leveran = $result;
    	$this->view->leverancount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Leverancier toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Leveran();
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
    			$naamLeveran = $form->getValue('lev_naam');
    			// customer cannot already exist in database
    			$klant = new Model_DbTable_Leveran();
    			
    			$foundLeveran = $klant->getLeveranByName($naamLeveran);
    			if ($foundLeveran->lev_id != ""){
    				$messages[] = 'Naam leverancier reeds in gebruik.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'lev_naam' => $this->_request->getPost('lev_naam')
    				);
    				// commit to db
    				$klant = new Model_DbTable_Leveran();
    				$klant->insert($data);
    				$this->flash("Leverancier bewaard.", '/leveran');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Leverancier aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Leveran();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('lev_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$lev_naam = $form->getValue('lev_naam');

    			$uleveran = new Model_DbTable_Leveran();
    			$uleveran->updateLeveranById($id, $lev_naam);
    			$this->flash("Leverancier aangepast.", '/leveran');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('lev_id', 0);
    		if ($id > 0){
    			$uleveran = new Model_DbTable_Leveran();
    			$form->populate($uleveran->getLeveranById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('lev_id');
    	$leveran = new Model_DbTable_Leveran();
    	$leveran->deleteLeveranbyID($id);
    	$this->flash("Leverancier verwijderd.", '/leveran');
    }
}

