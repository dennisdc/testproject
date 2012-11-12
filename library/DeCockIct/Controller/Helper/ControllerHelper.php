<?php
class DeCockIct_Controller_Helper_ControllerHelper{
	private $ignoreClass = array('DeCockIct_MainController', 'MainController');
	private $rivtypeswithtaxes19 = array(4);
	
    public function __construct(){
    	if(Zend_Registry::isRegistered('db')){
    		$this->db = Zend_Registry::get('db');
    	}
    }
    
    public function getAllControllers($front, $actions = true){
        $acl = array();
        foreach ($front->getControllerDirectory() as $module => $path) {
			foreach (scandir($path) as $file) {
            	if (strstr($file, "Controller.php") !== false) {
                	include_once $path . DIRECTORY_SEPARATOR . $file;
                    foreach (get_declared_classes() as $class) {
                    	if (is_subclass_of($class, 'Zend_Controller_Action') && !in_array($class, $this->ignoreClass)) {
                        	$controller = strtolower(substr($class, 0, strpos($class, "Controller")));
                        	if($actions){
	                            foreach (get_class_methods($class) as $action) {
	                            	if (strstr($action, "Action") !== false) {
	                            		if(array_key_exists($module, $acl) && array_key_exists($controller, $acl[$module])){
	                            			if(!in_array(substr($action, 0, -6), $acl[$module][$controller])){
		                            			$acl[$module][$controller][] = substr($action, 0, -6);
		                            		}
		                            		else{
		                            		}
	                            		}
	                            		else{
	                            			$acl[$module][$controller][] = substr($action, 0, -6);
	                            		}
//	                                	$actions[] = substr($action, 0, -6);
	                                }
								}
                        	}
                        	else{
                        		$acl[$module][] = $controller;
                        	}
						}
					}
//                    $acl[$module][$controller] = $actions;
				}
			}
        }
        return $acl;
    }
    
    public function makeContactGrid($contacts){
		$grid = new Ingot_JQuery_JqGrid('Contacts', new Ingot_JQuery_JqGrid_Adapter_Array($contacts), array('height' => '600px')); 
		//new Zend_Json_Expr('function(rowid,status){window.location = "/pand/fiche/pan_id/'.$pan_id.'";window.open("/contract/bruikleenbemiddeling/pan_id/'.$pan_id.'/con_id/" + $(\'#\'+rowid).children(\'td\').first().text());}')));
		
		$con_idcol = new Ingot_JQuery_JqGrid_Column('con_id', array('hidden' => true));
		$con_actiefcol = new Ingot_JQuery_JqGrid_Column('con_actief', array('width' => '50px', 'align' => 'center'));
		$con_typecol = new Ingot_JQuery_JqGrid_Column('con_type', array('width' => '75px', 'align' => 'left'));
		$con_naamcol = new Ingot_JQuery_JqGrid_Column('con_naam', array('width' => '100%'));
		$con_emailcol = new Ingot_JQuery_JqGrid_Column('con_email', array('width' => '100%'));
		$con_telcol = new Ingot_JQuery_JqGrid_Column('con_tel', array('width' => '75px', 'align' => 'center'));
		$con_faxcol = new Ingot_JQuery_JqGrid_Column('con_fax', array('width' => '75px', 'align' => 'center'));
		$con_mobielcol = new Ingot_JQuery_JqGrid_Column('con_mobiel', array('width' => '75px', 'align' => 'center'));
		
		$con_actiefcol->setLabel('Actief');
		$con_typecol->setLabel('Type');
		$con_naamcol->setLabel('Naam');
		$con_emailcol->setLabel('E-mail');
		$con_telcol->setLabel('Telefoon');
		$con_faxcol->setLabel('Fax');
		$con_mobielcol->setLabel('Mobiel');
		$grid->addColumn($con_idcol);
		$grid->addColumn($con_actiefcol);
		$grid->addColumn($con_typecol);
		$grid->addColumn($con_naamcol);
		$grid->addColumn($con_emailcol);
		$grid->addColumn($con_telcol);
		$grid->addColumn($con_faxcol);
		$grid->addColumn($con_mobielcol);

		if (!$grid->hasPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter())){
			$grid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter());
		}
		
		return $grid;
    }
    
    public function makePandGrid($panden){
      $grid = new Ingot_JQuery_JqGrid('Panden', new Ingot_JQuery_JqGrid_Adapter_Array($panden), array());
      
      $pan_idcol = new Ingot_JQuery_JqGrid_Column('pan_id', array('hidden' => true));
      $pan_inbeheercol = new Ingot_JQuery_JqGrid_Column('pan_inbeheer', array('width' => '50px', 'align' => 'center'));
      $pat_typecol = new Ingot_JQuery_JqGrid_Column('pat_oms', array('width' => '75px', 'align' => 'left'));
//      $pan_refcol = new Ingot_JQuery_JqGrid_Column('pan_ref', array('width' => '100%', 'align' => 'left'));
      $pan_pandcodecol = new Ingot_JQuery_JqGrid_Column('pan_pandcode', array('width' => '50px', 'align' => 'center'));
      $pan_adrescol = new Ingot_JQuery_JqGrid_Column('pan_adres', array('width' => '100%', 'align' => 'left'));
      $pan_plaatscol = new Ingot_JQuery_JqGrid_Column('pan_plaats', array('width' => '150px', 'align' => 'left'));
      
      $pan_inbeheercol->setLabel('In beheer');
      $pat_typecol->setLabel('Type');
//      $pan_refcol->setLabel('Referentie');
      $pan_pandcodecol->setLabel('Pandcode');
      $pan_adrescol->setLabel('Adres');
      $pan_plaatscol->setLabel('Plaats');
      $grid->addColumn($pan_idcol);
      $grid->addColumn($pan_pandcodecol);
      $grid->addColumn($pan_adrescol);
      $grid->addColumn($pan_plaatscol);
      $grid->addColumn($pan_inbeheercol);
      $grid->addColumn($pat_typecol);
//      $grid->addColumn($pan_refcol);

      if (!$grid->hasPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter())){
		$grid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter());
      }
      
      return $grid;
    }
    
    public function berekenKosten($kosten, $kostjaar){
    	$this->mainmodel = new Model_DbTable_Kosten();
    	$this->cosmodel = new Model_DbTable_Contactstatus();
    	$this->rekivbmodel = new Model_DbTable_RekeningIvb();
    	
     	$kostenpercontact = array();
     	foreach ($kosten as $k){ 
      		switch($k['kos_wederkerend']){
		      //1 = EENMALIG, 2 = MAANDELIJKS, 3 = JAARLIJKS
      			case '1':
      				$ex = explode('-', $k['kos_datum']);
	      			$kostenpercontact[$k['con_id']][] = $this->verwerkKost($k, $ex[2] . '-' . $ex[1] . '-' . $ex[0], ($k['kos_gebruikt'] + 1), true);
	      			;break;
	      		case '2':
	      			$newnumber = $k['kos_gebruikt'];
	      			$start = 1;
	      			$ex = explode('-', $k['kos_datum']);
	      			if($ex[2] == $kostjaar){
	      				$start = ((int)$ex[1]);
	      			}
	      			$start += $newnumber;
	      			for($i = $start; $i <= 12; $i++){
	      				$hulp = $k;
	      				$newdate = $kostjaar . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . $ex[0];
	      				$newnumber++;
	      				$kostenpercontact[$k['con_id']][] = $this->verwerkKost($k, $newdate, $newnumber, false);
	      			}
	      			;break;
	      		case '3':
	      			$newnumber = $k['kos_gebruikt'];
	      			$ex = explode('-', $k['kos_datum']);
	      			$isalgebruikt = false;
	      			if($ex[2] == $kostjaar && $newnumber > 0){
	      				$isalgebruikt = true;
	      			}
	      			if($ex[2] < $kostjaar && ($kostjaar - ($ex[2] + $newnumber)) != 0){
	      				$isalgebruikt = true;
	      			}
	      			
	      			if(!$isalgebruikt){
		      			$ex = explode('-', $k['kos_datum']);
		      			$kostenpercontact[$k['con_id']][] = $this->verwerkKost($k, $kostjaar . '-' . $ex[1] . '-' . $ex[0], $k['kos_gebruikt'] + 1, false);
	      			}
	      			;break;
	      		}
	      }
	      return $kostenpercontact;
    }
    
    private function verwerkKost($kost, $datum, $newnumber, $setfinished){
    	if($kost['pan_id'] > 0 && $kost['con_id'] > 0){
   			$sta_id = $this->cosmodel->getContactstatusByContactAndPand($kost['con_id'], $kost['pan_id']);
    	}
    	else{
    		if($kost['pan_id'] > 0){
    			$sta_id = 2;
    		}
    		else{
    			if($kost['con_id'] > 0){
    				$sta_id = 3;
    			}
    			else{
    				$sta_id = 0;
    			}
    		}
    	}
    	
    	$data = array(
    		'kos_id' => $kost['kos_id'],
    		'riv_type' => 2,
    		'riv_datum' => $datum,
    		'riv_bedrag' => $kost['kos_bedrag'],
    		'con_id' => $kost['con_id'],
    		'pan_id' => $kost['pan_id'],
    		'riv_omschrijving' => $kost['kos_oms'],
    		'riv_rekeningnr' => '',
    		'riv_vervaldatum' => $datum,
    		'sta_id' => $sta_id,
    		'riv_betaald' => 0,
    		'riv_boekhoudrek' => $kost['grb_id'],
    	);
//    	print_r($data);
    	$this->rekivbmodel->insert($data);
    	$riv_id = $this->db->lastInsertId();
    	if($setfinished){
    		$this->mainmodel->update(array('kos_finished' => 1, 'kos_gebruikt' => $newnumber), 'kos_id = ' . $kost['kos_id']);
    	}
    	else{
    		$this->mainmodel->update(array('kos_gebruikt' => $newnumber), 'kos_id = ' . $kost['kos_id']);
    	}
    	
    	return $riv_id;
    }
    
	public function maakFactuur($factureren){
		$this->mainmodel = new Model_DbTable_Kosten();
		
    	$conmodel = new Model_DbTable_Contact();
    	$cosmodel = new Model_DbTable_Contactstatus();
    	$coamodel = new Model_DbTable_Contactadres();
    	$facmodel = new Model_DbTable_Factuur();
    	$fadmodel = new Model_DbTable_FactuurDetail();
    	$rivmodel = new Model_DbTable_RekeningIvb();
    	$kosmodel = new Model_DbTable_Kosten();
    	
		//FACTUREN MAKEN VAN DE OP REKENING GEZETTE HUUR
		$errors = array ();
		$ids = array();
		foreach ( $factureren as $con_id => $kpc ) {
			//FACTUURADRES ZOEKEN
			$contact = $conmodel->getRecordById ($con_id);
			$hasadres = true;
			if ($contact ['con_type'] == 'Particulier') {
				$cos = $cosmodel->getContactstatusByContactAndStatusAndActiveAtJoined ( $con_id, 3, date ( 'Y-m-d' ) );
				if (count ( $cos ) > 0) {
					$factuuradres = array ('fac_adres' => $cos [0] ['pan_adres'], 'fac_postcode' => $cos [0] ['pan_postcode'], 'fac_plaats' => $cos [0] ['pan_plaats'] );
				} else {
					$adres = $coamodel->getContactadresByContactAndType ( $con_id, 6 );
					if (count ( $adres ) > 0) {
						$factuuradres = array ('fac_adres' => $adres [0] ['cad_adres'] . ' ' . $adres [0] ['cad_nr'], 'fac_postcode' => $adres [0] ['cad_post'], 'fac_plaats' => $adres [0] ['cad_woon'] );
					} else {
						$hasadres = false;
					}
				}
			} else {
				$adres = $coamodel->getContactadresByContactAndType ( $con_id, 6 );
				if (count ( $adres ) > 0) {
					$factuuradres = array ('fac_adres' => $adres [0] ['cad_adres'] . ' ' . $adres [0] ['cad_nr'], 'fac_postcode' => $adres [0] ['cad_post'], 'fac_plaats' => $adres [0] ['cad_woon'] );
				} else {
					$hasadres = false;
				}
			}
			
			if ($hasadres) {
				$data = array ('con_id' => $con_id, 'fac_datum' => date ( 'Y-m-d' ), 'fac_adres' => $factuuradres ['fac_adres'], 'fac_postcode' => $factuuradres ['fac_postcode'], 'fac_plaats' => $factuuradres ['fac_plaats'] );
				$facmodel->insert ( $data );
				$fac_id = $this->db->lastInsertId ();
				$ids[] = $fac_id;
				foreach ( $kpc as $k ) {
					$data2 = array ('fac_id' => $fac_id, 'riv_id' => $k );
					$fadmodel->insert ( $data2 );
					$riv = $rivmodel->getRecordById($k);
					if($riv['kos_id'] > 0){
						$kost = $kosmodel->getRecordById($riv['kos_id']);
						$riv['riv_bedrag'] = $riv['riv_bedrag'] + ($kost['kos_btwbed']);
						$facmodel->update(array('fac_bedrag' => new Zend_Db_Expr('fac_bedrag + ' . $riv['riv_bedrag']), 'fac_betaald' => $riv['riv_betaald'], 'fac_betaaldatum' => $riv['riv_betaaldatum']), 'fac_id = ' . $fac_id);
					}
					else{
						if(in_array($riv['riv_type'], $this->rivtypeswithtaxes19)){
							$riv['riv_bedrag'] = $riv['riv_bedrag'] * 1.19;
							$facmodel->update(array('fac_bedrag' => new Zend_Db_Expr('fac_bedrag + ' . $riv['riv_bedrag']), 'fac_betaald' => $riv['riv_betaald'], 'fac_betaaldatum' => $riv['riv_betaaldatum']), 'fac_id = ' . $fac_id);
						}
						else{
							$facmodel->update(array('fac_bedrag' => new Zend_Db_Expr('fac_bedrag + ' . $riv['riv_bedrag']), 'fac_betaald' => $riv['riv_betaald'], 'fac_betaaldatum' => $riv['riv_betaaldatum']), 'fac_id = ' . $fac_id);
						}
					}
				}
			}
			else {
				$errors [$con_id] = '<a href="/contact/fiche/con_id/' . $con_id . '">' . $contact ['con_naam'] . '</a> heeft geen facturatieadres';
				
				foreach ( $kpc as $k ) {
					$riv = $this->rekivbmodel->getRecordById ( $k );
					if($riv['kos_id'] > 0){
						$this->mainmodel->update ( array ('kos_gebruikt' => new Zend_Db_Expr ( '(kos_gebruikt - 1)' ), 'kos_finished' => '0' ), 'kos_id = ' . $riv['kos_id'] );
					}
					$this->rekivbmodel->delete ( 'riv_id = ' . $k );
				}
			}
		}
		return array('errors' => $errors, 'facturen' => $ids);
    }
    
    public function exportpdfByHtml($html, $filename, $folder = 'contactdocument'){
    	set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH . "/../library/dompdf");
    	require_once 'dompdf_config.inc.php'; 
		$autoloader = Zend_Loader_Autoloader::getInstance(); 
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$dompdf = new DOMPDF();
		$dompdf->set_paper("a4","portrait");
		$dompdf->load_html($html);
		$dompdf->set_base_path($_SERVER['DOCUMENT_ROOT']);
		$dompdf->render();
		$pdf = $dompdf->output();
		$filename = $this->changeName($filename, 'upload/'.$folder.'/', '.pdf');
		file_put_contents('upload/'.$folder.'/'.$filename.'.pdf', $pdf);
		
// 		return 'upload/pdf/'.$filename.'.pdf';
		return ''.$filename.'.pdf';
    }
    
    public function changeName($name, $folder, $extension){
    	if(file_exists($folder . $name . $extension)){
    		$i = 0;
    		do{
    			$i++;
//     			$explodedname = explode('.', $name);
//     			$extension = $explodedname[count($name)-1];
//     			unset($explodedname[count($name)-1]);
    			$newname = $name .'_'.$i;//.'.'.implode('.', $explodedname);
    			$test = file_exists($folder . $newname . $extension);
    		}while($test);
    		$name = $newname;
    	}
    	return $name;
    }
    
    public function makePaginator($vars, $p, $c, $extraparams = ''){
    	$pager = Zend_Paginator::factory($vars);
    	
    	$currentPage = isset($p) ? (int)htmlentities($p) : 1;
    	$itemsPerPage = isset ( $c ) ? ( int ) htmlentities ( $c ) : 24;
    	$itemsPerPage = $itemsPerPage . $extraparams;
    	
    	$pager->setCurrentPageNumber ( $currentPage );
    	
    	$pager->setItemCountPerPage ( $itemsPerPage );
    	
    	// set number of pages in page range
    	$pager->setPageRange(8);
    	
    	// get page data
    	$pages = $pager->getPages('Sliding');
    	
    	// create page links
    	$pageLinks = array();
    	$separator = ' | ';
    	
    	// build first page link
    	$pageLinks [] = urldecode($this->getLink ( $pages->first, $itemsPerPage, '<<' ));
    	
    	// build previous page link
    	if (! empty ( $pages->previous )) {
    		$pageLinks [] = urldecode($this->getLink ( $pages->previous, $itemsPerPage, '<' ));
    	}
    	
    	// build page number links
    	foreach ( $pages->pagesInRange as $x ) {
    		if ($x == $pages->current) {
    			$pageLinks [] = $x;
    		} else {
    			$pageLinks [] = urldecode($this->getLink ( $x, $itemsPerPage, $x ));
    		}
    	}
    	
    	// build next page link
    	if (! empty ( $pages->next )) {
    		$pageLinks [] = urldecode($this->getLink ( $pages->next, $itemsPerPage, '>' ));
    	}
    	
    	// build last page link
    	$pageLinks [] = urldecode($this->getLink ( $pages->last, $itemsPerPage, '>>' ));
    	
    	return array($pager, $pageLinks, $separator);
    }
	
	private function getLink($page, $itemsPerPage, $label) {
		$q = http_build_query ( array ('p' => $page, 'c' => $itemsPerPage ) );
		return "<a href=\"?$q\" class=\"art-button\">$label</a>";
	}
	
	/**
	 * 
	 * @param int $u_id
	 * @param array $cos
	 * @param string $datum
	 * @param int $type => 1 = pand uit beheer genomen
	 * 					=> 2 = contract beeindigd door bruikleennemer
	 */
	public function eindecontract($u_id, $cos, $datum, $type, $ubn = false){
		$parmodel = new Model_DbTable_Parameter();
		$todomodel = new Model_DbTable_Todo();
		$facmodel = new Model_DbTable_Factuur();
		$rekivbmodel = new Model_DbTable_RekeningIvb();
		$fadmodel = new Model_DbTable_FactuurDetail();
		$uitmodel = new Model_DbTable_Uitschrijving();
		
		$anouk = $parmodel->getParameterValue('contracteinde');
		
		$time = strtotime(implode('-', array_reverse(explode('-', $datum))));
		$out = mktime();
		if(((int)date('d', $time)) > 23){
			$out = mktime(0,0,0,((int)date('m', $time)), 23, date('Y', $time));
		}
		else{
			$out = mktime(0,0,0,((int)date('m', $time))-1, 23, date('Y', $time));
		}

		$data = array(
				'con_id' => $cos['con_id'],
				'pan_id' => $cos['pan_id'],
			);
		$uitmodel->insert($data);
		
		$einddatum = date('Y-m-d', $time);
/*		if(!$ubn || $ubn < 1){
			//borgsom terugbetalen
			$facdata = array(
					'con_id' => $cos['con_id'],
					'fac_bedrag' => -$cos['cos_borgsom'],
					'fac_datum' => $einddatum,
					'fac_borgsom' => 1,
			);
			$facmodel->insert($facdata);
			$fac_id = $this->db->lastInsertId();
			$rekdata = array(
					'cos_id' => $cos['cos_id'],
					'riv_type' => 1,
					'riv_omschrijving' => 'Borgsom',
					'con_id' => $cos['con_id'],
					'pan_id' => $cos['pan_id'],
					'riv_bedrag' => -$cos['cos_borgsom'],
					'riv_datum' => $einddatum,
					'riv_vervaldatum' => $einddatum,
					'sta_id' => 3,
					'riv_courtageberekend' => 1,
			);
			$rekivbmodel->insert($rekdata);
			$riv_id = $this->db->lastInsertId();
			$faddata = array(
					'fac_id' => $fac_id,
					'riv_id' => $riv_id
			);
			$fadmodel->insert($faddata);
		}
		else{
			$ubnmodel = new Model_DbTable_Uitbeheername();
			$ubn = $ubnmodel->getRecordById($ubn);

			$datum = explode(' ', $ubn['ubn_datum']);
			
			$facmodel->update(array('fac_datum' => $einddatum), 'fac_datum = "'.$datum[0].'" AND fac_bedrag = -'.$cos['cos_borgsom'].' AND fac_borgsom = 1');
			$rekivbmodel->update(array('riv_datum' => $einddatum, 'riv_vervaldatum' => $einddatum), 'con_id = '.$cos['con_id'].' AND pan_id = '.$cos['pan_id'].' AND riv_bedrag = -'.$cos['cos_borgsom'].' AND riv_datum = "'.$datum[0].'"');
		}
*/		
		$melding = '';
		switch ($type){
			case 1:
				$melding = 'Einde beheer / opzegging opdrachtgever';
				break;
			case 2:
				$melding = 'Opzegging bruikleenovereenkomst';
				break;
		}
		
		$melding .= '<a href="/contact/fiche/con_id/'.$cos['con_id'].'#tabs-4">Klik hier voor de bevestigingsmail te sturen</a>';
		
		$data = array(
				'todo_madeby' => $u_id,
				'act_id' => !$ubn ? '5' : '14',
				'con_id' => $cos['con_id'],
				'pan_id' => $cos['pan_id'],
				'todo_oms' => $melding,
				'u_id' => $anouk,
				'todo_datum' => date('d-m-Y', $out),
				'todo_begin' => '00:00',
				'todo_einde' => '00:00',
				'todo_af' => 'Nee',
				'todo_voor' => date('d-m-Y', $out),
		);
		$todomodel->insert($data);
		
		
	}
	
	public function makeSmallPicture($foto, $upload_dir, $subdir, $voorvoegsel, $grootte){
		$foto_naam = "".$upload_dir . $foto;
		$path_info = pathinfo($foto_naam);
		$naamfoto = $path_info['filename'] . "." . $path_info['extension'];
			// Thumbnail maken.
			// Stap 1: eigenschappen van de foto achterhalen
			list ($breedte, $hoogte, $image_type) = getimagesize($foto_naam);
		
			// Stap 2: bepaal de verhouding tussen hoogte en breedte
			$image_ratio = $breedte/$hoogte;
		
			// Stap 3: bereken op basis van de ratio de nieuwe hoogte
			if ($image_ratio > 1){
				$tn_breedte = $grootte * $image_ratio;
				$tn_hoogte = $grootte;
			}
			else{
				$tn_hoogte = $grootte / $image_ratio;
				$tn_breedte = $grootte;
			}
		
			// Stap 4: maak een lege thumbnail in het geheugen van de server
			$thumb = imagecreatetruecolor($tn_breedte,$tn_hoogte);
		
			// Stap 5: afhankelijk van het type foto het juiste type thumbnail maken
			switch ($image_type){
				case IMAGETYPE_GIF:
					$source = imagecreatefromgif($foto_naam);
					break;
				case IMAGETYPE_JPEG:
					$source = imagecreatefromjpeg($foto_naam);
					break;
				case IMAGETYPE_PNG:
					$source = imagecreatefrompng($foto_naam);
					break;
				default:
					// vangnet, dit komt als het goed is nooit voor.
					// indien toch: trachten gif-bestand te maken
					$source = imagecreatefromgif($foto_naam);
					break;
			}
		
			// Stap 6: De grote foto verkleinen en kopieren naar de thumbnail
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $tn_breedte, $tn_hoogte, $breedte, $hoogte);
		
			// Stap 7: naam van de thumbnail instellen
			$thumbname= "".$upload_dir . $subdir ."/" . $voorvoegsel. $naamfoto;
		
			// Stap 8: tot slot: de thumbnail opslaan, opnieuw afhankelijk van het type
			switch ($image_type){
				case IMAGETYPE_GIF:
					imagegif($thumb, $thumbname);
					break;
				case IMAGETYPE_JPEG:
					// jpeg-afbeelding opslaan, kwaliteit: 100%
					imagejpeg($thumb, $thumbname, 100);
					break;
				case IMAGETYPE_PNG:
					imagepng($thumb, $thumbname);
					break;
				default:
					echo "image type = '$image_type'";
					;break;
			}
		
			// rechten van de bestanden goed zetten
			chmod("".$upload_dir . $subdir ."/" . $voorvoegsel . $naamfoto, 0755);
//			chmod("".$upload_dir . $_FILES['p_foto']['name'], 0755);

		return $naamfoto;		
	}
}