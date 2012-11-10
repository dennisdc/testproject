<?php

class KlantController extends Zend_Controller_Action
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
   		$this->view->title = "Overzicht klanten";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$klant = new Model_DbTable_Klant();
    	$result = $klant->getKlantList();
    	$countresult = $klant->getKlantCount();
    	$this->view->klant = $result;
    	$this->view->usercount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Klant toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Klant();
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
    			$naamKlant = $form->getValue('kl_naam1');
    			// customer cannot already exist in database
    			$klant = new Model_DbTable_Klant();
    			
    			$foundKlant = $klant->getKlantByName($naamKlant);
    			if ($foundKlant->kl_id != ""){
    				$messages[] = 'Klant naam is reeds in gebruik.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'kl_actief' => $this->_request->getPost('kl_actief'),
    					'kl_taal' => $this->_request->getPost('kl_taal'),
    					'kl_naam1' => $this->_request->getPost('kl_naam1'),
    					'kl_naam2' => $this->_request->getPost('kl_naam2'),
    					'kl_jurvorm' => $this->_request->getPost('kl_jurvorm'),
    					'kl_adres1' => $this->_request->getPost('kl_adres1'),
    					'kl_adres2' => $this->_request->getPost('kl_adres2'),
    					'kl_post' => $this->_request->getPost('kl_post'),
    					'kl_woon' => $this->_request->getPost('kl_woon'),
    					'kl_land' => $this->_request->getPost('kl_land'),
    					'kl_btw' => $this->_request->getPost('kl_btw'),
    					'kl_tel1' => $this->_request->getPost('kl_tel1'),
    					'kl_tel2' => $this->_request->getPost('kl_tel2'),
    					'kl_gsm' => $this->_request->getPost('kl_gsm'),
    					'kl_fax1' => $this->_request->getPost('kl_fax1'),
    					'kl_fax2' => $this->_request->getPost('kl_fax2'),
    					'kl_email' => $this->_request->getPost('kl_email'),
    					'kl_website' => $this->_request->getPost('kl_website'),
    					'kl_uurtarief' => $this->_request->getPost('kl_uurtarief'),
    					'kl_aantkm' => $this->_request->getPost('kl_aantkm'),
    					'kl_betterm' => $this->_request->getPost('kl_betterm')
    				);
    				// commit to db
    				$klant = new Model_DbTable_Klant();
    				$klant->insert($data);
    				$this->flash("Klant bewaard.", '/klant');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Klant aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Klant();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('kl_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$kl_actief = $form->getValue('kl_actief');
    			$kl_taal = $form->getValue('kl_taal');
    			$kl_naam1 = $form->getValue('kl_naam1');
    			$kl_naam2 = $form->getValue('kl_naam2');
    			$kl_jurvorm = $form->getValue('kl_jurvorm');
    			$kl_adres1 = $form->getValue('kl_adres1');
    			$kl_adres2 = $form->getValue('kl_adres2');
    			$kl_post = $form->getValue('kl_post');
    			$kl_woon = $form->getValue('kl_woon');
    			$kl_land = $form->getValue('kl_land');
    			$kl_btw = $form->getValue('kl_btw');
    			$kl_tel1 = $form->getValue('kl_tel1');
    			$kl_tel2 = $form->getValue('kl_tel2');
    			$kl_gsm = $form->getValue('kl_gsm');
    			$kl_fax1 = $form->getValue('kl_fax1');
    			$kl_fax2 = $form->getValue('kl_fax2');
    			$kl_email = $form->getValue('kl_email');
    			$kl_website = $form->getValue('kl_website');
    			$kl_uurtarief = $form->getValue('kl_uurtarief');
    			$kl_aantkm = $form->getValue('kl_aantkm');
    			$kl_betterm = $form->getValue('kl_betterm');

    			$uklant = new Model_DbTable_Klant();
    			$uklant->updateKlantById($id, $kl_actief, $kl_taal, $kl_naam1, $kl_naam2, $kl_jurvorm,
    									$kl_adres1, $kl_adres2, $kl_post, $kl_woon, $kl_land, $kl_btw,
    									$kl_tel1, $kl_tel2, $kl_gsm, $kl_fax1, $kl_fax2, $kl_email, $kl_website,
    									$kl_uurtarief, $kl_aantkm, $kl_betterm);
    			$this->flash("Klant aangepast.", '/klant/fiche/kl_id/'.$id);
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('kl_id', 0);
    		if ($id > 0){
    			$uklant = new Model_DbTable_Klant();
    			$form->populate($uklant->getKlantById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('kl_id');
    	$user = new Model_DbTable_Klant();
    	$user->deleteKlantbyID($id);
    	$this->flash("Klant verwijderd.", '/klant');
    }
    
    public function ficheAction()
    {
    	$this->view->title = "Klantgegevens";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$id = $this->_getParam('kl_id', 0);
    	if ($id > 0){
    		$klant = new Model_DbTable_Klant();
    		$foundKlant = $klant->getKlantById($id);
    		$this->view->klant = $foundKlant;
    		
    		$this->view->title = "Klant gegevens van: " . $foundKlant[kl_naam1] . " " . $foundKlant[kl_naam2];
			
    		$request = clone $this->getRequest();
    		$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    		$this->_helper->actionStack($request);
    		
    	} else {
    		$this->flash("Klant niet gevonden!", '/klant/klantlist');
    	}
    }
}