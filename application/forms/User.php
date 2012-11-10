<?php

class Form_User extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('user');
		$this->setMethod('post');
		
		$usernameElement = new Zend_Form_Element_Text('u_naam');
		$usernameElement->setLabel('Gebruikersnaam')
				->setRequired(true)
				->setAttrib('size', 50)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('stringLength', false, array(4,20));
		
		$emailElement = new Zend_Form_Element_Text('u_email');
		$emailElement->setLabel('E-mail')
				->setRequired(true)
				->setAttrib('size', 50)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('EmailAddress');
				
		$passwordElement = new Zend_Form_Element_Password('u_paswoord');
		$passwordElement->setLabel('Paswoord')
				->setRequired(true)
				->setAttrib('size', 50)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength', false, array(6,20))
				->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($usernameElement, $emailElement, $passwordElement, $submit));
		
	}
}