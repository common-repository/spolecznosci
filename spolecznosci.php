<?php

/**
 * @package Spolecznosci
 * @version 1.1.8
 */
/*
	Plugin Name: Miniblog.pl
	Plugin URI:
	Description: Plugin serwisu Miniblog.pl. Zapewnia narzędzia do zarządzania Twoją stroną oraz jej poprawne działanie. Wyłączenie pluginu jest równoznaczne z utratą wsparcia i jest naruszeniem regulaminu Miniblog.pl.
	Author: Miniblog.pl
	Version: 1.1.8
	Author URI: http://miniblog.pl
 */

/*
 * LOGOWANIE LOGINEM I HASHEM
 */
if ( !function_exists('wp_check_password') ) {
	function wp_check_password($password, $hash, $user_id = '') {
		global $wp_hasher;

		// If the hash is still md5...
		if ( strlen($hash) <= 32 ) {
			$check = ( $hash == md5($password) );
			if ( $check && $user_id ) {
				// Rehash using new hash.
				wp_set_password($password, $user_id);
				$hash = wp_hash_password($password);
			}

			return apply_filters('check_password', $check, $password, $hash, $user_id);
		}

		// If the stored hash is longer than an MD5, presume the
		// new style phpass portable hash.
		if ( empty($wp_hasher) ) {
			require_once( ABSPATH . 'wp-includes/class-phpass.php');
			// By default, use the portable hash from phpass
			$wp_hasher = new PasswordHash(8, TRUE);
		}

		if(strlen($password) > 32) { //$password jest już hashem
			echo '@';
			$check = $hash == $password;
		} else {
			$check = $wp_hasher->CheckPassword($password, $hash);
		}

		return apply_filters('check_password', $check, $password, $hash, $user_id);
	}
}

define('SPOLECZNOSCI_DIR', WP_PLUGIN_URL.'/spolecznosci/');

$mediaElementPlayerIndex = 1;

class Spolecznosci {

	function __construct() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('spolecznosci', SPOLECZNOSCI_DIR.'js/spolecznosci.js', array('jquery'));
		wp_enqueue_style('spolecznosci', SPOLECZNOSCI_DIR.'css/spolecznosci.css');

		if (get_option('spolecznosci_flvvideo')) {
			$this->VideoFlvEmbed();
		}
	}

	/* VIDEO FLV EMBED */

	function VideoFlvEmbed() {
		add_filter('media_send_to_editor', array(&$this, 'spolecznosci_filter_video'), 20, 3);
		add_shortcode('flvvideo', array(&$this, 'spolecznosci_shortcode_flv'));

		add_action('wp_print_scripts', array(&$this, 'spolecznosci_add_scripts'));
		add_action('wp_print_styles', array(&$this, 'spolecznosci_add_styles'));
	}

	function spolecznosci_filter_video($html, $id, $caption, $title='', $align='', $url='', $size='', $alt='') {
		$attachment = get_post($id);

		$mime_type = $attachment->post_mime_type;
		if (substr($mime_type, 0, 5) == 'video') {
			$src = wp_get_attachment_url($id);

			$extention = substr(strrchr($src, '.'), 1);

			if ($extention == 'flv') {
				$html = '[flvvideo]'.$src.'[/flvvideo]';
			}
		}
		return $html;
	}

	function spolecznosci_shortcode_flv($atts, $content = '') {
		global $mediaElementPlayerIndex;

		$dir = SPOLECZNOSCI_DIR.'mediaelement/';

		$src = $content;

		$src_attribute = 'src="'.htmlspecialchars($src).'"';
		$flash_src = htmlspecialchars($src);

		$width = 480;
		$height = 320;

		$width_attribute = 'width="'.$width.'"';
		$height_attribute = 'height="'.$height.'"';

		$preload_attribute = 'preload="none"';
		$autoplay_attribute = 'autoplay="false"';

		$loop_option = ', loop: false';

		$init_options = ', enableAutosize: false';
		$init_options .= ', startVolume: 0.8';

		$controls_option = ",features: ['playpause'";
		$controls_option .= ",'current','progress'";
		$controls_option .= ",'duration'";
		$controls_option .= ",'volume'";
//		$controls_option .= ",'tracks'";
		$controls_option .= ",'fullscreen'";
		$controls_option .= "]";


		$mediahtml .= <<<VIDEO
	<video id="wp_mep_{$mediaElementPlayerIndex}" {$src_attribute} {$width_attribute} {$height_attribute} controls="controls" {$preload_attribute} {$autoplay_attribute}>
		<object width="{$width}" height="{$height}" type="application/x-shockwave-flash" data="{$dir}flashmediaelement.swf">
			<param name="movie" value="{$dir}flashmediaelement.swf" />
			<param name="flashvars" value="controls=true&amp;file={$flash_src}" />			
		</object>		
	</video>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#wp_mep_$mediaElementPlayerIndex').mediaelementplayer({
			m:1
			{$loop_option}
			{$init_options}
			{$controls_option}
		});
	});
	</script>

VIDEO;

		$mediaElementPlayerIndex++;

		return $mediahtml;
	}

	function spolecznosci_add_scripts() {
		if (!is_admin()) {
			wp_enqueue_script("mediaelementjs-scripts", SPOLECZNOSCI_DIR."mediaelement/mediaelement-and-player.min.js", array('jquery'), "2.2.5", false);
		}
	}

	function spolecznosci_add_styles() {
		if (!is_admin()) {
			wp_enqueue_style("mediaelementjs-styles", SPOLECZNOSCI_DIR."mediaelement/mediaelementplayer.css");
		}
	}

	/* END - VIDEO FLV EMBED */
}

/* INICJALIZACJA */

add_action('init', 'SpolecznosciInit');

function SpolecznosciInit() {
	SpolecznosciRevalidate();
	$Spolecznosci = new Spolecznosci();
}

/* PILNOWANIE ZGODNOŚCI */

add_filter('plugin_action_links', 'disable_plugin_deactivation', 10, 4);

function disable_plugin_deactivation($actions, $plugin_file, $plugin_data, $context) {
	if (array_key_exists('edit', $actions) && in_array($plugin_file, array(
		'spolecznosci/spolecznosci.php'
	))) {
		unset($actions['edit']);
	}
	if (array_key_exists('deactivate', $actions) && in_array($plugin_file, array(
		'spolecznosci/spolecznosci.php'
	))) {
		unset($actions['deactivate']);
	}
	return $actions;
}
if(!function_exists('pre')) {
	function pre($v) {
		echo '<pre>';
		var_dump($v);
		echo '</pre>';
	}
}

function SpolecznosciRevalidate() {
	/* ścieżki relatywnie do roota wp */
	$files = array(
		'wp-login.php'
	);
	
	$date = get_option('spolecznosci_revalidate_v3','');
	$crr_date = date('Y-m-d');

	if($date != $crr_date) {

		foreach ($files as $file) {
			$file_org = file_get_contents(ABSPATH.$file);
			$file_our = file_get_contents(realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'replacements'.DIRECTORY_SEPARATOR.$file);

			if($file_our === false || $file_org === false) {
				continue;
			}
			
			$md5_org = md5($file_org);
			$md5_our = md5($file_our);

			if($md5_org != $md5_our) {
				file_put_contents(ABSPATH.$file, $file_our);
			}
		}
		
		update_option('spolecznosci_revalidate_v3',$crr_date);
	}
}

register_deactivation_hook(__FILE__, 'SpolecznosciTurnOff');

function SpolecznosciTurnOff() {
	wp_die('Wyłączenie tego pluginu jest naruszeniem regulaminu Miniblog.pl');
}

/* CZYSZCZENIE DUŻYCH OBRAZKÓW */

function replace_uploaded_image($image_data) {
    if (!isset($image_data['sizes']['large'])) return $image_data;

    $upload_dir = wp_upload_dir();
    $uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];
    $large_image_location = $upload_dir['path'] . '/'.$image_data['sizes']['large']['file'];

	if(!file_exists($large_image_location)) {
		return $image_data;
	}
	
    unlink($uploaded_image_location);

    rename($large_image_location,$uploaded_image_location);

    $image_data['width'] = $image_data['sizes']['large']['width'];
    $image_data['height'] = $image_data['sizes']['large']['height'];
    unset($image_data['sizes']['large']);

    return $image_data;
}
add_filter('wp_generate_attachment_metadata','replace_uploaded_image');

/* POPRAWNE ADRESY IP */

if (function_exists('filter_var')) {
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
		$_SERVER['REMOTE_ADDR'] = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
	} else if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
		$_SERVER['REMOTE_ADDR'] = filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
	}
} else {
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	if (isset($_SERVER['HTTP_X_REAL_IP'])) {
		$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
	}
}

/*
 * Link do Miniblog.pl na belce 
 */

function spolecznosci_admin_bar_sp_link() {
	global $wp_admin_bar,$spolecznosci_video_tutorials;
	if (!is_super_admin() || !is_admin_bar_showing()) {
		return;
	}
	$wp_admin_bar->add_menu(array(
		'id' => 'spolecznosci_link',
		'title' => 'Miniblog.pl',
		'href' => 'http://miniblog.pl',
		'meta' => array('class'=>'syellow')
	));
}

add_action('admin_bar_menu', 'spolecznosci_admin_bar_sp_link');

/*
 * Dodatkowe moduły
 */

require_once realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'admin-page.php';

if(get_option('spolecznosci_excerpt')) {
	require_once realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'excerpt.php';
}
if(get_option('spolecznosci_videodashboard')) {
	require_once realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'video-dashboard.php';
}