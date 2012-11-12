<?php
class DeCockIct_Mainmodel extends Zend_Db_Table_Abstract
{
	/*
	 * name of the table
	 */
	protected $_name = '';
	
	/*
	 * primary key
	 */
	protected $_primary = '';
	
	/*
	 * array met de volgorde van de velden
	 */
	protected $veldvolgorde = array();
	
	function getRecordById($id){
		$query = $this->select();
		if(!is_array($id)){
			$query->where($this->_primary . ' = ?', $id);
			return $this->fetchRow($query)->toArray();
		}
		else{
			if(count($id) < 1){
				$id[] = 0;
			}
			$query->where($this->_primary . ' in (?)', $id);
			
			$select = $this->fetchAll($query);
			if(count($select) > 0){
				$select = $select->toArray();
			}
			return $select;
		}
	}
	
	function getAllRecords(){
		return $this->fetchAll()->toArray();
	}

	public function getCols(){
		return $this->_getCols();
	}
	/**
	 * @return the $_primary
	 */
	public function get_primary() {
		return $this->_primary;
	}
	
	protected function _setupPrimaryKey(){
       $primary = $this->_primary;
       parent::_setupPrimaryKey();
       $this->_primary = $primary;
	}
	
	function getVeldvolgorde(){
		return $this->veldvolgorde;
	}
	
	function deleteAll(){
		$this->delete('');
	}
	
	function displayDate($date, $format = 'dd-MM-YYYY')
	{
		if($date > 0){
			$newdate = new Zend_Date($date, null, 'nl_BE');
			return $newdate->get($format);
		}
	}
	
	function writeDate($date, $format = 'YYYY-MM-dd')
	{
		$newdate = new Zend_Date($date, null, 'nl_BE');
		return $newdate->get($format);
	}
	
	public function insert(array $data) {
		$return = parent::insert($data);
		
		if(isset($this->_historiek) && $this->_historiek > 0){
			$hismodel = new Model_DbTable_Historiek();
			
			$namespace = new Zend_Session_Namespace('Zend_Auth');
			
			$hismodel->insert(array(
					'u_id' => $namespace->userid,
					'con_id' => array_key_exists('con_id', $data) ? $data['con_id'] : 0,
					'pan_id' => array_key_exists('pan_id', $data) ? $data['pan_id'] : 0,
					'his_otherid' => $return,
					'his_type' => $this->_historiek,
				) 
			);
		}
		
		return $return;
	}
	
	public function update(array $data, $where) {
		$return = parent::update($data, $where);
		
		if(isset($this->_historiek) && $this->_historiek > 0){
			$hismodel = new Model_DbTable_Historiek();
				
			$namespace = new Zend_Session_Namespace('Zend_Auth');
			
			$id = 0;
			$split = explode(' = ', $where);
			for($i = 0; $i < count($split); $i++){
				if($split[$i] == $this->_primary){
					$id = $split[$i+1];
				}
			}
			
			$data2 = array(
					'u_id' => $namespace->userid,
					'con_id' => array_key_exists('con_id', $data) ? $data['con_id'] : 0,
					'pan_id' => array_key_exists('pan_id', $data) ? $data['pan_id'] : 0,
					'his_otherid' => $id,
					'his_type' => $this->_historiek,
				);
			
			$hismodel->insert($data2);
		}
		
		return $return;
	}
	
	/* (non-PHPdoc)
	 * @see Zend_Db_Table_Abstract::fetchAll()
	 */
	public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
// 		$where = $this->addRights($where);
		return parent::fetchAll($where, $order, $count, $offset);
	}

	/* (non-PHPdoc)
	 * @see Zend_Db_Table_Abstract::fetchRow()
	 */
	public function fetchRow($where = null, $order = null, $offset = null) {
		$where = $this->addRights($where);
		return parent::fetchRow($where, $order, $offset);
	}

	protected function addRights(Zend_Db_Table_Select $query){
		return $query;
	}
	
}