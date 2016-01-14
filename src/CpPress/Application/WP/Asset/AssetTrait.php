<?php
namespace CpPress\Application\WP\Asset;

use CpPress\Exception\AssetNotFoundException;
use CpPress\CpPress;

trait AssetTrait{
	
	private $baseUri;
	private $childUri;
	
	private $baseRoot;
	private $childRoot;
	
	public function getBaseUri(){
		return $this->baseUri;
	}
	
	public function getChildUri(){
		return $this->childUri;
	}
	
	public function getAssetSrc($asset, $type){
		if(!$this->isDefault($asset)){
			$src = $this->baseUri.'/'.$type.'/'.$asset.'.'.$type;
			$path = $this->baseRoot.'/'.$type.'/'.$asset.'.'.$type;
			
			if(is_file($path)){
				return $src;
			}
			$src = $this->childUri.'/'.$type.'/'.$asset.'.'.$type;
			$path = $this->childRoot.'/'.$type.'/'.$asset.'.'.$type;
			
			if(is_file($path)){
				return $src;
			}
			$src = plugins_url('/assets/'.$type.'/'.$asset.'.'.$type, dirname(dirname(CpPress::$FILE)));
			$path = dirname(dirname(dirname(CpPress::$FILE))).'/assets/'.$type.'/'.$asset.'.'.$type;
			
			if(is_file($path)){
				return $src;
			}
			$message = sprintf( __( 'Asset %1$s not found in: %2$s or %3$s or %4$s' ),
					"<code>$asset</code>", "<code>$this->baseRoot</code>", "<code>$this->childRoot</code>", "<code>".dirname(dirname(dirname(CpPress::$FILE)))."/assets/</code>");
			throw new AssetNotFoundException($message);
		}
		
		return null;
	}
	
	public function isRegistered($asset){
		return $this->is($asset, 'registered');
	}
	
	public function isQueued($asset){
		return $this->is($asset);
	}
	
	public function isDefault($asset){
		return in_array($asset, $this->default);
	}
	
	public function is($asset, $list = 'enqueued'){
		return (bool) $this->query($asset, $list);
	}
	
	public function dequeue($asset){
		parent::dequeue($asset);
	}
	
}