<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;
use CpPress\Application\WP\Admin\SettingsField\Field\AttachmentField;

class AttachmentSettingsFieldFactory extends BaseSettingsFieldFactory{
	
	public function create(SettingsSectionInterface $section){
		$this->createAttachmentField($section);
		$this->add();
	}
	
	private function createAttachmentField(SettingsSectionInterface $section){
		$attachmentField = new AttachmentField();
		$args = array(
			'tag' => 'selectmultiple',
			'options' => array(__('Mime Types', 'cppress') => array_flip(get_allowed_mime_types()))
		);
		$this->fields['attachment-valid-mime'] = $attachmentField
			->setId('attachment-valid-mime')
			->setTitle(__('Valid Attachment', 'cppress'))
			->setSection($section)
			->setArgs($args)
			->setName('validmime');
	}
}