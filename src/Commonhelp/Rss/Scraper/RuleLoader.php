<?php

namespace Commonhelp\Rss\Scraper;

use Commonhelp\Rss\RssConfig;

/**
 * RuleLoader class.
 *
 */
class RuleLoader{

	private $config;
	
	public function __construct(RssConfig $config){
		$this->config = $config;
	}
	
    /**
     * Get the rules for an URL.
     *
     * @param string $url the URL that should be looked up
     *
     * @return array the array containing the rules
     */
    public function getRules($url){
        $hostname = parse_url($url, PHP_URL_HOST);

        if ($hostname !== false) {
            $files = $this->getRulesFileList($hostname);

            foreach ($this->getRulesFolders() as $folder) {
                $rule = $this->loadRuleFile($folder, $files);
                if (!empty($rule)) {
                    return $rule;
                }
            }
        }

        return array();
    }

    /**
     * Get the list of possible rules file names for a given hostname.
     *
     * @param string $hostname Hostname
     *
     * @return array
     */
    public function getRulesFileList($hostname){
        $files = array($hostname);                 // subdomain.domain.tld
        $parts = explode('.', $hostname);
        $len = count($parts);

        if ($len > 2) {
            $subdomain = array_shift($parts);
            $files[] = implode('.', $parts);       // domain.tld
            $files[] = '.'.implode('.', $parts);   // .domain.tld
            $files[] = $subdomain;                 // subdomain
        } elseif ($len === 2) {
            $files[] = '.'.implode('.', $parts);    // .domain.tld
            $files[] = $parts[0];                   // domain
        }

        return $files;
    }

    /**
     * Load a rule file from the defined folder.
     *
     * @param string $folder Rule directory
     * @param array  $files  List of possible file names
     *
     * @return array
     */
    public function loadRuleFile($folder, array $files){
        foreach ($files as $file) {
            $filename = $folder.'/'.$file.'.php';
            if (file_exists($filename)) {
                return include $filename;
            }
        }

        return array();
    }

    /**
     * Get the list of folders that contains rules.
     *
     * @return array
     */
    public function getRulesFolders(){
        $folders = array(__DIR__.'/Rules');
        
        if ($this->config !== null && $this->config->getGrabberRulesFolder() !== null) {
        	$folder = $this->config->getGrabberRulesFolder();
        	if(is_array($folder)){
        		foreach($folder as $f){
        			$folders[] = $f;
        		}
        	}else{
        		$folders[] = $folder;
        	}
        }
        return $folders;
    }
}