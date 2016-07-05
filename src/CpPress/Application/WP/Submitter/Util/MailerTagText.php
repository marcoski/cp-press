<?php
namespace CpPress\Application\WP\Submitter\Util;

use CpPress\Application\WP\Shortcode\ContactFormShortcodeManager;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Submitter\Submitter;
class MailerTagText{
	
	private $html = false;
	private $content = '';
	private $replacedTags = array();
	
	private $request;
	private $submitter;
	private $postedData = null;
	
	public function __construct($content, Submitter $submitter, RequestInterface $request, $args=''){
		$args = wp_parse_args($args, array('html' => false));
		
		$this->html = (bool) $args['html'];
		$this->content = $content;
		$this->request = $request;
		$this->postedData = $submitter->getData();
		$this->submiter = $submitter;
	}
	
	public function getReplacedTags(){
		return $this->replacedTags;
	}
	
	public function replaceTags(){
		$regex = '/(\[?)\[[\t ]*'
			. '([a-zA-Z_][0-9a-zA-Z:._-]*)' // [2] = name
			. '((?:[\t ]+"[^"]*"|[\t ]+\'[^\']*\')*)' // [3] = values
			. '[\t ]*\](\]?)/';
		
		if($this->html){
			$callback = array($this, 'replaceTagCallbackHtml');
		}else{
			$callback = array($this, 'replaceTagCallback');
		}
		$value = preg_replace_callback($regex, $callback, $this->content);
		return $value;
	}
	
	public function replaceTagCallbackHtml($matches){
		return $this->replaceTagCallback($matches, true);
	}
	
	public function replaceTagCallback($matches, $html=false){
		if($matches[1] == '[' && $matches[4] == ']'){
			return substr($matches[0], 1, -1);
		}
		
		$tag = $matches[0];
		$tagname = $matches[2];
		$values = $matches[3];
		
		if(!empty($values)){
			preg_match_all('/"[^"]*"|\'[^\']*\'/', $values, $matches);
			$values = ContactFormShortcodeManager::stripQuoteDepp($matches[0]);
		}
		
		$doNotHeat = false;
		if(preg_match('/^_raw_(.+)$/', $tagname, $matches)){
			$tagname = trim($matches[1]);
			$doNotHeat = true;
		}
		
		$format = '';
		if(preg_match('/^_format_(.+)$/', $tagname, $matches)){
			$tagname = trim($matches[1]);
			$format = $values[0];
		}
		
		$submitted = $this->postedData;
		if($submitted !== null){
			if($doNotHeat){
				$submitted = !is_null($this->request->getParam($tagname)) ?
					$this->request->getParam($tagname) : '';
			}
			$replaced = $submitted[$tagname];
			
			if(!empty($format)){
				$replaced = $this->format($replaced, $format);
			}
			//$replaced = ContactFormShortcodeManager::flatJoin($replaced);
			if($html){
				$replaced = esc_html($replaced);
				$replaced = wptexturize($replaced);
			}
			$replaced = wp_unslash(trim($replaced));
			$this->replacedTags[$tag] = $replaced;
			return $replaced;
		}
		
		$special = $this->sepcial($tagname, $html);
		if(!empty($special)){
			$this->replacedTags[$tag] = $special;
			return $special;
		}
		
		return $tag;
	}
	
	private function format($original, $format){
		$original = (array) $original;
		
		foreach($original as $key => $value){
			if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value)){
				$original[$key] = mysql2date($format, $value);
			}
		}
		
		return $original;
	}
	
	private function sepcial($tagname, $html){
		if($name == '-remote-ip'){
			if($remoteIp = $this->submitter->getMeta('remote_ip')){
				return $remoteIp;
			}
			
			return '';
		}
		
		if($name == '-user-agent'){
			if($userAgent = $this->submitter->getMeta('user_agent')){
				return $html ? esc_html($userAgent) : $userAgent;
			}
			
			return '';
		}
		
		if($name == '-url'){
			if($url = $this->submitter->getMeta('url')){
				return esc_url($url);
			}
			
			return '';
		}
		
		if($name == '-date' || $name == '-time'){
			if($timestamp = $this->submitter->getMeta('timestamp')){
				if($name == '-date'){
					return date_i18n(get_option('date_format'), $timestamp);
				}
				
				if($name == '-time'){
					return date_i18n(get_option('time_format'), $timestamp);
				}
				
				return '';
			}
		}
		
		if('-post' == substr($name, 0, 6)){
			$unitTag = $this->submitter->getMeta('unit_tag');
			if($unitTag &&
					preg_match('/^cppress-cf(\d+)-p(\d+)-o(\d+)$/', $unitTag, $matches)){
				$postId = absint($matches[2]);
				if($post = get_post($postId)){
					if($name == '-postid'){
						return (string) $post->ID;
					}
					if($name == '-postname'){
						return (string) $post->post_name;
					}
					if($name == '-postitle'){
						return $html ? esc_html($post->post_title) : $post->post_title;
					}
					if($name == '-posturl'){
						return get_permalink($post->ID);
					}
					
					$user = new \WP_User($post->post_author);
					if($name == '-postauthor'){
						return $user->display_name;
					}
					
					if($name = 'postauthoremail'){
						return $user->user_email;
					}
				}
			}
			
			return '';
		}
		
		return '';
	}
	
}