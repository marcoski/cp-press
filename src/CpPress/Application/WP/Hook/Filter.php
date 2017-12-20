<?php
namespace CpPress\Application\WP\Hook;

use CpPress\Application\CpPressApplication;

class Filter extends Hook{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}
	
	public function massRegister(){
	    $this->register('manage_event_posts_columns', function($columns){
	        return array_merge($columns, ['author' => __('Author')]);
        });
	    $this->register('pre_get_posts', function($query){
            if(in_array($query->get('post_type'), ['event'])){
                if(!current_user_can('edit_others_events')){
                    global $user_ID;
                    $query->set('author', $user_ID);
                }
            }
	        return $query;
        });
	}
	
	public function exec($hook, $flush=true){
		foreach($this->registered[$hook] as $hookInfo){
			list($closure, $priority, $acceptedArgs) = $hookInfo;
			add_filter($hook, $closure, $priority, $acceptedArgs);
		}
		if($flush){
			$this->flush($hook);
		}
	}
	
	public function apply(){
		$args = func_get_args();
		return call_user_func_array('apply_filters', $args);
	}
	
}