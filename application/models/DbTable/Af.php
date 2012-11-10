<?php

class Model_DbTable_Af extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'fact_aank';

	protected $_primary = 'af_id';
	
	function getAfById($id)
	{
		$query = $this->select();
		$query->where('af_id = ?', $id);
		$result = $this->fetchRow($query);
		$result[af_dat] = $this->displayDate($result[af_dat]);
		$result[af_vervaldat] = $this->displayDate($result[af_vervaldat]);
		return $result->toArray();
	}

	function getAfByRef($ref)
	{
		$query = $this->select();
		$query->where('af_ref = ?', $ref);
		$result = $this->fetchRow($query);
		$result[af_dat] = $this->displayDate($result[af_dat]);
		$result[af_vervaldat] = $this->displayDate($result[af_vervaldat]);
		$result[af_betaaldop] = $this->displayDate($result[af_betaaldop]);
		return $result;
	}
	
	function getAfByLeveran($lev_id)
	{
		$query = $this->select();
		$query->where('lev_id = ?', $lev_id);
		$result = $this->fetchall($query);
		return $result;
	}
	
	function getAfCount()
	{
		$query = $this->select('af_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getAfUnpaid()
	{
		$select = $this->select();
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
    		->join(
				array('l' => 'leveran'),
				'l.lev_id = fact_aank.lev_id', '*'
    			)
    		->joinleft(
				array('a' => 'afrek'),
				'a.afrek_id = fact_aank.afrek_id', '*'
    			)
    		->where('af_betaald = ?', 0);
    	$result = $this->fetchAll($select);
    	foreach ($result as $fact){
    		$fact[af_dat] = $this->displayDate($fact[af_dat]);
    		$fact[af_vervaldat] = $this->displayDate($fact[af_vervaldat]);
    		$fact[af_betaaldop] = $this->displayDate($fact[af_betaaldop]);
    	}
		return $result;
	}
	
	function getAfUnpaidOverdue()
	{
		$select = $this->select();
		$date = date("Y-m-d");
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
    		->join(
				array('l' => 'leveran'),
				'l.lev_id = fact_aank.lev_id', '*'
    			)
    		->join(
				array('a' => 'afrek'),
				'a.afrek_id = fact_aank.afrek_id', '*'
    			)
    		->where('af_betaald = ?', 0)
    		->where('af_vervaldat < ?', $date);
    	$result = $this->fetchAll($select);
    	foreach ($result as $fact){
    		$fact[af_dat] = $this->displayDate($fact[af_dat]);
    		$fact[af_vervaldat] = $this->displayDate($fact[af_vervaldat]);
    		$fact[af_betaaldop] = $this->displayDate($fact[af_betaaldop]);
    	}
		return $result;
	}
	
	function getAfUnpaidCount()
	{
		$query = $this->select();
		$query->where('af_betaald = ?', 0);
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getAfUnpaidLateCount()
	{
		$query = $this->select();
		$date = date("Y-m-d");
		$query->where('af_betaald = ?', 0);
		$query->where('af_vervaldat < ?', $date);
		$result = $this->fetchAll($query);
		return count($result);
	}	
	
	function getAfList()
	{
		$select = $this->select();
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
    		->join(
				array('l' => 'leveran'),
				'l.lev_id = fact_aank.lev_id', '*'
    			)
    		->joinleft(
				array('a' => 'afrek'),
				'a.afrek_id = fact_aank.afrek_id', '*'
    			);
    	$result = $this->fetchAll($select);
    	foreach ($result as $fact){
    		$fact[af_dat] = $this->displayDate($fact[af_dat]);
    		$fact[af_vervaldat] = $this->displayDate($fact[af_vervaldat]);
    		$fact[af_betaaldop] = $this->displayDate($fact[af_betaaldop]);
    	}
		return $result;
	}
	
	public function addAf($data)
	{
		$data[af_dat] = $this->writeDate($data[af_dat]);
		$data[af_vervaldat] = $this->writeDate($data[af_vervaldat]);
		$this->insert($data);
	}
	
	public function updateAfById($id, $af_dat, $af_vervaldat, $lev_id, $afrek_id, $af_ref, $af_oms, $af_bedrag)
	{ 
		$data = array(
			'af_dat' => $this->writeDate($af_dat),
			'af_vervaldat' => $this->writeDate($af_vervaldat),
			'lev_id' => $lev_id,
			'afrek_id' => $afrek_id,
			'af_ref' => $af_ref,
			'af_oms' => $af_oms,
			'af_bedrag' => $af_bedrag
		);
		$this->update($data, 'af_id = ' .(int)$id);
	}
	
	public function updateAfPaid($id, $af_betaald)
	{ 
		$date = date("d-m-Y");
		$data = array(
			'af_betaald' => $af_betaald,
			'af_betaaldop' => $this->writeDate($date)
		);
		$this->update($data, 'af_id = ' .(int)$id);
	}
	
	function deleteAfByID($id)
	{
		$where = $this->getAdapter()->quoteInto('af_id= ?', $id);
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