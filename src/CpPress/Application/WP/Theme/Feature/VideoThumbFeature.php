<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\WP\WPContainer;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

class VideoThumbFeature extends BaseFeature
{

    public function __construct(Hook $hook, Filter $filter, Scripts $scripts, Styles $styles, WPContainer $container ) {
        parent::__construct( $hook, $filter, $scripts, $styles, [], $container );

        $this->options = array(
            'id' => 'cp-viedo-thumb',
            'label' => __('Featured Video', 'cppress'),
            'post_type' => null,
            'priority' => 'low',
            'context' => 'side'
        );

        $this->hooks();
    }

    public function hooks() {
        $this->hook->register('save_post', array($this, 'save'));
        parent::hooks();
    }

    public function getMetaKey() {
        return 'cp-viedo-thumb';

    }

    public function render(){
        global $post;
        echo '<p> 
			<input type="text" name="'.$this->getMetaKey().'" class="cp-viedo-thumb" value="'.PostMeta::find($post->ID, $this->getMetaKey()).'" />
		</p>';
    }

    public function save($postId){
        /** @var Request $request */
        $request = $this->container->getRequest();
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
            return;
        }
        if($request->getParam($this->getMetaKey(), null) !== null){
            update_post_meta($postId, $this->getMetaKey(), sanitize_text_field($request->getParam($this->getMetaKey())));
        }
    }
}