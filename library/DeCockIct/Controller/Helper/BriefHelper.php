<?php
class DeCockIct_Controller_Helper_BriefHelper{
		
	private $replace = array(
			'Bedrijf' => 'con_bedrijf',
			'Afdeling' => 'afd_oms',
			'tit' => 'con_aanhef',
			'Voorletters' => 'con_voorletter',
			'Tussenvoegsels' => 'con_tussen',
			'Achternaam' => 'con_naam',
			'Adres 1' => 'cad_adres',
			'Postcode' => 'cad_post',
			'Plaats' => 'cad_woon',
			'Roepnaam' => 'con_roepnaam',
		);
	
    public function __construct(){
    	$this->replace['Datum'] = date('d-m-Y');
    	if(Zend_Registry::isRegistered('db')){
    		$this->db = Zend_Registry::get('db');
    	}
    }
    
    public function replaceText($tekst, $data, $user){    	
		$newtekst = $tekst;
		foreach($this->replace as $key => $r){
			if(array_key_exists($r, $data)){
				$newtekst = str_ireplace('[' . $key . ']', $data[$r], $newtekst);
			}
			else{
				if(array_key_exists($r, $user)){
					$newtekst = str_ireplace('[' . $key . ']', $user[$r], $newtekst);
				}
			}
		}
		foreach($user as $key => $r){
			$newtekst = str_ireplace('[' . $key . ']', $user[$key], $newtekst);
		}
		foreach($data as $key => $r){
			$newtekst = str_ireplace('[' . $key . ']', $data[$key], $newtekst);
		}

		return $newtekst;
    }
}