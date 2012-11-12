<?php

class Model_DbTable_Btw extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'btw';

	protected $_primary = 'btw_id';
	
	public function getBtwById($btw_id){
		$query = $this->select();
		$query->where('btw_id = ?', $btw_id);
		$select = $this->fetchRow($query);
		return $select;
	}
	
	public function getBtws(){
		return $this->fetchAll();
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

	function getCols(){
		return $this->_getCols();
	}
	
	function get_primary(){
		return $this->_primary;
	}
}