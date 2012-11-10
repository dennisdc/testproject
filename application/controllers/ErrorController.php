<?php
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Stream.php';

class ErrorController extends Zend_Controller_Action
{

    /**
     * This action handles
     *    - Application errors
     *    - Errors in the controller chain arising from missing
     *     controller classes and/or action methods
     */
    public function errorAction ()
    {
        $content = null;
        $errors = $this->_getParam ('error_handler') ;
        $exception = $errors->exception;
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                // 404 error -- controller or action not found
                $this->getResponse ()->setRawHeader ( 'HTTP/1.1 404 Not Found' ) ;
                // ... get some output to display...
                $content .= "<h1>404 Page not found!</h1>" . PHP_EOL;
                $content .= "<p>The page you requested was not found.</p>";
                break ;
            default :
                // application error; display error page, but don't change             
                // status code 
                $content .= "<h1>Error!</h1>" . PHP_EOL;
                $content .= $exception->getMessage() . PHP_EOL . $exception->getTraceAsString();
                // ...
				/* online not possible
                // Log the exception
                $exception = $errors->exception;
                $log = new Zend_Log(
                    new Zend_Log_Writer_Stream('log' )
                );
                $log->debug(
                 $exception->getMessage() . PHP_EOL . $exception->getTraceAsString()
                );
                */
                break ;
        }

        // Clear previous content
        $this->getResponse()->clearBody();
        $this->view->content = $content;
    }
}

