<?php
namespace CpPress\Application\WP\Asset;

class Styles implements Asset{
	use AssetTrait;
	
	
	private $default = array(
    	'colors','wp-admin','login','install','wp-color-picker','customize-controls',
		'customize-widgets','customize-nav-menus','press-this','ie','buttons','dashicons',
		'open-sans','admin-bar','wp-auth-check','editor-buttons','media-views','wp-pointer',
		'customize-preview','imgareaselect','wp-jquery-ui-dialog','mediaelement','wp-mediaelement',
		'thickbox','media','farbtastic','jcrop','colors-fresh',
	);
	
	public function __construct($base, $child){
		list($this->baseRoot, $this->baseUri) = $base;
		list($this->childRoot, $this->childUri) = $child;
	}
	
	public function register($asset, $deps = array(), $ver = false, $extra = ''){
		if($this->isDefault($asset)){
			throw new CpPressException('Cannot register a default asset');
		}
		$src = $this->getAssetSrc($asset, 'css');
		
		$media = $extra == '' ? 'all' : $extra;
		return wp_register_style($asset, $src, $deps, $ver, $media);
	}
	
	public function enqueue($asset, $deps = array(), $ver = false, $extra = ''){
		$media = $extra == '' ? 'all' : $extra;
		if($this->isRegistered($asset)){
			return wp_enqueue_style($asset, false, $deps, $ver, $media);
		}
		if($this->isDefault($asset)){
			return wp_enqueue_style($asset, false, $deps, $ver, $media);
		}

		$src = $this->getAssetSrc($asset, 'css');
		if(is_null($src)){
			return null;
		}
		
		return wp_enqueue_style($asset, $src, $deps, $ver, $media);
	}
	
	public function enqueueFonts($fonts){
		foreach($fonts as $name => $asset){
			wp_enqueue_style($name, $asset);
		}
	}
	
	public function deregister($asset){
		wp_deregister_style($asset);
	}
	
	public function isRegistered($asset){
		return wp_style_is($asset, 'registered');
	}
	
	public function localize($asset, $objectName, $data){
		throw new CpPressException('localize method is not available for Styles');
	}
	
	public function inline($asset, $data){
		return wp_add_inline_style( $asset, $data );
	}
	
}