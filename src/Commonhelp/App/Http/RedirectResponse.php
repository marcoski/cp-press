<?php
namespace Commonhelp\App\Http;

class RedirectResponse extends Response{
	
	private $redirectURL;
	
	
	public function __construct($redirectURL) {
		$this->redirectURL = $redirectURL;
		$this->setStatus(Http::STATUS_TEMPORARY_REDIRECT);
		$this->addHeader('Location', $redirectURL);
	}
	
	public function getRedirectURL() {
		return $this->redirectURL;
	}
}