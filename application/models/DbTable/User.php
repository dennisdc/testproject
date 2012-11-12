<?php
class Model_DbTable_User extends Zend_Db_Table_Abstract
{
	/*
	 * name of the table
	 */
	protected $_name = 'users';
	
	/*
	 * primary key
	 */
	protected $_primary = 'u_id';
	
	protected $_checkentity = true;
	
	/*
	 * get user by ID
	 * @param string id
	 * @return UserRow
	 */
	function getUserById($id)
	{
		$this->_checkentity = false;
		$query = $this->select();
		$query->where('u_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
	
	function getUserByIdJoined($id)
	{
		$this->_checkentity = false;
		$query = $this->select();
		$query->setIntegrityCheck(false);
		$query->from($this->_name, '*');
		$query->join(array('e' => 'entiteit'), 'e.ent_id = users.ent_id', '*');
		$query->where('u_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
	
	/*
	 * get user by email
	 * @param string email
	 * @return UserRow
	 */
	function getUserByEmail($email)
	{
		$this->_checkentity = false;
		$query = $this->select();
		$query->where('u_email = ?', $email);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getUserByUsername($username)
	{
		$this->_checkentity = false;
		$query = $this->select();
		$query->where('u_naam = ?', $username);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	/*
	 * get total users count
	 * @return integer
	 */
	function getUserCount()
	{
		$query = $this->select('u_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getUserList()
	{
		$query = $this->select();
		$query->order('u_naam ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	function getActiveUserList(){
		$query = $this->select();
		$query->where('u_active = ?', true);
		$query->order('u_naam ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	function deleteUserByID($id)
	{
		$where = $this->getAdapter()->quoteInto('u_id= ?', $id);
		$result = $this->delete($where);
	}
	
	public function updateUserById($id, $username, $email, $paswoord)
	{
		$data = array(
			'u_naam' => $username,
			'u_email' => $email,
			'u_paswoord' => md5($paswoord));
		$this->update($data, 'u_id = ' .(int)$id);
	}
	
    protected function _fetch(Zend_Db_Table_Select $select)
    {
    	if($this->_checkentity){
	    	$usermodel = new Model_DbTable_User();
	    	$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
	    	$user = $usermodel->getUserById($ddcNamespace->userid);
	    	$this->_where($select, $this->_name . '.ent_id = ' . $user['ent_id']);
    	}
    	return parent::_fetch($select);
    }
    
	/* (non-PHPdoc)
	 * @see Zend_Db_Table_Abstract::insert()
	 */
	public function insert(array $data) {
		if(!array_key_exists('ent_id', $data)){
	    	$usermodel = new Model_DbTable_User();
	    	$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
	    	$user = $usermodel->getUserById($ddcNamespace->userid);
			$data['ent_id'] = $user['ent_id'];
		}
		parent::insert($data);
	}
	
	public function delete($where){
		$this->update(array('u_active' => 0), $where);
// 		parent::delete($where);
	}
	
}