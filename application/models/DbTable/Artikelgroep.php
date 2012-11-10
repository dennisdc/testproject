<?php

class Model_DbTable_Artikelgroep extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'artikelgroepen';

	protected $_primary = 'artgroep_id';
	
	function getArtikelgroepById($id)
	{
		$query = $this->select();
		$query->where('artgroep_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
		
	function getArtikelgroepByName($artgroep_oms)
	{
		$query = $this->select();
		$query->where('artgroep_oms = ?', $artgroep_oms);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getArtikelgroepCount()
	{
		$query = $this->select('artgroep_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getArtikelgroepList()
	{
		$query = $this->select();
		$query->order('artgroep_oms ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	public function updateArtikelgroepById($id, $artgroep_oms)
	{ 
		$data = array(
			'artgroep_oms' => $artgroep_oms
		);
		$this->update($data, 'artgroep_id = ' .(int)$id);
	}
	
	function deleteArtikelgroepByID($id)
	{
		$where = $this->getAdapter()->quoteInto('artgroep_id= ?', $id);
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
}