<?php
abstract class DeCockIct_MainController extends Zend_Controller_Action
{
	
	protected $_redirector;
	protected $_flashMessenger;
	
	protected $user;
	
	protected $mainmodel;
	protected $mainform; 
	
	protected $titel;
	protected $redirectlink;
	protected $admin;
	
	protected $exportfields;
	protected $filterfields;
	protected $titles;
	protected $function = 'getAllRecords';
	protected $params = array();
	protected $folder = 'upload';
    protected $letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    protected $numbertoletters2 = array();

	protected function setMessages()
	{
		$this->view->messages = join("",$this->_flashMessenger->getMessages());
	}
	
    public function postDispatch()
	{
		$this->setMessages();
		parent::postDispatch();
	}
	
	public function init() {
		$this->config = Zend_Registry::get('config');
		require_once('Zend/Db.php');
        $this->db = Zend_Db::factory($this->config->resources->db);
        require_once('Zend/Db/Table/Abstract.php');
        Zend_Db_Table_Abstract::setDefaultAdapter($this->db);
//        if ($this->config->database->profiler->enable){
//            $profiler = new Zend_Db_Profiler_Firebug('All Queries');
//            $profiler->setEnabled(true);
//            $this->db->setProfiler($profiler);
//        }
        Zend_Registry::set('db', $this->db);
	}

	public function preDispatch() {
		$this->_flashMessenger 	= $this->_helper->getHelper('FlashMessenger');
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		if (!Zend_Auth::getInstance()->hasIdentity()) {
// 			$this->_redirect('/user/login');
		}  
		
		//MAKE NAMESPACE
    	$telecomNamespace = new Zend_Session_Namespace('Zend_Auth');
    	
    	//GET USER FROM SESSION
    	$userid = $telecomNamespace->userid;
    	if(isset($userid) && $userid > 0){
			$usermodel = new Model_DbTable_User();
			$this->user = $usermodel->getUserById($telecomNamespace->userid);
			$this->view->user = $this->user;
    	}
		
    	$this->view->filterfields = $this->filterfields;
		
		if(isset($this->mainmodel) && (!$this->exportfields || count($this->exportfields) == 0)){
			$this->exportfields = $this->mainmodel->getCols();
			$this->titles = array();
			foreach($this->exportfields as $e){
				$this->titles[$e] = $e;
			}
		}
		
		$this->_helper->layout->setLayout('layout');
	}

	protected function flash($message,$to)
	{
		$this->_flashMessenger->addMessage($message);
		$this->_redirector->gotoUrl($to);
	}
	
	public function addAction($flash = true){
        $this->view->title = $this->titel . " toevoegen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$this->mainform->submit->setLabel('Opslaan');
    	$this->view->form = $this->mainform;
    	
    	if($this->getRequest()->isPost()){
    		$formdata = $this->getRequest()->getPost();
    		if($this->mainform->isValid($formdata)){
    			$data = array();
    			foreach($this->mainmodel->getCols() as $c){
    				if(array_key_exists($c, $formdata)){
    					$data[$c] = $this->getRequest()->getPost($c);
    				}
    				elseif($c != $this->mainmodel->get_primary()){
    					if(array_key_exists($c, $this->getRequest()->getParams())){
    						$data[$c] = $this->getRequest()->getParam($c);
    					}
    				}
    			}
//    			print_r($data);
    			$this->mainmodel->insert($data);
    			if($flash){
    				$this->flash($this->titel . ' succesvol toegevoegd', $this->redirectlink);
    			}
    			else{
    				return true;
    			}
    		}
    		else{
    			$this->mainform->populate($formdata);
    		}
    	}
    	return false;
	}

	public function updateAction($flash = true){
		$this->view->title = $this->titel . " wijzigen";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$this->mainform->submit->setLabel('Wijzigen');
    	$this->view->form = $this->mainform;
    	
    	$id = $this->getRequest()->getParam($this->mainmodel->get_primary(), 0);
    	
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
    				$this->flash($this->titel . ' succesvol gewijzigd!', $this->redirectlink);
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
	
	public function duplicateAction($flash = true){
		$this->view->title = $this->titel . " dupliceren";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	
    	$this->mainform->submit->setLabel('Opslaan');
    	$this->view->form = $this->mainform;

		$id = $this->getRequest()->getParam($this->mainmodel->get_primary(), 0);
		
		$controller = $this->getRequest()->getControllerName();
		// form is processed
		if ($this->getRequest()->isPost()){
			$request = clone $this->getRequest();
			$request->setActionName('add')
			->setControllerName($controller);
			$this->_helper->actionStack($request);
		}
		else{
			$uur = $this->mainmodel->getRecordById($id);
			$this->mainform->populate($uur);
		}
	}
	
	public function exportAction(){
		$this->view->titles = $this->titles;
		$this->view->exportfields = $this->exportfields;
		if(is_array($this->exportfields) && count($this->exportfields) > 0){
			//exportfields hier vervangen door de verzonden velden zodat ze zelf kunnen kiezne welke getoond worden en welke niet
			$this->exportfields = $this->getRequest()->getParam('fields', $this->exportfields);
			$rows = call_user_func_array(array($this->mainmodel, $this->function), $this->params);
			if(is_array($this->filterfields) && count($this->filterfields) > 0){
				if($this->getRequest()->isPost()){
					$newrows = array();
					foreach($rows as $row){
						$test = true;
						foreach($this->filterfields as $key => $value){
							$key = str_ireplace('[]', '', $key);
							if(array_key_exists($key, $this->getRequest()->getParams())){
								$sent = $this->getRequest()->getParam($key, '');
								if(!empty($sent)){
									if(array_key_exists('type', $value)){
										$switch = $value['type'];
									}
									else{
										$switch = $value[0]['type'];
									}
									switch ($switch) {
										case 'date':
											if(is_array($sent)){
												if(!empty($sent[0]) && !empty($sent[1])){
													if(!(strtotime($this->mainmodel->writeDate($row[$key])) >= strtotime($this->mainmodel->writeDate($sent[0])) &&
														strtotime($this->mainmodel->writeDate($row[$key])) <= strtotime($this->mainmodel->writeDate($sent[1])))){
														$test = false;
													}
												}
												break;
											}
										case 'text':
											if(!(stripos($row[$key], $sent) !== false)){
												$test = false;
											}
										break;
										case 'multiselect':
										case 'select':
											if(!(($row[$key] == $sent) || (is_array($sent) && in_array($row[$key], $sent)))){
												$test = false;
											}
										break;
										default:
											if(!($row[$key] == $sent || (is_array($sent) && in_array($row[$key], $sent)))){
												$test = false;
											}
										break;
									}
								}
							}
						}
						if($test){
							$newrows[] = $row;
						}
					}
					$this->export($newrows, $this->exportfields);
				}
				else{
					foreach($this->getRequest()->getParams() as $key => $val){
						$this->view->$key = $val;
					}
				}
			}
			else{
				$this->export($rows, $this->exportfields);
			}
		}
		else{
			$this->flash('', $this->redirectlink);
		}
	}
    
    private function export($rows, $include){
    	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle($this->mainmodel->getName());
		$objPHPExcel->getProperties()->setSubject($this->mainmodel->getName());
		$objPHPExcel->getProperties()->setDescription($this->mainmodel->getName());
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

		$this->view->velden = $this->exportfields;
		$this->view->titles = $this->titles;
		
		$rownumber = 1;
		$cols = array_keys($rows[0]);
    	$this->makeNumberToLetters($cols);
		
		$colnumber = 0;
		foreach ($cols as $c){
   			if(in_array($c, $include)){
				$letter = strtoupper($this->numbertoletters[$colnumber]);
				$cijfer = $rownumber;
				$objWorksheet->getCell($letter . $cijfer)->setValue($this->titles[$c]);
	   			$colnumber++;
   			}
		}
		$rownumber++;
		
		foreach($rows as $r){
			$colnumber = 0;
   			foreach($cols as $key => $c){
   				if(in_array($c, $include)){
   					$letter = strtoupper($this->numbertoletters[$colnumber]);
					$cijfer = $rownumber;
					$objWorksheet->getCell($letter . $cijfer)->setValue($this->transform($r[$c], $c));
   					$colnumber++;
   				}
   			}
//    			echo '<br />';
			$rownumber++;
		}
		
    	//Now that all cell contents are edited, you are ready to save it as Excel 2002
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		//This is the file name: write.xlsx
		$objWriter->save($this->folder . '/'.$this->mainmodel->getName().'.xlsx');
		$this->flash('', '/' . $this->folder . '/'.$this->mainmodel->getName().'.xlsx');
    }
    
    private function makeNumberToLetters($cols){
    	$lettercount = count($this->letters);
    	
    	for($i = 0; $i < count($cols); $i++){
    		if($i > ($lettercount-1)){
	    		$deling = (int)($i / $lettercount)-1;
	    		$rest = ($i % $lettercount);
	    		
	    		$this->numbertoletters[] = $this->letters[$deling] . $this->letters[$rest];
    		}
    		else{
    			$this->numbertoletters[] = $this->letters[$i];
    		}
    	}
    }
    
    private function transform($value, $column){
    	$return = $value;
    	switch($column){
//    		VOORBEELD VAN UIT PARKEERBEHEER!!
//    		case 'pl_id':
//    			$return = $this->plaatsen[$value]['pl_naam'];
//    			;break;
    	}
    	return $return;
    }
	
	public function deleteAction($flash = true){
		$id = $this->getRequest()->getParam($this->mainmodel->get_primary(), 0);
		$this->mainmodel->delete($this->mainmodel->get_primary() . ' = ' . $id);
    	if($flash){
			$this->flash($this->titel . ' succesvol verwijderd!', $this->redirectlink);
    	}
    	else{
			return true;
    	}
    	return false;
	}

   	public abstract function indexAction();
    
}
