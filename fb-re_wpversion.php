<?php
/*
Plugin Name: Replace WP-Version
Plugin URI: http://bueltge.de/wordpress-version-verschleiern-plugin/602/
Description: Replace the WP-version with a random string &lt; WP 2.4 and eliminate WP-version &gt; WP 2.4
Author: Frank Bueltge
Version: 0.1
License: GPL
Author URI: http://bueltge.de/
*/

if (!is_admin() && !is_archive() && !is_attachment() && !is_author() &&
		!is_category() && !is_comments_popup() && !is_date() && !is_day() &&
		!is_home() && !is_month() && !is_page() && !is_paged() && !is_preview() &&
		!is_robots() && !is_search() && !is_single() && !is_singular() &&
		!is_time() && !is_trackback() && !is_year() && !is_404() ) {
	
	// random value
	$v = intval( rand(0, 9999) );
	
	if ( function_exists('the_generator') ) {
		// eliminate version for wordpress >= 2.4
		add_filter( 'the_generator', create_function('$a', "return null;") );
		// add_filter( 'wp_generator_type', create_function( '$a', "return null;" ) );
		
	} else {
		// for wordpress < 2.4
		add_filter( "bloginfo_rss('version')", create_function('$a', "return $v;") );
		
		// for rdf and rss v0.92
		$wp_version = $v;
	}
}
?>