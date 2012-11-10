<?php
class Form_Af extends ZendX_JQuery_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('af');
		$this->setMethod('post');
		
		// datum vanaf
		$datumElement = new ZendX_JQuery_Form_Element_DatePicker('af_dat', array('jQueryParams' => array('dateFormat' => 'dd-mm-yy')));
		$datumElement->setLabel('Factuurdatum')
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(true);

		// datum vanaf
		$vervaldatElement = new ZendX_JQuery_Form_Element_DatePicker('af_vervaldat', array('jQueryParams' => array('dateFormat' => 'dd-mm-yy')));
		$vervaldatElement->setLabel('Vervaldatum')
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->setRequired(true);
								
		// LEVERANCIER
		$levElement = new Zend_Form_Element_Select('lev_id');
		$leveran = new Model_DbTable_Leveran();
		$flev = $leveran->getLeveranList();
		foreach ($flev as $lev){
			$levElement->addMultiOption($lev->lev_id, $lev->lev_naam);
		}
		$levElement->setLabel('Leverancier')
						->addValidator('NotEmpty')
						->setRequired(true);

		// AANKOOPREKENING
		$afrekElement = new Zend_Form_Element_Select('afrek_id');
		$afrek = new Model_DbTable_Afrek();
		$fafrek = $afrek->getAfrekList();
		foreach ($fafrek as $afrek){
			$afrekElement->addMultiOption($afrek->afrek_id, $afrek->afrek_oms);
		}
		$afrekElement->setLabel('Aankooprekening')
						->addValidator('NotEmpty')
						->setRequired(true);
																
		// REF
		$refElement = new Zend_Form_Element_Text('af_ref');
		$refElement->setLabel('Referentie')
		->setAttrib('size', 50)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(false);	

		// OMSCHRIJVING
		$omsElement = new Zend_Form_Element_Text('af_oms');
		$omsElement->setLabel('Omschrijving')
		->setAttrib('size', 75)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		// bedrag
		$bedragElement = new Zend_Form_Element_Text('af_bedrag');
		$bedragElement->setLabel('Bedrag')
		->setAttrib('size', 10)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);		
		
		// SUBMIT
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($datumElement, $vervaldatElement, $levElement, $afrekElement, $refElement, $omsElement, $bedragElement, $submit));
	}
}