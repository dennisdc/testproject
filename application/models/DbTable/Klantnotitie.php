<?php

class Model_DbTable_Klantnotitie extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'klantnotitie';

	protected $_primary = 'kn_id';
	
	function getNotitieById($id)
	{
		$query = $this->select();
		$query->where('kn_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
	
	function getNotitieByDate($date)
	{
		$query = $this->select();
		$query->where('kn_datum = ?', $date);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getNotitieByKlant($k_id)
	{
		$query = $this->select();
		$query->where('k_id = ?', $k_id);
		$result = $this->fetchall($query);
		return $result;
	}
	
	function getNotitieCount($k_id)
	{
		$query = $this->select('kn_id');
		$query->where('k_id = ?', $k_id);
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getNotitieList($k_id)
	{
		$query = $this->select();
		$query->where('k_id = ?', $k_id);
		$query->order('kn_datum ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	function deleteKnotitieByID($id)
	{
		$where = $this->getAdapter()->quoteInto('kn_id= ?', $id);
		$result = $this->delete($where);
	}
	
    protected function _fetch(Zend_Db_Table_Select $select)
    {
    	$usermodel = new Model_DbTable_User();
    	$ddcNamespace = new Zend_Session_Namespace('Zend_Auth');
    	$user = $usermodel->getUserById($ddcNamespace->userid);
    	$this->_where($select, $this->_name . '.ent_id = ' . $user['ent_id']);
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
}
