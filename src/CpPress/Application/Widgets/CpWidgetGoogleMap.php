<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\WP\Theme\Media\Image;

class CpWidgetGoogleMap extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
			__('Google Map Widget', 'cppress'),
			array(
				'description' 	=> __('Create a map', 'cppress'),
				'default_style' => 'simple'
			),
			array(),
			$templateDirs
		);
		$this->icon = 'dashicons-location';
		$this->frontScripts = array(
				array(
						'source' => 'cp-widget-google-map',
						'deps' => array('jquery')
				)
		);
		$this->adminScripts = array(
				array(
						'source' => 'cp-widget-google-map-backend',
						'deps' => array('jquery')
				)
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$instance['markers'] = $this->formatMarkers($instance['markers']);
		$styles = $this->getStyles($instance);
		if($instance['maptype'] === 'static'){
			$this->assign('mapsrc', $this->getStaticImage($instance, $instance['mapwidth'], $instance['mapheight']));
		}else if($instance['maptype'] === 'interactive'){
			$markers = $instance['markers'];
			$markerSrc = array();
			if (!empty($instance['marker_media'])){
				$image = new Image();
				$image->set($instance['marker_media']);
				$markerSrc = $image->getImage($instance['marker_media']);
				
			}
			$mapData = array(
					'address'           	=> $instance['mapcenter'],
					'zoom'              	=> $instance['mapzoom'],
					'scroll_zoom'       	=> $instance['scrolltozoom'],
					'draggable'         	=> $instance['draggable'],
					'disable_ui'        	=> $instance['disableui'],
					'keep_centered'     	=> $instance['keepcenter'],
					'marker_icon'       	=> ! empty($markerSrc) ? $markerSrc[0] : '',
					'markers'  						=> ! empty($markers) ? $markers : '',
					'marker_info_display' => $instance['infodisplay'],
					'map_name'          	=> ! empty($styles) ? $styles['map_name'] : '',
					'map_styles'        	=> ! empty($styles) ? $styles['styles'] : '',
			);
			$this->assign('mapData', $mapData);
		}
		return parent::widget($args, $instance);
	}
	
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance){
		$markerMedia = BackEndApplication::part(
				'FieldsController', 'media_button', $this->container,
				array(
						array(
								'media' => $this->get_field_id( 'marker_media' ),
								'external' => $this->get_field_id( 'external' )
						),
						array(
								'media' => $this->get_field_name( 'marker_media' ),
								'external' => $this->get_field_name( 'external' ),
								'libtitle' => __('Marker icon', 'cppress'),
								'description' => __('Replaces the default map marker with your own image.', 'cppress')
						),
						$instance['marker_media'],
						false
				)
		);
		$markerRepeater = BackEndApplication::part(
				'FieldsController', 'repeater', $this->container,
				array(
						$this->get_field_id( 'markers' ),
						$this->get_field_name( 'markers' ),
						$instance['markers'],
						array('add' => 'widget_maps_add_marker'),
						__('Marker position', 'cppress'),
						__('Marker', 'cppress')
				)
		);
		$customStyleRepeater = BackEndApplication::part(
				'FieldsController', 'repeater', $this->container,
				array(
						$this->get_field_id( 'customstyles' ),
						$this->get_field_name( 'customstyles' ),
						$instance['customstyles'],
						array('add' => 'widget_maps_add_customstyles'),
						__('Custom map styles', 'cppress'),
						__('Water', 'cppress')
				)
		);
		$this->assign('customStyles', $customStyleRepeater);
		$this->assign('repeater', $markerRepeater);
		$this->assign('media', $markerMedia);
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
	
	private function getStaticImage($instance, $width, $height, $styles=array()) {
		$srcUrl = "https://maps.googleapis.com/maps/api/staticmap?";
		$srcUrl .= "center=" . $instance['mapcenter'];
		$srcUrl .= "&zoom=" . $instance['mapzoom'];
		$srcUrl .= "&size=" . $width . "x" . $height;
		
		if(!empty($styles)){
			/**
			 * @see https://github.com/siteorigin/so-widgets-bundle/blob/develop/widgets/google-map/google-map.php
			 */
		}
		if (!empty($instance['markers'])){
			$markers = $instance['markers'];
			$markersSt = '';
			if (!empty($instance['marker_media'])){
				$image = new Image();
				$image->set($instance['marker_media']);
				$markerSrc = $image->getImage($instance['marker_media']);
				if (!empty($markerSrc)){
					$markersSt .= 'icon:' . $markerSrc[0];
				}
			}
			
			if (!empty($markers['place'])){
				foreach($markers['place'] as $marker){
					if (!empty($markersSt)){
						$markersSt .= "|";
					}
					$markersSt .= urlencode($marker);
				}
			}
			$markersSt = '&markers=' . $markersSt;
			$srcUrl .= $markersSt;
		}
		return $srcUrl;
	}
	
	private function formatMarkers($markers){
		$toReturn = array();
		foreach($markers['place'] as $key => $place){
			$toReturn[$key]['place'] = $place;
			$toReturn[$key]['content'] = $markers['content'][$key];
			$toReturn[$key]['infomaxwidth'] = $markers['infomaxwidth'][$key];
		}
		
		return $toReturn;
	}
	
	private function getStyles($instance){
		$type = $instance['mapstyles'];
		switch($type){
			case 'custom':
				if(empty($instance['customstyles'])){
					return array();
				}else{
					$mapStyles = array();
					foreach($instance['customstyles']['mapfeature'] as $key => $value){
						$mapStyles[$key]['mapfeature'] = $value;
						$mapStyles[$key]['elementtype'] = $instance['customstyles']['elementtype'][$key];
						$mapStyles[$key]['visible'] = $instance['customstyles']['visible'][$key];
						$mapStyles[$key]['color'] = $instance['customstyles']['color'][$key];
					}
					$mapName = ! empty( $instance['styledmapname'] ) ? $instance['styledmapname'] : __('Custom Map', 'cppress');
					$styles = array();
					foreach($mapStyles as $styleItem ) {
						$mapFeature = $styleItem['mapfeature'];
						unset($styleItem['mapfeature']);
						$elementType = $styleItem['elementtype'];
						unset( $styleItem['elementtype'] );
						$stylers = array();
						foreach($styleItem as $styleName => $styleValue ) {
							if ($styleValue !== '' && ! is_null($styleValue)){
								$styleValue = $styleValue === false ? 'off' : $styleValue;
								array_push($stylers, array($styleName => $styleValue ));
							}
						}
						$mapFeature = str_replace( '_', '.', $mapFeature );
						$mapFeature = str_replace( '-', '_', $mapFeature );
						array_push( $styles, array(
								'featureType' => $mapFeature,
								'elementType' => $elementType,
								'stylers'     => $stylers
						) );
					}
					return array('map_name' => $mapName, 'styles' => $styles );
				}
			case 'rawjson':
				if(empty( $instance['rawjsonmapstyles'])){
					return array();
				}else{
					$mapName = ! empty($instance['styledmapname']) ? $instance['styledmapname'] : __('Custom Map', 'cppress');
					$stylesString = $instance['rawjsonmapstyles'];
					return array('map_name' => $mapName, 'styles' => json_decode($stylesString, true));
				}
			case 'normal':
			default:
				return array();
		}
	}

}