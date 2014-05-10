<?php
/*This file handles images*/

/*Fallback image*/
//function to call first uploaded image in functions file
function first_image($post_id) {
$files = get_children('post_parent='. $post_id .'&post_type=attachment
&post_mime_type=image&order=desc');
  if($files) :
    $keys = array_reverse(array_keys($files));
    $j=0;
    $num = $keys[$j];
    $image=wp_get_attachment_image($num, 'gm-thumb', true);
    $imagepieces = explode('"', $image);
    $imagepath = $imagepieces[1];
    $main=wp_get_attachment_url($num);
		$template=get_template_directory();
		$the_title=get_the_title();
    return "<img src='$main' alt='$the_title' class='frame'/>";
  endif;
}

/*Get Post Image*/
//fucntion to get image for post
function content_image($post_id){
	$thumb_image=get_the_post_thumbnail($post_id, 'gm-thumb');
	$fallback_image = first_image($post_id);
	$default_image = '<img src="' . GM_PLUGIN_URL . '/img/default.jpg">';						
	if ($thumb_image != ''){
		return $thumb_image;
	}elseif($fallback_image != ''){							
		return $fallback_image;
	}elseif(DEFAULT_IMAGE_ON){
		return $default_image;
	}else{							
		return '';
	}
}
?>