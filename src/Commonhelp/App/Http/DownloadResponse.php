<?php
namespace Commonhelp\App\Http;

class DownloadResponse extends Response {
	
	private $filename;
	private $contentType;
	
	
	public function __construct($filename, $contentType) {
		$this->filename = $filename;
		$this->contentType = $contentType;
		$this->addHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
		$this->addHeader('Content-Type', $contentType);
	}
}