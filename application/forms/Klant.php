<?php
class Form_Klant extends ZendX_JQuery_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('klant');
		$this->setMethod('post');
		
		// actief
		$actiefElement = new Zend_Form_Element_Checkbox('kl_actief');
		$actiefElement->setLabel('Actief')
						->setRequired(true);
						
		// TAAL
		$taalcodeElement = new Zend_Form_Element_Radio('kl_taal');
    	$taalcodeElement->addMultiOption('NL', 'NL')
    					->addMultiOption('FR', 'FR')
    					->addMultiOption('EN', 'EN');
		$taalcodeElement->setLabel('Taal')
						->addValidator('NotEmpty')
						->setRequired(true);
		
		// NAAM 1
		$naam1Element = new Zend_Form_Element_Text('kl_naam1');
		$naam1Element->setLabel('Naam')
		->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		// NAAM 2
		$naam2Element = new Zend_Form_Element_Text('kl_naam2');
		$naam2Element->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);

		// jur vorm
		$jurvormElement = new Zend_Form_Element_Select('kl_jurvorm');
    	$jurvormElement->addMultiOption('BVBA', 'BVBA')
    					->addMultiOption('NV', 'NV')
    					->addMultiOption('EZ', 'EZ')
    					->addMultiOption('', '');
		$jurvormElement->setLabel('Jur.vorm')
						->setRequired(false);		

						
		// ADRES 1
		$adres1Element = new Zend_Form_Element_Text('kl_adres1');
		$adres1Element->setLabel('Adres')
		->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		// ADRES 2
		$adres2Element = new Zend_Form_Element_Text('kl_adres2');
		$adres2Element->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
		
		// POSTCODE
		$postElement = new Zend_Form_Element_Text('kl_post');
		$postElement->setLabel('Postcode')
		->setAttrib('size', 10)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);

		// WOONPLAATS
		$woonElement = new Zend_Form_Element_Text('kl_woon');
		$woonElement->setLabel('Woonplaats')
		->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);

		// LAND
		$landElement = new Zend_Form_Element_Text('kl_land');
		$landElement->setLabel('Land')
		->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
				
		// BTW NUMMER
		$btwElement = new Zend_Form_Element_Text('kl_btw');
		$btwElement->setLabel('Ondernemingsnummer')
		->setAttrib('size', 20)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);

		// TEL1
		$tel1Element = new Zend_Form_Element_Text('kl_tel1');
		$tel1Element->setLabel('Tel. 1')
		->setAttrib('size', 25)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
		
		// TEL2
		$tel2Element = new Zend_Form_Element_Text('kl_tel2');
		$tel2Element->setLabel('Tel. 2')
		->setAttrib('size', 25)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
				
		// GSM
		$gsmElement = new Zend_Form_Element_Text('kl_gsm');
		$gsmElement->setLabel('GSM')
		->setAttrib('size', 25)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
		
		// FAX1
		$fax1Element = new Zend_Form_Element_Text('kl_fax1');
		$fax1Element->setLabel('Fax 1')
		->setAttrib('size', 25)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);	

		// FAX2
		$fax2Element = new Zend_Form_Element_Text('kl_fax2');
		$fax2Element->setLabel('Fax 2')
		->setAttrib('size', 25)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);

		// EMAIL
		$emailElement = new Zend_Form_Element_Text('kl_email');
		$emailElement->setLabel('E-mail')
		->setAttrib('size', 50)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
		
		// WEBSITE
		$websiteElement = new Zend_Form_Element_Text('kl_website');
		$websiteElement->setLabel('Website')
		->setAttrib('size', 75)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
		
		// UURTARIEF
		$uurtarElement = new Zend_Form_Element_Text('kl_uurtarief');
		$uurtarElement->setLabel('Uurtarief')
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);
		
		// KM
		$kmElement = new Zend_Form_Element_Text('kl_aantkm');
		$kmElement->setLabel('KM enkel')
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);		

		// BETALINGSTERMIJN
		$betElement = new Zend_Form_Element_Text('kl_betterm');
		$betElement->setLabel('Betalingstermijn')
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(false);		
		
		// SUBMIT
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($actiefElement, $taalcodeElement, $naam1Element, $naam2Element, $jurvormElement, $adres1Element,
							$adres2Element, $postElement, $woonElement, $landElement, $btwElement, $tel1Element, $tel2Element,
							$gsmElement, $fax1Element, $fax2Element, $emailElement, $websiteElement, $uurtarElement, $kmElement,
							$betElement, $submit));
	}
}