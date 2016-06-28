<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;
use CpPress\Application\WP\Submitter\ContactFormSubmitter;

class FileEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$name = $eval->name;
		$id = $eval->getIdOption();
		
		$file = !is_null($this->request->getUploadedFile($name)) ? $this->request->getUploadedFile($name) : null;
		
		if($file['error'] && UPLOAD_ERR_NO_FILE != $file['error']){
			$this->validator->invalidate($eval, __('Failed to upload file. Error occurred.', 'cppress'));
			return;
		}
		
		if(empty($file['tmp_name']) && $eval->isRequired()){
			$this->validator->invalidate($eval, __('Please fill in the required field.', 'cppress'));
			return;
		}
		
		if(!is_uploaded_file($file['tmp_name'])){
			return;
		}
		
		$allowedTypes = array();
		
		if($fileTypes = $eval->getOption('filetypes')){
			foreach($fileTypes as $fTypes){
				$fTypes = explode('|', $fTypes);
				foreach($fTypes as $fType){
					$fType = trim($fType, '.');
					$fType = str_replace(
							array('.', '+', '*', '?'),
							array('\.', '\+', '\*', '\?'),
							$fType
					);
					$allowedTypes[] = $fType;
				}
			}
		}
		
		$allowedTypes = array_unique($allowedTypes);
		$fileTypePattern = implode('|', $allowedTypes);
		
		$allowedSize = 1048576; //default 1MB
		if($fileSizes = $eval->getOption('limit')){
			$limitPattern = '/^([1-9][0-9]*)([kKmM]?[bB])?$/';
			foreach($fileSizes as $fileSize){
				if(preg_match($limitPattern, $fileSize, $matches)){
					$allowedSize = (int) $matches[1];
					if(!empty($matches[2])){
						$kbmb = strtolower($matches[2]);
						if($kbmb == 'kb'){
							$allowedSize *= 1024;
						}else if($kbmb == 'mb'){
							$allowedSize *= 1024*1024;
						}
					}
					
					break;
				}
			}
		}
		
		if($fileTypePattern == ''){
			$fileTypePattern = 'jpg|jpeg|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv';
		}
		
		$fileTypePattern = trim($fileTypePattern, '|');
		$fileTypePattern = '(' . $fileTypePattern . ')';
		$fileTypePattern = '/\.' .$fileTypePattern .'$\i';
		
		if(!preg_match($fileTypePattern, $file['name'])){
			$this->validator->invalidate($eval, __('This file type is not allowed.', 'cppress'));
			return;
		}
		
		if($file['size'] > $allowedSize){
			$this->validator->invalidate($eval, __('This file is too large.', 'cppress'));
			return;
		}
		
		ContactFormSubmitter::initUploads();
		$uploadsDir = ContactFormSubmitter::uploadTmpDir();
		$uploadsDir = ContactFormSubmitter::maybeAddRandomDir($uploadsDir);
		
		$filename = $file['name'];
		$filename = $this->canonicalizeFileName($filename);
		$filename = sanitize_file_name($filename);
		$filename = $this->antiscriptFileName($filename);
		$filename = wp_unique_filename($uploadsDir, $filename);
		
		$newFile = trailingslashit($uploadsDir) . $filename;
		
		if(false === @move_uploaded_file($file['tmp_name'], $newFile)){
			$this->validator->invalidate($eval, __('Failed to upload file.', 'cppress'));
			return;
		}
		@chmod($newFile, 0400);
		
		return $newFile;
	}
	
	private function canonicalizeFileName($filename){
		$text = strtolower($text);
		return trim($text);
	}
	
	private function antiscriptFileName($filename){
		$filename = basename($filename);
		$parts = explode('.', $filename);
		
		if(count($parts) < 2){
			return $filename;
		}
		
		$scripPattern = '/^(php|phtml|pl|py|rb|cgi|asp|aspx)\d?$/i';
		$filename = array_shift($parts);
		$extension = array_pop($parts);
		
		foreach((array) $parts as $part){
			if(preg_match($scripPattern, $part)){
				$filename .= '.' . $part . '_';
			}else{
				$filename .= '.' . $part;
			}
		}
		
		if(preg_match($scripPattern, $extension)){
			$filename .= '.' . $extension . '_.txt';
		}else{
			$filename .= '.' . $extension;
		}
		
		return $filename;
		
	}
	
}