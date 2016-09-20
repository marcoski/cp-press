<?php
namespace Commonhelp\WP;

use Commonhelp\App\Http\TemplateResponse;

class WPTemplateResponse extends TemplateResponse{
	
	public function __construct(WPIController $controller, $templateName){
		$engine = new WPTemplate($controller, $templateName);
		parent::__construct($engine, $controller->getVars());
	}
	
}