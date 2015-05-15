<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'YouTubEE',
	'pi_version' =>'1.0',
	'pi_author' =>'Steve Callan',
	'pi_author_url' => 'http://www.stevecallan.com/',
	'pi_description' => 'Displays loop of a users YouTube videos.',
	'pi_usage' => youtubee::usage()
);

/**
* Youtubee Class
*
* @package		ExpressionEngine
* @category		Plugin
* @author		Steve Callan
* @copyright	Copyright (c) 2012, Callan Interactive
*/

class YouTubee {

	function __construct()
	{
		$this->EE =& get_instance();
	}
	
	function entries()
	{	
	
		/* Intitial Variables */
			$output = "";
			$tag_content = $this->EE->TMPL->tagdata;
			$user = $this->EE->TMPL->fetch_param('user');
			$limit = $this->EE->TMPL->fetch_param('limit');
			$key_limit = $this->EE->TMPL->fetch_param('key');
			
			if($limit == "")
			{
				$limit = 0;
			}
			
			if($key_limit == "")
			{
				$key_limit = NULL;
			}
		
		/* Get the feed as an array */
            $i = 1;
			$key = $this->EE->config->item('youtubee:googleapikey');
			$playlist = $this->_get_playlist_for_user($user, $key);
			$feed_array = $this->_get_data($playlist, $key, $limit, $key_limit);
		
			foreach($feed_array AS $video)
			{
				
				$swap = array(
					'title' => $video["title"], 
					'image' => $video["image"], 
					'short_description' => $video["short_description"], 
					'time' => $video["time"],
					"url" => $video["url"],
					"views" => $video["views"],
					"date" => $video["date"],
					"key" => $video["key"],
					"count" => $i
				);
	  			
	  			$item_content	= $this->EE->functions->prep_conditionals( $tag_content, $swap );
				
				$record_contents = "";
				$record_contents = $this->EE->functions->var_swap($item_content, $swap);
				$output .= $record_contents;
				
				$i++;
				
			}
		
		/*  AND finally return */
			if($output == "")
			{
				$output = "<p>There were no videos found for this user.</p>";
			}
		
			return $output;
		
	}
	
	function _get_playlist_for_user($user, $key)
	{
		$url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=' . urlencode($user) . '&key=' . urlencode($key);
		try {
			$data = @file_get_contents($url);
			$data = json_decode($data, true);
			return $data['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
		}
		catch (Exception $e) {
			return NULL;
		}
	}

	function _get_data($playlist, $key, $count = 0, $key_limit = NULL)
	{
		$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=' . urlencode($playlist) . '&key=' . urlencode($key);
		
		if($key_limit != NULL)
		{
			$key_array = explode("|",$key_limit);
		}
	
		$feed_array = array();
	
		try{
			$data = @file_get_contents($url);
			$data = json_decode($data, true);
			$i = 0;
	
			foreach($data['items'] AS $entry){
		
				$push_data = FALSE;
				$entry_data = $this->_parse_item($entry);
	
				if($count != 0)
				{
					if($i < $count){
						
						/* If we are filting our keys check if it's in the key array */
						if($key_limit != NULL)
						{
							
							if(in_array($entry_data["key"],$key_array))
							{
								$push_data = TRUE;
							}
							
						}
						else
						{
							$push_data = TRUE;
						}
					}
				}	
				else
				{
					/* If we are filting our keys check if it's in the key array */
						if($key_limit != NULL)
						{
							
							if(in_array($entry_data["key"],$key_array))
							{
								$push_data = TRUE;
							}
							
						}
						else
						{
							$push_data = TRUE;
						}
				}
				
				if($push_data == TRUE)
				{
						
					array_push($feed_array,array(
						"title"=>$entry_data["title"],
						"image"=>$entry_data["image"], 
						"short_description"=>$entry_data["short_description"], 
						"time"=>$entry_data["time"],
						"date"=>$entry_data["date"],
						"url"=>$entry_data["url"],
						"views"=>number_format($entry_data["views"]),
						"key" => $entry_data["key"]
					));
				}
	
				$i++;
			}		
	
		}catch (Exception $e) {
			return $feed_array;
		}
		
		return $feed_array;
	
	}
	
	function _parse_item($item)
	{
		$title = $item['snippet']['title'];
		$image = $item['snippet']['thumbnails']['standard']['url'];
		$short_description = $item['snippet']['description'];
		$time = '0:00'; // Google's v3 YouTube API does not return time.
		$date = $item['snippet']['publishedAt'];
		$views = 0; // Google's v3 YouTube API does not return views.
		$key = $item['snippet']['resourceId']['videoId'];
		$url = 'https://www.youtube.com/watch?v=' . urlencode($key);
	
		$data_array = array(
			"title"=> $title,
			"image"=> $image, 
			"short_description"=> $short_description, 
			"time"=>$time,
			"date"=>$date,
			"url"=>$url,
			"views"=>$views,
			"key"=>$key
		);
	
		return $data_array;
	}
		
	function usage()
	{
	
		ob_start(); 
		?>
		YouTubEE plugin allows you to display the contents of a users YouTube stream.
		
		{exp:youtubee:entries}
			<article>
				<h3>{title}</h3>
				{short_description}
			</article>
		{/exp:youtubee:entries}
		
		Variables:
		{title} - The title of the video
		{short_description} - The short description of the video
		{image} - thumbnail of the video
		{views} - The number of views this video has
		{time} - The total time of the video
		{url} - The YouTube Video Link of the video
		{key} - The unique identifier for this video
		{count} - The current count in the loop
		
		<?php
		$buffer = ob_get_contents();
		
		ob_end_clean(); 
		
		return $buffer;
	
	}
	
}