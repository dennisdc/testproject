<?php

class EntiteitController extends Zend_Controller_Action
{

	protected $_redirector;
	protected $_flashMessenger;
	protected $user;
	
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
			$this->user = $usrmodel->getUserByIdJoined($ddcNamespace->userid);
			$this->view->headeruser = $this->user;
		}
	}
	
	protected function flash($message,$to)
	{
		$this->_flashMessenger->addMessage($message);
		$this->_redirector->gotoUrl($to);
	}
	
	public function indexAction()
	{
// 		$this->view->title = "Entiteit";
		$this->view->headTitle("Entiteit", 'PREPEND');
		
		$entmodel = new Model_DbTable_Entiteit();
		$entiteit = $entmodel->getEntiteitById($this->user['ent_id']);
		$form = new Form_Entiteit(array('ent_id' => $this->user['ent_id']));
		if(is_object($entiteit)){
			$form->populate($entiteit->toArray());
		}
		else{
			$form->populate($entiteit);
		}
		
		
		$this->view->entiteit = $entiteit;
		$this->view->form = $form;
		
		$request = clone $this->getRequest();
		$request->setActionName('leftcontent')
		->setControllerName('tools');
		$this->_helper->actionStack($request);
	}
	
	public function updateAction($flash = true){
		$this->view->title = $this->titel . " wijzigen";
		$this->view->headTitle($this->view->title, 'PREPEND');

		$id = $this->user['ent_id'];
		$this->mainform = new Form_Entiteit(array('ent_id' => $id));
		$this->mainmodel = new Model_DbTable_Entiteit();
		 
		$this->mainform->submit->setLabel('Wijzigen');
		$this->view->form = $this->mainform;
		
		if($this->getRequest()->isPost()){
			$formdata = $this->getRequest()->getPost();
			if($this->mainform->isValid($formdata)){
				$data = array();
				foreach($this->mainmodel->getCols() as $c){
					if(array_key_exists($c, $formdata)){
						$data[$c] = $this->getRequest()->getPost($c);
					}
				}
				$this->mainmodel->update($data, $this->mainmodel->get_primary() . ' = ' .(int)$id);
				if($flash){
					$this->flash('Entiteit succesvol gewijzigd!', '/entiteit');
				}
				else{
					return true;
				}
			}
			else{
				$this->mainform->populate($formdata);
			}
		}
		else{
			$uur = $this->mainmodel->getRecordById($id);
		
			$this->mainform->populate($uur);
		}
		return false;
	}
	
	public function logoAction(){
		$id = $this->user['ent_id'];
		$this->mainform = new Form_EntiteitLogo();
		$this->mainmodel = new Model_DbTable_Entiteit();
		
		if($this->getRequest()->isPost()){
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$adapter->addValidator('MimeType', false, array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'));
			$adapter->setDestination(APPLICATION_FRONT.'/upload/logo/');
				
			if($adapter->isValid() && $this->mainform->isValid($this->getRequest()->getParams())){
			
				$name = $adapter->getFileName('ent_logo', false);
			
				if (!$adapter->receive()) {
					$messages = $adapter->getMessages();
				}
			
				if(file_exists(APPLICATION_FRONT.'/upload/logo/'.$name)){
					$oldname = $name;
					$i = 0;
					do{
						$i++;
						$explodedname = explode('.', $name);
						$extension = $explodedname[count($name)-1];
						unset($explodedname[count($name)-1]);
						$newname = $extension .'_'.$i.'.'.implode('.', $explodedname);
						$test = file_exists(APPLICATION_FRONT.'/upload/logo/'.$newname);
					}while($test);
					$name = $newname;
					rename(APPLICATION_FRONT.'/upload/logo/'.$oldname, APPLICATION_FRONT.'/upload/logo/'.$name);
					//					$adapter->addFilter('Rename', array('target' => APPLICATION_FRONT.'/upload/advantages/'.$name, 'overwrite' => true),'Filedata');
				}
			
				$helper = new DeCockIct_Controller_Helper_ControllerHelper();
				$helper->makeSmallPicture($name, 'upload/logo/', 'logo', '', 111);
// 				$smalladvantage = $this->makeSmallPicture($name, 'upload/logo/', 'logo', 'logo', 111);
			
				$this->mainmodel->update(array('ent_logo' => $name), 'ent_id = ' . $id);
				
				$this->flash('logo updated', '/entiteit');
			}
		}
		
		$this->view->form = $this->mainform;
		
		$request = clone $this->getRequest();
		$request->setActionName('leftcontent')
		->setControllerName('tools');
		$this->_helper->actionStack($request);
		
	}
}