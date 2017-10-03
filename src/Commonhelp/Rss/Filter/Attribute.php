<?php
namespace Commonhelp\Rss\Filter;

use Commonhelp\Client\Url;

/**
 * Attribute Filter class.
 *
 */
class Attribute{
	/**
	 * Image proxy url.
	 *
	 * @var string
	 */
	private $imageProxyUrl = '';

	/**
	 * Image proxy callback.
	 *
	 * @var \Closure|null
	 */
	private $imageProxyCallback = null;

	/**
	 * limits the image proxy usage to this protocol.
	 *
	 * @var string
	 */
	private $imageProxyLimitProtocol = '';

	/**
	 * Tags and attribute whitelist.
	 *
	 * @var array
	 */
	private $attributeWhitelist = array(
			'audio' => array('controls', 'src'),
			'video' => array('poster', 'controls', 'height', 'width', 'src'),
			'source' => array('src', 'type'),
			'dt' => array(),
			'dd' => array(),
			'dl' => array(),
			'table' => array(),
			'caption' => array(),
			'tr' => array(),
			'th' => array(),
			'td' => array(),
			'tbody' => array(),
			'thead' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'h6' => array(),
			'strong' => array(),
			'em' => array(),
			'code' => array(),
			'pre' => array(),
			'blockquote' => array(),
			'p' => array(),
			'ul' => array(),
			'li' => array(),
			'ol' => array(),
			'br' => array(),
			'del' => array(),
			'a' => array('href'),
			'img' => array('src', 'title', 'alt'),
			'figure' => array(),
			'figcaption' => array(),
			'cite' => array(),
			'time' => array('datetime'),
			'abbr' => array('title'),
			'iframe' => array('width', 'height', 'frameborder', 'src', 'allowfullscreen'),
			'q' => array('cite'),
	);

	/**
	 * Scheme whitelist.
	 *
	 * For a complete list go to http://en.wikipedia.org/wiki/URI_scheme
	 *
	 * @var array
	*/
	private $schemeWhitelist = array(
			'bitcoin:',
			'callto:',
			'ed2k://',
			'facetime://',
			'feed:',
			'ftp://',
			'geo:',
			'git://',
			'http://',
			'https://',
			'irc://',
			'irc6://',
			'ircs://',
			'jabber:',
			'magnet:',
			'mailto:',
			'nntp://',
			'rtmp://',
			'sftp://',
			'sip:',
			'sips:',
			'skype:',
			'smb://',
			'sms:',
			'spotify:',
			'ssh:',
			'steam:',
			'svn://',
			'tel:',
	);

	/**
	 * Iframe source whitelist, everything else is ignored.
	 *
	 * @var array
	*/
	private $iframeWhitelist = array(
			'http://www.youtube.com',
			'https://www.youtube.com',
			'http://player.vimeo.com',
			'https://player.vimeo.com',
			'http://www.dailymotion.com',
			'https://www.dailymotion.com',
	);

	/**
	 * Blacklisted resources.
	 *
	 * @var array
	*/
	private $mediaBlacklist = array(
			'api.flattr.com',
			'feeds.feedburner.com',
			'share.feedsportal.com',
			'da.feedsportal.com',
			'rc.feedsportal.com',
			'rss.feedsportal.com',
			'res.feedsportal.com',
			'res1.feedsportal.com',
			'res2.feedsportal.com',
			'res3.feedsportal.com',
			'pi.feedsportal.com',
			'rss.nytimes.com',
			'feeds.wordpress.com',
			'stats.wordpress.com',
			'rss.cnn.com',
			'twitter.com/home?status=',
			'twitter.com/share',
			'twitter_icon_large.png',
			'www.facebook.com/sharer.php',
			'facebook_icon_large.png',
			'plus.google.com/share',
			'www.gstatic.com/images/icons/gplus-16.png',
			'www.gstatic.com/images/icons/gplus-32.png',
			'www.gstatic.com/images/icons/gplus-64.png',
	);

	/**
	 * Attributes used for external resources.
	 *
	 * @var array
	*/
	private $mediaAttributes = array(
			'src',
			'href',
			'poster',
	);

	/**
	 * Attributes that must be integer.
	 *
	 * @var array
	*/
	private $integerAttributes = array(
			'width',
			'height',
			'frameborder',
	);

	/**
	 * Mandatory attributes for specified tags.
	 *
	 * @var array
	*/
	private $requiredAttributes = array(
			'a' => array('href'),
			'img' => array('src'),
			'iframe' => array('src'),
			'audio' => array('src'),
			'source' => array('src'),
	);

	/**
	 * Add attributes to specified tags.
	 *
	 * @var array
	*/
	private $addAttributes = array(
			'a' => array('rel' => 'noreferrer', 'target' => '_blank'),
			'video' => array('controls' => 'true'),
	);

	/**
	 * List of filters to apply.
	 *
	 * @var array
	*/
	private $filters = array(
			'filterAllowedAttribute',
			'filterIntegerAttribute',
			'rewriteAbsoluteUrl',
			'filterIframeAttribute',
			'filterBlacklistResourceAttribute',
			'filterProtocolUrlAttribute',
			'rewriteImageProxyUrl',
			'secureIframeSrc',
			'removeYouTubeAutoplay',
	);

	/**
	 * Add attributes to specified tags.
	 *
	 * @var \Commonhelp\Client\Url
	*/
	private $website;

	/**
	 * Constructor.
	 *
	 * @param \Commonhelp\Client\Url $website Website url instance
	 */
	public function __construct(Url $website){
		$this->website = $website;
	}

	/**
	 * Apply filters to the attributes list.
	 *
	 * @param string $tag        Tag name
	 * @param array  $attributes Attributes dictionary
	 *
	 * @return array Filtered attributes
	 */
	public function filter($tag, array $attributes){
		foreach ($attributes as $attribute => &$value) {
			foreach ($this->filters as $filter) {
				if (!$this->$filter($tag, $attribute, $value)) {
					unset($attributes[$attribute]);
					break;
				}
			}
		}

		return $attributes;
	}

	/**
	 * Return true if the value is allowed (remove not allowed attributes).
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function filterAllowedAttribute($tag, $attribute, $value){
		return isset($this->attributeWhitelist[$tag]) && in_array($attribute, $this->attributeWhitelist[$tag]);
	}

	/**
	 * Return true if the value is not integer (remove attributes that should have an integer value).
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function filterIntegerAttribute($tag, $attribute, $value){
		if (in_array($attribute, $this->integerAttributes)) {
			return ctype_digit($value);
		}

		return true;
	}

	/**
	 * Return true if the iframe source is allowed (remove not allowed iframe).
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function filterIframeAttribute($tag, $attribute, $value){
		if ($tag === 'iframe' && $attribute === 'src') {
			foreach ($this->iframeWhitelist as $url) {
				if (strpos($value, $url) === 0) {
					return true;
				}
			}

			return false;
		}

		return true;
	}

	/**
	 * Return true if the resource is not blacklisted (remove blacklisted resource attributes).
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function filterBlacklistResourceAttribute($tag, $attribute, $value){
		if ($this->isResource($attribute) && $this->isBlacklistedMedia($value)) {
			return false;
		}

		return true;
	}

	/**
	 * Convert all relative links to absolute url.
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function rewriteAbsoluteUrl($tag, $attribute, &$value){
		if ($this->isResource($attribute)) {
			$value = Url::resolve($value, $this->website);
		}

		return true;
	}

	/**
	 * Turns iframes' src attribute from http to https to prevent
	 * mixed active content.
	 *
	 * @param string $tag       Tag name
	 * @param array  $attribute Atttributes name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function secureIframeSrc($tag, $attribute, &$value){
		if ($tag === 'iframe' && $attribute === 'src' && strpos($value, 'http://') === 0) {
			$value = substr_replace($value, 's', 4, 0);
		}

		return true;
	}

	/**
	 * Removes YouTube autoplay from iframes.
	 *
	 * @param string $tag       Tag name
	 * @param array  $attribute Atttributes name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function removeYouTubeAutoplay($tag, $attribute, &$value){
		$regex = '%^(https://(?:www\.)?youtube.com/.*\?.*autoplay=)(1)(.*)%i';
		if ($tag === 'iframe' && $attribute === 'src' && preg_match($regex, $value)) {
			$value = preg_replace($regex, '${1}0$3', $value);
		}

		return true;
	}

	/**
	 * Rewrite image url to use with a proxy.
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function rewriteImageProxyUrl($tag, $attribute, &$value){
		if ($tag === 'img' && $attribute === 'src'
				&& !($this->imageProxyLimitProtocol !== '' && stripos($value, $this->imageProxyLimitProtocol.':') !== 0)) {
					if ($this->imageProxyUrl) {
						$value = sprintf($this->imageProxyUrl, rawurlencode($value));
					} elseif (is_callable($this->imageProxyCallback)) {
						$value = call_user_func($this->imageProxyCallback, $value);
					}
				}

				return true;
	}

	/**
	 * Return true if the scheme is authorized.
	 *
	 * @param string $tag       Tag name
	 * @param string $attribute Attribute name
	 * @param string $value     Attribute value
	 *
	 * @return bool
	 */
	public function filterProtocolUrlAttribute($tag, $attribute, $value){
		if ($this->isResource($attribute) && !$this->isAllowedProtocol($value)) {
			return false;
		}

		return true;
	}

	/**
	 * Automatically add/override some attributes for specific tags.
	 *
	 * @param string $tag        Tag name
	 * @param array  $attributes Attributes list
	 *
	 * @return array
	 */
	public function addAttributes($tag, array $attributes){
		if (isset($this->addAttributes[$tag])) {
			$attributes += $this->addAttributes[$tag];
		}

		return $attributes;
	}

	/**
	 * Return true if all required attributes are present.
	 *
	 * @param string $tag        Tag name
	 * @param array  $attributes Attributes list
	 *
	 * @return bool
	 */
	public function hasRequiredAttributes($tag, array $attributes){
		if (isset($this->requiredAttributes[$tag])) {
			foreach ($this->requiredAttributes[$tag] as $attribute) {
				if (!isset($attributes[$attribute])) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Check if an attribute name is an external resource.
	 *
	 * @param string $attribute Attribute name
	 *
	 * @return bool
	 */
	public function isResource($attribute){
		return in_array($attribute, $this->mediaAttributes);
	}

	/**
	 * Detect if the protocol is allowed or not.
	 *
	 * @param string $value Attribute value
	 *
	 * @return bool
	 */
	public function isAllowedProtocol($value){
		foreach ($this->schemeWhitelist as $protocol) {
			if (strpos($value, $protocol) === 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Detect if an url is blacklisted.
	 *
	 * @param string $resource Attribute value (URL)
	 *
	 * @return bool
	 */
	public function isBlacklistedMedia($resource){
		foreach ($this->mediaBlacklist as $name) {
			if (strpos($resource, $name) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Convert the attribute list to html.
	 *
	 * @param array $attributes Attributes
	 *
	 * @return string
	 */
	public function toHtml(array $attributes){
		$html = array();

		foreach ($attributes as $attribute => $value) {
			$html[] = sprintf('%s="%s"', $attribute, Filter::escape($value));
		}

		return implode(' ', $html);
	}

	/**
	 * Set whitelisted tags and attributes for each tag.
	 *
	 * @param array $values List of tags: ['video' => ['src', 'cover'], 'img' => ['src']]
	 *
	 * @return Attribute
	 */
	public function setWhitelistedAttributes(array $values){
		$this->attributeWhitelist = $values ?: $this->attributeWhitelist;

		return $this;
	}

	/**
	 * Set scheme whitelist.
	 *
	 * @param array $values List of scheme: ['http://', 'ftp://']
	 *
	 * @return Attribute
	 */
	public function setSchemeWhitelist(array $values){
		$this->schemeWhitelist = $values ?: $this->schemeWhitelist;

		return $this;
	}

	/**
	 * Set media attributes (used to load external resources).
	 *
	 * @param array $values List of values: ['src', 'href']
	 *
	 * @return Attribute
	 */
	public function setMediaAttributes(array $values){
		$this->mediaAttributes = $values ?: $this->mediaAttributes;

		return $this;
	}

	/**
	 * Set blacklisted external resources.
	 *
	 * @param array $values List of tags: ['http://google.com/', '...']
	 *
	 * @return Attribute
	 */
	public function setMediaBlacklist(array $values){
		$this->mediaBlacklist = $values ?: $this->mediaBlacklist;

		return $this;
	}

	/**
	 * Set mandatory attributes for whitelisted tags.
	 *
	 * @param array $values List of tags: ['img' => 'src']
	 *
	 * @return Attribute
	 */
	public function setRequiredAttributes(array $values){
		$this->requiredAttributes = $values ?: $this->requiredAttributes;

		return $this;
	}

	/**
	 * Set attributes to automatically to specific tags.
	 *
	 * @param array $values List of tags: ['a' => 'target="_blank"']
	 *
	 * @return Attribute
	 */
	public function setAttributeOverrides(array $values){
		$this->addAttributes = $values ?: $this->addAttributes;

		return $this;
	}

	/**
	 * Set attributes that must be an integer.
	 *
	 * @param array $values List of tags: ['width', 'height']
	 *
	 * @return Attribute
	 */
	public function setIntegerAttributes(array $values){
		$this->integerAttributes = $values ?: $this->integerAttributes;

		return $this;
	}

	/**
	 * Set allowed iframe resources.
	 *
	 * @param array $values List of tags: ['http://www.youtube.com']
	 *
	 * @return Attribute
	 */
	public function setIframeWhitelist(array $values){
		$this->iframeWhitelist = $values ?: $this->iframeWhitelist;

		return $this;
	}

	/**
	 * Set image proxy URL.
	 *
	 * The original image url will be urlencoded
	 *
	 * @param string $url Proxy URL
	 *
	 * @return Attribute
	 */
	public function setImageProxyUrl($url){
		$this->imageProxyUrl = $url ?: $this->imageProxyUrl;

		return $this;
	}

	/**
	 * Set image proxy callback.
	 *
	 * @param \Closure $callback
	 *
	 * @return Attribute
	 */
	public function setImageProxyCallback($callback){
		$this->imageProxyCallback = $callback ?: $this->imageProxyCallback;

		return $this;
	}

	/**
	 * Set image proxy protocol restriction.
	 *
	 * @param string $value
	 *
	 * @return Attribute
	 */
	public function setImageProxyProtocol($value){
		$this->imageProxyLimitProtocol = $value ?: $this->imageProxyLimitProtocol;

		return $this;
	}
}