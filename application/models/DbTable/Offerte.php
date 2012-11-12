<?php

class Model_DbTable_Offerte extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'offerte';

	protected $_primary = 'off_id';
	
	function getOfferteById($id)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
    		->join(
				array('k' => 'klanten'),
				'k.kl_id = offerte.kl_id', '*'
    			)
    		->where('off_id = ?', $id);
    	$result = $this->fetchAll($select);
    	foreach ($result as $off){
    		$off[off_datum] = $this->displayDate($off[off_datum]);
    		$off[off_geldig] = $this->displayDate($off[off_geldig]);
    	}
		return $result;
	}
	
	function getOfferteCount()
	{
		$query = $this->select('off_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getOfferteUnapproved()
	{
		$select = $this->select();
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
    		->join(
				array('k' => 'klanten'),
				'k.kl_id = offerte.kl_id', '*'
    			)
    		->where('off_goedgekeurd = ?', 0);
    	$result = $this->fetchAll($select);
    	foreach ($result as $fact){
    		$fact[off_datum] = $this->displayDate($fact[off_datum]);
    		$fact[off_geldig] = $this->displayDate($fact[off_geldig]);
    	}
		return $result;
	}
	
	function getOfferteList()
	{
		$select = $this->select();
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
    		->join(
				array('k' => 'klanten'),
				'k.kl_id = offerte.kl_id', '*'
    			);
    	$result = $this->fetchAll($select);
    	foreach ($result as $fact){
    		$fact[off_datum] = $this->displayDate($fact[off_datum]);
    		$fact[off_geldig] = $this->displayDate($fact[off_geldig]);
    	}
		return $result;
	}
	
	public function addOfferte($data)
	{
		$data[off_datum] = $this->writeDate($data[off_datum]);
		$data[off_geldig] = $this->writeDate($data[off_geldig]);
		$this->insert($data);
	}
	
	public function updateOfferteById($id, $off_datum, $off_geldig, $kl_id, $off_excl, $off_btw, $off_incl, $off_goedgekeurd)
	{ 
		$data = array(
			'off_datum' => $this->writeDate($off_datum),
			'off_geldig' => $this->writeDate($off_geldig),
			'kl_id' => $kl_id,
			'off_incl' => $off_excl,
			'off_btw' => $off_btw,
			'off_excl' => $off_incl,
			'off_goedgekeurd' => $off_goedgekeurd
		);
		$this->update($data, 'off_id = ' .(int)$id);
	}
	
	public function updateOfferteApproved($id, $off_goedgekeurd)
	{ 
		$data = array(
			'off_goedgekeurd' => $off_goedgekeurd
		);
		$this->update($data, 'off_id = ' .(int)$id);
	}
	
	function deleteOfferteByID($id)
	{
		$where = $this->getAdapter()->quoteInto('off_id= ?', $id);
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