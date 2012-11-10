<?php

class UserController extends Zend_Controller_Action
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
        if ($this->config->database->profiler->enable){
            $profiler = new Zend_Db_Profiler_Firebug('All Queries');
            $profiler->setEnabled(true);
            $this->db->setProfiler($profiler);
        }
        Zend_Registry::set('db', $this->db);
	}

	protected function flash($message,$to)
	{
		$this->_flashMessenger->addMessage($message);
		$this->_redirector->gotoUrl($to);
	}

    public function indexAction()
    {
        $this->view->title = "Gebruikersbeheer";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    }
    
    public function registerAction()
    {
    	$this->view->title = "Nieuwe gebruiker registreren";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_User();
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
    			$username = $form->getValue('u_naam');
    			$email = $form->getValue('u_email');
    			// E-mail cannot already exist in database
    			$user = new Model_DbTable_User();
    			
    			$foundUser = $user->getUserByEmail($email);
    			if ($foundUser->id != ""){
    				$messages[] = 'E-mail is reeds in gebruik.';
    				$iserror = 1;
    			}
 
    			// username cannot already exist in database
    			$foundUser = $user->getUserByUsername($username);
    			if ($foundUser->id != ""){
    				$messages[] = 'Username is already in use.';
    				$iserror = 1;
    			}
    			
    			// in case of error, repopulate the data in the form
    			if($iserror > 0){
 					$form->populate($formdata);
 					$this->view->errormessages = $messages;
    			} else{
    				// no errors found, prepare data for insertion to db
    				$data = array(
    					'u_naam' => $this->_request->getPost('u_naam'),
    					'u_email' => $this->_request->getPost('u_email'),
    					'u_paswoord' => md5($this->_request->getPost('u_paswoord')));
    				// commit to db
    				$user = new Model_DbTable_User();
    				$user->insert($data);
    				$this->flash("Gebruiker bewaard.", '/user');
    			}
    		}
    	}
    	if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
    public function loginAction()
	{
		$this->view->title = "Inloggen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_Login();
    	$form->submit->setLabel('Login');
    	$this->view->form = $form;
		$messages = array();
    	
    	// form is processed
    	if ($this->getRequest()->isPost()){
    	    $formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
				// Retrieve the provided email address and password
				$email = $this->_request->getPost('u_email');
				$password = $this->_request->getPost('u_paswoord');		
		
				// Identify the authentication adapter
				$authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
		
				// Identify the table where the user data is stored
				$authAdapter->setTableName('users');
		
				// Identify the column used to store the "username"
				$authAdapter->setIdentityColumn('u_email');
		
				// Identify the column used to store the password
				$authAdapter->setCredentialColumn('u_paswoord');
		
				// How is the password stored?
				$authAdapter->setCredentialTreatment('MD5(?)');
		
				// Pass the provided information to the adapter
				$authAdapter->setIdentity($email);
				$authAdapter->setCredential($password);
		
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate($authAdapter);
		
				// Did the participant successfully login?
				if ($result->isValid()) {
					// store data in session telecomNameSpace
					$user = new Model_DbTable_User();
					$result = $user->getUserByEmail($email);
					$telecomNamespace = new Zend_Session_Namespace('Zend_Auth');
					$telecomNamespace->username = $result->u_naam ;
					$telecomNamespace->userid = $result->u_id;					
					$this->_redirect('/index/dashboard');
				} else {
					$this->flash("Login niet correct, probeer nogmaals aub.", '/user/login');
				}
    		}
    	}
	    if ($this->_flashMessenger->hasMessages()){
    		$this->view->messages = implode("<br/>", $this->_flashMessenger->getMessages());
    	}
    }
    
   public function logoutAction()
    {
       Zend_Auth::getInstance()->clearIdentity();
       $this->flash("Uitgelogd.", '/user/login');
    }
    
    public function updateAction()
    {
		
    	$this->view->title = "Gebruiker aanpassen";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	$form = new Form_User();
    	$form->submit->setLabel('Update');
    	$this->view->form = $form;
		$iserror = 0;
		$messages = array();
		
		$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	                ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
		
        if ($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if ($form->isValid($formdata)){
    			$id = $this->_getParam('u_id', 0);
    			$username = $form->getValue('u_naam');
    			$email = $form->getValue('u_email');
    			$pass = $form->getValue('u_paswoord');
    			$user = new Model_DbTable_User();
    			$user->updateUserById($id, $username, $email, $pass);
    			$this->flash("Gebruiker aangepast.", '/user/userlist');
    			
    		} else {
    			$form->populate($formdata);
    		}
    	} else {
    		$id = $this->_getParam('u_id', 0);
    		if ($id > 0){
    			$user = new Model_DbTable_User();
    			$form->populate($user->getUserById($id));
    		}
    	}   	
    }
    
    public function userlistAction()
    {
    	$this->view->title = "Overzicht gebruikers";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$user = new Model_DbTable_User();
    	$this->view->user = $user->getUserList();
    	$request = clone $this->getRequest();
    	$request->setActionName('leftcontent')
    	        ->setControllerName('tools');        
    	$this->_helper->actionStack($request);
    }
    
    public function deleteAction(){
    	$id = $this->_request->getParam('u_id');
    	$user = new Model_DbTable_User();
    	$user->deleteUserByID($id);
    	$this->flash("Gebruiker verwijderd.", '/user/userlist');
    }
}