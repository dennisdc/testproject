<?php
class Form_EntiteitLogo extends Zend_Form
{
	public function init()
	{
		$this->setName('entiteitlogo');
		$this->setMethod('post');
		
		// file
		$fileElement = new Zend_Form_Element_File('ent_logo');
		$fileElement->setRequired(true);

		// SUBMIT
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($fileElement ));
		$this->addElement($submit);
		
	}
}