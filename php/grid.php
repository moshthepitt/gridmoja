<?php
/*This file handles grid display shortcodes*/

/** Shortcode to display posts in grid views **/
add_shortcode('grid_moja', 'gm_grid_display');

function gm_grid_display($atts, $content=null) {
		//extract shortcode attributes
		extract(shortcode_atts(array(
                'category' => '1',
                'total' => '12',
                'column' => '3',
                'per_page' => '',
                'type' => 'post',				
                'order' => 'DESC',
				'class'=> '',
				'exclude' => ''
                    ), $atts));		
		//Create pagination		
	    if (!empty($per_page)) {
        $pagenum = isset($_GET['dpage']) ? $_GET['dpage'] : 1;		
		if ($type != 'page'){
			$count = count(get_posts('numberposts=' . $total . '&post_type=' . $type . '&category=' . $category));
		}else {
			$count = count(get_posts('numberposts=' . $total . '&post_type=' . $type));
		}		
        $page_links = paginate_links(array(
                    'base' => add_query_arg('dpage', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($count / $per_page),
                    'current' => $pagenum
                ));
        $post_offset = ($pagenum - 1) * $per_page;
        $offset = '&offset=' . $post_offset;
        $page_links = '<div class="gm_grid_pagination">' . $page_links . '</div>';
		} else {
			$per_page = $total;
			$offset = '';
			$page_links = '';
		}		
		//Handle the order of items
		if ($order != 'rand') {
			$order_string = 'orderby=post_date&order=' . $order . '&';
		} else {
			$order_string = 'orderby=rand&';
		}		
		//Get the content
		if ($type != 'page'){
			$content_items = get_posts($order_string . 'numberposts=' . $per_page . '&post_type=' . $type . '&category=' . $category . $offset);
		} else {
			$content_items = get_posts($order_string . 'numberposts=' . $per_page . '&post_type=' . $type . $offset);
		}		
		//Build the stuff for shortcode to output
		if (is_array($content_items) && count($content_items) > 0) {
			$content .= '<div class="gm_grid_display ' .$class. '">';
			$count = 1;
			$all_count = 0;				
			//get comma seperated ids in an array and split at comma
			$content_ids_exclude = explode(",", $exclude);			
			//Create output for each item
			foreach ($content_items as $content_item) {	
				//check if the item should be excluded
				if(!in_array($content_item->ID, $content_ids_exclude)){
					$output = get_post($content_item->ID);
					if ($output) {						
						$content_item_permalink = get_permalink($content_item->ID);					
						$content .= '<div class="gm_grid_item item-' . $content_item->ID . '">';					
						$content .= '<div class="gm_grid_item_image">';								
						$content .= '<a href="' . $content_item_permalink . '" title="' . $content_item->post_title . '"> ' .content_image($content_item->ID). ' </a>';				
						$content .= '</div>';
						$content .= '<div class="gm_grid_item_detail">';
						$content .= '<p class="title"><a href="' . $content_item_permalink . '" title="' . $content_item->post_title . '" alt="' . $content_item->post_title . '">' . __($content_item->post_title) . '</a></p>';
						$content .= '<p class="detail">' . $content_item->post_excerpt . '</p>';                
						$content .= '</div>';						
						$content .= '</div>';
						if ($count === intval($column)) {
							$content .= '<div class="clear"></div>';
							$count = 0;
						}
						$count++;
						$all_count++;
					}
				}
			}
			$content .= '<div class="clear"></div>' . $page_links . '<div class="clear"></div>';
			$content .= '</div>';
			$content .= '<div class="clear"></div>';
		}
	return $content;
	}

/** ID grid views - handles custom taxonomies **/
add_shortcode('id_grid', 'gm_id_grid_display');

function gm_id_grid_display($atts, $content=null) {
		//extract shortcode attributes
		extract(shortcode_atts(array(                
                'total' => '12',
                'column' => '3',
                'per_page' => '',
                'id' => '',                
                'order' => 'DESC',
				'orderby' => 'post_date',				
				'class'=> ''
                    ), $atts));	

		//get comma seperated ids of posts to include in an array and split at comma
		$content_ids = explode(",", $id);	
		//Create pagination		
	    if (!empty($per_page)) {
			$pagenum = isset($_GET['dpage']) ? $_GET['dpage'] : 1;		
			$count = count($content_ids);			
        	$page_links = paginate_links(array(
                    'base' => add_query_arg('dpage', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($count / $per_page),
                    'current' => $pagenum
                ));
        	$post_offset = ($pagenum - 1) * $per_page;
        	$offset = $post_offset;
        	$page_links = '<div class="gm_grid_pagination">' . $page_links . '</div>';
		} else {
			$per_page = $total;
			$offset = 0;
			$page_links = '';
		}	
		//Get content
		$content_items = array();
		$args = array( 'post__in' => array(id) );
		foreach ($content_ids as $content_id) :			
    		$content_items[] = get_posts($content_id);
		endforeach;

		//Build the stuff for shortcode to output
		if (is_array($content_items) && count($content_items) > 0) {
			$content .= '<div class="gm_grid_display ' .$class. '">';
			$count = 1;
			$all_count = 0;		
			//Create output for each item
			foreach ($content_ids as $content_id) {				
					$output = get_post($content_id);
					if ($output) {						
						$content_item_permalink = get_permalink($content_id);					
						$content .= '<div class="gm_grid_item item-' . $content_id . '">';					
						$content .= '<div class="gm_grid_item_image">';								
						$content .= '<a href="' . $content_item_permalink . '" title="' . $output->post_title . '"> ' .content_image($content_id). ' </a>';				
						$content .= '</div>';
						$content .= '<div class="gm_grid_item_detail">';
						$content .= '<p class="title"><a href="' . $content_item_permalink . '" title="' . $output->post_title . '" alt="' . $output->post_title . '">' . __($output->post_title) . '</a></p>';
						if (POST_CONTENT_ON){
							$content .= '<div class="detail">' . $output->post_content . '</div>';
						}else{	
							$content .= '<p class="detail">' . $output->post_excerpt . '</p>';  
						}		
						$content .= '</div>';						
						$content .= '</div>';
						if ($count === intval($column)) {
							$content .= '<div class="clear"></div>';
							$count = 0;
						}
						$count++;
						$all_count++;
					}
				
			}
			$content .= '<div class="clear"></div>' . $page_links . '<div class="clear"></div>';
			$content .= '</div>';
			$content .= '<div class="clear"></div>';
		}	
	return $content;	
}
	
/** Advanced grid views - handles custom taxonomies **/
add_shortcode('advanced_grid', 'gm_advanced_grid_display');

function gm_advanced_grid_display($atts, $content=null) {
		//extract shortcode attributes
		extract(shortcode_atts(array(
                'category' => '1',
                'total' => '12',
                'column' => '3',
                'per_page' => '',
                'type' => 'post',
                'order' => 'DESC',
				'orderby' => 'post_date',				
				'class'=> '',
				'exclude' => ''
                    ), $atts));	
		//extract taxonomies
		$taxonomies = array();
		foreach ($atts as $key => $val){			
			if (taxonomy_exists($key)){				
				$taxonomies[$key] = $val;
			}
		}	
		//build advanced query
		$query_array=array();
		foreach ($taxonomies as $tax_name => $tax_id){
			$query_array[$tax_name] = array(
				'taxonomy' => $tax_name,
				'terms' => explode(",",$tax_id),
				'field' => 'term_id',
				);
		}
		$get_content_items = get_posts(array(
			'post_type' => $type,
			'numberposts' => $total,
			'tax_query' => $query_array,
			)
		);					
		//Create pagination		
	    if (!empty($per_page)) {
			$pagenum = isset($_GET['dpage']) ? $_GET['dpage'] : 1;		
		if ($type != 'page'){
			$count = count($get_content_items);			
		}else {
			$count = count(get_posts('numberposts=' . $total . '&post_type=' . $type));
		}		
        $page_links = paginate_links(array(
                    'base' => add_query_arg('dpage', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($count / $per_page),
                    'current' => $pagenum
                ));
        $post_offset = ($pagenum - 1) * $per_page;
        $offset = $post_offset;
        $page_links = '<div class="gm_grid_pagination">' . $page_links . '</div>';
		} else {
			$per_page = $total;
			$offset = 0;
			$page_links = '';
		}		
		//Get the content
		if ($type != 'page'){			
			$content_items = get_posts(array(
				'post_type' => $type,
				'numberposts' => $per_page,
				'order' => $order,
				'orderby' => $orderby,
				'offset' => $offset,
				'tax_query' => $query_array,
				)
			);
		} else {			
			$content_items = get_posts(array(
				'post_type' => $type,
				'numberposts' => $per_page,
				'order' => $order,
				'orderby' => $orderby,
				'offset' => $offset,				
				)
			);
		}
		//Build the stuff for shortcode to output
		if (is_array($content_items) && count($content_items) > 0) {
			$content .= '<div class="gm_grid_display ' .$class. '">';
			$count = 1;
			$all_count = 0;				
			//get comma seperated ids of posts to exclude in an array and split at comma
			$content_ids_exclude = explode(",", $exclude);			
			//Create output for each item
			foreach ($content_items as $content_item) {	
				//check if the item should be excluded
				if(!in_array($content_item->ID, $content_ids_exclude)){
					$output = get_post($content_item->ID);
					if ($output) {						
						$content_item_permalink = get_permalink($content_item->ID);					
						$content .= '<div class="gm_grid_item item-' . $content_item->ID . '">';					
						$content .= '<div class="gm_grid_item_image">';								
						$content .= '<a href="' . $content_item_permalink . '" title="' . $content_item->post_title . '"> ' .content_image($content_item->ID). ' </a>';				
						$content .= '</div>';
						$content .= '<div class="gm_grid_item_detail">';
						$content .= '<p class="title"><a href="' . $content_item_permalink . '" title="' . $content_item->post_title . '" alt="' . $content_item->post_title . '">' . __($content_item->post_title) . '</a></p>';
						if (POST_CONTENT_ON){
							$content .= '<div class="detail">' . $content_item->post_content . '</div>';
						}else{	
							$content .= '<p class="detail">' . $content_item->post_excerpt . '</p>';  
						}		
						$content .= '</div>';						
						$content .= '</div>';
						if ($count === intval($column)) {
							$content .= '<div class="clear"></div>';
							$count = 0;
						}
						$count++;
						$all_count++;
					}
				}
			}
			$content .= '<div class="clear"></div>' . $page_links . '<div class="clear"></div>';
			$content .= '</div>';
			$content .= '<div class="clear"></div>';
		}
	return $content;		
	}					
?>