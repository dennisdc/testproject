<?php
class Form_Artikel extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('artikel');
		$this->setMethod('post');
		
		// actief
		$actiefElement = new Zend_Form_Element_Checkbox('art_actief');
		$actiefElement->setLabel('Actief')
						->setRequired(true);
						
		// ARTIKELGROEP
		$artgroepElement = new Zend_Form_Element_Select('artgroep_id');
		$groepen = new Model_DbTable_Artikelgroep();
		$fgroepen = $groepen->getArtikelgroepList();
		$artgroepElement->addMultiOption('', '');
		foreach ($fgroepen as $groep){
			$artgroepElement->addMultiOption($groep->artgroep_id, $groep->artgroep_oms);
		}
		$artgroepElement->setLabel('Artikelgroep')
						->addValidator('NotEmpty')
						->setRequired(true);
		
		// NAAM
		$naamElement = new Zend_Form_Element_Text('art_oms');
		$naamElement->setLabel('Naam')
		->setAttrib('size', 100)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);	

		// Aankoopprijs
		$akpElement = new Zend_Form_Element_Text('art_akp');
		$akpElement->setLabel('Aankoopprijs')
		->setAttrib('size', 10)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(false);

		// Verkoopprijs
		$vkpElement = new Zend_Form_Element_Text('art_vkp');
		$vkpElement->setLabel('Verkoopprijs')
		->setAttrib('size', 10)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);		

		// LEVERANCIER
		$btwElement = new Zend_Form_Element_Select('btw_id');
		$btwmodel = new Model_DbTable_Btw();
		$btwElement->addMultiOption('', '');
		foreach ($btwmodel->getBtws() as $b){
			$btwElement->addMultiOption($b->btw_id, $b->btw_waarde);
		}
		$btwElement->setLabel('Btw')
		->addValidator('NotEmpty')
		->setRequired(true);
		
		// LEVERANCIER
		$levElement = new Zend_Form_Element_Select('lev_id');
		$leveran = new Model_DbTable_Leveran();
		$flev = $leveran->getLeveranList();
		$levElement->addMultiOption('', '');
		foreach ($flev as $lev){
			$levElement->addMultiOption($lev->lev_id, $lev->lev_naam);
		}
		$levElement->setLabel('Leverancier')
						->addValidator('NotEmpty')
						->setRequired(true);

		// REF LEV
		$reflevElement = new Zend_Form_Element_Text('art_reflev');
		$reflevElement->setLabel('Referentie leverancier')
		->setAttrib('size', 30)
		->addFilter('StripTags')
		->addfilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(false);	
		
		// SUBMIT
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		
		$this->addElements(array($actiefElement, $artgroepElement, $naamElement, $akpElement, $vkpElement, $btwElement,
								$levElement, $reflevElement, $submit));
	}
}