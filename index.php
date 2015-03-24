<?php
/*
	Plugin Name: Contact Form Pro
	Plugin URI: https://wordpress.org/plugins/custom-user-contact-form-builder/
	Description: An easy to use, simple but powerful contact form system that also tracks submissions through a nifty interface. You can create unlimited forms with custom fields and use them through WordPress shortcode system.
	Version: 2.0
	Author: CMSHelpLive
	Author URI: https://profiles.wordpress.org/cmshelplive
	License: gpl2
*/
ob_start();
/*Plugin activation hook*/
global $cfp_db_version;
$cfp_db_version = 1.7;

register_activation_hook ( __FILE__, 'activate_contact_form_pro_plugin' );
function activate_contact_form_pro_plugin()
{
	add_option('cfp_db_version','1.7');
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	$cfp_option=$wpdb->prefix."cfp_option";
	$cfp_fields =$wpdb->prefix."cfp_fields";
	$cfp_forms =$wpdb->prefix."cfp_forms";
	$cfp_entries =$wpdb->prefix."cfp_entries";
	$cfp_stats = $wpdb->prefix."cfp_stats";
	$sqlcreate = "CREATE TABLE IF NOT EXISTS $cfp_stats
	(
		`id` int NOT NULL AUTO_INCREMENT,
			`form_id` int(11),
			`stats_key` varchar(255),
			`details` longtext,
			PRIMARY KEY(id)
	)";
	dbDelta( $sqlcreate );
	
	$sqlcreate = "CREATE TABLE IF NOT EXISTS $cfp_option
	(
		`id` int NOT NULL AUTO_INCREMENT,
		`fieldname` varchar(255),
		`value` longtext,
		PRIMARY KEY(id)
	)";
	dbDelta( $sqlcreate );

	$insert="INSERT INTO $cfp_option VALUES
		(1, 'enable_captcha', 'no'),
		(2, 'public_key', ''),
		(3, 'private_key', ''),
		(4, 'autogeneratedepass', 'no'),
		(5, 'userautoapproval', 'yes'),
		(6, 'adminemail', ''),
		(7, 'adminnotification', 'no'),
		(8, 'from_email', ''),
		(9, 'userip', 'yes'),
		(10, 'cfp_theme','default')";
		$wpdb->query($insert);

	$sqlcreate = "CREATE TABLE IF NOT EXISTS $cfp_entries
	(
		`id` int NOT NULL AUTO_INCREMENT,
		`form_id` int NOT NULL,
		`form_type` varchar(255),
		`user_approval` varchar(255),
		`value` longtext,
		 PRIMARY KEY (`id`)
	)";
	dbDelta( $sqlcreate );

	$sqlcreate = "CREATE TABLE IF NOT EXISTS $cfp_forms (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) DEFAULT NULL,
  `form_desc` longtext,
  `form_type` varchar(255) NOT NULL,
  `custom_text` longtext,
  `cfp_welcome_email_subject` varchar(255) NOT NULL,
  `success_message` varchar(255) NOT NULL,
  `cfp_welcome_email_message` longtext,
  `redirect_option` varchar(255) NOT NULL,
  `redirect_page_id` int(11) NOT NULL,
  `redirect_url_url` longtext NOT NULL,
  `send_email` int(11) NOT NULL,
  `form_option` longtext,
  PRIMARY KEY (`id`)
)";
	dbDelta( $sqlcreate );
	
	$sqlcreate = "CREATE TABLE IF NOT EXISTS $cfp_fields (
	  `Id` int(11) NOT NULL AUTO_INCREMENT,
	  `Form_Id` int(11) NOT NULL,
	  `Type` varchar(50) DEFAULT NULL,
	  `Name` varchar(256) NOT NULL,
	  `Value` longtext DEFAULT NULL,
	  `Class` varchar(256) DEFAULT NULL,
	  `Max_Length` varchar(256) DEFAULT NULL,
	  `Cols` varchar(256) DEFAULT NULL,
	  `Rows` varchar(256) DEFAULT NULL,
	  `Option_Value` varchar(256) DEFAULT NULL,
	  `Description` longtext DEFAULT NULL,
	  `Require` varchar(256) DEFAULT NULL,
	  `Readonly` varchar(256) DEFAULT NULL,
	  `Visibility` varchar(256) DEFAULT NULL,
	  `Ordering` int(11) DEFAULT NULL,
	  PRIMARY KEY (`Id`))";
	dbDelta( $sqlcreate );
}

add_action( 'plugins_loaded', 'cfp_update_db_check' );
function cfp_update_db_check()
{
	 global $cfp_db_version;
	 global $wpdb;
	 $cfp_option=$wpdb->prefix."cfp_option";
	 $save_db_version =  floatval(get_site_option( 'cfp_db_version','1.0' ));
    if ( $save_db_version < $cfp_db_version ) 
	{	
		$insert="INSERT IGNORE INTO $cfp_option VALUES
		(6, 'adminemail', ''),
		(7, 'adminnotification', 'no')";
		$wpdb->query($insert);
		
		$insert="INSERT IGNORE INTO $cfp_option VALUES
		(8, 'from_email', '')";
		$wpdb->query($insert);
		
		$insert="INSERT IGNORE INTO $cfp_option VALUES
		(9, 'userip', 'no')";
		$wpdb->query($insert);
		
		$qry="select `value` from $cfp_option where fieldname='cfp_theme'";
		$cfp_theme = $wpdb->get_var($qry);
		
		if(isset($cfp_theme) && $cfp_theme!="")
		{
			if($cfp_theme=='default')
			{
				$wpdb->query("update $cfp_option set value='classic' where fieldname='cfp_theme'");
			}
			else
			{
				$wpdb->query("update $cfp_option set value='default' where fieldname='cfp_theme'");	
			}
		}
		
		$insert="INSERT IGNORE INTO $cfp_option VALUES
		(10, 'cfp_theme', 'default')";
		$wpdb->query($insert);
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$cfp_stats = $wpdb->prefix."cfp_stats";
		$sqlcreate = "CREATE TABLE IF NOT EXISTS $cfp_stats
		(
			`id` int NOT NULL AUTO_INCREMENT,
			`form_id` int(11),
			`stats_key` varchar(255),
			`details` longtext,
			PRIMARY KEY(id)
		)";
		dbDelta( $sqlcreate );
		
		$cfp_forms =$wpdb->prefix."cfp_forms";
		$cfpform = $wpdb->get_row("SELECT * FROM $cfp_forms");
		//Add column if not present.
		if(!isset($cfpform->form_option)){
			$wpdb->query("ALTER TABLE $cfp_forms ADD form_option longtext");
		}
		
		update_option( "cfp_db_version", $cfp_db_version );
		
	}
}

add_action( 'wp_enqueue_scripts', 'cfp_frontend_script' );
add_action( 'admin_init', 'cfp_admin_script' );
/*Defines enqueue style/ script for front end*/
function cfp_frontend_script() {
	global $wpdb;
	$cfp_option=$wpdb->prefix."cfp_option";
	$qry="select `value` from $cfp_option where fieldname='cfp_theme'";
	$cfp_theme = $wpdb->get_var($qry);
	wp_enqueue_style( 'cfp-style-'.$cfp_theme, plugin_dir_url(__FILE__) . 'css/cfp-style-'.$cfp_theme.'.css');
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css');
}
/*Defines enqueue style/ script for dashboard*/
function cfp_admin_script() {
	wp_enqueue_style( 'cfp-admin.css', plugin_dir_url(__FILE__) . 'css/cfp-admin.css');
	wp_enqueue_style( 'jquery-ui.css', 'http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css');
	wp_enqueue_script( 'jquery' );
	wp_enqueue_Script('jquery-ui-dialog');
	wp_enqueue_Script('jquery-ui-sortable');
	wp_register_style('cfp_googleFonts', 'http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700');
    wp_enqueue_style( 'cfp_googleFonts');
	wp_enqueue_script( 'ZeroClipboard.js',  plugin_dir_url(__FILE__) . 'js/ZeroClipboard.js');	
	wp_enqueue_script( 'jquery-ui-tooltip');
	wp_enqueue_style( 'cfp_analytics', plugin_dir_url(__FILE__) . 'css/cfp_analytics.css');
	wp_enqueue_script( 'cfp_jsapi', 'https://www.google.com/jsapi');	
}

/*Defines menu and sub-menu items in dashboard*/
add_action('admin_menu', 'contact_form_pro_menu');
function contact_form_pro_menu()
{
	add_menu_page("Contact Form Pro","Contact Form Pro","manage_options","cfp_manage_forms","cfp_manage_forms","dashicons-feedback","28.02");
	add_submenu_page("","Add Form","Add Form","manage_options","cfp_add_form","cfp_add_form");
	add_submenu_page("cfp_manage_forms","Settings","Settings","manage_options","cfp_settings","cfp_settings");
	add_submenu_page("cfp_manage_forms","Submissions","Submissions","manage_options","cfp_entries","cfp_entries");
	add_submenu_page("","Manage Form Fields","Manage Form Fields","manage_options","cfp_manage_form_fields","cfp_manage_form_fields");
	add_submenu_page("","View Entry","View Entry","manage_options","cfp_view_entry","cfp_view_entry");
	add_submenu_page("","Add Field","Add Field","manage_options","cfp_add_field","cfp_add_field");
	add_submenu_page("cfp_manage_forms","Upgrade","Upgrade","manage_options","cfp_Pro","cfp_Pro");
	add_submenu_page("cfp_manage_forms","Analytics (Demo)","Analytics (Demo)","manage_options","cfp_analytics_demo","cfp_analytics_demo");
	add_submenu_page("cfp_manage_forms","Support","Support","manage_options","cfp_support","cfp_support");
}

function add_cfp_menu_adminbar() {
	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'id'    => 'cfp_add_form',
		'title' => 'Form',
		'href'  => admin_url().'admin.php?page=cfp_add_form',
		'parent'=>'new-content'
	));
}
add_action( 'wp_before_admin_bar_render', 'add_cfp_menu_adminbar' ); 

function cfp_Pro()
{
	include 'pro_features.php';	
}

function cfp_analytics_demo()
{
	include 'cfp_analytics.php';	
}

function cfp_support()
{
	include 'cfp_support.php';	
}

function cfp_settings()
{
	include 'cfpoption.php';
}

function cfp_add_form()
{
	include 'add-form.php';	
}

function cfp_add_field()
{
	include 'add_field.php';
}

function cfp_view_entry()
{
	include 'view_entry.php';	
}

function cfp_manage_forms()
{
	include 'manage-form.php';
}

function cfp_entries()
{
	include 'manage_entries.php';
}

function cfp_manage_form_fields()
{
	include 'manage-form-fields.php';
}

add_shortcode( 'CFP_Form', 'CFP_view_form_fun' );
function CFP_view_form_fun($content)
{
	global $wpdb;
	$cfp_option=$wpdb->prefix."cfp_option";
	$qry="select `value` from $cfp_option where fieldname='cfp_theme'";
	$cfp_theme = $wpdb->get_var($qry);
	include 'view-form-'.$cfp_theme.'.php';
}

add_action('wp_ajax_cfp_set_field_order', 'CFP_set_field_order');
add_action('wp_ajax_nopriv_cfp_set_field_order', 'CFP_set_field_order');
function CFP_set_field_order()
{
	global $wpdb;
	include('set_field_order.php');die;
}

add_action('wp_ajax_cfp_ajaxcalls', 'CFP_ajaxcalls');
add_action('wp_ajax_nopriv_cfp_ajaxcalls', 'CFP_ajaxcalls');
function CFP_ajaxcalls()
{
	global $wpdb;
	include('cfp_ajaxCalls.php');
	die;
}

add_action('wp_ajax_check_cfp_field_name', 'CFP_check_field_name');
add_action('wp_ajax_nopriv_check_cfp_field_name', 'CFP_check_field_name');
function CFP_check_field_name()
{
	global $wpdb;
	include('check_cfp_field_name.php');
	die;
}

add_action('wp_ajax_check_cfp_form_name', 'CFP_check_form_name');
add_action('wp_ajax_nopriv_check_cfp_form_name', 'CFP_check_form_name');
function CFP_check_form_name()
{
	global $wpdb;
	include('check_cfp_form_name.php');
	die;
}

add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode');
?>