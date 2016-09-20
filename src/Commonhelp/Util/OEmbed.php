<?php
namespace Commonhelp\Util;

use Commonhelp\Client\Client;
use Commonhelp\App\Http\Request;

class OEmbed{
	
	private $providers = array(
			'#http://((m|www)\.)?youtube\.com/watch.*#i'	=> array('http://www.youtube.com/oembed', true),
			'#https://((m|www)\.)?youtube\.com/watch.*#i' => array('http://www.youtube.com/oembed?scheme=https', true),
			'#http://((m|www)\.)?youtube\.com/playlist.*#i' => array('http://www.youtube.com/oembed', true),
			'#https://((m|www)\.)?youtube\.com/playlist.*#i' => array('http://www.youtube.com/oembed?scheme=https', true),
			'#http://youtu\.be/.*#i' => array('http://www.youtube.com/oembed', true),
			'#https://youtu\.be/.*#i' => array('http://www.youtube.com/oembed?scheme=https', true),
			'#https?://(.+\.)?vimeo\.com/.*#i' => array('http://vimeo.com/api/oembed.{format}', true),
			'#https?://(www\.)?dailymotion\.com/.*#i' => array('https://www.dailymotion.com/services/oembed', true),
			'http://dai.ly/*' => array('https://www.dailymotion.com/services/oembed', false),
			'#https?://(www\.)?flickr\.com/.*#i' => array('https://www.flickr.com/services/oembed/', true),
			'#https?://flic\.kr/.*#i'  => array('https://www.flickr.com/services/oembed/', true),
			'#https?://(.+\.)?smugmug\.com/.*#i' => array('http://api.smugmug.com/services/oembed/', true),
			'#https?://(www\.)?hulu\.com/watch/.*#i' => array('http://www.hulu.com/api/oembed.{format}', true),
			'http://i*.photobucket.com/albums/*' => array('http://api.photobucket.com/oembed', false),
			'http://gi*.photobucket.com/groups/*' => array('http://api.photobucket.com/oembed', false),
			'#https?://(www\.)?scribd\.com/doc/.*#i' => array('http://www.scribd.com/services/oembed', true),
			'#https?://wordpress.tv/.*#i'  => array('http://wordpress.tv/oembed/', true),
			'#https?://(.+\.)?polldaddy\.com/.*#i' => array('https://polldaddy.com/oembed/', true),
			'#https?://poll\.fm/.*#i' => array('https://polldaddy.com/oembed/', true),
			'#https?://(www\.)?funnyordie\.com/videos/.*#i' => array('http://www.funnyordie.com/oembed', true),
			'#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i' => array('https://api.twitter.com/1/statuses/oembed.{format}', true),
			'#https?://vine.co/v/.*#i' => array('https://vine.co/oembed.{format}', true),
			'#https?://(www\.)?soundcloud\.com/.*#i' => array('http://soundcloud.com/oembed', true),
			'#https?://(.+?\.)?slideshare\.net/.*#i' => array('https://www.slideshare.net/api/oembed/2', true),
			'#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i' => array('https://api.instagram.com/oembed', true),
			'#https?://(open|play)\.spotify\.com/.*#i' => array('https://embed.spotify.com/oembed/', true),
			'#https?://(.+\.)?imgur\.com/.*#i' => array('http://api.imgur.com/oembed', true),
			'#https?://(www\.)?meetu(\.ps|p\.com)/.*#i' => array('http://api.meetup.com/oembed', true),
			'#https?://(www\.)?issuu\.com/.+/docs/.+#i' => array('http://issuu.com/oembed_wp', true),
			'#https?://(www\.)?collegehumor\.com/video/.*#i' => array('http://www.collegehumor.com/oembed.{format}', true),
			'#https?://(www\.)?mixcloud\.com/.*#i' => array('http://www.mixcloud.com/oembed', true),
			'#https?://(www\.|embed\.)?ted\.com/talks/.*#i' => array('http://www.ted.com/talks/oembed.{format}', true),
			'#https?://(www\.)?(animoto|video214)\.com/play/.*#i' => array('https://animoto.com/oembeds/create', true),
			'#https?://(.+)\.tumblr\.com/post/.*#i' => array('https://www.tumblr.com/oembed/1.0', true),
			'#https?://(www\.)?kickstarter\.com/projects/.*#i' => array('https://www.kickstarter.com/services/oembed', true),
			'#https?://kck\.st/.*#i' => array('https://www.kickstarter.com/services/oembed', true),
			'#https?://cloudup\.com/.*#i' => array('https://cloudup.com/oembed', true),
			'#https?://(www\.)?reverbnation\.com/.*#i' => array('https://www.reverbnation.com/oembed', true),
			'#https?://(www\.)?reddit\.com/r/[^/]+/comments/.*#i' => array('https://www.reddit.com/oembed', true),
			'#https?://(www\.)?speakerdeck\.com/.*#i' => array('https://speakerdeck.com/oembed.{format}', true),
		);
	
	private $linkTypes =array(
		'application/json+oembed' => 'json',
		'text/xml+oembed' => 'xml',
		'application/xml+oembed' => 'xml',
	);
	
	private $client;
	private $request;
	
	public function __construct(Request $request){
		$this->client = Client::getInstance();
		$this->request = $request;
	}
	
	public function getProviders(){
		return $this->providers;
	}
	
	
	/**
	 * Takes a URL and returns the corresponding oEmbed provider's URL, if there is one.
	 *
	 * @access public
	 *
	 * @see OEmbed::discover()
	 *
	 * @param string        $url  The URL to the content.
	 * @param string|array  $args Optional provider arguments.
	 * @return null|string null on failure, otherwise the oEmbed provider URL.
	 */
	public function getProvider($url, $args = array()){
		if (!isset($args['discover'])){
			$args['discover'] = true;
		}

		foreach($this->providers as $matchmask => $data){
			list($providerurl, $regex) = $data;

			// Turn the asterisk-type provider URLs into regex
			if (!$regex) {
				$matchmask = '#' . 
					str_replace('___wildcard___', '(.+)', 
							preg_quote(str_replace('*', '___wildcard___', $matchmask), '#')) . '#i';
				$matchmask = preg_replace('|^#http\\\://|', '#https?\://', $matchmask);
			}

			if (preg_match( $matchmask, $url )){
				return str_replace('{format}', 'json', $providerurl); // JSON is easier to deal with than XML
			}
		}

		if (!$provider && $args['discover']){
			return $this->discover($url);
		}

		return null;
	}
	
	public function setProvider($regexp, array $provider){
		$this->providers[$regexp] = $provider;
	}
	
	/**
	 * Attempts to discover link tags at the given URL for an oEmbed provider.
	 *
	 * @param string $url The URL that should be inspected for discovery `<link>` tags.
	 * @return false|string False on failure, otherwise the oEmbed provider URL.
	 */
	public function discover($url) {
		$providers = array();
	
		$this->client->execute($url);
		if($html = $this->client->getContent()){
	
			// Strip <body>
			$html = substr($html, 0, stripos( $html, '</head>' ));
	
			// Do a quick check
			$tagfound = false;
			foreach ($this->linkTypes as $linktype => $format){
				if (stripos($html, $linktype)) {
					$tagfound = true;
					break;
				}
			}
	
			if($tagfound && preg_match_all('#<link([^<>]+)/?>#iU', $html, $links)){
				foreach ($links[1] as $link){
					$atts = Inflector::atts($link);
					if(!empty($atts['type']) && !empty($linktypes[$atts['type']]) && !empty($atts['href'])){
						$providers[$this->linkTypes[$atts['type']]] = htmlspecialchars_decode($atts['href']);
	
						// Stop here if it's JSON (that's all we need)
						if ('json' == $this->linkTypes[$atts['type']]){
							break;
						}
					}
				}
			}
		}
		
		// JSON is preferred to XML
		if(!empty($providers['json'])){
			return $providers['json'];
		}elseif(!empty($providers['xml'])){
			return $providers['xml'];
		}else{
			return false;
		}
	}
	
	public function getOEmbed($url, $args=array()){
		$provider = $this->getProvider($url, $args);
		if($provider === null || null === $data = $this->fetch($provider, $url, $args)){
			return null;
		}
		
		return $data;
	}
	
	public function getHtml($url, $args=array()){
		$data = $this->getOEmbed($url, $args);
		if($data === null){
			return null;
		}
		
		return $this->toHtml($data, $url);
	}
	
	/**
	 * Converts a data object and returns the HTML.
	 *
	 * @param object $data A data object result from an oEmbed provider.
	 * @param string $url The URL to the content that is desired to be embedded.
	 * @return bool|string False on error, otherwise the HTML needed to embed.
	 */
	function toHtml($data, $url){
		if (!is_object($data) || empty($data->type)){
			return false;
		}
		
		$return = false;
		
		switch($data->type){
			case 'photo':
				if(empty($data->url) || empty($data->width) || empty($data->height)){
					break;
				}
				if(!is_string($data->url) || !is_numeric($data->width) || !is_numeric($data->height)){
					break;
				}
				$title = ! empty( $data->title ) && is_string( $data->title ) ? $data->title : '';
				$return = '<a href="' . Sanitize::url( $url ) . '">
						<img 
							src="' . htmlspecialchars( $data->url, ENT_QUOTES, 'UTF-8' ) . '" 
							alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" 
							width="' . htmlspecialchars($data->width, ENT_QUOTES, 'UTF-8') . '" 
							height="' . htmlspecialchars($data->height, ENT_QUOTES, 'UTF-8') . '" />
						</a>';
				break;
			case 'video':
			case 'rich':
				if(!empty($data->html) && is_string($data->html)){
					$return = $data->html;
				}
				break;
			case 'link':
				if(!empty($data->title) && is_string($data->title)){
					$return = '<a href="' . Sanitize::url($url) . '">' 
							. htmlspecialchars($data->title, ENT_QUOTES, 'UTF-8') . 
					'</a>';
				}
				break;
			default:
				$return = false;
		}
		// Strip any new lines from the HTML.
		if(false !== strpos( $return, "\n" )){
			$return = str_replace( array( "\r\n", "\n" ), '', $return );
		}
		
		return $return;
	}
	
	/**
	 * Connects to a oEmbed provider and returns the result.
	 *
	 * @param string $provider The URL to the oEmbed provider.
	 * @param string $url The URL to the content that is desired to be embedded.
	 * @param array $args Optional arguments.
	 * @return bool|object False on failure, otherwise the result in the form of an object.
	 */
	public function fetch($provider, $url, $args){
		$width = 500;
		$height = min(ceil($width * 1.5), 1000);
		$args = array_merge(compact('width', 'height'), $args);
		$provider = $this->addQueryArg('maxwidth', (int) $args['width'], $provider);
		$provider = $this->addQueryArg('maxheight', (int) $args['height'], $provider);
		$provider = $this->addQueryArg('url', $url, $provider);
		foreach(array('json', 'xml') as $format){
			$result = $this->fetchWithFormat($provider, $format);
			return $result;
		}
		
		return null;
	}
	
	public function addQueryArg(){
		$ret = '';
		$args = func_get_args();
		
		if(is_array( $args[0])){
			if(count( $args ) < 2 || false === $args[1]){
				$uri = $this->request->getRequestUri();
			}else{
				$uri = $args[1];
			}
		}else{
			if(count( $args ) < 3 || false === $args[2]){
				$uri = $this->request->getRequestUri(); 
			}else{
				$uri = $args[2];
			}
		}
		
		if($frag = strstr($uri, '#')){
			$uri = substr($uri, 0, -strlen($frag));
		}else{
			$frag = '';
		}
		
		if(0 === stripos('http://', $uri)){
			$protocol = 'http://';
			$uri = substr( $uri, 7 );
		}else if( 0 === stripos('https://', $uri)){
			$protocol = 'https://';
			$uri = substr( $uri, 8 );
		}else{
			$protocol = '';
		}
		if(strpos($uri, '?') !== false){
			$parts = explode('?', $uri, 2);
			if(1 == count($parts)){
				$base = '?';
				$query = $parts[0];
			}else{
				$base = $parts[0] . '?';
				$query = $parts[1];
			}
		}else if($protocol || strpos( $uri, '=' ) === false){
			$base = $uri . '?';
			$query = '';
		}else{
			$base = '';
			$query = $uri;
		}
		
		parse_str($query, $qs);
		
		if(is_array($args[0])){
			$kayvees = $args[0];
			$qs = array_merge($qs, $kayvees);
		}else{
			$qs[ $args[0] ] = $args[1];
		}
		
		foreach($qs as $k => $v){
			if ($v === false){
				unset($qs[$k]);
			}
		}
		
		$ret = http_build_query($qs, null, '&');
		$ret = trim($ret, '?');
		$ret = preg_replace('#=(&|$)#', '$1', $ret);
		$ret = $protocol . $base . $ret . $frag;
		$ret = rtrim($ret, '?');
		return $ret;
	}
	
	private function fetchWithFormat($provider, $format){
		$provider = $this->addQueryArg('format', $format, $provider);
		$this->client->execute($provider);
		if(!$body = $this->client->getContent()){
			return false;
		}
		
		$parseMethod = 'parse'.Inflector::camelize($format);
		return $this->$parseMethod($body);
	}
	
	/**
	 * Parses a json response body.
	 * @access private
	 */
	private function parseJson($response){
		return (($data = json_decode(trim($response))) 
				&& is_object($data)) ? $data : false;
	}
	/**
	 * Parses an XML response body.
	 * @access private
	 */
	private function parseXml($response){
		$errors = XmlParser::disableInternalErrors();
		$oldValue = XmlParser::disableExternalEntityLoad();
		$succes = XmlParser::getDomDocument($response);
		if (!is_null($old_value) && $oldValue){
			XmlParser::enableExternalEntityLoad();
		}
		if($errors){
			XmlParser::enableInternalErrors();
		}
		if (!$success) {
			return false;
		}
		$data = XmlParser::getSimpleXml($response);
		if (!is_object($data)){
			return false;
		}
		$return = new stdClass;
		foreach($data as $key => $value){
			$return->$key = (string) $value;
		}
		return $return;
	}
	
}
