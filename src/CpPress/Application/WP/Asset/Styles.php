<?php
namespace CpPress\Application\WP\Asset;

class Styles extends \WP_Styles implements Asset{
	use AssetTrait;
	
	
	private $default = array(
    	'colors','wp-admin','login','install','wp-color-picker','customize-controls',
		'customize-widgets','customize-nav-menus','press-this','ie','buttons','dashicons',
		'open-sans','admin-bar','wp-auth-check','editor-buttons','media-views','wp-pointer',
		'customize-preview','imgareaselect','wp-jquery-ui-dialog','mediaelement','wp-mediaelement',
		'thickbox','media','farbtastic','jcrop','colors-fresh',
	);
	
	public function __construct($base, $child){
		parent::__construct();
		list($this->baseRoot, $this->baseUri) = $base;
		list($this->childRoot, $this->childUri) = $child;
	}
	
	public function register($asset, $deps = array(), $ver = false, $extra = false){
		if($this->isDefault($asset)){
			throw new CpPressException('Cannot register a default asset');
		}
		if(!$extra){
			$extra = 'all';
		}
		$src = $this->getAssetSrc($asset, 'css');
		return $this->add($asset, $src, $deps, $ver, $extra);
	}
	
	public function enqueue($asset, $deps = array(), $ver = false, $extra = false){
		if(!$extra){
			$extra = 'all';
		}
		if(!$this->isRegistered($asset)){
			$src = $this->getAssetSrc($asset, 'css');
			$_asset = explode('?', $asset);
			$this->add( $_asset[0], $src, $deps, $ver, $extra );
		}
		parent::enqueue($asset);
	}
	
	public function deregister($asset){
		$this->remove($asset);
	}
	
	public function localize($asset, $objectName, $data){
		throw new CpPressException('localize method is not available for Styles');
	}
	
	public function inline($asset, $data){
		if(false !== stripos( $data, '</style>' )){
			$data = trim(preg_replace('#<style[^>]*>(.*)</style>#is', '$1', $data));
		}
		
		return $this->add_inline_style( $handle, $data );
	}
	
}