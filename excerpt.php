<?php

/*
  Modified by Miniblog.pl
 * 
  Oryginal plugin:  Auto Excerpt everywhere
  Plugin URI: http://www.josie.it/wordpress/wordpress-plugin-auto-excerpt-everywhere/
  Description: The plugin shows excerpts instead of contents in your blog, single posts and pages excluded. It tries to display your custom excerpt text and if it doesn't find it it will show an automatically generated excerpt. You can also define an excerpt length (default is 500) and a custom read more link.
  Version: 1.1
  Author: Serena Villa
  Author URI: http://www.josie.it
 */

function auto_spolecznosci_excerpt_construct() {
	$rss_disable = get_option("spolecznosci_excerpt_rss");
	if ($rss_disable == "yes") {
		if (!is_single() && !is_page() && !is_feed()) {
			add_filter('the_content', 'auto_excerpt');
		}
	} else {
		if (!is_single() && !is_page()) {
			//add_filter('the_excerpt_rss','auto_excerpt');
			add_filter('the_content', 'auto_excerpt');
		}
	}
	if (!get_option("spolecznosci_excerpt_length")) {
		update_option("spolecznosci_excerpt_length", "500");
	}
	if (!get_option("spolecznosci_excerpt_moretext")) {
		update_option("spolecznosci_excerpt_moretext", "Read more [...]");
	}
	if (!get_option("spolecznosci_excerpt_rss")) {
		update_option("spolecznosci_excerpt_rss", "yes");
	}
	if (!get_option("spolecznosci_excerpt_thumb")) {
		update_option("spolecznosci_excerpt_rss", "none");
	}
}

function myTruncate($string, $limit, $break=".", $pad="...") {

	if (strlen($string) <= $limit)
		return $string;
	if (false !== ($breakpoint = strpos($string, $break, $limit))) {
		if ($breakpoint < strlen($string) - 1) {
			$string = substr($string, 0, $breakpoint).$pad;
		}
	} return $string;
}

function auto_excerpt($content) {
	global $post;
	$testomore = get_option("spolecznosci_excerpt_moretext");
	$whatthumb = get_option("spolecznosci_excerpt_thumb");
	if ($whatthumb == "none") {
		$thumb = "";
	} else {
		if (has_post_thumbnail()) { // check if the post has a Post Thumbnail assigned to it.
			$default_attr = array(
				'class' => "attachment-$size alignleft",
				'alt' => trim(strip_tags(strip_shortcodes($attachment->post_excerpt))),
				'title' => trim(strip_tags(strip_shortcodes($attachment->post_title))),
			);
			$thumb = get_the_post_thumbnail($post->ID, $whatthumb, $default_attr);
		} else {
			$thumb = "";
		}
	}
	if ($post->post_excerpt != "") {
		$excerpt = $thumb.$post->post_excerpt;
		$linkmore = ' <a href="'.get_permalink().'" class="more-link">'.$testomore.'</a>';
	} else {
		if (strlen($post->post_content) > get_option("spolecznosci_excerpt_length")) {
			$excerpt = $thumb.myTruncate(strip_tags(strip_shortcodes($post->post_content)), get_option("spolecznosci_excerpt_length"), " ", "");
			$linkmore = ' <a href="'.get_permalink().'" class="more-link">'.$testomore.'</a>';
		} else {
			$excerpt = $thumb.$post->post_content;
			$linkmore = "";
		}
	}

	return $excerpt.$linkmore;
}

function custom_spolecznosci_excerpt_length() {
	return get_option("spolecznosci_excerpt_length");
}

function add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin)
		$this_plugin = plugin_basename(__FILE__);

	if ($file == $this_plugin) {
		$settings_link = '<a href="options-general.php?page=auto-excerpt-everywhere/options.php">'.__("Settings", "auto-excerpt-everywhere").'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

add_action('the_post', 'auto_spolecznosci_excerpt_construct');
add_filter('excerpt_length', 'custom_spolecznosci_excerpt_length');
add_filter('plugin_action_links', 'add_settings_link', 10, 2);
if (function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');
}
?>