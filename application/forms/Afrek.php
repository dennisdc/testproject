<?php
class Form_Afrek extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('afrek');
		$this->setMethod('post');
		
		// NAAM 1
		$naamElement = new Zend_Form_Element_Text('afrek_oms');
		$naamElement->setLabel('Omschrijving')
		->setAttrib('size', 75)
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