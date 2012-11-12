<?php
/**
 * Een klasse om automatisch aan de hand van de tabel een formulier op te bouwen
 *  
 *
 */
class DeCockIct_MainForm extends Zend_Form
{
	/**
	 * 
	 * De database waarvan ik de description kan gaan halen
	 * @var db
	 */
	private $db;
	/**
	 * 
	 * Naam van de tabel waarvoor het formulier dient
	 * @var String
	 */
	private $table;
	/**
	 * 
	 * De description van de tabel, wordt aangemaakt wanneer een tabel wordt toegevoegd
	 * @var Array()
	 */
	private $tabledescription;
	/**
	 * 
	 * Een array met alle elementen die verplicht zijn
	 * @var Array()
	 */
	private $required = array();
	/**
	 * 
	 * Een array met de display groups, is niet verplicht!
	 * @var Array()
	 */
	private $groups;
	/**
	 * 
	 * Een array met alle elementen die niet moeten toegevoegd worden aan het formulier
	 * @var Array()
	 */
	private $ignore = array();
	/**
	 * 
	 * Een array met alle elementen die foreign keys zijn en dus moeten opgevuld worden met waardes
	 * Moet er als volgt uitzien:
	 * een array met 
	 * als key de naam de kolom die een select box moet weergeven
	 * als value een array met 
	 * 		model => het model dat overerft van DeCockIct_MainModel
	 * 		value => de naam van de kolom waarin de waarde staat die moet doorgestuurd worden door het formulier
	 * 		name => de naam van de kolom waarin de waarde staat die moet getoond worden in het formulier
	 * Array(
	 * 		[u_id] => Array(
	 * 					model => new Model_DbTable_Users(),
	 * 					value => 'u_id',
	 * 					name => 'u_name'
	 * 				)
	 * 		...
	 * )
	 * OF
	 * een array met
	 * als key de naam van de kolom die een select box moet weergeven
	 * als value een array met
	 * 		als key de waarde die moet doorgestuurd worden door het formulier
	 * 		als value de waarde die moet getoond worden in het formulier
	 * Array(
	 * 		[u_id] => Array(
	 * 					1 => 'Matias Van de Velde', 
	 * 					2 => 'Dennis de Cock',
	 * 					...
	 * 				),
	 * 		...
	 * )
	 * OF
	 * een array met
	 * als key de naam van de kolom die een select box moet weergeven
	 * als value een array met
	 * 		model => het model dat overerft van DeCockIct_MainModel
	 * 		function => een string met daarin de naam van de functie uit het model dat moet opgeroepen worden
	 * 		parameters => een array met daarin alle parameters die die functie nodig heeft
	 * 		value => de naam van de kolom waarin de waarde staat die moet doorgestuurd worden door het formulier
	 * 		name => de naam van de kolom waarin de waarde staat die moet getoond worden door het formulier
	 * array(
	 * 		[u_id] => Array(
     *   				model => $umodel, 
     *   				function => 'getUserByGroups', 
     *   				parameters => array(array(3,1)), 
     *   				value => 'u_id', 
     *   				name => 'u_naam'
     *   			),
     *   	...
     *  )
	 * @var Array()
	 */
	private $foreignkeys = array();
	/**
	 * 
	 * een array met de nieuwe labels voor sommige kolommen
	 * @var Array()
	 */
	private $labels = array();
	/**
	 * 
	 * Een array met per (datum)element alle extra parameters die moeten worden meegegeven aan de zendx_jquery_datepicker
	 * @var Array()
	 */
	private $dateparameters = array();
	/**
	 * string met daarin de soort van layout die gebruikt wordt
	 * var string
	 */
	private $layout = '';
	
	/**
	 * @return the $layout
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * @param field_type $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

	/**
	 * @return the $dateparameters
	 */
	public function getDateparameters() {
		return $this->dateparameters;
	}

	/**
	 * @param unknown_type $dateparameters
	 */
	public function setDateparameters($dateparameters) {
		$this->dateparameters = $dateparameters;
	}

	/**
	 * @return the $labels
	 */
	public function getLabels() {
		return $this->labels;
	}

	/**
	 * @param Array() $labels
	 */
	public function setLabels($labels) {
		$this->labels = $labels;
	}

	/**
	 * @return the $db
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * @param db $db
	 */
	public function setDb($db) {
		$this->db = $db;
	}

	/**
	 * @return the $ignore
	 */
	public function getIgnore() {
		return $this->ignore;
	}

	/**
	 * @return the $foreignkeys
	 */
	public function getForeignkeys() {
		return $this->foreignkeys;
	}

	/**
	 * @param Array() $foreignkeys
	 */
	public function setForeignkeys($foreignkeys) {
		$this->foreignkeys = $foreignkeys;
	}

	/**
	 * @param Array() $ignore
	 */
	public function setIgnore($ignore) {
		$this->ignore = $ignore;
	}

	/**
	 * @return the $groups
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @param field_type $groups
	 */
	public function setGroups($groups) {
		$this->groups = $groups;
	}

	/**
	 * @return the $table
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @return the $required
	 */
	public function getRequired() {
		return $this->required;
	}

	/**
	 * @param field_type $table
	 */
	public function setTable($table) {
		$this->table = $table;
		if(isset($this->db)){
			$this->tabledescription = $this->db->describeTable($this->table);
		}
		else{
			$this->tabledescription = array();
		}
	}

	/**
	 * @param field_type $required
	 */
	public function setRequired($required) {
		$this->required = $required;
	}
	
	

	/**
	 * @param * db => $this->db (*required)
	 * @param * table => de naam vd tabel (vb: 'users') (*required)
	 * @param * required => een array met de velden die verplicht zijn<br />
	 * @param * ignore => een array met de velden die niet moeten toegevoegd worden aan het formulier<br />
	 * @param * labels => een array met een label voor een bepaalde kolom. (indien niet ingevuld voor een kolom word dit bijvoorbeeld voor u_naam => Naam, en vd_datum_tot => Datum tot)<br />
	 * @param * foreignkeys => een array met de kolommen die dropdown boxes moeten worden. en de waardes of een manier om de waardes te vinden<br />
	 * @param * dateparameters => een array waarin eventuele extra parameters kunnen bijgezet worden voor een jquery datepicker<br />
	 * @param * groups => een array met daarin alle display groups indien gewenst<br />
	 * @param * layout => een string met de soort layout die je wilt (voorlopig enkel niets (= standaard) of table)<br />
	 * 
	 * @example
	 *	$users = array();<br />
	 *	$usermodel = new Model_DbTable_User();<br />
	 *	foreach($usermodel->getUserlist() as $a){<br />
	 *		$users[$a['u_id']] = $a['u_naam'];<br />
	 *	}<br />
	 *	$this->mainform = new DeCockIct_MainForm(array(<br />
	 *						'db' => $this->db, <br />
	 *						'table' => 'vakantiedagen',<br />
	 *						'required' => array('vd_datum', 'vd_datum_tot'),<br />
	 *						'ignore' => array('vd_status', 'vd_opmstatus'),<br />
	 *						'labels' => array('u_id' => 'Gebruiker', 'ved_id' => 'Type', 'vd_opm' => 'Opmerking'),<br />
	 *						'foreignkeys' => array(<br />
	 *										'u_id' => $users,<br /> 
	 *										'ved_id' => array(<br />
	 *														'model' => new Model_DbTable_Verlofdagen(),<br />
	 *														'value' => 'ved_afk',<br />
	 *														'name' => 'ved_oms',<br />
	 *													)),<br />
	 *						'dateparameters' => array('vd_datum_tot' => array('minDate' => '+1')),<br />
	 *						'groups' => array(<br />
	 *										array(<br />
	 *											'elements' => array('u_id','ved_id' ,'vd_datum', 'vd_datum_tot', 'vd_opm'),<br />
	 *											'name' => 'Vakantiedagaanvragen',<br />
	 *											'options' => array('legend' => 'Vakantiedag aanvragen')<br />
	 *										)<br />
	 *									),<br />
	 * 						'layout' => 'table'<br />
	 *	));<br />
	 * @author Matias Van de Velde
	 */
	public function __construct($options = null) {
		parent::__construct($options);
	}

	public function init()
    {
    	parent::init();
		$this->setName($this->table);
		
		foreach($this->tabledescription as $td){
			if(!$td['PRIMARY'] && !in_array($td['COLUMN_NAME'], $this->ignore)){
				$element = new Zend_Form_Element($td['COLUMN_NAME']);
				
				//KIJKEN OF HET ELEMENT REQUIRED IS
				if(in_array($td['COLUMN_NAME'], $this->required)){
					$required = true;
				}
				else{
					$required = false;
				}
				
				if(array_key_exists($td['COLUMN_NAME'], $this->foreignkeys)){
					$element = $this->makeSelectBox($td);
				}
				else{
					//AFHANKELIJK VAN HET DATA TYPE EEN ELEMENT AANMAKEN
					switch($td['DATA_TYPE']){
						case 'int':
							$element = new Zend_Form_Element_Text($td['COLUMN_NAME']);
							$element->addValidator('Int');
							if(!empty($td['LENGTH'])){
								$element->addValidator('StringLength', false, array(0, $td['LENGTH']));
							}
							$element = $this->addAjaxValidation('blur', $element);
							;break;
						case 'mediumtext':
						case 'varchar':
							if($td['LENGTH'] > 150 || $td['DATA_TYPE'] == 'mediumtext'){
								$element = new Zend_Form_Element_Textarea($td['COLUMN_NAME']);
							}
							else{
								$element = new Zend_Form_Element_Text($td['COLUMN_NAME']);
							}
							if(in_array($td['COLUMN_NAME'], $this->required)){
								$element->addValidator('StringLength', false, array(1, $td['LENGTH']));
							}
							else{
								$element->addValidator('StringLength', false, array(0, $td['LENGTH']));
							}
							$element->addFilter('StringTrim');
							$element = $this->addAjaxValidation('blur', $element);
							;break;
						case 'date':
							if(isset($this->dateparameters[$td['COLUMN_NAME']])){
								$dateparams = $this->dateparameters[$td['COLUMN_NAME']];
								if(!isset($dateparams['dateFormat'])){
									$dateparams['dateFormat'] = 'dd-mm-yy';
								}
							}
							else{
								$dateparams = array('dateFormat' => 'dd-mm-yy');
							}
							$element = new ZendX_JQuery_Form_Element_DatePicker($td['COLUMN_NAME'], array('jQueryParams' => $dateparams));
							$element = $this->addAjaxValidation('change', $element);
							;break;
						case 'time':
							$element = new Zend_Form_Element_Select($td['COLUMN_NAME']);
							$element->addMultiOption('','');
							for($i = 7; $i <= 20; $i++){
								for($j = 0; $j <= 3; $j++){
									$element->addMultiOption(str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . str_pad(($j*15), 2, '0', STR_PAD_LEFT) . ':00', str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . str_pad(($j*15), 2, '0', STR_PAD_LEFT));
								}
							}
							$element = $this->addAjaxValidation('change', $element);
							break;
						case 'tinyint':
							$element = new Zend_Form_Element_Checkbox($td['COLUMN_NAME']);
							;break;
						case 'decimal':
	//						[SCALE] => 2, [PRECISION] => 5
							$element = new Zend_Form_Element_Text($td['COLUMN_NAME']);
							$element->addValidator('Float');
							$floatvalidator = new Zend_Validate_Float(array('locale' => 'be'));
							if(!empty($td['PRECISION'])){
								$element->addValidator('StringLength', false, array(0, ($td['PRECISION']+1)));
							}
							$element = $this->addAjaxValidation('blur', $element);
							;break;
						default:
							if(substr($td['DATA_TYPE'], 0,4) == 'enum'){
								$element = new Zend_Form_Element_Select($td['COLUMN_NAME']);
								$element->addMultiOption('','');
								$array = substr($td['DATA_TYPE'], 5, strlen($td['DATA_TYPE']) - 6);
								$ex = explode('\',\'', $array);
								
								$i = 0;
								foreach($ex as $e){
									$hulp = trim($e, '\'');
									$hulp2 = str_ireplace('\'\'', '\'', $hulp);
									$element->addMultiOption($hulp2, $hulp2);
								}
								$element = $this->addAjaxValidation('change', $element);
							}
							;break;
					}
				}
				//LABEL ZETTEN => is de naam van de kolom zonder de eerste x-aantal karakters en _ (dus voor bijvoorbeeld u_id => id)
				
				$explode = explode('_', $td['COLUMN_NAME'], 2);
				if(isset($this->labels[$td['COLUMN_NAME']])){
					$element->setLabel($this->labels[$td['COLUMN_NAME']]);
				}
				else{
					$element->setLabel(ucfirst(str_ireplace('_', ' ', $explode[1])));
				}
				//ALS HET REQUIRED IS REQUIRED OP TRUE ZETTEN
				if($required){
					$element->setRequired(true);
					$element->addValidator('NotEmpty');
				}
				
				$element = $this->addLayout($element);
				
				$this->addElement($element);
			}
		}
			
		//DE SUBMIT KNOP MAKEN EN TOEVOEGEN
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setAttrib('class', 'art-button');
		$this->addLayout($submit);
		$this->addElement($submit);
		
		//DE DISPLAYGROUPS INVULLEN
		if(count($this->groups) > 0){
			$this->addDisplayGroups($this->groups);
			$this->addDisplayGroup(array('submit'), 'submitdg');
			
			foreach($this->getDisplayGroups() as $d){
	    		$d->setDecorators(array(
	               'FormElements',
	               array(
	               		array('data'=>'HtmlTag'),
	               		array('tag'=>'table', 'class' => 'noborder mw2')
	               	),
	               'Fieldset'
	               ));
	    	}
		}
	}
	
	private function makeSelectBox($td){
		if(isset($this->foreignkeys[$td['COLUMN_NAME']]['type']) && $this->foreignkeys[$td['COLUMN_NAME']]['type'] == 'multicheckbox'){
			$element = new Zend_Form_Element_MultiCheckbox($td['COLUMN_NAME']);
		}
		else{
			$element = new Zend_Form_Element_Select($td['COLUMN_NAME']);
			$element->addMultiOption('','');
		}
		if(isset($this->foreignkeys[$td['COLUMN_NAME']]['model']) && !empty($this->foreignkeys[$td['COLUMN_NAME']]['model'])){
			$values = array();
			$valuekolom = $this->foreignkeys[$td['COLUMN_NAME']]['value'];
			$namekolom = $this->foreignkeys[$td['COLUMN_NAME']]['name'];
			if(isset($this->foreignkeys[$td['COLUMN_NAME']]['function']) && !empty($this->foreignkeys[$td['COLUMN_NAME']]['function'])){
				$function = $this->foreignkeys[$td['COLUMN_NAME']]['function'];
				$params = $this->foreignkeys[$td['COLUMN_NAME']]['parameters'];
			}
			else{
				$function = 'getAllRecords';
				$params = array();
			}
			foreach(call_user_func_array(array($this->foreignkeys[$td['COLUMN_NAME']]['model'], $function), $params) as $v){
				$values[$v[$valuekolom]] = $v[$namekolom];
			}
		}
		else{
			$values = $this->foreignkeys[$td['COLUMN_NAME']];
		}
		
		foreach($values as $key => $v){
			$element->addMultiOption($key, $v);
		}
		$element = $this->addAjaxValidation('change', $element);
		return $element;
	}
	
	public function addLayout($element){
		switch($this->layout){
			case 'table':
				if(empty($this->groups)){
					$this->setDecorators(array(
		               'FormElements',
		               array(
		               		array('data'=>'HtmlTag'),
		               		array('tag'=>'table', 'class' => 'noborder')
		               	),
		               'Form'));
				}
				
				if($element->getType() == 'Zend_Form_Element_File'){
					
				}
				elseif($element->getType() == 'Zend_Form_Element_Submit'){
					$element->setDecorators(array(
						'ViewHelper',
	               		'Description',
	               		'Errors', 
						array(
							array('data'=>'HtmlTag'), 
							array('tag' => 'td',
	               				'colspan'=>'2','class'=>'right'
							)
						),
	               		array(
	               			array('row'=>'HtmlTag'),
	               			array('tag'=>'tr'))));
				}
				elseif($element->getType() == 'ZendX_JQuery_Form_Element_DatePicker'){
					$element->setDecorators(array(
							array('UiWidgetElement', array('tag' => '')),
							'Description',
							'Errors', 
							array(
								array(
									'data'=>'HtmlTag'
								), 
								array(
									'tag' => 'td'
								)
							),
                   			array('Label', array('tag' => 'td')),
                   			array(
                   				array(
                   					'row'=>'HtmlTag'
                   				),
                   				array(
                   					'tag'=>'tr', 'class' => 'middle'
                   				)
                   			)));
				}
				else{
					$element->setDecorators(array(
							'ViewHelper',
							'Description',
							'Errors', 
							array(
								array(
									'data'=>'HtmlTag'
								), 
								array(
									'tag' => 'td'
								)
							),
                   			array('Label', array('tag' => 'td')),
                   			array(
                   				array(
                   					'row'=>'HtmlTag'
                   				),
                   				array(
                   					'tag'=>'tr', 'class' => 'middle'
                   				)
                   			)));
				}
				;break;
		}
		
		return $element;
	}
	
	private function addAjaxValidation($event, $element){
		return $element->setAttrib('on'.$event, 'checkElement(this)');
	}
}