<?php

class Form_Login extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('login');
		
		$emailElement = new Zend_Form_Element_Text('u_email');
		$emailElement->setLabel('Gebruikersnaam')
				->setAttrib('size', 30)
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('EmailAddress');

		$passwordElement = new Zend_Form_Element_Password('u_paswoord');
		$passwordElement->setLabel('Paswoord')
				->setAttrib('size', 30)
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($emailElement, $passwordElement, $submit));
		
	}
}