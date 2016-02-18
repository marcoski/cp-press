<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\FrontEndApplication;
use CpPress\Application\BackEnd\FieldsController;

class CpWidgetContactForm extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Contact Form Widget', 'cppress'),
				array(
						'description' 	=> __('Contact form generator', 'cppress'),

						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-email';
		$this->adminScripts = array(
			array(
				'source' => 'cp-contact-form-admin',
				'deps' => array('jquery', 'backbone', 'underscore')
			)
		);
		$this->frontScripts = array(
			array(
					'source' => 'cp-press-ajax-form',
					'deps' => array('jquery')
			),
			array(
					'source' => 'cp-contact-form-front',
					'deps' => array('jquery', 'backbone', 'underscore')
			)
		);
		$this->frontStyles = array(
				array(
						'source' => 'cp-contact-form'
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
		$instance['desturi'] = FieldsController::getLinkPermalink($instance['desturi']);
		$w = FrontEndApplication::part('ContactForm', 'form_template', $this->container, array($instance));
		$this->assign('widget', $w);
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$fields = array(
			'form' => array(
				'id' => $this->get_field_id('form'),
				'name' => $this->get_field_name('form')
			),
			'to' => array(
				'id' => $this->get_field_id('to'),
				'name' => $this->get_field_name('to')
			),
			'subject' => array(
				'id' => $this->get_field_id('subject'),
				'name' => $this->get_field_name('subject')
			),
			'subjectpre' => array(
				'id' => $this->get_field_id('subjectpre'),
				'name' => $this->get_field_name('subject')
			),
			'submit' => array(
				'id' => $this->get_field_id('submit'),
				'name' => $this->get_field_name('submit')
			)
		);
		$link = BackEndApplication::part(
				'FieldsController', 'link_button', $this->container,
				array(
						$this->get_field_id( 'desturi' ),
						$this->get_field_name( 'desturi' ),
						$instance['desturi']
				)
		);
		$contactForm = BackEndApplication::part('ContactFormController', 'form', $this->container, array($instance, $fields));
		$mailTemplateForm = BackEndApplication::part(
			'ContactFormController', 
			'mail_template_form', 
			$this->container, 
			array($instance, $fields, $link)
		);
		$dialog = BackEndApplication::part('ContactFormController', 'dialog_form', $this->container);
		$this->assign('contact_form', $contactForm);
		$this->assign('mail_template_form', $mailTemplateForm);
		$this->assign('dialog', $dialog);
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
