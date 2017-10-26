<?php
namespace CpPress\Application\Widgets;

use Commonhelp\Rss\Reader\Reader;
use Commonhelp\Rss\RssConfig;
class CpWidgetRss extends CpWidgetBase{

	private $reader;
	
	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Rss Widget', 'cppress'),
				array(
						'description' 	=> __('Rss Aggregator', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-rss';
		$rssConfig = new RssConfig();
		$rssConfig->setGrabberRulesFolder(array(
				$this->templateDirs[0].'/templates/widget/' . $this->id_base . '/rules',
				get_stylesheet_directory() . '/rules'
		));
		$this->reader = new Reader($rssConfig);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		if(filter_var($instance['rsslink'], FILTER_VALIDATE_URL)){
			try{
				$resource = $this->reader->download($instance['rsslink']);
				$parser = $this->reader->getParser(
						$resource->getUrl(),
						$resource->getContent(),
						$resource->getEncoding()
				);
				$parser->enableContentGrabber(true);
				dump($parser->execute());
				$this->assign('feeds', $parser->execute());
			}catch(\Exception $e){
				$this->assign('feeds', array());
			}
		}else{
			$this->assign('feeds', array());
		}
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		return parent::form($instance);
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $old_instance) {
		return parent::update($new_instance, $old_instance);
	}

}
