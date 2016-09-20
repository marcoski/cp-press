<?php
namespace Commonhelp\App\Http;

class StreamResponse extends Response implements CallbackResponse {
	/** @var string */
	private $filePath;
	/**
	 * @param string $filePath the path to the file which should be streamed
	 * @since 8.1.0
	 */
	public function __construct ($filePath) {
		$this->filePath = $filePath;
	}
	/**
	 * Streams the file using readfile
	 *
	 * @param IOutput $output a small wrapper that handles output
	 * @since 8.1.0
	 */
	public function callback (OutputInterface $output) {
		// handle caching
		if ($output->getHttpResponseCode() !== Http::STATUS_NOT_MODIFIED) {
			if (!file_exists($this->filePath)) {
				$output->setHttpResponseCode(Http::STATUS_NOT_FOUND);
			} elseif ($output->setReadfile($this->filePath) === false) {
				$output->setHttpResponseCode(Http::STATUS_BAD_REQUEST);
			}
		}
	}
}