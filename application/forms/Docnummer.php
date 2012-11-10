<?php
class Form_Docnummer extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('leveran');
		$this->setMethod('post');
		
		// NAAM 1
		$naamElement = new Zend_Form_Element_Text('doc_oms');
		$naamElement->setLabel('Omschrijving')
		->setAttrib('size', 3)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);
		
		// WAARDE
		$nrElement = new Zend_Form_Element_Text('doc_nr');
		$nrElement->setLabel('Nummer')
		->setAttrib('size', 10)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);		

		// SUBMIT
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($naamElement, $nrElement, $submit));
	}
}