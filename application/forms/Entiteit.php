<?php

class Form_Entiteit extends Zend_Form
{
	protected $ent_id;
	
	/**
	 * @return the $ent_id
	 */
	public function getEnt_id() {
		return $this->ent_id;
	}

	/**
	 * @param field_type $ent_id
	 */
	public function setEnt_id($ent_id) {
		$this->ent_id = $ent_id;
	}

	public function init(){
		$this->setName('login');
		$this->setAction('/entiteit/update');
		
		$naamElement = new Zend_Form_Element_Text('ent_naam');
		$naamElement->setLabel('Naam')
		->setAttrib('size', 30)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$adresElement = new Zend_Form_Element_Text('ent_adres');
		$adresElement->setLabel('Adres')
		->setAttrib('size', 30)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$nrElement = new Zend_Form_Element_Text('ent_nummer');
		$nrElement->setLabel('Nummer')
		->setAttrib('size', 5)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$pcElement = new Zend_Form_Element_Text('ent_postcode');
		$pcElement->setLabel('Postcode')
		->setAttrib('size', 10)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$wpElement = new Zend_Form_Element_Text('ent_woonplaats');
		$wpElement->setLabel('Woonplaats')
		->setAttrib('size', 30)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$orgElement = new Zend_Form_Element_Text('ent_btw');
		$orgElement->setLabel('Ondernemingsnummer')
		->setAttrib('size', 20)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$telElement = new Zend_Form_Element_Text('ent_tel');
		$telElement->setLabel('Telefoon')
		->setAttrib('size', 30)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty');
		
		$emailElement = new Zend_Form_Element_Text('ent_email');
		$emailElement->setLabel('Email')
		->setAttrib('size', 30)
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->addValidator('EmailAddress');
		
		// 		$passwordElement = new Zend_Form_Element_Text('ent_logo');
		// 		$passwordElement->setLabel('Paswoord')
		// 				->setAttrib('size', 30)
		// 				->setRequired(true)
		// 				->addFilter('StripTags')
		// 				->addFilter('StringTrim')
		// 				->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($naamElement, $adresElement, $nrElement, $pcElement, $wpElement, $orgElement, $telElement, $emailElement, $submit));
	}
}