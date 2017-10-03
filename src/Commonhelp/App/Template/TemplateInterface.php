<?php
namespace Commonhelp\App\Template;

use Commonhelp\App\AbstractController;

interface TemplateInterface{
	public static function renderError(AbstractController $controller, $error_msg);
	public static function renderException(AbstractController $controller, \Exception $exception);
	
}