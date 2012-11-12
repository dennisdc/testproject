<?php
class DeCockIct_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		$acl = Zend_Registry::get('acl');
		$usersNs = new Zend_Session_NameSpace('Zend_Auth');
		$roleName = DeCockIct_Controller_Helper_Acl::getUserRole();
		$privilegeName=$request->getActionName();
		$resourceName = $request->getControllerName();
		
		if(!$acl->has($resourceName) || !$acl->isAllowed($roleName,$resourceName,$privilegeName)){
			if(isset($usersNs->userid) && $usersNs->userid > 0){
				$request->setControllerName('Error');
				$request->setActionName('denied');
			}
			else{
				if($request->getControllerName() != 'user' || $request->getActionName() != 'login'){
					$request->setControllerName('user');
					$request->setActionName('login');
					$request->setParam('redirect', base64_encode('/'.$request->getRequestUri()));
				}
			}
		}
	}
}