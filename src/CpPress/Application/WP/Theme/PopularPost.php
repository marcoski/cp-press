<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Theme;


use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Hook\Hook;
use CpPress\Application\WP\Query\Query;

class PopularPost
{

    const COUNT_KEY = 'cppress_post_views_count';

    /**
     * @var Hook
     */
    private $hook;

    public function __construct(Hook $hook)
    {
        $this->hook = $hook;
    }

    public function run()
    {
        $this->hook->register('wp_head', function(){
            $this->track();
        });
        $this->hook->exec('wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    }

    public function track($id = null)
    {
        if(!is_single()) {
            return;
        }

        if($id === null){
            global $post;
            $id = $post->ID;
        }
        $this->setPostView($id);
    }

    public function getPostView($id){
        $count = PostMeta::find($id, self::COUNT_KEY);
        if($count === ''){
            PostMeta::delete($id, self::COUNT_KEY);
            PostMeta::add($id, self::COUNT_KEY, '0');

            return 0;
        }

        return $count;
    }

    public function getPopularPosts($limit = 10)
    {
        $query = new Query();
        $query->setLoop([
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'meta_key' => self::COUNT_KEY,
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ]);

        return $query;
    }

    private function setPostView($id)
    {
        $count = PostMeta::find($id, self::COUNT_KEY);
        if($count === ''){
            PostMeta::delete($id, self::COUNT_KEY);
            PostMeta::add($id, self::COUNT_KEY, 0);
        }else{
            $count++;
            PostMeta::update($id, self::COUNT_KEY, $count);
        }
    }

}