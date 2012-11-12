<?php

class Model_DbTable_Klant extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'klanten';

	protected $_primary = 'kl_id';
	
	function getKlantById($id)
	{
		$query = $this->select();
		$query->where('kl_id = ?', $id);
		$result = $this->fetchRow($query);
		return $result->toArray();
	}
	
	function getKlantByEmail($email)
	{
		$query = $this->select();
		$query->where('kl_email = ?', $email);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getKlantByName($name)
	{
		$query = $this->select();
		$query->where('kl_naam1 = ?', $name);
		$result = $this->fetchRow($query);
		return $result;
	}
	
	function getKlantCount()
	{
		$query = $this->select('kl_id');
		$result = $this->fetchAll($query);
		return count($result);
	}
	
	function getKlantList()
	{
		$query = $this->select();
		$query->order('kl_naam1 ASC');
		$result = $this->fetchAll($query);
		return $result;
	}
	
	public function updateKlantById($id, $kl_actief, $kl_taal, $kl_naam1, $kl_naam2, $kl_jurvorm,
    									$kl_adres1, $kl_adres2, $kl_post, $kl_woon, $kl_land, $kl_btw,
    									$kl_tel1, $kl_tel2, $kl_gsm, $kl_fax1, $kl_fax2, $kl_email, $kl_website,
    									$kl_uurtarief, $kl_aantkm, $kl_betterm)
	{
		$data = array(
			'kl_actief' => $kl_actief,
			'kl_taal' => $kl_taal,
			'kl_naam1' => $kl_naam1,
			'kl_naam2' => $kl_naam2,
			'kl_jurvorm' => $kl_jurvorm,
			'kl_adres1' => $kl_adres1,
			'kl_adres2' => $kl_adres2,
			'kl_post' => $kl_post,
			'kl_woon' => $kl_woon,
			'kl_land' => $kl_land,
			'kl_btw' => $kl_btw,
			'kl_tel1' => $kl_tel1,
			'kl_tel2' => $kl_tel2,
			'kl_gsm' => $kl_gsm,
			'kl_fax1' => $kl_fax1,
			'kl_fax2' => $kl_fax2,
			'kl_email' => $kl_email,
			'kl_website' => $kl_website,
			'kl_uurtarief' => $kl_uurtarief,
			'kl_aantkm' => $kl_aantkm,
			'kl_betterm' => $kl_betterm
		);
		$this->update($data, 'kl_id = ' .(int)$id);
	}
	
	function deleteKlantByID($id)
	{
		$where = $this->getAdapter()->quoteInto('kl_id= ?', $id);
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
