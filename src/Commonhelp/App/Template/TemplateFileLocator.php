<?php
namespace Commonhelp\App\Template;


use Commonhelp\App\Exception\TemplateNotFoundException;
use Commonhelp\Util\Inflector;
class TemplateFileLocator {

	protected $dirs;
	private $path;
	
	private $extension;

	/**
	 * @param string[] $dirs
	 */
	public function __construct( $dirs, $extension = '.php' ) {
		$this->dirs = $dirs;
		$this->extension = $extension;
	}

	/**
	 * @param string $template
	 * @return string
	 * @throws \Exception
	 */
	public function find( $template ) {
		if ($template === '') {
			throw new \InvalidArgumentException('Empty template name');
		}
		foreach($this->dirs as $dir) {
			$file = $dir.$template. $this->extension;
			if (is_file($file)) {
				$this->path = $dir;
				return $file;
			}
		}
		throw new TemplateNotFoundException(
				'template not found template: '.$template.'. ' . $this->extension . ' in '.print_r($this->dirs, true)
		);

	}

	public function getPath() {
		return $this->path;
	}

	public static function getAppFolder($app){
		$app = Inflector::underscore($app);

		return substr($app, 0, strlen($app)-4);
	}
}
