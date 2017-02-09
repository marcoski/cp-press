<?php

namespace CpPress\Application\WP\Theme\Feature;


use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

class MultiThumbnailFeature extends BaseFeature {


    private static $enqueued = false;


	public function __construct(Hook $hook, Filter $filter, Scripts $scripts, Styles $styles, array $options) {
		parent::__construct($hook, $filter, $scripts, $styles, $options);
		if(!current_theme_supports('post-thumbnails')){
			add_theme_support('post-thumbnails');
		}
	}

	public function getMetaKey() {
        return "{$this->getPostTypeName()}_{$this->get('id')}_thumbnail_id";
    }

    public function hooks() {
	    $this->hook->register('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
	    $this->hook->register('admin_print_scripts-post.php', array($this, 'adminHeaderScripts'));
	    $this->hook->register('admin_print_scripts-post-new.php', array($this, 'adminHeaderScripts'));
        $this->hook->register("wp_ajax_set-{$this->getPostTypeName()}-{$this->get('id')}-thumbnail", array($this, 'setThumbnail'));
        $this->hook->register('delete_attachment', array($this, 'deleteAttachment'));
        $this->filter->register('is_protected_meta', array($this, 'filterIsProtectedMeta'), 20, 2);
	    parent::hooks();
    }

    public function render($thumbId = null){
    	global $post;
	    $thumbId = PostMeta::find($post->ID, $this->getMetaKey());
	    echo $this->renderPostThumbnail($thumbId);
    }

    private function renderPostThumbnail($thumbId = null){
	    global $post, $content_width, $_wp_additional_image_sizes;

	    $urlClass = '';
	    $ajaxNonce = wp_create_nonce("set_post_thumbnail-{$this->getPostTypeName()}-{$this->get('id')}-{$post->ID}");
	    $imgLibraryUrl = '#';
	    $modalJs = sprintf(
		    'var cp_mm_%3$s = new MediaModal({
                calling_selector: "#set-%1$s-%2$s-thumbnail",
                cb: function(attachment){
                    MultiThumbnails.setAsThumbnail(attachment.id, "%2$s", "%1$s", "%4$s");
                }
            });',
		    $this->getPostTypeName(), $this->get('id'), md5($this->get('id')), $ajaxNonce
	    );
	    $formatString =
		    '<div id="%3$s-%4$s"><p class="hide-if-no-js">
                <a title="%1$s" href="%2$s", id="set-%3$s-%4$s-thumbnail" class="%5$s" data-thumbnail_id="%7$s" data-uploader_title="%1$S" data-uploader_button_text="%1$s">%%s</a>
             </p></div>';
	    $setThumbnailLink = sprintf(
		    $formatString,
		    sprintf(esc_attr__("set %s", 'cppress'), $this->get('label')),
		    esc_url($imgLibraryUrl),
		    $this->getPostTypeName(),
		    $this->get('id'),
		    $urlClass,
		    $this->get('label'),
		    $thumbId
	    );
	    $content = sprintf(
		    $setThumbnailLink,
		    sprintf(esc_html__("Set %s", 'cppress'), $this->get('label')));
	    if($thumbId && get_post($thumbId)){
		    $oldContentWidth = $content_width;
		    $content_width = 266;
		    $attr = array('class' => 'cp-multithumbnail');
		    if(!isset($_wp_additional_image_sizes["{$this->getPostTypeName()}-{$this->get('id')}-thumbnail"])){
			    $thumbnailHtml = wp_get_attachment_image($thumbId, array($content, $content_width), false, $attr);
		    }else{
			    $thumbnailHtml = wp_get_attachment_image($thumbId, "{$this->getPostTypeName()}-{$this->get('id')}-thumbnail", false, $attr);
		    }
		    if(!empty($thumbnailHtml)){
			    $content = sprintf($setThumbnailLink, $thumbnailHtml);
			    $formatString = '<div id="%1$s-%2$s"><p class="hide-if-no-js"><a href="#" id="remove-%1$s-%2$s-thumbnail" onclick="MultiThumbnails.removeThumbnail(\'%2$s\', \'%1$s\', \'%4$s\');return false;">%3$s</a></p></div>';
			    $content .= sprintf(
				    $formatString,
				    $this->getPostTypeName(),
				    $this->get('id'),
				    sprintf(esc_html__("Remove %s", 'cppress'), $this->get('label')),
				    $ajaxNonce
			    );
		    }

		    $content_width = $oldContentWidth;
	    }

	    $content .= sprintf('<script>%s</script>', $modalJs);

	    return $this->filter->apply(
		    sprintf('cp-%s-%s-admin-post-thumbnail-html', $this->getPostTypeName(), $this->get('id')),
		    $content,
		    $post->ID,
		    $thumbId
	    );
    }

    /**
     * @param $hook
     */
    public function adminEnqueueScripts( $hook ) {
        if(self::$enqueued){
            return;
        }

        global $post_ID;

        if(!in_array($hook, array('post-new.php', 'post.php', 'media-upload-popup'))){
            return;
        }

        $this->scripts->enqueue('cp-press-multithumbnail-admin', array('jquery', 'set-post-thumbnail', 'media-models'));
        self::$enqueued = true;
    }

    public function adminHeaderScripts(){
        $postId = get_the_ID();
        echo '<script>var post_id = '.$postId.';</script>';
    }

    public function deleteAttachment($postId){
        global $wpdb;

        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '%s' AND meta_value = '%s'", $this->getMetaKey(), $postId));
    }

    public function filterIsProtectedMeta($protected, $metaKey){
        if($this->filter->apply('cp-multithumbnail-unprotected-meta', false)){
            return $protected;
        }

        if($metaKey === $this->getMetaKey()){
            $protected = true;
        }

        return $protected;
    }

    public function setThumbnail(){
    	global $post_ID;
	    $post_ID = intval($_POST['post_id']);

	    if(!current_user_can('edit_post', $post_ID)){
	    	return -1;
	    }

	    $thumbId = intval($_POST['thumbnail_id']);
	    if($thumbId < 0){
	    	delete_post_meta($post_ID, $this->getMetaKey());
		    echo $this->renderPostThumbnail();
		    exit;
	    }
	    if($thumbId && get_post($thumbId)){
	    	$thumbHtml = wp_get_attachment_image($thumbId, 'thumbnail');
		    if(!empty($thumbHtml)){
		    	update_post_meta($post_ID, $this->getMetaKey(), $thumbId);
			    echo $this->renderPostThumbnail($thumbId);
			    exit;
		    }
	    }

	    return 0;
    }

    public static function hasPostThumbnail($postType, $id, $postId = null){
        if(null === $postId){
            $postId = get_the_ID();
        }

        if(!$postId){
            return false;
        }

        return PostMeta::find($postId, "{$postType}_{$id}_thumbnail_id", true);
    }

    public static function thePostThumbnail($postType, $thumbId, $postId = null, $size = 'post-thumbnail', $attr = '', $linkToOriginal = false){
        echo self::getThePostThumbnail($postType, $thumbId, $postId, $size, $attr, $linkToOriginal);
    }

    public static function getThePostThumbnail($postType, $thumbId, $postId = null, $size = 'post-thumbnail', $attr = '', $linkToOriginal = false){
        global $id;
        $postId  = null === $postId ? get_the_ID() : $postId;
        $postThumbnailId = self::getPostThumbnailId($postType, $thumbId, $postId);
        $size = apply_filters("{$postType}-{$postId}-thumbnail-size", $size);
        if($postThumbnailId){
            do_action("begin-fetch-multi-{$postType}-thumbnail-html", $postId, $postThumbnailId, $size);
            $html = wp_get_attachment_image($postThumbnailId, $size, false, $attr);
            do_action("end-fetch-multi-{$postType}-thumbnail-html", $postId, $postThumbnailId, $size);
        }else{
            $html = '';
        }

        if($linkToOriginal && $html){
            $html = sprintf('<a href="%s">%s</a>', wp_get_attachment_url($postThumbnailId), $html);
        }

        return apply_filters("cp-{$postType}-{$thumbId}-thumbnail-html", $html, $postId, $postThumbnailId, $size, $attr);

    }

    public static function getPostThumbnailId($postType, $id, $postId){
        return PostMeta::find($postId, "{$postType}_{$id}_thumbnail_id", true);
    }

    public static function getPostThumbnailUrl($postType, $id, $postId = 0, $size = null){
        if(!$postId){
            $postId = get_the_ID();
        }
        $postThumbnailId = self::getPostThumbnailId($postType, $id, $postId);
        if($size){
            if($url = wp_get_attachment_image_src($postThumbnailId, $size)){
                $url = $url[0];
            }else{
                $url = '';
            }
        }else{
            $url = wp_get_attachment_url($postThumbnailId);
        }

        return $url;
    }
}