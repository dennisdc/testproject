<?php

class ArtikelController extends Zend_Controller_Action
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
       	$this->view->title = "Overzicht artikels";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$artikel = new Model_DbTable_Artikel();
    	$result = $artikel->getArtikelList();
    	$countresult = $artikel->getArtikelCount();
    	$this->view->artikel = $result;
    	$this->view->artikelcount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Artikel toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Artikel();
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
    			$naam = $form->getValue('art_oms');
    			// artikel cannot already exist in database
    			$artikel = new Model_DbTable_Artikel();
    			
    			$foundartikel = $artikel->getArtikelByName($naam);
    			if ($foundartikel->art_id != ""){
    				$messages[] = 'Naam artikel reeds in gebruik.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'art_actief' => $this->_request->getPost('art_actief'),
    					'artgroep_id' => $this->_request->getPost('artgroep_id'),
    					'btw_id' => $this->_request->getPost('btw_id'),
    					'art_oms' => $this->_request->getPost('art_oms'),
    					'art_akp' => $this->_request->getPost('art_akp'),
    					'art_vkp' => $this->_request->getPost('art_vkp'),
    					'lev_id' => $this->_request->getPost('lev_id'),
    					'art_reflev' => $this->_request->getPost('art_reflev')
    				);
    				// commit to db
    				$art = new Model_DbTable_Artikel();
    				$art->insert($data);
    				$this->flash("Artikel bewaard.", '/artikel');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Artikel aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Artikel();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('art_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$art_actief = $form->getValue('art_actief');
    			$artgroep_id = $form->getValue('artgroep_id');
    			$art_oms = $form->getValue('art_oms');
    			$art_akp = $form->getValue('art_akp');
    			$art_vkp = $form->getValue('art_vkp');
    			$lev_id = $form->getValue('lev_id');
    			$art_reflev = $form->getValue('art_reflev');

    			$uartikel = new Model_DbTable_Artikel();
    			$uartikel->updateArtikelById($id, $art_actief, $artgroep_id, $art_oms, $art_akp, $art_vkp,
    										$lev_id, $art_reflev);
    			$this->flash("Artikel aangepast.", '/artikel');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('art_id', 0);
    		if ($id > 0){
    			$uartikel = new Model_DbTable_Artikel();
    			$form->populate($uartikel->getArtikelById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('art_id');
    	$artikel = new Model_DbTable_Artikel();
    	$artikel->deleteArtikelbyID($id);
    	$this->flash("Artikel verwijderd.", '/artikel');
    }
}