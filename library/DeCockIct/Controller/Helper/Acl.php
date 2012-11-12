<?php
class DeCockIct_Controller_Helper_Acl{
	private static $acl;
	private static $namespaceRolename = 'userType';
	private static $defaultRole = 'Public';

    public $localacl;
    protected $roles = array();
    protected $resources = array();
    protected $front;
    
    public function __construct($front){
    	$this->front = $front;
    	$this->localacl = new Zend_Acl();
		$this->setRoles();
		$this->setResources();
		$this->setPrivileges();
		$this->setAdmin();
		$this->setAcl();
    }
    
    private function setAdmin(){
    	if(!$this->localacl->hasRole('admin')){
    		$this->localacl->addRole('admin');
    	}
    	$this->localacl->allow('admin', null, null);
    }
    
    private function setRoles(){
    	$rolmodel = new Model_DbTable_Roles();
    	
    	$allrecords = $rolmodel->getAllRecords();
    	foreach($allrecords as $r){
    		$this->roles[$r['rol_id']] = $r['rol_name'];
    	}
    	
    	foreach($allrecords as $r){
    		if(isset($r['rol_parent']) && $r['rol_parent'] != null){
    			if(!$this->localacl->hasRole($this->roles[$r['rol_parent']])){
    				//if parent hasn't been created in memory, do so
    				$parent = new Zend_Acl_Role($this->roles[$r['rol_parent']]);
    				$this->localacl->addRole($parent);
    			}
    			else{
    				$parent = $this->localacl->getRole($this->roles[$r['rol_parent']]);
    			}
    			$this->localacl->addRole(new Zend_Acl_Role($r['rol_name']), $parent);
    		}
    		else{
    			if(!$this->localacl->hasRole($r['rol_name'])){
    				$this->localacl->addRole(new Zend_Acl_Role($r['rol_name']));
    			}
    		}
    	}
    }

    private function setResources(){
    	$resmodel = new Model_DbTable_Resources();
    	
    	$allresources = $resmodel->getAllRecords();
    	
    	foreach($allresources as $r){
    		$this->resources[$r['res_id']] = $r['res_name'];
    	}
    	
    	/*foreach($allresources as $r){
    		$this->resources[$r['res_id']] = $r['res_name'];
    		if(isset($r['res_parent']) && $r['res_parent'] != null){
    			if(!$this->localacl->has($this->resources[$r['res_parent']])){
    				$parent = new Zend_Acl_Resource($this->resources[$r['res_parent']]);
    				$this->localacl->addResource($parent);
    			}
    			else{
    				$parent = $this->localacl->get($this->resources[$r['res_parent']]);
    			}
    			$this->localacl->addResource(new Zend_Acl_Resource($r['res_name']), $parent);
    		}
    		else{
    			if(!$this->localacl->has($r['res_name'])){
    				$this->localacl->addResource(new Zend_Acl_Resource($r['res_name']));
    			}
    		}
    	}*/
    	$controllerhelper = new DeCockIct_Controller_Helper_ControllerHelper();
    	$resources = $controllerhelper->getAllControllers($this->front, false);
    	foreach($resources as $modules){
	    	foreach($modules as $r){
	    		if(!$this->localacl->has($r)){
	    			$this->localacl->addResource(new Zend_Acl_Resource($r));
	    		}
	    	}
    	}
    }

    private function setPrivileges(){
    	$rormodel = new Model_DbTable_RolesResources();
    	$privileges = $rormodel->getAllRecords();
    	foreach($privileges as $p){
    		//make sure role and resource are valid
    		if($p['ror_role'] && $p['ror_resource']){
    			if($p['ror_allow']){
    				$this->localacl->allow($this->roles[$p['ror_role']], $this->resources[$p['ror_resource']], $p['ror_privilege']);
    			}
    			else{
    				$this->localacl->deny($this->roles[$p['ror_role']], $this->resources[$p['ror_resource']], $p['ror_privilege']);
    			}
    		}
    	}
    }
    
    private function setAcl(){
    	Zend_Registry::set('acl',$this->localacl);
    }
	
	public static function getUserRole(){
		$name = self::$namespaceRolename;
		$usersNs = new Zend_Session_NameSpace('Zend_Auth');
		$userrole = $usersNs->$name == '' ? self::$defaultRole : $usersNs->$name;
		return $userrole;
	}
	
	public static function getDefaultRole(){
		return self::$defaultRole;
	}
	
	public static function getAcl(){
		if(!self::$acl){
			self::$acl = Zend_Registry::get('acl');
		}
		return self::$acl;
	}
	
	public static function isAllowed($controller, $action){
		$acl = self::getAcl();
		$role = self::getUserRole();
		return $acl->isAllowed($role, $controller, $action);
	}
}