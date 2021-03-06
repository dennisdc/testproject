<?php

class Model_DbTable_Leveran extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'leveran';

	protected $_primary = 'lev_id';
	
	function getLeveranById($id)
	{
		$query = $this->select();
		$query->where('lev_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
		
	function getLeveranByName($name)
	{
		$query = $this->select();
		$query->where('lev_naam = ?', $name);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getLeveranCount()
	{
		$query = $this->select('lev_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getLeveranList()
	{
		$query = $this->select();
		$query->order('lev_naam ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	public function updateLeveranById($id, $lev_naam)
	{
		$data = array(
			'lev_naam' => $lev_naam
		);
		$this->update($data, 'lev_id = ' .(int)$id);
	}
	
	function deleteLeveranByID($id)
	{
		$where = $this->getAdapter()->quoteInto('lev_id= ?', $id);
		$result = $this->delete($where);
	}
	
	// date conversion functions
	
	function writeDate($date, $format = 'YYYY-MM-dd')
	{
		if ($date <> ''){
			$newdate = new Zend_Date($date, null, 'nl_BE');
			return $newdate->get($format);
		}
	}
	
	function displayDate($date, $format = 'dd-MM-YYYY')
	{
		if ($date <> ''){
			$newdate = new Zend_Date($date, null, 'nl_BE');
			return $newdate->get($format);
		}
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
