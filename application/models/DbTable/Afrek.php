<?php

class Model_DbTable_Afrek extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'afrek';

	protected $_primary = 'afrek_id';
	
	function getAfrekById($id)
	{
		$query = $this->select();
		$query->where('afrek_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
		
	function getAfrekByName($afrek_oms)
	{
		$query = $this->select();
		$query->where('afrek_oms = ?', $afrek_oms);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getAfrekCount()
	{
		$query = $this->select('afrek_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getAfrekList()
	{
		$query = $this->select();
		$query->order('afrek_oms ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	public function updateAfrekById($id, $afrek_oms)
	{ 
		$data = array(
			'afrek_oms' => $afrek_oms
		);
		$this->update($data, 'afrek_id = ' .(int)$id);
	}
	
	function deleteAfrekByID($id)
	{
		$where = $this->getAdapter()->quoteInto('afrek_id= ?', $id);
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