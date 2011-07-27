<?php
/**
 Plugin Name: Tweet&Get It! 
 Plugin URI: http://tweetandgetit.com
 Version: 1.00
 Description: Tweet&Get it! is an automatic process to get Twitter followers in exchange of a downloadable file. 
 Author: Tweet & Get is powered by Viuu / Via Internet UK LTD
 Author URI: http://tweetandgetit.com
 # Copyright (c) 2010 - 2011 Via Internet UK Ltd.

	# 'Tweet&Get it !' is an unregistered trademark of Via Internet UK Ltd., 
	# and may not be used in conjuction with any redistribution 
	# of this software without express prior permission from Via Internet UK Ltd.	

	# This program is free software; you can redistribute it and/or
	# modify it under the terms of the GNU General Public License
	# as published by the Free Software Foundation; either version 2
	# of the License, or (at your option) any later version.

	# This program is distributed in the hope that it will be useful,
	# but WITHOUT ANY WARRANTY; without even the implied warranty of
	# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	# GNU General Public License for more details.

	# You should have received a copy of the GNU General Public License
	# along with this program; if not, write to the Free Software
	# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


define (WP_SITEURL,get_option(siteurl));
define('APP_NAME', 'tweetandgetit' );
define('TWEEGI_TRASNLATE', dirname(plugin_basename( __FILE__ )) );
if (version_compare(PHP_VERSION, '5.0.0.', '<'))
{
	die(__(APP_NAME." requires php 5 or a greater version to work.", "wordpress"));
}

if (!defined('WP_CONTENT_URL')) {
   define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}

if (!defined('TWEEGI_PLUGIN_DIR')) {
		if (!defined('TWEEGI_CONTENT_DIR'))
		define('TWEEGI_CONTENT_DIR', substr(ABSPATH,0,strlen(ABSPATH)-1). DIRECTORY_SEPARATOR. 'wp-content');
		if (defined('WP_PLUGIN_DIR')) {
			if (!defined('TWEEGI_PLUGIN_DIR')) define('TWEEGI_PLUGIN_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . basename(dirname(__FILE__)));
		} else {
			if (!defined('TWEEGI_PLUGIN_DIR')) define('TWEEGI_PLUGIN_DIR', TWEEGI_CONTENT_DIR . DIRECTORY_SEPARATOR.'plugins' .DIRECTORY_SEPARATOR. basename(dirname(__FILE__)));
		}
		if (defined('WP_PLUGIN_URL')) {
			if (!defined('TWEEGI_PLUGIN_DIR')) define('TWEEGI_PLUGIN_DIR', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)));
		} else {
			if (!defined('TWEEGI_PLUGIN_DIR')) define('TWEEGI_PLUGIN_DIR', WP_CONTENT_URL . '/plugins/' . basename(dirname(__FILE__)));
		}
	}

$h = wp_upload_dir();
if(!empty($h['path']))
	define('WP_UPLOAD_PATH', $h['path']);		
else
	define('WP_UPLOAD_PATH', TWEEGI_CONTENT_DIR.DIRECTORY_SEPARATOR."uploads");		

define('TWEEGI_URLPATH', WP_PLUGIN_URL."/".plugin_basename( dirname(__FILE__) ) );
define('TWEEGI_UPLOAD_PATH', WP_UPLOAD_PATH.DIRECTORY_SEPARATOR."tweetandgetit");	
define('TWEEGI_UPLOAD_URL', $h['url']."/tweetandgetit");

global $wpdb;
global $tbl_tweetandgetit_buttons;
$tbl_tweetandgetit_buttons = $wpdb->prefix . "tweetandgetit_buttons";
load_plugin_textdomain( TWEEGI_TRASNLATE, false,dirname(plugin_basename( __FILE__ )).'/lang' );
include_once("engine.php");

add_action ('init', 'tweegi_plugin_init');
register_activation_hook(__FILE__, 'tweegi_activate');
register_deactivation_hook(__FILE__,'tweegi_deactivate');



add_action('wp_ajax_tweegi_createbutton_action', 'tweegi_action_callback');
add_action('wp_ajax_tweegi_buttonlist', 'tweegi_buttons_list_callback');

add_action('wp_ajax_tweegi_delete_buttons', 'tweegi_delete_buttons');

add_shortcode( 'tweegi-button', 'tweegi_shortcode_handler' );
add_filter('widget_text', 'do_shortcode');

add_filter('the_content_rss', 'do_shortcode', 11); 
add_filter('the_excerpt', 'do_shortcode', 11); 
add_filter('the_excerpt_rss', 'do_shortcode', 11);
add_filter('get_the_excerpt','do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_content', 'tweegi_content_hook', 100);
function tweegi_activate() { 

	global $wpdb, $tbl_tweetandgetit_buttons;
    $twt_db_ver = 94;
	
	
   

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	
	$sql = "CREATE TABLE IF NOT EXISTS ".$tbl_tweetandgetit_buttons." (
        id BIGINT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		twitter_name VARCHAR(225) NOT NULL ,
		button_name VARCHAR(225) NOT NULL unique,
		tweet VARCHAR(280) NOT NULL,
		file_path longtext,
		shortcode longtext
		) {$charset_collate};";
	
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);			
	
	
	tweegi_make_upload_dir();
	
	
}

function tweegi_deactivate() {


global $wpdb, $tbl_tweetandgetit_buttons;
remove_filter('the_content', 'tweegi_content_hook');
remove_action('admin_menu', 'tweegi_admin');
	remove_action('wp_print_scripts', 'tweegi_boot_page');
   	

}

 
?>