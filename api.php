<?php
/*
*	Handling diskusijam.lv API calls
*/
	function diskusijam_apiCall($action, $args = array()){
		$url = 'http://diskusijam.lv/api/?page_apikey='.get_option('diskusijam_api_page_key').'&user_apikey='.get_option('diskusijam_api_user_key').'&format=json';
		$url.='&action='.$action;
		if(!empty($args)){
			foreach($args as $k=>$v){
				if($v!==false){
					$url.='&'.urlencode($k).'='.($v);
				}
			}
		}
		if(ini_get('allow_url_fopen') == 1){
			$response = @file_get_contents($url);
		}elseif(function_exists('curl_init')){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$response = curl_exec($ch);
			curl_close($ch);			
		}else{
            $parts = parse_url($url); 
            $target = $parts['host'];
            $port = 80;
            
            $page    = isset($parts['path'])        ? $parts['path']            : '';
            $page   .= isset($parts['query'])       ? '?' . $parts['query']     : '';
            $page   .= isset($parts['fragment'])    ? '#' . $parts['fragment']  : '';
            $page    = ($page == '')                ? '/'                       : $page;
            if($fp = fsockopen($target, $port, $errno, $errstr, 15))
            {
                $headers  = "GET $page HTTP/1.1\r\n";
                $headers .= "Host: {$parts['host']}\r\n";
                $headers .= "Connection: Close\r\n\r\n";
                if(fwrite($fp, $headers))
                {
                    $response = '';                   
                    while(!feof($fp) && ($curr = fgets($fp, 128)) !== false)
                        $response .= $curr;
                    if(isset($curr) && $curr !== false)
                        $response = substr(strstr($response, "\r\n\r\n"), 3);
                }
                fclose($fp);
            }
		}

		if($response === false){//Request failed
			return false;
		}
		
		return $response;
	}
	
	/*
	*	Checking if api keys are valid
	*/
	function diskusijam_checkApiKeys($response){
		$response = json_decode($response);
		
		if(isset($response->page_id) && isset($response->profile_id)){
			return array(
				'page_id' => $response->page_id,
				'profile_id' => $response->profile_id
			);
		}
		return false;
	}
	
	/*
	*	Parsing comment counts
	*/
	function diskusijam_getCommentCount($thread_ids){
		$threads = implode(',', $thread_ids);
		
		$threads = json_decode(diskusijam_apiCall('count', array('threads' => $threads)));

		if((!empty($threads))){
			$c = array();
			foreach($threads as $th => $t){	
				$c[] = array(
					'thread_id' => $th,
					'count' => $t->count
				);
			}
		}else{
			return false;
		}
		return $c;
	}
	
	/*
	*	Syncing comments
	*/
	function diskusijam_syncComments($diskusijam_thread_id){
		global $wpdb;
		$import = 1;
		$diskusijam_comment = json_decode(diskusijam_apiCall('list', array('thread_id' => $diskusijam_thread_id, 'page' => 1, 'per_page' => 1)));
		
		// Comparing last comments for this thread
		if(!empty($diskusijam_comment)){
			$diskusijam_comment = array_keys(get_object_vars($diskusijam_comment));
			$wp_comment = $wpdb->get_results("SELECT M.meta_value AS `cid` FROM $wpdb->comments C LEFT JOIN $wpdb->commentmeta M ON M.comment_id = C.comment_ID AND M.meta_key = 'diskusijam_comment' ORDER BY M.meta_value + 0 DESC LIMIT 0,1");
			if(isset($wp_comment[0]->cid)){
				if($wp_comment[0]->cid != $diskusijam_comment[0]){
					// Last imported comment id
					$import = $wp_comment[0]->cid;
				}else{ 
					// All comments already synced
					$import = 0;
				}
			}

			// Syncing
			if($import > 0){
				$diskusijam_comments = json_decode(diskusijam_apiCall('list', array('thread_id' => $diskusijam_thread_id, 'cid' => $import, 'accept_required' => 0)));
				if(is_object($diskusijam_comments)){
					$comments_tmp = array_reverse(get_object_vars($diskusijam_comments));

					if(!empty($comments_tmp)){
						$comments = array();
						foreach($diskusijam_comments as $cid => $comment){
							$c = array();
							$c['comment_post_ID'] = $comment->thread_id;
							$c['parent_id'] = $comment->parent_id;
							$c['comment_author'] = $comment->author;
							$c['comment_date'] = $comment->date;
							$c['comment_date_gmt'] = $comment->date;
							$c['comment_content'] = $comment->text;
							$c['comment_approved'] = 1;						
							$comments[$cid] = $c;
						}
						ksort($comments);
						foreach($comments as $cid => $comment){	
							$parent_id = $comment['parent_id'];
							unset($comment['parent_id']);							
							if($parent_id > 0){
								$parent = $wpdb->get_results("SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'diskusijam_comment' AND meta_value = {$parent_id}");							
								if(isset($parent[0]->comment_id)){
									$comment['comment_parent'] = $parent[0]->comment_id;
								}								
							}							
							$wp_cid = wp_insert_comment($comment);
							update_comment_meta($wp_cid, 'diskusijam_comment', $cid);
						}
					}
				}
			}

		}
	}
?>