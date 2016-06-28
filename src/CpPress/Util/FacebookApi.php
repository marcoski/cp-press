<?php
namespace CpPress\Util;

class FacebookApi{
	
	private $appId;
	
	private $appSecret;
	
	private $fbId;
	
	private $error;
	
	public function __construct($appId, $appSecret, $fbId){
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->fbId = $fbId;
	}
	
	public function getPosts(){
		$result = $this->call("{$this->fbId}/posts", array(
			'fields' => 'id,picture,type,from,message,status_type,object_id,name,caption,description,link,created_time,comments.limit(1).summary(true),likes.limit(1).summary(true)'
		));
		if(is_object($result)){
			if(isset($result->data)){
				return $this->formatData($result->data);
			}else if(isset($result->error->message)){
				$this->error = __('Facebook error:', 'cppress') . '<code>' . $result->error->message . '</code>';
				return false;
			}
		}
		
		return false;
	}
	
	public function hasError(){
		return !empty($this->error);
	}
	
	public function getErrorMessage(){
		if(is_object($this->error)){
			return $this->error->message;
		}
		
		return $this->error;
	}
	
	private function formatData($data){
		$posts = array();
		foreach($data as $p){
			if(!in_array($p->type, array('status', 'photo', 'video', 'link'))){
				continue;
			}
			
			if($p->type === 'status' && (!isset($p->message) || empty($p->message))){
				continue;
			}
			
			if($p->type === 'link' && !isset($p->name) && (!isset($p->message) || empty($p->message))){
				continue;
			}
			
			if($p->type === 'status' && $p->status_type === 'approved_friend'){
				continue;
			}
			$idArray = explode('_', $p->id);
			$post = array();
			$post['type'] = sanitize_text_field($p->type);
			$post['content'] = '';
			$post['image'] = null;
			$post['name'] = '';
			$post['link'] = '#';
			if(isset($p->message)){
				$post['content'] = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]/u', '', $p->message);
				$post['content'] = sanitize_text_field($post['content']);
			}
			
			if(isset($p->name)){
				$post['name'] = $p->name;
			}
			if(isset($p->link)){
				$post['link'] = $p->link;
			}
			switch($p->type){
				case 'photo':
					$post['image'] = "//graph.facebook.com/" . $p->object_id . '/picture';
					break;
				case 'video':
					$post['image'] = $p->picture;
					break;
				case 'link':
					$post['link_image'] = isset($p->picture) ? $p->picture : '';
					$post['link_name'] = isset($p->name) ? sanitize_text_field($p->name) : '';
					$post['link_caption'] = isset($p->caption) ? sanitize_text_field($p->caption) : '';
					$post['link_description'] = isset($p->description) ? sanitize_text_field($p->description) : '';
					$post['link_url'] = $p->link;
					break;
			}
			
			if(isset($p->likes->summary->total_count)){
				$post['like_count'] = absint($p->likes->summary->total_count);
			}else{
				$post['like_count'] = 0;
			}
			
			if(isset($p->comments->summary->total_count)){
				$post['comment_count'] = absint($p->comments->summary->total_count);
			}else{
				$post['comment_count'] = 0;
			}
			
			$post['timestamp'] = strtotime($p->created_time);
			$post['url'] = 'https://facebook.com/' . $this->fbId . '/posts' . $idArray[1];
			$posts[] = $post;
		}
		return $posts;
	}
	
	private function ping(){
		$result = $this->call("{$this->fbId}/posts", array('fields' => 'name', 'limit' => 1));
		if(is_object($restul)){
			if(isset($result->data)){
				return true;
			}else if(isset($result->error_message)){
				$this->error = __('Facebook error', 'cppress') . '<code>' . $result->error_message . '</code>';
				return false;
			}
		}
		
		return false;
	}
	
	private function call($endpoint, array $data = array()){
		if(empty($this->appId) || empty($this->appSecret)){
			return false;
		}
		
		
		$url = "https://graph.facebook.com/{$endpoint}";
		$data['access_token'] = "{$this->appId}|{$this->appSecret}";
		$data['locale'] = get_locale();
		$url = add_query_arg($data, $url);
		$response = wp_remote_get($url, array(
				'timeout' => 10,
				'headers' => array('Accept-Encoding' => ''),
				'sslverify' => false
		));
		
		if(is_wp_error($response)){
			$this->error = __('Connection error:', 'cppress') . '<code>' . $response->get_error_message() . '</code>';
			return false;
		}else{
			$body = wp_remote_retrieve_body($response);
			return json_decode($body);
		}
	}
	
}