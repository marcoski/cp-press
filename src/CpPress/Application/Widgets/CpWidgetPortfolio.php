<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\WP\Query\Query;
class CpWidgetPortfolio extends CpWidgetBase{

	private $wpQuery;
	
	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Portfolio Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Portfolio', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-portfolio';
		$this->adminScripts = array(
				array(
						'source' => 'cp-portfolio',
						'deps' => array('jquery')
				)
		);
		$this->wpQuery = new Query();
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$queryArgs = array(
				'post__in' => array(),
				'post_type' => array()
		);
		if(!empty($instance['portfolioitems'])){
			for($i=0; $i<count($instance['portfolioitems']['id']); $i++){
				if(isset($instance['portfolioitems']['id'][$i])){
					$itemArgs = FieldsController::getLinkArgs($instance['portfolioitems']['id'][$i]);
					if(!in_array($itemArgs['p'], $queryArgs['post__in'])){
						$queryArgs['post__in'][] = $itemArgs['p'];
					}
					if(!in_array($itemArgs['post_type'], $queryArgs['post_type'])){
						$queryArgs['post_type'][] = $itemArgs['post_type'];
					}
				}
			}
		}
		$this->wpQuery->setLoop($queryArgs);
		$this->assign('wpQuery', $this->wpQuery);
		if(isset($instance['itemperrow']) && $instance['itemperrow'] !== ''){
			$instance['rowclass'] = floor(12/$instance['itemperrow']);
		}else{
			$instance['rowclass'] = 12;
		}
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$repeater = BackEndApplication::part(
				'FieldsController', 'repeater', $this->container,
				array(
						$this->get_field_id( 'portfolioitems' ),
						$this->get_field_name( 'portfolioitems' ),
						$instance['portfolioitems'],
						array('add' => 'widget_portfolio_add'),
						__('Portfolio items', 'cppress'),
						__('Item', 'cppress')
				)
		);
		$this->assign('repeater', $repeater);
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
