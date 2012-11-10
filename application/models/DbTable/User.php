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
	
	/*
	 * get user by ID
	 * @param string id
	 * @return UserRow
	 */
	function getUserById($id)
	{
		$query = $this->select();
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
		$query = $this->select();
		$query->where('u_email = ?', $email);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getUserByUsername($username)
	{
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
			'u_paswoord' => $paswoord);
		$this->update($data, 'u_id = ' .(int)$id);
	}
	
}