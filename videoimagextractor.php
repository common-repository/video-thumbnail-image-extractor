<?php
/*
Plugin Name:  Video Thumbnail Image Extractor
Plugin URI: http://arfeen.net/development
Description: A way to quickly embed web video thumbnails on your post (youtube and vimeo)
Version: 1.0
Author: Muhammad Arfeen
Author URI: http://arfeen.net
License: Freeware
*/


add_action('admin_menu', 'videoimagextractor_admin_operations'); 

add_filter('the_content', 'videoimagextractor_filterurls');  

function videoimagextractor_admin_operations() {
	  
   add_options_page("Video Thumbnail Image Extractor", "Video Thumbnail Image Extractor", 1, "VideoThumbnailImageExtractor", "videoimagextractor_admin");  
   
}  

function videoimagextractor_filterurls( $content ){
	
	preg_match_all('(((images_ht|images_withlink_ht|){1}(tp://|tps://))[-a-zA-Z0-9@:%_+.~#?&//=]+)',$content,$hyperlinksArray);
	
	for(	$iIndex=0;	$iIndex	<	count	(	$hyperlinksArray[0]	)	;	$iIndex++	){
		$link = $hyperlinksArray[0][$iIndex];		
		
			$videcontent = videoimagextractor_extractimagesfromvideo ( $link );		
			if(!$videcontent)
				return $content;
			$content = str_replace( $link, $videcontent, $content );
			
		
	}
	
	return $content;
	
}

function videoimagextractor_admin(){
	include "videoimagextractor_admin_help.php";	
}

function videoimagextractor_extractimagesfromvideo( $link = null) {
	
		if(strpos($link,"youtube.com") || strpos($link,"youtu.be"))
			$images = getYoutubeVideoImages( $link );
		else if(strpos($link,"vimeo.com"))
			$images = getVimeoVideoImages( $link );	
		else
			return false;
			
			
		$originalvideo = str_replace("images_withlink_","",$link);
		$originalvideo = str_replace("images_","",$originalvideo);
			
		for($i=0;$i<count($images);$i++){
			$divhtml .= "<img src=".$images[$i].">&nbsp;";
		}
			
		if(strpos($link,"images_withlink_")!==false){
			$linked = "<div><a target=_blank href=".$originalvideo.">".$divhtml."</a></div>";
		} else {
			$linked = "<div>".$divhtml."</div>";
		}
		
		
		return $linked;
		
}




function getYoutubeVideoImages($url) {

	if (preg_match('/watch\?v\=([A-Za-z0-9_-]+)/', $url, $matches))
  	$videoid = $matches[1];
  else if(preg_match('/youtu\.be\/([A-Za-z0-9_-]+)/', $url, $matches))    
  	$videoid = $matches[1];
  else if(preg_match('/youtube\.com\/vi\/([A-Za-z0-9_-]+)/', $url, $matches))    
  	$videoid = $matches[1];
  else
    return false;
            
   $thumb_image[] = "http://img.youtube.com/vi/".$videoid."/1.jpg";
   $thumb_image[] = "http://img.youtube.com/vi/".$videoid."/2.jpg";
   $thumb_image[] = "http://img.youtube.com/vi/".$videoid."/3.jpg";
   
   return $thumb_image;

}  

function getVimeoVideoImages( $url ){
	
	if(preg_match('/vimeo\.com\/([A-Za-z0-9_-]+)/', $url, $matches))    
  	$videoid = $matches[1];
  
  	
	$videoapi = "http://vimeo.com/api/v2/video/".$videoid.".json";
	
	$json = json_decode(file_get_contents($videoapi));
	$thumb_image[] = $json[0]->thumbnail_small;
	
	return $thumb_image;
	
}


?>