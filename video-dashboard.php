<?php

/* LINKI NA BELCE */

$spolecznosci_video_tutorials = array(
	array('embed' => 'http://www.youtube.com/watch?v=xHkKlBRl5wc', 'name' => 'Pierwszy wpis, kategoria, odnośniki w tekscie'),
	array('embed' => 'http://www.youtube.com/watch?v=XF5MHEu0zOw', 'name' => 'Usuwanie i dodawanie odnośników'),
	array('embed' => 'http://www.youtube.com/watch?v=czpjWGrnSiU', 'name' => 'Ustawienia ogólne'),
	array('embed' => 'http://www.youtube.com/watch?v=VO_iM6jytYE', 'name' => 'Dodawanie i usuwanie stron'),
	array('embed' => 'http://www.youtube.com/watch?v=1L64Y7JUFvs', 'name' => 'Dodawanie obrazu, zdjęcia do wpisu '),
	array('embed' => 'http://www.youtube.com/watch?v=1-Zy3-4t5w0', 'name' => 'Wstawianie zdjęcia do widgetu'),
	array('embed' => 'http://www.youtube.com/watch?v=Zd4AThxj4rw', 'name' => 'Wstawianie video z youtube na strone i do widgetów'),
	array('embed' => 'http://www.youtube.com/watch?v=oVtf2witiFM', 'name' => 'Dodawanie tagów'),
	array('embed' => 'http://www.youtube.com/watch?v=sjOL5x2889c', 'name' => 'Instalowanie wtyczek'),
	array('embed' => 'http://www.youtube.com/watch?v=3I0hrvsviMo', 'name' => 'Dodawanie polskiego motywu'),
);

function spolecznosci_admin_bar_link() {
	global $wp_admin_bar,$spolecznosci_video_tutorials;
	if (!is_super_admin() || !is_admin_bar_showing()) {
		return;
	}
	$wp_admin_bar->add_menu(array(
		'id' => 'spolecznosci_menu',
		'title' => 'Poradniki wideo',
		'href' => false,
		'meta' => array('class'=>'syellow')
	));
	
	foreach($spolecznosci_video_tutorials as $video) {
		$wp_admin_bar->add_menu(array(
			'id' => preg_replace('#[^a-z]#i','-',$video['name']),
			'parent' => 'spolecznosci_menu',
			'title' => $video['name'],
			'href' => $video['embed'],
			'meta' => array('class'=>"LBVideo",'title' => $video['name'])
		));
	}
}

add_action('wp_before_admin_bar_render', 'spolecznosci_admin_bar_link');

function spolecznosci_dashboard_widget_function() {?>
	<ul style="width:420px;margin:10px auto 0;list-style:square">
		<?
		global $spolecznosci_video_tutorials;
		
		foreach($spolecznosci_video_tutorials as $video) {?>
		<li class="LBVideo"><a title="<?php echo $video['name']; ?>" taget="_blank" href="<?php echo $video['embed']; ?>"><?php echo $video['name']; ?></a></li>
		<?}
		?>
	</ul>
<?}

function spolecznosci_add_dashboard_widgets() {
	wp_add_dashboard_widget('spolecznosci_dashboard_widget', 'Wprowadzenie do WordPress\'a', 'spolecznosci_dashboard_widget_function');	
	global $wp_meta_boxes;
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	$spolecznosci_widget_backup = array('spolecznosci_dashboard_widget' => $normal_dashboard['spolecznosci_dashboard_widget']);
	unset($normal_dashboard['example_dashboard_widget']);
	$sorted_dashboard = array_merge($spolecznosci_widget_backup, $normal_dashboard);
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

}

add_action('wp_dashboard_setup', 'spolecznosci_add_dashboard_widgets');
	
if (!function_exists('wp_get_current_user')) {

	function wp_get_current_user() {
		// Insert pluggable.php before calling get_currentuserinfo()
		require (ABSPATH.WPINC.'/pluggable.php');
		global $current_user;
		get_currentuserinfo();
		return $current_user;
	}

}

$user = wp_get_current_user();

if(!empty($user) && !empty($user->data) && !empty($user->data->show_admin_bar_front) && $user->data->show_admin_bar_front == "true" && get_option('spolecznosci_videotip') === false) {
	add_action('wp_head', 'videotip_action_javascript');
	add_action('wp_ajax_videotip_action', 'videotip_action_callback');
}

function videotip_action_javascript() {
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		spolecznosci.init();
	});
	</script>
	<?php
}

function videotip_action_callback() {
	global $wpdb; // this is how you get access to the database

	update_option('spolecznosci_videotip', 1);

	die(); // this is required to return a proper result
}