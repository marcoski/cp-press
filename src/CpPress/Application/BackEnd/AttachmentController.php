<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Admin\PostMeta;

class AttachmentController extends WPController{

	
	public function featured($files, $validMime){
		if($files == ''){
			$files = array();
		}

		$this->assign('validMime', htmlspecialchars($validMime));
		$this->assign('json_files', htmlspecialchars(json_encode($files, JSON_HEX_TAG)));
	}
	
}