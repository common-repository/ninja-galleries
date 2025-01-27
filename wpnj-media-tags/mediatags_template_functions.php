<?php
function is_mediatag()
{
	global $wp_query;
	
	if ($wp_query->is_mediatags == true)
		return true;
	else
		return false;
}

function in_mediatag($mediatag_id = '')
{
	if (!$mediatag_id) return;
	
	$mediatag_var = get_query_var(WPNJ_MEDIA_TAGS_QUERYVAR);
	if ($mediatag_var)
	{	
		$mediatag_term = term_exists( $mediatag_var, WPNJ_MEDIA_TAGS_TAXONOMY );
		if ($mediatag_id === $mediatag_term['term_id'])
			return true;
	}
	return false;	
}


function &wpnj_get_mediatags( $args = '' ) {
	
	$media_tags = get_terms( WPNJ_MEDIA_TAGS_TAXONOMY, $args );

	if ( empty( $media_tags ) ) {
		$return = array();
		return $return;
	}

	$media_tags = apply_filters( 'wpnj_get_mediatags', $media_tags, $args );
	return $media_tags;
}

function list_mediatags($args = '' ) {
	
	$defaults = array(
		'echo' => '1'		
	);
	$r = wp_parse_args( $args, $defaults );
	
	$media_tag_list = wpnj_get_mediatags( $args );
	if (!$media_tag_list)
	{
		$return = array();
		return $return;
	}		
	
	$media_tag_list = apply_filters( 'list_mediatags', $media_tag_list, $args );
	if (!$media_tag_list)
	{
		$return = array();
		return $return;
	}		
	
	$media_tag_list_items = "";
	foreach($media_tag_list as $media_tag_item)
	{
		$media_tag_list_items .= '<li><a href="'. wpnj_get_mediatag_link($media_tag_item->term_id). '">'. 
			$media_tag_item->name. '</a></li>';
	}
	
	if ($r['echo'] == 1)
		echo $media_tag_list_items;
	else
		return $media_tag_list_items;
}

// Return the href link value for a given tag_id
// modeled after WP get_tag_link() function
function wpnj_get_mediatag_link( $mediatag_id, $is_feed=false ) {
	global $wp_rewrite;

	$mediatag_link = "";
	if (isset($wp_rewrite) && $wp_rewrite->using_permalinks())
	{
		$wpnjmediatags_token = '%' . WPNJ_MEDIA_TAGS_QUERYVAR . '%';
		$mediatag_link = $wp_rewrite->front . WPNJ_MEDIA_TAGS_URL . "/".$wpnjmediatags_token;	
	}

	$media_tag = &get_term( $mediatag_id, WPNJ_MEDIA_TAGS_TAXONOMY );
	if ( is_wp_error( $media_tag ) )
		return $media_tag;
	
	$mediatag_slug = $media_tag->slug;

	if ( empty( $mediatag_link ) ) {
		$file = get_option( 'home' ) . '/';
		$mediatag_link = $file . '?wpnj-media-tag=' . $mediatag_slug;
	} 
	else {
		$mediatag_link = str_replace( '%wpnj-media-tag%', $mediatag_slug, $mediatag_link );
		$mediatag_link = get_option( 'home' ) . user_trailingslashit( $mediatag_link );
	}

	if ($is_feed == true)
	{
		if (isset($wp_rewrite) && $wp_rewrite->using_permalinks())
		{
			$mediatag_link .= "feed/";
		}
		else
		{
			$mediatag_link .= "&feed=rss2";
		}
	}

	return apply_filters( 'wpnj_get_mediatag_link', $mediatag_link, $mediatag_id );
}

// Stadnard template function modeled after WP the_tags function. Used to list tags for a given post. 
function the_mediatags( $before = 'Media-Tags: ', $sep = ', ', $after = '' ) {
	return the_terms( 0, WPNJ_MEDIA_TAGS_TAXONOMY, $before, $sep, $after );
}

function wpnj_get_attachments_by_media_tags($args='')
{
	global $wpnjmediatags;
	
	return $wpnjmediatags->wpnj_get_attachments_by_media_tags($args);
}

function single_mediatag_title()
{
	$mediatag_var = get_query_var(WPNJ_MEDIA_TAGS_QUERYVAR);
	if ($mediatag_var) {	
		$mediatag_term = term_exists( $mediatag_var, WPNJ_MEDIA_TAGS_TAXONOMY );
		if (isset($mediatag_term['term_id'])) {
			$media_tag = &get_term( $mediatag_term['term_id'], WPNJ_MEDIA_TAGS_TAXONOMY );
			echo $media_tag->name;
		}
	}	
}

function mediatags_cloud( $args='' ) {
	if (function_exists('wp_tag_cloud'))
	{
		$defaults = array(
			'taxonomy' => WPNJ_MEDIA_TAGS_TAXONOMY		
		);
		$r = wp_parse_args( $args, $defaults );
		return wp_tag_cloud( $r );
	}
}

function get_the_mediatags( $id = 0 ) {
	return apply_filters( �get_the_mediatags�, get_the_terms( $id, WPNJ_MEDIA_TAGS_TAXONOMY ) );
}

?>