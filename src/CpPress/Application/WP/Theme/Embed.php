<?php
namespace CpPress\Application\WP\Theme;

use WP_Embed;
use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\Util\OEmbed;

class Embed extends WP_Embed{
	
	private $oembed;
	private $filter;
	
	private $cache;
	private $cachedRecently;
	private $cacheTime;
	private $cacheKey;
	private $cachekeyTime;
	
	private $post;
	
	public function __construct(Request $request, Filter $filter){
		$this->oembed = new OEmbed($request);
		$this->filter = $filter;
		$this->usecache = false;
		$this->cache = null;
		$this->cachedRecently = false;
		$this->cacheTime = 0;
		$this->cacheKey = null;
		$this->post = get_post();
		$this->post_ID = $this->post->ID;
		$this->cachekeyTime = null;
	}
	
	public function cache($attr, $url, $key){
		// Check for a cached result (stored in the post meta)
		$keySuffix = md5($url . serialize($attr));
		$this->cacheKey = $key . $keySuffix;
		$this->cachekeyTime = $key . '_time_' . $keySuffix;
		
		/**
		 * Filter the oEmbed TTL value (time to live).
		 *
		 * @since 4.0.0
		 *
		 * @param int    $time    Time to live (in seconds).
		 * @param string $url     The attempted embed URL.
		 * @param array  $attr    An array of shortcode attributes.
		 * @param int    $post_ID Post ID.
		 */
		$ttl = $this->filter->apply('cppress_oembed_ttl', DAY_IN_SECONDS, $url, $attr, $this->post_ID);
		
		$this->cache = get_post_meta($this->post_ID, $this->cacheKey, true);
		$this->cacheTime = get_post_meta($this->post_ID, $this->cachekeyTime, true);
		
		if ( ! $this->cacheTime ) {
			$this->cacheTime = 0;
		}
		
		$this->cachedRecently = (time() - $this->cacheTime) < $ttl;
	}
	
	public function setCache($obj){
		if($obj){
			update_post_meta($this->post_ID, $this->cacheKey, $obj);
			update_post_meta($this->post_ID, $this->cachekeyTime, time());
		} elseif (!$this->cache) {
			update_post_meta($this->post_ID, $this->cacheKey, '{{unknown}}');
		}
	}
	
	public function getEmbedObj($url, $attr=array()){
		$attr = wp_parse_args($attr, wp_embed_defaults($url));
		$url = str_replace('&amp;', '&', $url);
		$post_ID = (!empty($this->post->ID)) ? $this->post->ID : null;
		if (!empty( $this->post_ID)) // Potentially set by WP_Embed::cache_oembed()
			$post_ID = $this->post_ID;
		
		// Unknown URL format. Let oEmbed have a go.
		if($post_ID){
		
			$this->cache($attr, $url, 'cppress_oembed_object_');
			if ($this->usecache || $this->cachedRecently) {
				// Failures are cached. Serve one if we're using the cache.
				if ( '{{unknown}}' === $this->cache )
					return $this->maybe_make_link($url);
		
				if (!empty($this->cache)){
						
					return $this->filter->apply('cppress_embed_oembed_object', $this->cache, $url, $attr, $post_ID);
				}
			}
		
		
			$attr['discover'] = $this->filter->apply('cppress_embed_oembed_discover', true);
			// Use oEmbed to get the HTML
			$obj = $this->oembed->getOEmbed($url, $attr);
		
			// Maybe cache the result
			$this->setCache($obj);
		
			// If there was a result, return it
			if ($obj) {
				/** This filter is documented in wp-includes/class-wp-embed.php */
				return $this->filter->apply('cppress_embed_oembed_object', $obj, $url, $attr, $post_ID);
			}
		}
		
		// Still unknown
		return $this->maybe_make_link( $url );
	}
	
	public function shortcode($attr, $url=''){		
		if(empty( $url ) && ! empty($attr['src'])){
			$url = $attr['src'];
		}
		
		$this->last_url = $url;
		
		if(empty($url)){
			$this->last_attr = $attr;
			return '';
		}
		
		$rawattr = $attr;
		$attr = wp_parse_args($attr, wp_embed_defaults($url));
		
		$this->last_attr = $attr;
		
		// kses converts & into &amp; and we need to undo this
		// See https://core.trac.wordpress.org/ticket/11311
		$url = str_replace('&amp;', '&', $url);
		// Look for known internal handlers
		
		$post_ID = (!empty($this->post->ID)) ? $this->post->ID : null;
		if (!empty( $this->post_ID)) // Potentially set by WP_Embed::cache_oembed()
			$post_ID = $this->post_ID;
		
		// Unknown URL format. Let oEmbed have a go.
		if($post_ID){
		
			$this->cache($attr, $url, 'cppress_oembed_');
			if ($this->usecache || $this->cachedRecently) {
				// Failures are cached. Serve one if we're using the cache.
				if ( '{{unknown}}' === $this->cache )
					return $this->maybe_make_link($url);
		
				if (!empty($this->cache)){
					
					return $this->filter->apply('cppress_embed_oembed_html', $this->cache, $url, $attr, $post_ID);
				}
			}
		
		
			$attr['discover'] = $this->filter->apply('cppress_embed_oembed_discover', true);
			// Use oEmbed to get the HTML
			$html = $this->oembed->getHtml($url, $attr);
		
			// Maybe cache the result
			$this->setCache($html);
		
			// If there was a result, return it
			if ($html) {
				/** This filter is documented in wp-includes/class-wp-embed.php */
				return $this->filter->apply('cppress_embed_oembed_html', $html, $url, $attr, $post_ID);
			}
		}
		
		// Still unknown
		return $this->maybe_make_link( $url );
	}
	
	
}