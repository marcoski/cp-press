<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;

class SocialmediaController extends WPController{
	
	private $networks;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
		$this->networks = array(
			'facebook'    => array(
				'label'    => __( 'Facebook', 'cppress' ),
				'base_url' => 'https://www.facebook.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#3A5795'
			),
			'twitter'     => array(
				'label'    => __( 'Twitter', 'cppress' ),
				'base_url' => 'https://twitter.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#78BDF1'
			),
			'google-plus' => array(
				'label'    => __( 'Google+', 'cppress' ),
				'base_url' => 'https://plus.google.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#DD4B39'
			),
			'rss'         => array(
				'label'    => __( 'RSS', 'cppress' ),
				'base_url' => get_bloginfo('rss_url'),
				'icon_color' => '#FFFFFF',
				'button_color' => '#FAA21B'
			),
			'envelope'   => array(
				'label'    => __( 'Email', 'cppress' ),
				'base_url' => 'mailto:',
				'icon_color' => '#FFFFFF',
				'button_color' => '#99C4E6'
			),
			'linkedin'    => array(
				'label'    => __( 'LinkedIn', 'cppress' ),
				'base_url' => 'https://www.linkedin.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#0177B4'
			),
			'pinterest'   => array(
				'label'    => __( 'Pinterest', 'cppress' ),
				'base_url' => 'https://www.pinterest.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#DB7C83'
			),
			'tumblr'   => array(
				'label'    => __( 'Tumblr', 'cppress' ),
				'base_url' => 'https://www.tumblr.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#36465D'
			),
			'instagram'   => array(
				'label'    => __( 'Instagram', 'cppress' ),
				'base_url' => 'https://instagram.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#3D739C'
			),
			'vk'   => array(
				'label'    => __( 'VK', 'cppress' ),
				'base_url' => 'https://vk.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#537599'
			),
			'flickr'   => array(
				'label'    => __( 'Flickr', 'cppress' ),
				'base_url' => 'https://www.flickr.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#D40057'
			),
			'vine'   => array(
				'label'    => __( 'Vine', 'cppress' ),
				'base_url' => 'https://vine.co/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#17B48A'
			),
			'behance'   => array(
				'label'    => __( 'Behance', 'cppress' ),
				'base_url' => 'https://www.behance.net/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#333333'
			),
			'bitbucket'   => array(
				'label'    => __( 'Bitbucket', 'cppress' ),
				'base_url' => 'https://bitbucket.org/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#205081'
			),
			'codepen'   => array(
				'label'    => __( 'Codepen', 'cppress' ),
				'base_url' => 'https://codepen.io/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#2A2A2A'
			),
			'delicious'   => array(
				'label'    => __( 'Delicious', 'cppress' ),
				'base_url' => 'https://delicious.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#58ACFD'
			),
			'deviantart'   => array(
				'label'    => __( 'deviantArt', 'cppress' ),
				'base_url' => 'http://www.deviantart.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#B2C01C'
			),
			'dribbble'   => array(
				'label'    => __( 'Dribbble', 'cppress' ),
				'base_url' => 'https://dribbble.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#F26798'
			),
			'dropbox'   => array(
				'label'    => __( 'Dropbox', 'cppress' ),
				'base_url' => 'https://www.dropbox.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#1388E6'
			),
			'foursquare'   => array(
				'label'    => __( 'Foursquare', 'cppress' ),
				'base_url' => 'https://foursquare.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#EB4E79'
			),
			'github'   => array(
				'label'    => __( 'Github', 'cppress' ),
				'base_url' => 'https://github.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#202021'
			),
			'gittip'   => array(
				'label'    => __( 'Gratipay', 'cppress' ),
				'base_url' => 'https://gratipay.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#653614'
			),
			'hacker-news'   => array(
				'label'    => __( 'Hacker News', 'cppress' ),
				'base_url' => 'https://news.ycombinator.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#FF6600'
			),
			'jsfiddle'   => array(
				'label'    => __( 'JSFiddle', 'cppress' ),
				'base_url' => 'http://jsfiddle.net/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#4679BD'
			),
			'lastfm'   => array(
				'label'    => __( 'Last.fm', 'cppress' ),
				'base_url' => 'https://www.last.fm/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#C02C0C'
			),
			'reddit'   => array(
				'label'    => __( 'Reddit', 'cppress' ),
				'base_url' => 'https://www.reddit.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#CEE3F8'
			),
			'slack'   => array(
				'label'    => __( 'Slack', 'cppress' ),
				'base_url' => 'https://www.slack.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#4D394B'
			),
			'slideshare'   => array(
				'label'    => __( 'Slideshare', 'cppress' ),
				'base_url' => 'https://www.slideshare.net/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#00A8AA'
			),
			'soundcloud'   => array(
				'label'    => __( 'Soundcloud', 'cppress' ),
				'base_url' => 'https://soundcloud.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#FE4600'
			),
			'spotify'   => array(
				'label'    => __( 'Spotify', 'cppress' ),
				'base_url' => 'https://www.spotify.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#7BB72F'
			),
			'stack-exchange'   => array(
				'label'    => __( 'Stack Exchange', 'cppress' ),
				'base_url' => 'http://stackexchange.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#245598'
			),
			'stack-overflow'   => array(
				'label'    => __( 'Stack Overflow', 'cppress' ),
				'base_url' => 'http://stackoverflow.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#F57920'
			),
			'steam'   => array(
				'label'    => __( 'Steam', 'cppress' ),
				'base_url' => 'http://steamcommunity.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#171A21'
			),
			'stumbleupon'   => array(
				'label'    => __( 'StumbleUpon', 'cppress' ),
				'base_url' => 'https://www.stumbleupon.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#EB4924'
			),
			'trello'   => array(
				'label'    => __( 'Trello', 'cppress' ),
				'base_url' => 'https://trello.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#0E74AF'
			),
			'tripadvisor'   => array(
				'label'    => __( 'TripAdvisor', 'cppress' ),
				'base_url' => 'https://www.tripadvisor.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#589442'
			),
			'twitch'   => array(
				'label'    => __( 'Twitch', 'cppress' ),
				'base_url' => 'https://www.twitch.tv/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#6542A6'
			),
			'vimeo-square'   => array(
				'label'    => __( 'Vimeo', 'cppress' ),
				'base_url' => 'https://vimeo.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#5BC8FF'
			),
			'wordpress'   => array(
				'label'    => __( 'Wordpress', 'cppress' ),
				'base_url' => 'https://wordpress.org/',
				'icon_color' => '#797979',
				'button_color' => '#222222'
			),
			'xing'   => array(
				'label'    => __( 'Xing', 'cppress' ),
				'base_url' => 'https://www.xing.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#00605E'
			),
			'yahoo'   => array(
				'label'    => __( 'Yahoo', 'cppress' ),
				'base_url' => 'https://yahoo.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#4101AF'
			),
			'yelp'   => array(
				'label'    => __( 'Yelp', 'cppress' ),
				'base_url' => 'https://www.yelp.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#B4282E'
			),
			'youtube'   => array(
				'label'    => __( 'YouTube', 'cppress' ),
				'base_url' => 'https://www.youtube.com/',
				'icon_color' => '#FFFFFF',
				'button_color' => '#CF3427'
			)
		);
	}
	
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add(){
		$this->assign('networks', $this->networks);
		$this->assign('id', $this->getParam( 'id' ));
		$this->assign('name', $this->getParam( 'name' ));
		$this->assign('values', $this->getParam('values', array()));
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_get_network(){
		$network = $this->getParam('network', '');
		if(!isset($this->networks[$network])){
			return array();
		}
		return $this->networks[$network];
	}
	
}