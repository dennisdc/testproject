<?php

class Model_DbTable_Docnummer extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'docnummer';

	protected $_primary = 'doc_id';
	
	function getDocnummerById($id)
	{
		$query = $this->select();
		$query->where('doc_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
		
	function getDocnummerByName($doc_oms)
	{
		$query = $this->select();
		$query->where('doc_oms = ?', $doc_oms);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getDocnummerCount()
	{
		$query = $this->select('doc_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getDocnummerList()
	{
		$query = $this->select();
		$query->order('doc_oms ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	public function updateDocnummerById($id, $doc_oms, $doc_nr)
	{
		$data = array(
			'doc_oms' => $doc_oms,
			'doc_nr' => $doc_nr
		);
		$this->update($data, 'doc_id = ' .(int)$id);
	}
	
	function deleteDocnummerByID($id)
	{
		$where = $this->getAdapter()->quoteInto('doc_id= ?', $id);
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
