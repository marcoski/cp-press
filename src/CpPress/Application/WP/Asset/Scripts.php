<?php
namespace CpPress\Application\WP\Asset;
use CpPress\Exception\CpPressException;

class Scripts implements Asset{
	use AssetTrait;
	
	private $no = array(
			'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-ui-accordion',
			'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-ui-dialog',
			'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse',
			'jquery-ui-position', 'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable',
			'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-ui-spinner', 'jquery-ui-tabs',
			'jquery-ui-tooltip', 'jquery-ui-widget', 'underscore', 'backbone',
	);
	
	private $default = array(
    	'utils','common','wp-a11y','sack','quicktags','colorpicker','editor','wp-fullscreen-stub',
		'wp-ajax-response','wp-pointer','autosave','heartbeat','wp-auth-check','wp-lists',
		'prototype','scriptaculous-root','scriptaculous-builder','scriptaculous-dragdrop',
		'scriptaculous-effects','scriptaculous-slider','scriptaculous-sound','scriptaculous-controls',
		'scriptaculous','cropper','jquery','jquery-core','jquery-migrate','jquery-ui-core','jquery-effects-core',
		'jquery-effects-blind','jquery-effects-bounce','jquery-effects-clip','jquery-effects-drop',
		'jquery-effects-explode','jquery-effects-fade','jquery-effects-fold','jquery-effects-highlight',
		'jquery-effects-puff','jquery-effects-pulsate','jquery-effects-scale','jquery-effects-shake',
		'jquery-effects-size','jquery-effects-slide','jquery-effects-transfer','jquery-ui-accordion',
		'jquery-ui-autocomplete','jquery-ui-button','jquery-ui-datepicker','jquery-ui-dialog','jquery-ui-draggable',
		'jquery-ui-droppable','jquery-ui-menu','jquery-ui-mouse','jquery-ui-position','jquery-ui-progressbar',
		'jquery-ui-resizable','jquery-ui-selectable','jquery-ui-selectmenu','jquery-ui-slider','jquery-ui-sortable',
		'jquery-ui-spinner','jquery-ui-tabs','jquery-ui-tooltip','jquery-ui-widget','jquery-form','jquery-color',
		'suggest','schedule','jquery-query','jquery-serialize-object','jquery-hotkeys','jquery-table-hotkeys','jquery-touch-punch',
		'masonry','jquery-masonry','thickbox','jcrop','swfobject','plupload','plupload-all','plupload-html5','plupload-flash',
		'plupload-silverlight','plupload-html4','plupload-handlers','wp-plupload','swfupload','swfupload-swfobject',
		'swfupload-queue','swfupload-speed','swfupload-all','swfupload-handlers','comment-reply','json2','underscore',
		'backbone','wp-util','wp-backbone','revisions','imgareaselect','mediaelement','wp-mediaelement','froogaloop',
		'wp-playlist','zxcvbn-async','password-strength-meter','user-profile','language-chooser','user-suggest','admin-bar',
		'wplink','wpdialogs','word-count','media-upload','hoverIntent','customize-base','customize-loader','customize-preview',
		'customize-models','customize-views','customize-controls','customize-widgets','customize-preview-widgets','customize-nav-menus',
		'customize-preview-nav-menus','accordion','shortcode','media-models','media-views','media-editor','media-audiovideo','mce-view'
	);
	
	public function __construct($base, $child){
		list($this->baseRoot, $this->baseUri) = $base;
		list($this->childRoot, $this->childUri) = $child;
	}
	
	public function register($asset, $deps = array(), $ver = false, $extra = ''){
		if($this->isDefault($asset)){
			throw new CpPressException('Cannot register a default asset');
		}
		$src = $this->getAssetSrc($asset, 'js');
		
		$inFooter = $extra == '' ? false : true;
		return wp_register_script($asset, $src, $deps, $ver, $inFooter);
	}
	
	public function deregister($asset){
		wp_deregister_script($asset);
	}
	
	public function enqueue($asset, $deps = array(), $ver = false, $extra = ''){
		$inFooter = $extra == '' ? false : $extra;
		if ($this->isRegistered($asset)){
			return wp_enqueue_script($asset, false, $deps, $ver, $inFooter);
		}
		$src = $this->getAssetSrc($asset, 'js');
		if(is_null($src)){
			return null;
		}
		
		return wp_enqueue_script($asset, $src, $deps, $ver, $inFooter);
	}
	
	public function isRegistered($asset){
		return wp_script_is($asset, 'registered');
	}
	
	public function localize($asset, $objectName, $data){
		return wp_localize_script($asset, $objectName, $data);
	}
	
	public function inline($asset, $data){
		throw new CpPressException('inline method is not available for Scripts');
	}
	
	
	
}