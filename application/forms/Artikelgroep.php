<?php
class Form_Artikelgroep extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('artikelgroep');
		$this->setMethod('post');
		
		// NAAM 1
		$naamElement = new Zend_Form_Element_Text('artgroep_oms');
		$naamElement->setLabel('Naam groep')
		->setAttrib('size', 50)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		// SUBMIT
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($naamElement, $submit));
	}
}