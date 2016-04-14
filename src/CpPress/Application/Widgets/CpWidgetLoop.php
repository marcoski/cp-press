<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\FrontEndApplication;
class CpWidgetLoop extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Loop Widget', 'cppress'),
				array(
						'description' 	=> __('Create a Loop', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-update';
		$this->frontScripts = array(
				array(
						'source' => 'ajaxloop',
						'deps' => array('jquery')
				),
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$loop = FrontEndApplication::part('Post', 'loop', $this->container, array($instance));
		$paginate = '';
		if(isset($instance['paginate'])){
			$paginate = FrontEndApplication::part('Post', 'paginate', $this->container, array($instance));
		}
		$this->assign('paginate', $paginate);
		$this->assign('loop', $loop);
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$advInstance = array(
			'id' => array(
					'posttype' => $this->get_field_id('posttype'),
					'limit' => $this->get_field_id( 'limit' ),
					'offset' => $this->get_field_id( 'offset' ),
					'order' => $this->get_field_id( 'order' ),
					'orderby' => $this->get_field_id( 'orderby' ),
					'categories' => $this->get_field_id( 'categories' ),
					'excludecat' => $this->get_field_id('excludecat'),
					'tags' => $this->get_field_id( 'tags' ),
					'excludetags' => $this->get_field_id('excludetags'),
					'linktitle' => $this->get_field_id('linktitle'),
					'showinfo' => $this->get_field_id('showinfo'),
					'showexcerpt' => $this->get_field_id('showexcerpt'),
					'showthumbnail' => $this->get_field_id('showthumbnail'),
					'hidecontent' => $this->get_field_id('hidecontent'),
					'linkthumbnail' => $this->get_field_id('linkthumbnail'),
					'postspercolumn' => $this->get_field_id('postspercolumn')
			),
			'name' => array(
					'posttype' => $this->get_field_name('posttype'),
					'limit' => $this->get_field_name( 'limit' ),
					'offset' => $this->get_field_name( 'offset' ),
					'order' => $this->get_field_name( 'order' ),
					'orderby' => $this->get_field_name( 'orderby' ),
					'categories' => $this->get_field_name( 'categories' ),
					'excludecat' => $this->get_field_name('excludecat'),
					'tags' => $this->get_field_name( 'tags' ),
					'excludetags' => $this->get_field_name('excludetags'),
					'linktitle' => $this->get_field_name('linktitle'),
					'showinfo' => $this->get_field_name('showinfo'),
					'showexcerpt' => $this->get_field_name('showexcerpt'),
					'showthumbnail' => $this->get_field_name('showthumbnail'),
					'hidecontent' => $this->get_field_name('hidecontent'),
					'linkthumbnail' => $this->get_field_name('linkthumbnail'),
					'postspercolumn' => $this->get_field_name('postspercolumn')
			),
			'value' => $instance
		);
		$advanced = BackEndApplication::part('PostController', 'advanced', $this->container, array($advInstance, false, true));
		$this->assign('advanced', $advanced);
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
