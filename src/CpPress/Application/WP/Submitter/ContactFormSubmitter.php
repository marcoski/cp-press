<?php
namespace CpPress\Application\WP\Submitter;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;
use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;
use CpPress\Application\WP\Submitter\Validators\ContactFormValidator;
use CpPress\Application\WP\Submitter\Util\Mailer;

class ContactFormSubmitter extends Submitter{

	private $status = 'init';
	private $usedShortcode;
	private $response;
	private $invalidFields = array();
	private $skipMail;

	private $error;

	public function __construct(Request $request, Filter $filter, Hook $hook){
		parent::__construct($request, $filter, $hook);
		$this->usedShortcode = json_decode(
			wp_unslash($this->request->getParam('_cppress-cf-scannedtag', array())), true
		);
		$this->setupData();
		$this->validator = new ContactFormValidator($request);
		$this->skipMail = false;
		$this->error = null;
		add_action('wp_mail_failed', function(\WP_Error $error){
			$this->error = implode(', ', $error->errors['wp_mail_failed']);
		});
	}

	public function getInvalidField($name){
		if(isset($this->invalidFields[$name])){
			return $this->invalidFields[$name];
		}

		return null;
	}

	public function getResponse(){
		return $this->response;
	}

	public function getStatus(){
		return $this->status;
	}

	protected function submit($ajax=false){
		if(!$this->is('init')){
			return $this->status;
		}

		$this->meta = array(
			'remote_ip' => $this->request->getRemoteAddress(),
			'user_agent' => substr($this->request->server['HTTP_USER_AGENT'], 0, 254),
			'timestamp' => current_time('timestamp'),
			'unit_tag' => $this->request->getParam('_cppress-cf-unit-tag', '')
		);

		if(!$this->validate()){
			$this->status = 'validation_failed';
			$this->response = __('Validation errors occurred. Please confirm the fields and submit it again.', 'cppress');
		}else if(!$this->accepted()){
			$this->status = 'acceptance_missing';
			$this->response = __('Please accept the terms to proceed.', 'cppress');
		}else if($this->spam()){
			$this->status = 'spam';
			$this->response = __('Failed to send your message. Please try later or contact the administrator by another method.', 'cppress');
		}else if($this->mail()){
			$this->status = 'mail_sent';
			$this->response =  __('Your message was sent successfully. Thanks.', 'cppress');
			$this->hook->create('cppress-cf-mailsent', $this);
		}else{
			$this->status = 'mail_failed';
			$this->response = __('Failed to send your message. Please try later or contact the administrator by another method.', 'cppress');
			if($this->error !== null){
				$this->response .= '<strong> Error: '.$this->error.'</strong>';
			}
			$this->hook->create('cppress-cf-mailfailed', $this);
		}

		$this->removeUploadedFiles();

		$result = array(
			'status' => $this->status,
			'message' => $this->response,
		);

		if($this->is('validation_failed')){
			$result['invalid_fields'] = $this->invalidFields;
		}

		$this->hook->create('cppress-cf-submit', $this, $result);

		return $result;
	}

	private function validate(){
		if($this->invalidFields){
			return false;
		}

		foreach($this->usedShortcode as $tag){
			$this->validator = $this->validator->validate($tag['type'], $tag);
			$evaluator = trim($tag['type'], '*');
			if($evaluator == 'file'){
				$this->addUploadedFile($tag['name'], $this->validator->getUploadedFile());
			}
		}

		$this->invalidFields = $this->validator->getInvalidFields();

		return $this->validator->isValid();

	}

	private function accepted(){
		return $this->filter->apply('cppress-cf-acceptance', true);
	}

	private function spam(){
		$spam = false;

		$userAgent = (string) $this->meta['user_agent'];

		if(strlen($userAgent) < 2){
			$spam = true;
		}

		if(!$this->verifyNonce()){
			$spam = true;
		}

		if($this->blacklistCheck()){
			$spam = true;
		}

		return $this->filter->apply('cppress-cf-spam', $spam);
	}

	private function verifyNonce(){
		return wp_verify_nonce($this->request->getParam('_wpnonce'), 'cppress-cf');
	}

	private function blacklistCheck(){
		$target = $this->data; /** SHOULD BE FLATTEN ARRAY? */
		$target[] = $this->meta['remote_ip'];
		$target[] = $this->meta['user_agent'];

		$target = implode("\n", $target);
		$modKeys = trim(get_option('blacklist_keys'));

		if(empty($modKeys)){
			return false;
		}

		$words = explode("\n", $modKeys);
		foreach((array) $words as $word){
			$word = trim($word);
			if(empty($word) || 256 < strlen($word)){
				continue;
			}

			$pattern = sprintf('#%s#i', preg_quote($word, '#'));
			if(preg_match($pattern, $target)){
				return true;
			}
		}

		return false;
	}

	private function mail(){
		$this->hook->create('cppress-cf-before-send-mail', $this);
		$skipMail = $this->filter->apply('cppress-cf-skipmail', $this->skipMail, $this);

		if($skipMail){
			return true;
		}
		$template = array(
			'subject' => $this->filter->apply('cppress_translate_field', $this->instance['subject']),
			'to' => $this->instance['to'],
			'from' => $this->instance['from'],
			'body' => $this->filter->apply('cppress_translate_field', $this->instance['body']),
			'additionalheaders' => $this->instance['additionalheaders'],
			'excludeblank' => isset($this->instance['excludeblank']) ?  true : false,
			'usehtml' => isset($this->instance['usehtml']) ? true : false
		);
		$mailer = new Mailer($template, $this, $this->request);
		$result = $mailer->send();

		if($result){
			return true;
		}

		return false;
	}

	public function is($status){
		return $this->status == $status;
	}

	public function ajaxSubmit($instance, $args){
		$this->instance = $instance;
		$this->args = $args;
		$unitTag = $this->sanitizeUnitTag($this->request->getParam('_cppress-cf-unit-tag'));
		$items = array(
			'mailSent' => false,
			'into' => '#' . $unitTag
		);
		$result = $this->submit(true);

		if(!empty($result['message'])){
			$items['message'] = $result['message'];
		}

		if('mail_sent' == $result['status']){
			$items['mailSent'] = true;
		}
		if('validation_failed' == $result['status']){
			$invalids = array();
			foreach($result['invalid_fields'] as $name => $field){
				$invalids[] = array(
					'into' => 'span.cppress-cf-control-wrap.' . sanitize_html_class($name),
					'message' => $field['reason'],
					'idref' => $field['idref']
				);
			}

			$items['invalids'] = $invalids;
		}

		if('spam' == $result['status']){
			$items['spam'] = true;
		}

		if(!empty($result['scripts_on_sent_ok'])){
			$items['onSentOk'] = $result['scripts_on_sent_ok'];
		}

		if(!empty($result['script_on_submit'])){
			$items['onSubmit'] = $result['script_on_submit'];
		}

		return $items;

	}

	public function nonajaxSubmit($instance, $args){
		$this->instance = $instance;
		$this->args = $args;
		return $this->submit();
	}

	protected function setupData(){
		$data = $this->request->getParams();
		$data = array_diff_key($data, array('_wp_nonce' => ''));
		$data = $this->sanitize($data);
		foreach($this->usedShortcode as $tag){
			if(empty($tag['name'])){
				continue;
			}

			$name = $tag['name'];
			$value = '';
			if(isset($data[$name])){
				$value = $data[$name];
			}

			$data[$name] = $value;
		}

		$this->data = $data; /** TODO filter it */
		return $this->data;
	}

	private function sanitizeUnitTag($tag){
		$tag = preg_replace('/[^A-Za-z0-9_-]/', '', $tag);
		return $tag;
	}

	public static function initUploads(){
		$dir = self::uploadTmpDir();
		wp_mkdir_p($dir);
		$htaccesFile = trailingslashit($dir) . '.htaccess';
		if(file_exists($htaccesFile)){
			return;
		}

		if($hanlde = @fopen($htaccesFile, 'w')){
			fwrite($handle, "Deny from all\n");
			fclose($handle);
		}
	}

	public static function uploadTmpDir(){
		return self::uploadDir('dir') . 'cppress-cf-uploads';
	}

	public static function uploadDir($type=false){
		$uploads = wp_upload_dir();

		$uploads = apply_filters('cppress-cf-upload-dir', array(
			'dir' => $uploads['basedir'],
			'url' => $uploads['url']
		));

		if($type == 'dir'){
			return $uploads['dir'];
		}
		if($type == 'url'){
			return $uploads['url'];
		}

		return $uploads;
	}

	public static function maybeAddRandomDir($dir){
		do{
			$randMax = mt_getrandmax();
			$rand = zeroise(mt_rand(0, $randMax), strlen($randMax));
			$dirNew = path_join($dir, $rand);
		} while(file_exists($dirNew));

		if(wp_mkdir_p($dirNew)){
			return $dirNew;
		}

		return $dir;
	}

}