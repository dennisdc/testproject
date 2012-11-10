<?php

class AfController extends Zend_Controller_Action
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
       	$this->view->title = "Aankoop";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }

    public function addAction()
    {
        $this->view->title = "Aankoopfactuur toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Af();
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
    			$ref = $form->getValue('af_ref');
    			// Ref cannot already exist in database
    			$af = new Model_DbTable_Af();   			
    			$foundAf = $af->getAfByRef($ref);
    			if ($foundAf->af_id != ""){
    				$messages[] = 'Referentie reeds in gebruik, factuur reeds aanwezig.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'af_dat' => $this->_request->getPost('af_dat'),
    					'af_vervaldat' => $this->_request->getPost('af_vervaldat'),
    					'lev_id' => $this->_request->getPost('lev_id'),
    					'afrek_id' => $this->_request->getPost('afrek_id'),
    					'af_ref' => $this->_request->getPost('af_ref'),
    					'af_oms' => $this->_request->getPost('af_oms'),
    					'af_bedrag' => $this->_request->getPost('af_bedrag'),
    				);
    				// commit to db
    				$af = new Model_DbTable_Af();
    				$af->addAf($data);
    				$this->flash("Aankoopfactuur bewaard.", '/af/listunpaid');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function updateAction()
    {
    	$this->view->title = "Aankoopfactuur aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Af();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		$id = $this->_getParam('af_id', 0);
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$af_dat = $form->getValue('af_dat');
    			$af_vervaldat = $form->getValue('af_vervaldat');
    			$lev_id = $form->getValue('lev_id');
    			$afrek_id = $form->getValue('afrek_id');
    			$af_ref = $form->getValue('af_ref');
    			$af_oms = $form->getValue('af_oms');
    			$af_bedrag = $form->getValue('af_bedrag');

    			$uaf = new Model_DbTable_Af();
    			$uaf->updateAfById($id, $af_dat, $af_vervaldat, $lev_id, $afrek_id, $af_ref, $af_oms, $af_bedrag);
    			$this->flash("Aankoopfactuur aangepast.", '/af/listunpaid');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('af_id', 0);
    		if ($id > 0){
    			$uaf = new Model_DbTable_Af();
    			$form->populate($uaf->getAfById($id));
    		}
    	}   	
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('af_id');
    	$af = new Model_DbTable_Af();
    	$af->deleteAfbyID($id);
    	$this->flash("Aankoopfactuur verwijderd.", '/af/listunpaid');
    }
    
   public function listallAction(){
      	$this->view->title = "Overzicht aankoopfacturen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$af = new Model_DbTable_Af();
    	$result = $af->getAfList();
    	$countresult = $af->getAfCount();
    	$this->view->af = $result;
    	$this->view->afcount = $countresult;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
    public function listunpaidAction(){
    	
    	$this->view->title = "Onbetaalde aankoopfacturen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$af = new Model_DbTable_Af();
    	$unpaid = $af->getAfUnpaid();
    	$this->view->unpaid = $unpaid;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	        ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
    public function printunpaidAction(){
    	
    	$this->view->title = "Onbetaalde aankoopfacturen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$af = new Model_DbTable_Af();
    	$unpaid = $af->getAfUnpaid();
    	$this->view->unpaid = $unpaid;
    	
    	 
// Create new PDF 
$pdf = new Zend_Pdf();

// Add new page to the document 
$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4); 
$pdf->pages[] = $page; 

// Set font 
$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12); 

// Draw text 
$page->drawText('Hello world frans!', 50, 730);     	

// Load image 
$image = Zend_Pdf_Image::imageWithPath(APPLICATION_FRONT . '/images/decockbvba_logo.tif'); 

// Draw image 
$page->drawImage($image, 40, 764, 240, 820); 
$page->drawLine(50, 755, 545, 755); 

$pdf->save('invoice.pdf'); 
    	
    }
    
    public function listunpaidoverdueAction(){
    	
    	$this->view->title = "Onbetaalde en vervallen aankoopfacturen vandaag";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$af = new Model_DbTable_Af();
    	$unpaid = $af->getAfUnpaidOverdue();
    	$this->view->unpaid = $unpaid;
    	
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	        ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
    public function payunpaidAction(){
    	$invoices = $this->_request->getPost('toadd');
    	$af_betaald = 1;
    	foreach ($invoices as $af){
     				$af_id = $af;
    				$paf = new Model_DbTable_Af();
    				$paid = $paf->updateAfPaid($af_id, $af_betaald);
    	}
    	$this->flash("Wijzigingen bewaard.", '/af/listunpaid');
    }
}