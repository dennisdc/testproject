<?php
class DeCockIct_Controller_Helper_ViewHelper {
	public $priserdvtacheid = 9;
	private $recups = null;
	
	public function __construct() {
		if (Zend_Registry::isRegistered ( 'db' )) {
			$this->db = Zend_Registry::get ( 'db' );
		}
	}
	
	public function isDayOff($jour, $mois, $anne){
		//LETTERLIJK OVERGENOMEN VAN FUNCTION_ISDAYOFF.PHP VAN DE OUDE APPLICATIE
		if(jddayofweek ( cal_to_jd(CAL_GREGORIAN, $mois,$jour, $anne) , 0 )==0) return(TRUE);
		if(jddayofweek ( cal_to_jd(CAL_GREGORIAN, $mois,$jour, $anne) , 0 )==6) return(TRUE);
		
		if($this->isHoliday($jour, $mois, $anne)){
			return true;
		}
		
		//LAS HET EEN RECUPERATIEDAG IS IS HET OOK EEN FEESTDAG
		$recmodel = new Model_DbTable_Recuperation();
		$date = mktime(null, null, null, $mois, $jour, $anne);
		if($this->recups === null){
			$this->recups = $recmodel->getRecordByNewdate(date('Y-m-d',$date));
		}
		if(count($this->recups) > 0){
			return true;
		}
	}
	
	public function isHoliday($jour, $mois, $anne){
		$paques=date("j-n-Y", easter_date($anne)); // Pâques
		$paq=explode("-",$paques);
		if("$jour-$mois"=="1-1") return(TRUE);
		$jourP=$jour-1;
		if("$jourP-$mois-$anne"==$paques) return(TRUE);
		if("$jour-$mois"=="1-5") return(TRUE);
		if("$jour-$mois"=="21-7") return(TRUE);
		if("$jour-$mois"=="15-8") return(TRUE);
		if("$jour-$mois"=="1-11") return(TRUE);
		if("$jour-$mois"=="11-11") return(TRUE);
		if("$jour-$mois"=="25-12") return(TRUE);
		if("$jour-$mois-$anne"==date("j-n-Y",mktime(0,0,0,$paq[1],$paq[0]+39,$paq[2]))) return (TRUE); // Ascension
		if("$jour-$mois-$anne"==date("j-n-Y",mktime(0,0,0,$paq[1],$paq[0]+50,$paq[2]))) return (TRUE); // Pentecôte
	}
	
	public function order($class, $sort) {
		$out = '';
		if (strtolower ( $class ) == strtolower ( $sort )) {
			$out = '<img style="margin: 0; margin-right: 10px; padding:0" src="/images/1downarrow.png" />';
		} else {
			$out = '<img style="margin: 0; margin-right: 5px; padding:0" src="/images/bullet_arrow_down.png" />';
		}
		return $out;
	}
	
	public function getGenreImage($genre, $stock = true) {
		$gifgenre = '';
		switch ($genre) {
			case 1 :
				if($stock){
					$gifgenre = "S_stock.gif";
				}
				break;
			case 2 :
				$gifgenre = "D_demo.gif";
				break;
			case 3 :
				$gifgenre = "B_bourse.gif";
				break;
			case 4 :
				$gifgenre = "O_occasion.gif";
				break;
			case 5 :
				if($stock){
					$gifgenre = "R_remplacement.gif";
				}
				break;
			case 6 :
				$gifgenre = "L_leasing.gif";
				break;
		}
		$out = '';
		if (! empty ( $gifgenre )) {
			$out = '<img class="nomargin" src="/images/' . $gifgenre . '" />';
		}
		return $out;
	}
	
	public function getRepriseOptionsByClass($class, $d, $vars){
		echo $class;
		$options = array (
				'all' => array (
								'Reprise' => array (
									'fichereprise' => "<option value=\"/soulte/fichereprise/id/{$d['id']}\">Voire Reprise</option>",
									'dossiervente' => "<option value=\"/soulte/voir/id/{$d['id']}\">Dossier vente VN</option>",
									'annulereprise' => "<option value=\"/soulte/annulereprise/id/{$d['id']}\">Client annule reprise</option>",
									'supprimerreprise' => "<option value=\"/soulte/supprimerreprise/id/{$d['id']}\">Supprimer reprise</option>",
									'modifierreprise' => "<option value=\"/dossier/reprise/seul/1/id/{$d['id']}\">Modifier Reprise</option>",
									'archiverreprise' => "<option value=\"/soulte/archiverreprise/id/{$d['id']}\">Archiver reprise</option>",
									),
							),
				'valider' => array (
						'valider' => "<option value=\"/soulte/valider/id/{$d['id']}\">Valider dossier</option>"
						),
				'attente_ara' => array (
						'arrivereprise' => "<option value=\"/soulte/arrivereprise/id/{$d['id']}\">Confirmer arriv&eacute;e</option>",
				),
			);
		$tests = array (
				'valider' => array (
						'valider' => array (
								'valider' => in_array ( $vars ['spost'], array (
										6 
								) ) || $vars ['sadmin'] == 't' 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => in_array ( $vars ['spost'], array (
												6,
												13,
												14,
												20 
										) ) || $vars ['sadmin'] == 't',
										'livraison' => in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't' 
								) 
						) 
				),
				'attente_ara' => array (
						'attente_ara' => array (
								'arrivereprise' => true,
						),
						'all' => array (
								true
						)
				),
		);
		
		$tekst = $this->doChecks($options, $class, $tests);
		
		return $tekst;
	}
	
	public function getOptionsByClass($class, $d, $vars) {
		$options = array (
				'all' => array (
						'Voir' => array (
								'ficheclient' => "<option value=\"/client/fiche/id/{$d['id_client']}\">Fiche Client</option>",
								'fichesociete' => "<option value=\"/societe/fiche/id/{$d['id_societe']}\">Fiche Soci&eacute;t&eacute;</option>",
								'dossiervente' => "<option value=\"/soulte/voir/id/{$d['id']}\">Dossier vente</option>" 
						),
						'Reprise' => array (
								'fichereprise' => "<option value=\"/soulte/fichereprise/id/{$d['id']}\">Fiche Reprise</option>",
								'modifierreprise' => "<option value=\"/dossier/reprise/seul/1/id/{$d['id']}\">Modifier Reprise</option>",
								'supprimerreprise' => "<option value=\"/soulte/supprimerreprise/id/{$d['id']}\">Supprimer reprise</option>",
								'arrivereprise' => "<option value=\"/soulte/arrivereprise/id/{$d['id']}\">Confirmer arriv&eacute;e</option>" 
						),
						'Modifier' => array (
								'dossiervente' => "<option value=\"/dossier/update/id/{$d['id']}\">Dossier vente</option>",
								'livraison' => "<option value=\"/soulte/modifcommande/id/{$d['id']}\">Livraison</option>",
								'ficheclient' => "<option value=\"/client/update/id/{$d['client']}\">Fiche Client</option>" ,
								'correction' => "<option value=\"/dossier/update/id/{$d['id']}/correction/1\">Correction</option>" 
						),
						'Rajouter' => array (
								'tache' => "<option value=\"/soulte/ajoutetache/id_soulte/{$d['id']}\">Tache</option>",
								'acompte' => "<option value=\"/dossier/paiement/seul/1/id_soulte/{$d['id']}\">Acompte</option>",
								'accessoires' => "<option value=\"/dossier/accessoires/seul/1/id_soulte/{$d['id']}\">Accessoires</option>" 
						),
						'Lettre' => array (
								'remerciements' => "<option value=\"/lettre/remerciements/id_soulte/{$d['id']}\">de remerciements</option>",
								'livraison' => "<option value=\"/lettre/livraison/id_soulte/{$d['id']}\">de livraison</option>",
								'retard' => "<option value=\"/lettre/retard/id_soulte/{$d['id']}\">de retard</option>" 
						),
						'Imprimer' => array (
								'farde' => "<option value=\"/lettre/fardevn/id_soulte/{$d['id']}\">Farde VN</option>",
								'dossiervente' => "<option value=\"/lettre/dossierdevente/id_soulte/{$d['id']}\">Dossier vente</option>",
								'accessoires' => "<option value=\"/lettre/preparationaccessoires/id_soulte/{$d['id']}\">Fiche Accessoires</option>" 
						),
						'Supprimer' => array (
								'supprimer' => "<option value=\"/soulte/supprimer/id_soulte/{$d['id']}\">Supprimer dossier</option>" 
						) 
				),
				'corriger' => array (
						'corriger' => "<option value=\"/dossier/update/id/{$d['id']}\">Corriger Dossier</option>" 
				),
				'valider' => array (
						'valider' => "<option value=\"/soulte/valider/id/{$d['id']}\">Valider dossier</option>" 
				),
				'commander' => array (
						'commander' => "<option value=\"/soulte/commande/id/{$d['id']}\">Commande VN</option>" 
				),
				'arrivage' => array (
						'retard' => "<option value=\"/lettre/retard/id_soulte/{$d['id']}\">Lettre de retard</option>",
						'dateara' => "<option value=\"/soulte/modifierdateara/id_soulte/{$d['id']}\">Modifier date ARA</option>",
						'traitement' => "<option value=\"/soulte/traitementcmr/id_soulte/{$d['id']}\">Traitement CMR</option>" 
				),
				'preparation' => array (
						'montage' => "<option value=\"/lettre/preparationaccessoires/id_soulte/{$d['id']}\">Montage access.</option>",
						'priserdv' => "<option value=\"/soulte/ajoutetache/tac_id/".$this->priserdvtacheid."/id_soulte/{$d['id']}\">Prise RDV Liv</option>" 
				),
				'livre' => array (
						'welcome' => "<option value=\"/lettre/welcome/id_soulte/{$d['id']}\">Welcome print</option>",
						'livraison' => "<option value=\"/soulte/fichelivraison/id_soulte/{$d['id']}\">Fiche livraison</option>" 
				),
				'relance' => array (
						'relance' => "<option value=\"/soulte/ficherelance/id_soulte/{$d['id']}\">Fiche relance tel.</option>" 
				),
				'cloturer' => array (
						'cloturer' => "<option value=\"/soulte/cloturer/id_soulte/{$d['id']}\">Cl&ocirc;turer dossier</option>" 
				) 
		);
		$tests = array (
				'corriger' => array (
						'corriger' => array (
								'corriger' => in_array ( $vars ['spost'], array (
										6,
										13,
										20 
								) ) || $vars ['sadmin'] == 't' 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2 ,
										'correction' => false 
								) 
						) 
				),
				'valider' => array (
						'valider' => array (
								'valider' => in_array ( $vars ['spost'], array (
										6 
								) ) || $vars ['sadmin'] == 't' 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => in_array ( $vars ['spost'], array (
												6,
												13,
												14,
												20 
										) ) || $vars ['sadmin'] == 't',
										'livraison' => in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't' ,
										'correction' => false 
								) 
						) 
				),
				'commander' => array (
						'commander' => array (
								'commander' => in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't' 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'correction' => false  
								),
								'Supprimer' => (in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't') 
						) 
				),
				'arrivage' => array (
						'arrivage' => array (
								'retard' => true,
								'dateara' => true,
								'traitement' => true 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2 ,
										'correction' => false 
								),
								'Supprimer' => (in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't') 
						) 
				),
				'preparation' => array (
						'preparation' => array (
								'montage' => true,
								'priserdv' => true
							) 
						// weggehaald, want taken kunnen nu overal worden
						// toegevoegd
						// 'priserdv' => true
						,
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2 ,
										'correction' => false 
								),
								'Supprimer' => (in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't') 
						) 
				),
				'livre' => array (
						'livre' => array (
								'welcome' => true,
								'livraison' => true 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2 ,
										'correction' => false 
								),
								'Supprimer' => (in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't') 
						) 
				),
				'relance' => array (
						'relance' => array (
								'relance' => true 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2 ,
										'correction' => false 
								),
								'Supprimer' => (in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't') 
						) 
				),
				'cloturer' => array (
						'cloturer' => array (
								'cloturer' => (in_array ( $vars ['spost'], array (
										14 
								) ) || $vars ['sadmin'] == 't') && $d ['validation'] == 8 
						),
						'all' => array (
								'Voir' => array (
										'fichesociete' => $d ['id_societe'] > 0 
								),
								'Reprise' => $d ['reprise'] == 't',
								'Modifier' => array (
										'dossiervente' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2,
										'livraison' => (in_array ( $vars ['spost'], array (
												6,
												14 
										) ) || $vars ['sadmin'] == 't') && $d ['validation'] > 2 
								),
								'Supprimer' => (in_array ( $vars ['spost'], array (
										6,
										14 
								) ) || $vars ['sadmin'] == 't') 
						) 
				)
		);
		
		$tekst = $this->doChecks($options, $class, $tests);
		
		return $tekst;
	}
	
	private function doChecks($options, $class, $tests){
		$tekst = '';
		
		foreach ( $options [$class] as $key => $o ) {
			if (array_key_exists ( $key, $tests [$class] [$class] ) && $tests [$class] [$class] [$key]) {
				$tekst .= $o;
			} else {
			}
		}
		
		foreach ( $options ['all'] as $key => $o ) {
			if (			// OFWEL MOET HET ALS DE KEY BESTAAT OP TRUE STAAN
					(array_key_exists ( $key, $tests [$class] ['all'] ) && ! is_array ( $tests [$class] ['all'] [$key] ) && $tests [$class] ['all'] [$key]) ||
					// OFWEL EEN ARRAY ZIJN
					(is_array ( $tests [$class] ['all'] [$key] )) ||
					// OFWEL NIET BESTAAN
					(! array_key_exists ( $key, $tests [$class] ['all'] ))) {
				$tekst .= '<optgroup label="' . $key . '">';
				foreach ( $o as $key2 => $o2 ) {
					if (					// OFWEL MOET ALLES TOEGLATEN ZIJN
							(array_key_exists ( $key, $tests [$class] ['all'] ) && ! is_array ( $tests [$class] ['all'] [$key] ) && $tests [$class] ['all'] [$key]) ||
							// OFWEL MOET ALS DE KEY BESTAAT OP TRUE STAAN
							(array_key_exists ( $key, $tests [$class] ['all'] ) && array_key_exists ( $key2, $tests [$class] ['all'] [$key] ) && $tests [$class] ['all'] [$key] [$key2]) ||
							// OFWEL NIET BESTAAN
							(! is_array ( $tests [$class] ['all'] [$key] ) || ! array_key_exists ( $key2, $tests [$class] ['all'] [$key] ))) {
						$tekst .= $o2;
					}
				}
				$tekst .= '</optgroup>';
			}
		}
		
		return $tekst;
	}
}