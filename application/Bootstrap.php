<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath' => APPLICATION_PATH));
		return $moduleLoader;
	}
	
	protected function _initViewHelpers(){
		$view = new Zend_View();
		$view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');  
		$view->jQuery()->addStylesheet('/css/custom-theme/jquery-ui-1.8rc3.custom.css')
						->setLocalPath('/js/jquery/jquery-1.4.2.min.js')
						->setUiLocalPath('/js/jquery/jquery-ui-1.8rc3.custom.min.js')
						->addJavascriptFile('/js/datatables/jquery.dataTables.js')
						->addJavascriptFile('/js/ddcerp.js');
		$viewrenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewrenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewrenderer);
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->headTitle()->setSeparator(' - ');
		$view->headTitle('ddcErp');
	}
	
	protected function _initConfiguration()
	{
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		date_default_timezone_set('Europe/Brussels');
		Zend_Registry::set('config', $config);
		$locale = new Zend_Locale('nl_BE');
		Zend_Registry::set('Zend_Locale', $locale);
		date_default_timezone_set('Europe/Berlin');
		Zend_Session::start();
	}
}