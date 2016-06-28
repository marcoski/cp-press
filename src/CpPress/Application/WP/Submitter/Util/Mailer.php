<?php
namespace CpPress\Application\WP\Submitter\Util;

use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Submitter\Submitter;

class Mailer{
	
	private $template = array();
	
	private $request;
	private $submitter;
	
	public function __construct($template, Submitter $submitter, RequestInterface $request){
		$this->setupTemplate($template);
		$this->request = $request;
		$this->submitter = $submitter;
	}
	
	public function setupTemplate($template){
		$defaults = array(
			'subject' => '', 'to' => '', 'from' => '',
			'body' => '', 'additionalheaders' => '',
			'excludeblank' => false, 'usehtml' => false 	
		);
		
		$this->template = wp_parse_args($template, $defaults);
	}
	
	public function send(){
		return $this->compose();
	}
	
	public function compose($send=true){
		$useHtml = (bool) $this->template['usehtml'];
		$subject = $this->replaceTag($this->template['subject']);
		$from = $this->replaceTag($this->template['from']);
		$to = $this->replaceTag($this->template['to']);
		$additionalHeaders = $this->replaceTag($this->template['additionalheaders']);
		
		if($useHtml){
			$body = $this->replaceTag($this->template['body'], true);
			$body = wpautop($body);
		}else{
			$body = $this->replaceTag($this->template['body']);
		}
		
		$subject = $this->stripNewLine($subject);
		$from = $this->stripNewLine($from);
		$to = $this->stripNewLine($to);
		$additionalHeaders = trim($additionalHeaders);
		
		$headers = "From: $to\n";
		if($useHtml){
			$headers .= "Content-type: text/html\n";
		}
		if($additionalHeaders){
			$headers .= $additionalHeaders . "\n";
		}
		
		if($send){
			return wp_mail($to, $subject, $body, $headers);
		}
		
		return compact('subject', 'from', 'body', 'to', 'additionalHeaders');
		
	}
	
	private function replaceTag($content, $html=false){
		$args = array(
			'html' => $html,
			'exclude_blank' => $this->template['excludeblank']
		);
		if(is_array($content)){
			foreach($content as $key => $value){
				$content[$key] = $this->replaceTag($value, $html);
			}
			
			return $content;
		}
		
		$content = explode("\n", $content);
		foreach($content as $num => $line){
			$line = new MailerTagText($line, $this->submitter, $this->request, $args);
			$replaced = $line->replaceTags();
			
			if($this->template['excludeblank']){
				$replacedTags = $line->getReplacedTags();
				if(empty($replacedTags) || array_filter($replacedTags)){
					$content[$num] = $replaced;
				}else{
					unset($content[$num]);
				}
			}else{
				$content[$num] = $replaced;
			}
		}
		
		$content = implode("\n", $content);
		
		return $content;
	}
	
	private function stripNewLine($str){
		$str = (string) $str;
		$str = str_replace(array("\r", "\n"), '', $str);
		
		return trim($str);
	}
	
}