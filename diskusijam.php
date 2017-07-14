<?php
/*  Copyright 2013 Jānis Akmentiņš (janis.akmentins@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
	Plugin Name: Diskusijam.lv Wordpress plugin
	Description: Plugin for integrating diskusijam.lv comment system in Wordpress.
	Author: Jānis Akmentiņš
	Version: 1.3.2
*/

/*
*	Admin menu content
*/
define('DISKUSIJAM_PLUGIN_URL', WP_CONTENT_URL . '/plugins/diskusijamlv-wordpress-plugin/');

function diskusijam_admin_content(){
    global $wpdb;
    include 'comments_admin.php';
}

/*
*	Adding diskusijam.lv to admin menu
*/
function diskusijam_admin_menu() {
     add_submenu_page(
         'edit-comments.php',
         'Diskusijam.lv',
         'Diskusijam.lv',
         'moderate_comments',
         'diskusijam',
         'diskusijam_admin_content'
     );
}
add_action('admin_menu', 'diskusijam_admin_menu');

/*
*	Making diskusijam.lv appear on top 
*/
function diskusijam_menu_admin_head() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			var diskusijam_menu = jQuery('#menu-comments');
			diskusijam_menu.find('a.wp-has-submenu').attr('href', 'edit-comments.php?page=diskusijam').end().find('.wp-submenu  li:has(a[href="edit-comments.php?page=diskusijam"])').prependTo(diskusijam_menu.find('.wp-submenu ul'));
		});
	</script>
	<?php
}
add_action('admin_head', 'diskusijam_menu_admin_head');

/*
*	Collecting thread_ids
*/
function diskusijam_loop_end($query){
	if(!count($query->posts) || is_single() || is_page() || is_feed()){
		return;
	}
	$thread_ids = array();
    foreach ($query->posts as $post){
        $thread_ids[] = intval($post->ID);
    }

   diskusijam_output_loop_comment_js($thread_ids);    
}
add_action('loop_end', 'diskusijam_loop_end');

/*
*	Showing comment count for each comment link
*/
function diskusijam_comments_number($text){
	global $post;
	return '<span id="diskusijam_comment_count_'. $post->ID .'">'.$text.'</span>';
}
add_filter('comments_number', 'diskusijam_comments_number');

/*
*	Showing comment count for each post
*/
$diskusijam_comment_count_loaded = false;
function diskusijam_output_loop_comment_js($thread_ids){
    global $diskusijam_comment_count_loaded;
    if ($diskusijam_comment_count_loaded) return;
    $diskusijam_comment_count_loaded = true;
    if(count($thread_ids)){
		include 'api.php';
		$comment_counts = diskusijam_getCommentCount($thread_ids);
		if($comment_counts){
			include 'comment_counts.php';
		}
	}
}

/*
*	Disabling default comment form
*/
function diskusijam_comments_open(){
	return false;
}
add_filter('comments_open', 'diskusijam_comments_open');

/*
*	Loading diskusijam.lv comment template
*/
function diskusijam_comments_template(){
	return dirname(__FILE__) . '/comments.php';
}
add_filter('comments_template', 'diskusijam_comments_template');


/*
*	Cleaning system after deactivation
*/
function diskusijam_uninstall(){
	delete_option('diskusijam_api_page_key');
	delete_option('diskusijam_api_user_key');
	delete_option('diskusijam_profile_id');
	delete_option('diskusijam_page_id');
	delete_option('diskusijam_comment_sync');		
	delete_option('diskusijam_lang');
}
register_deactivation_hook(__FILE__, 'diskusijam_uninstall');

?>