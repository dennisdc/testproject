<?php

class Model_DbTable_Artikel extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'artikels';

	protected $_primary = 'art_id';
	
	function getArtikelById($id)
	{
		$query = $this->select();
		$query->where('art_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
		
	function getArtikelByName($art_oms)
	{
		$query = $this->select();
		$query->where('art_oms = ?', $art_oms);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getArtikelCount()
	{
		$query = $this->select('art_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getArtikelList()
	{
		$select = $this->select();
		$select->setIntegrityCheck(false)
			->from($this->_name, '*')
			->join(
				array('g' => 'artikelgroepen'),
				'g.artgroep_id = artikels.artgroep_id', '*'
    			)
    		->join(
				array('l' => 'leveran'),
				'l.lev_id = artikels.lev_id', '*'
    			);
		return $this->fetchAll($select);
	}
	
	public function updateArtikelById($id, $art_actief, $artgroep_id, $art_oms, $art_akp, $art_vkp,
										$lev_id, $art_reflev)
	{ 
		$data = array(
			'art_actief' => $art_actief,
			'artgroep_id' => $artgroep_id,
			'art_oms' => $art_oms,
			'art_akp' => $art_akp,
			'art_vkp' => $art_vkp,
			'lev_id' => $lev_id,
			'art_reflev' => $art_reflev
		);
		$this->update($data, 'art_id = ' .(int)$id);
	}
	
	function deleteArtikelByID($id)
	{
		$where = $this->getAdapter()->quoteInto('art_id= ?', $id);
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