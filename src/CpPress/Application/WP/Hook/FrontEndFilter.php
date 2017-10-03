<?php
namespace CpPress\Application\WP\Hook;

use Closure;
use CpPress\Application\CpPressApplication;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Query\Query;

class FrontEndFilter extends Filter{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}

	public function massRegister(){
		$this->register('the_content', function($content){
			$post = $this->app->getWPPost();
			$container = $this->app->getContainer();
			if($post->post_type == 'page'){
				$layout = PostMeta::find($post->ID, 'cp-press-page-layout');
				if(!empty($layout)){
						$content = $this->app->part(Inflector::camelize($post->post_type), 'layout', $container, array($post, $layout, $content));
				}
			}
			return $content;
		});
		
		$this->register('single_template', function($template){
			$post = get_queried_object();
			if($post->post_parent > 0){
				$query = new Query();
				$parent = $query->find(
						array(
								'p' => $post->post_parent,
								'post_type' => $post->post_type
						)
				);
				$file = 'single-' . $post->post_type . '-child';
				$t = locate_template("{$file}.php");
				if($t){
					return $t;
				}
				$file = 'single-' . $post->post_type . '-' . $parent[0]->post_name . '-child';
				$t = locate_template("{$file}.php");
				if($t){
					return $t;
				}
			}
			return $template;
		});

		$this->register('cppress_embed_oembed_html', function($html){
			if(false !== strpos($html, 'iframe')){
				return '<div class="cp-video-container">'.$html.'</div>';
			}
			return $html;
		});
	}
	
}