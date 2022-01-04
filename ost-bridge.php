<?php

/*

Plugin Name: osTicket Bridge

Plugin URI: https://key4ce.com/projects/key4ce-osticket-bridge

Description: Integrate osTicket (v1.9.3 - 1.15.2) into wordpress. including user integration and scp. Originally authored by Key4ce now authored by Zbierajewski Group

Version: 1.4.1

Author: Zbierajewski

Author URI: https://www.zbtechgroup.com/

License: GPLv3

Text Domain: key4ce-osticket-bridge

Domain Path: /languages/

Copyright (C) 2015  Key4ce



This program is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

GNU General Public License for more details.



You should have received a copy of the GNU General Public License

along with this program.  If not, see http://www.gnu.org/licenses/


Every bit of code is kept as-is from the previous developer for legacy reasons.
*/



add_action('admin_menu', 'mb_admin_menu');

add_action('admin_head', 'mb_admin_css');

register_activation_hook(__FILE__,'mb_install');

register_uninstall_hook(__FILE__,'mb_uninstall');

register_activation_hook(__FILE__,'mb_table_install');

register_activation_hook(__FILE__,'mb_database_install');



if ( ! defined( 'KEYOST_FILE' ) ) {

	define( 'KEYOST_FILE', __FILE__ );

}

function keyost_load_textdomain() {

	load_plugin_textdomain( 'key4ce-osticket-bridge', false, dirname( plugin_basename( KEYOST_FILE ) ) . '/languages/' );

}

add_action( 'upgrader_process_complete', 'updateemailtemplate', 10, 0 );

function update_db_hourly() {

	$config = get_option('os_ticket_config');

	extract($config);

	$ost_wpdb = new wpdb($username, $password, $database, $host);

	$getkey4celicensekeyvalue=$ost_wpdb->get_var("SELECT value FROM ".$keyost_prefix."config WHERE `key` LIKE 'key4celicensekey'");

	$license_explode=explode("|",$getkey4celicensekeyvalue);

	$key4ce_license_key=$license_explode[0];

	$key4ce_license_email=$license_explode[1];

	$key4ce_license_status=$license_explode[2];

	$key4ce_license_expirydate=$license_explode[3];

	$key4ce_license_instance=$license_explode[4];

	if(current_time( 'timestamp' ) > $key4ce_license_expirydate)

	{

		 $data_url=get_site_url()."?wc-api=software-api&request=deactivation&email=".$key4ce_licenseemail."&licence_key=".$key4ce_licensekey."&product_id=123&instance=".$key4ce_instance_secure."&platform=".$key4ce_platform;

		 $response_data = wp_remote_get($data_url);

	}

	//else

	//{

	//	$msg="This is crontesting for oneday from magetechno ticket.";

	//}

	



} // end update_csv_hourly

add_action( 'my_hourly_event',  'update_db_hourly' );

add_filter('cron_schedules','my_cron_definer');    

function my_cron_definer($schedules)

{  

	$schedules['monthly'] = array('interval'=> 3600,'display'=> __('Once Every 1 Hour'));  return $schedules;

}

function updateemailtemplate(){

	 global $wpdb;

	 $table_name = $wpdb->prefix . "ost_emailtemp";

	 $id = '4';

         $name = "Admin-New-Ticket";

         $subject = "Ticket ID [#\$ticketid]";

         $text = "";

		 $user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE id=4" );

		 if($user_count > 0)

		 {

			 

		 }

		 else

		 {

			 $rows_affected = $wpdb->insert(

                 $table_name, array(

                     'id' => $id,

                     'name' => $name,

                     'subject' => $subject,

                     'text' => $text,

                     'created' => current_time('mysql'),

                     'updated' => current_time('mysql')));

		 }   

}

add_action( 'init', 'keyost_load_textdomain', 1 );

function mb_settings_link($actions, $file) {

if(false !== strpos($file, 'ost-bridge'))

    $actions['settings'] = '<a href="admin.php?page=ost-config">Config</a>';

return $actions; 

}

add_filter('plugin_action_links', 'mb_settings_link', 2, 2);

function addtemplate(){

	 ob_start();

         require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/osticket-wp.php');

         $ticketpage = ob_get_clean();

         return $ticketpage;

}

define('OST_SHORTCODE_ADDOSTICKET', 'addosticket');

add_shortcode(OST_SHORTCODE_ADDOSTICKET, 'addtemplate');

 function custom_toolbar_openticket() {

 	global $wp_admin_bar;	

 	$wp_admin_bar->add_menu(array('id' => 'opensupport',

	        'title' => sprintf(__('Open Tickets')),			

		'href' => get_admin_url(null,'admin.php?page=ost-tickets&service=list&status=open'),

	));

 } 

function addcontact(){

	ob_start();

       	require_once(WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/contact_ticket.php');

	$contact_ticket = ob_get_clean();

	 return $contact_ticket;

}



add_shortcode('addoscontact', 'addcontact');

function addkbtemplate(){

		 ob_start();

		if (isset($_GET['service']) && $_GET['service'] == 'viewfaq') 

		{

            require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/view_kb.php');

			$kbpage = ob_get_clean();

        }

		else

		{

			 require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/knowledgebase.php');

			 $kbpage = ob_get_clean();

		}

         return $kbpage;

}

define('OST_SHORTCODE_ADDOSKB', 'addoskb');

add_shortcode(OST_SHORTCODE_ADDOSKB, 'addkbtemplate');

function custom_toolbar_supportticket() {

	global $wp_admin_bar;	

	$wp_admin_bar->add_menu(array(

		'id'     => 'supportsupport',

	        'title' => sprintf(__('Support Tickets')),			

		'href' => get_admin_url(null,'admin.php?page=ost-tickets&service=list&status=all'),

	));

}

function addopenticketcount(){

	$config = get_option('os_ticket_config');

	extract($config);

	$ost_wpdb = new wpdb($username, $password, $database, $host);	

	$num_rows=0;

	$dept_table=$keyost_prefix."department";

	$ticket_table=$keyost_prefix."ticket";

	$ticket_cdata=$keyost_prefix."ticket__cdata";

	$ost_ticket_status=$keyost_prefix."ticket_status";

	$current_user = wp_get_current_user();

	$e_address=$current_user->user_email;

	$user_id = $ost_wpdb->get_var("SELECT user_id FROM ".$keyost_prefix."user_email WHERE `address` = '".$e_address."'");

	if($keyost_version>="194")

	{

	$num_rows=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table

	LEFT JOIN $ticket_cdata ON $ticket_cdata.ticket_id = $ticket_table.ticket_id

	INNER JOIN $dept_table ON $dept_table.dept_id=$ticket_table.dept_id 

	INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id

	WHERE $ost_ticket_status.state='open' AND ost_ticket.user_id='$user_id'");

	} else {

	$num_rows=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table

	LEFT JOIN $ticket_cdata ON $ticket_cdata.ticket_id = $ticket_table.ticket_id

	INNER JOIN $dept_table ON $dept_table.dept_id=$ticket_table.dept_id WHERE $ticket_table.status='open' AND ost_ticket.user_id='$user_id'");

	} if($num_rows > 0) {

		return $num_rows;

        } else {

		return 0;

        }

}

$config = get_option('os_ticket_config');

extract($config);

$ost_wpdb = new wpdb($username, $password, $database, $host);

$license_data=$ost_wpdb->get_var("SELECT value FROM ".$keyost_prefix."config WHERE `key` LIKE 'key4celicensekey'");

$l_ary=explode("|",$license_data);

$license_activate=$l_ary[2];

if($keyost_kbcustomslg!="" && $license_activate=="activated")

{

if ( ! function_exists( 'key4cce_kb_cpt' ) ) {

    function key4ce_kb_cpt() {

 

        // these are the labels in the admin interface, edit them as you like

        $labels = array(

            'name'                => _x( 'Knowledge Bases', 'Post Type General Name', 'key4ce-osticket-bridge' ),

            'singular_name'       => _x( 'Knowledge Base', 'Post Type Singular Name', 'key4ce-osticket-bridge' ),

            'menu_name'           => __( 'Knowledge Base', 'key4ce-osticket-bridge' ),

            'parent_item_colon'   => __( 'Parent Item:', 'key4ce-osticket-bridge' ),

            'all_items'           => __( 'All Items', 'key4ce-osticket-bridge' ),

            'view_item'           => __( 'View Item', 'key4ce-osticket-bridge' ),

            'add_new_item'        => __( 'Add New Knowledge Base Item', 'key4ce-osticket-bridge' ),

            'add_new'             => __( 'Add New', 'key4ce-osticket-bridge' ),

            'edit_item'           => __( 'Edit Item', 'key4ce-osticket-bridge' ),

            'update_item'         => __( 'Update Item', 'key4ce-osticket-bridge' ),

            'search_items'        => __( 'Search Item', 'key4ce-osticket-bridge' ),

            'not_found'           => __( 'Not found', 'key4ce-osticket-bridge' ),

            'not_found_in_trash'  => __( 'Not found in Trash', 'key4ce-osticket-bridge' ),

        );

        $args = array(

            // use the labels above

            'labels'              => $labels,

            // we'll only need the title, the Visual editor and the excerpt fields for our post type

            'supports'            => array( 'title', 'editor', 'excerpt', ),

            // we're going to create this taxonomy in the next section, but we need to link our post type to it now

            'taxonomies'          => array( 'key4ce_kb_tax' ),

            // make it public so we can see it in the admin panel and show it in the front-end

            'public'              => true,

            // show archives, if you don't need the shortcode

            'has_archive'         => true,

			'show_in_menu'          => true,

			'show_in_nav_menus'     => true,	

			'exclude_from_search'   => false,

			'publicly_queryable'    => true,

			'capability_type'       => 'page',

			'hierarchical'          => false,



        );

        register_post_type( 'key4ce_kb', $args );

    }

    // hook into the 'init' action

	if($license_activate=="activated")

	{

    add_action( 'init', 'key4ce_kb_cpt', 0 );

	}

}

if ( ! function_exists( 'key4ce_kb_tax' ) ) {

 

    // register custom taxonomy

    function key4ce_kb_tax() {

	

        // again, labels for the admin panel

        $labels = array(

            'name'                       => _x( 'Knowledge Base Categories', 'Taxonomy General Name', 'key4ce-osticket-bridge' ),

            'singular_name'              => _x( 'Knowledge Base Category', 'Taxonomy Singular Name', 'key4ce-osticket-bridge' ),

            'menu_name'                  => __( 'Knowledge Base Categories', 'key4ce-osticket-bridge' ),

            'all_items'                  => __( 'All Knowledge Base Categories', 'key4ce-osticket-bridge' ),

            'parent_item'                => __( 'Parent Knowledge Base Category', 'key4ce-osticket-bridge' ),

            'parent_item_colon'          => __( 'Parent Knowledge Base Category:', 'key4ce-osticket-bridge' ),

            'new_item_name'              => __( 'New Knowledge Base Category', 'key4ce-osticket-bridge' ),

            'add_new_item'               => __( 'Add New Knowledge Base Category', 'key4ce-osticket-bridge' ),

            'edit_item'                  => __( 'Edit Knowledge Base Category', 'key4ce-osticket-bridge' ),

            'update_item'                => __( 'Update Knowledge Base Category', 'key4ce-osticket-bridge' ),

            'separate_items_with_commas' => __( 'Separate items with commas', 'key4ce-osticket-bridge' ),

            'search_items'               => __( 'Search Items', 'key4ce-osticket-bridge' ),

            'add_or_remove_items'        => __( 'Add or remove items', 'key4ce-osticket-bridge' ),

            'choose_from_most_used'      => __( 'Choose from the most used items', 'key4ce-osticket-bridge' ),

            'not_found'                  => __( 'No record found', 'key4ce-osticket-bridge' ),

        );

		$config = get_option('os_ticket_config');

		extract($config);

        $args = array(

            // use the labels above

            'labels'                     => $labels,

            // taxonomy should be hierarchial so we can display it like a category section

            'hierarchical'               => true,

            // again, make the taxonomy public (like the post type)

            'public'                     => true,

			'show_ui'                    => true,

			'show_admin_column'          => true,

			'show_in_nav_menus'   => true,

			'rewrite'      => array( 'slug' =>$keyost_kbcustomslg),

			'query_var'    => true,

        );

        // the contents of the array below specifies which post types should the taxonomy be linked to

        register_taxonomy( 'key4ce_kb_tax', array( 'key4ce_kb' ), $args );

		flush_rewrite_rules();

    }

	if($license_activate=="activated")

	{

		// hook into the 'init' action

		add_action( 'init', 'key4ce_kb_tax', 0 );

	}

    

}

	}

add_shortcode('addosopenticketcount', 'addopenticketcount');

// Ticket Count Short Code End Here Added By Pratik Maniar

function mb_admin_menu() { 

$config = get_option('os_ticket_config');

extract($config);



if (($database=="") || ($username=="") || ($password=="") || ($keyost_prefix=="")) {

    $page_title = 'Support/Request List';

    $menu_title = 'Tickets';

} else {

$ost_wpdb = new wpdb($username, $password, $database, $host);	

$license_data=$ost_wpdb->get_var("SELECT value FROM ".$keyost_prefix."config WHERE `key` LIKE 'key4celicensekey'");

$l_ary=explode("|",$license_data);

$license_activate=$l_ary[2];

if (isset($ost_wpdb->error) ){

    $page_title = 'Support/Request List';

    $menu_title = 'Tickets';

} else {

    $dept_table=$keyost_prefix."department";

    $ticket_table=$keyost_prefix."ticket";

    $ticket_cdata=$keyost_prefix."ticket__cdata";

    $ost_ticket_status=$keyost_prefix."ticket_status";

    $num_rows=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table

    LEFT JOIN $ticket_cdata ON $ticket_cdata.ticket_id = $ticket_table.ticket_id

    INNER JOIN $dept_table ON $dept_table.dept_id=$ticket_table.dept_id 

    INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id

    WHERE $ost_ticket_status.state='open' AND $ticket_table.isanswered='0'");

        $page_title = 'Support/Request List';

	if ($num_rows > 0) {

            $menu_title = __("Tickets", 'key4ce-osticket-bridge').'<span class="awaiting-mod"><span class="pending-count">' . $num_rows . '</span></span>';

	} else {

	$menu_title = __("Tickets", 'key4ce-osticket-bridge'); } } }

        $capability = 'manage_options';

        $menu_slug = 'ost-tickets';

        $function = 'ost_tickets_page';

        $position = '51';

        //$icon_url = plugin_dir_url( __FILE__ ) . 'images/status.png';

		$icon_url ='dashicons-sos';

        add_menu_page(__("Support/Request List", 'key4ce-osticket-bridge'),$menu_title, $capability, $menu_slug, $function, $icon_url, $position);

        $sub_menu_title = 'Email Tickets';

    add_submenu_page($menu_slug,__("Support/Request List", 'key4ce-osticket-bridge'),__("Email Tickets", 'key4ce-osticket-bridge'), $capability, $menu_slug, $function);

    // Added By Pratik Maniar on 21/09/2014 code start here

        $submenu_page_title = 'Create Ticket';

        $submenu_title = 'Create Ticket';

        $submenu_slug = 'ost-create-ticket';

        $submenu_function = 'ost_create_ticket';

    add_submenu_page($menu_slug,__("Create Ticket", 'key4ce-osticket-bridge'),__("Create Ticket", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function);

	// Added By Pratik Maniar on 21/09/2014 code end here

        $submenu_page_title = 'Settings';

        $submenu_title = 'Settings';

        $submenu_slug = 'ost-settings';

        $submenu_function = 'ost_settings_page';

    add_submenu_page($menu_slug, __("Settings", 'key4ce-osticket-bridge'),__("Settings", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function);

	$submenu_page_title = 'osT-Config';

        $submenu_title = 'osT-Config';

        $submenu_slug = 'ost-config';

        $submenu_function = 'ost_config_page';

    add_submenu_page($menu_slug,__("osT-Config", 'key4ce-osticket-bridge'), __("osT-Config", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function); 

	$submenu_page_title = 'Email Templates';

        $submenu_title = 'Email Templates';

        $submenu_slug = 'ost-emailtemp';

        $submenu_function = 'ost_emailtemp_page';

    add_submenu_page($menu_slug,__("Email Templates", 'key4ce-osticket-bridge'), __("Email Templates", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function);

	if($license_activate=="activated")

	{

		$submenu_page_title = 'Knowledge Base Synchronous';

        $submenu_title = 'Knowledge Base Synchronous';

        $submenu_slug = 'ost-kb';

        $submenu_function = 'ost_kb_page';

		add_submenu_page($menu_slug,__("Knowledge Base Synchronous", 'key4ce-osticket-bridge'), __("Knowledge Base Synchronous", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function);

	

	$submenu_page_title = 'Departments';

        $submenu_title = 'Departments';

        $submenu_slug = 'ost-departments';

        $submenu_function = 'ost_departments';

	add_submenu_page($menu_slug,__("Departments", 'key4ce-osticket-bridge'), __("Departments", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function);

	}

	$submenu_page_title = 'Key4ce License Key';

        $submenu_title = 'Key4ce License Key';

        $submenu_slug = 'ost-licensekey';

        $submenu_function = 'ost_licensekey';

	add_submenu_page($menu_slug,__("Key4ce License Key", 'key4ce-osticket-bridge'), __("Key4ce License Key", 'key4ce-osticket-bridge'), $capability, $submenu_slug, $submenu_function);

	// Hook into the 'wp_before_admin_bar_render' action

if (($database=="") || ($username=="") || ($password=="")) {

    add_action( 'wp_before_admin_bar_render', 'custom_toolbar_supportticket', 999 );

    } else {

        if(@$num_rows > 0)

            add_action( 'wp_before_admin_bar_render', 'custom_toolbar_openticket', 998 );

      else

add_action( 'wp_before_admin_bar_render', 'custom_toolbar_supportticket', 999 );

} }

function mb_admin_css() {

wp_enqueue_style('ost-bridge-admin', plugin_dir_url(__FILE__).'css/admin-style.css">');

}

function mb_install(){

$host='localhost';

$database='';

$username='';

$password='';

$keyost_prefix='ost_';

$supportpage='Support';

$config=array('host'=>$host,'database'=>$database,'username'=>$username,'password'=>$password,'keyost_prefix'=>$keyost_prefix,'supportpage'=>$supportpage);

update_option( 'os_ticket_config', $config);

//key4ce_kb_cpt();

flush_rewrite_rules();

wp_schedule_event( time(), 'hourly', 'my_hourly_event' );

}



// Looks for a shortcode within the current post's content.

// Optimized for shortcodes that don't have parameters.



function ost_has_shortcode_without_params($shortcode = '') {

  global $post;

  if (!$shortcode || $post == null) {  

    return false;  

  }

  if (stripos($post->post_content, '[' . $shortcode . ']') === false) {

    return false;

  }

  return true;

}

// User must be logged in to view pages that use the shortcode

function ost_enforce_login_action() {

  if(ost_has_shortcode_without_params(OST_SHORTCODE_ADDOSTICKET) && !is_user_logged_in()) {

    auth_redirect();

  }

}



add_action('wp', 'ost_enforce_login_action');

function mb_table_install() {

global $wpdb;

$sql="";

$table_name = $wpdb->prefix . "ost_emailtemp"; 

 if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

    //table is not created. you may create the table here.

    $sql ="DROP TABLE IF EXISTS ".$table_name.";\n";

    $sql .= "CREATE TABLE $table_name (

id mediumint(9) NOT NULL AUTO_INCREMENT,

name varchar(32) NOT NULL,

subject varchar(255) NOT NULL DEFAULT '',

text text NOT NULL,

ispublic int DEFAULT 0,

isactive int DEFAULT 0,

created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,

updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,

UNIQUE KEY id (id)

)ENGINE=InnoDB  DEFAULT CHARSET=utf8";

}   



require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

dbDelta($sql);

}



function mb_database_install() {

   global $wpdb;

   $table_name = $wpdb->prefix . "ost_emailtemp";

   // if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)

   // {

    $id = '1';

    $name = "Admin-Response";

    $subject = "Ticket ID [#\$ticketid]";

    $text = "";

    $rows_affected = $wpdb->insert(

            $table_name,

            array(

                'id' => $id,

                'name' => $name,

                'subject' => $subject,

                'text' => $text,

                'created' => current_time('mysql'),

                'updated' => current_time('mysql')

                ) );

    $id = '2';

    $name = "New-Ticket";

    $subject = "Ticket ID [#\$ticketid]";

    $text = "";

    $rows_affected = $wpdb->insert(

            $table_name,

            array(

                'id' => $id,

                'name' => $name,

                'subject' => $subject,

                'text' => $text,

                'created' => current_time('mysql'),

                'updated' => current_time('mysql')

                ) ); 

    $id = '3';

    $name = "Post-Confirmation";

    $subject = "Ticket ID [#\$ticketid]";

    $text = "";

    $rows_affected = $wpdb->insert(

            $table_name,

            array(

                'id' => $id,

                'name' => $name,

                'subject' => $subject,

                'text' => $text,

                'created' => current_time('mysql'),

                'updated' => current_time('mysql')

            ) );

    $id = '4';

    $name = "Admin-New-Ticket";

    $subject = "Ticket ID [#\$ticketid]";

    $text = "";

    $rows_affected = $wpdb->insert(

            $table_name, array(

                'id' => $id,

                'name' => $name,

                'subject' => $subject,

                'text' => $text,

                'created' => current_time('mysql'),

                'updated' => current_time('mysql')

                ));

// }

    }

function mb_uninstall(){

    delete_option('os_ticket_config');

    global $wpdb;

    $table = $wpdb->prefix."ost_emailtemp";

    $wpdb->query("DROP TABLE IF EXISTS $table");

    $table_config = "ost_config";

    $wpdb->query("DELETE FROM $table_config WHERE `namespace`='core' and `key`='smtp_username'");

    $wpdb->query("DELETE FROM $table_config WHERE `namespace`='core' and `key`='smtp_password'");

    $wpdb->query("DELETE FROM $table_config WHERE `namespace`='core' and `key`='smtp_host'");

    $wpdb->query("DELETE FROM $table_config WHERE `namespace`='core' and `key`='smtp_port'");

    $wpdb->query("DELETE FROM $table_config WHERE `namespace`='core' and `key`='smtp_status'");

	 flush_rewrite_rules();

	 wp_clear_scheduled_hook('my_hourly_event');

    }

function ost_create_ticket() {

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );}

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/admin_create_ticket.php' );

        }

function ost_config_page() {

    if (!current_user_can('manage_options')) {

        wp_die( __('You do not have sufficient permissions to access this page.') );

        }

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-config.php' );

        }

function ost_kb_page() {

     $config = get_option('os_ticket_config');

    extract($config);

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );

    }

    if (($database=="") || ($username=="") || ($password=="")){

        echo "<div class='headtitleerror'>".__("osTicket Settings", 'key4ce-osticket-bridge')."</div><div id='message' class='error'>" . __( '<p><b>Error:</b> You must complete "osTicket Data Configure" before this page will display... <a href="admin.php?page=ost-config">click here</a></p>', 'ost-menu' ) . "</div>";

    } else {

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-kb.php' );

        }

        }

function ost_departments() {

     $config = get_option('os_ticket_config');

    extract($config);

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );

    }

    if (($database=="") || ($username=="") || ($password=="")){

        echo "<div class='headtitleerror'>".__("osTicket Settings", 'key4ce-osticket-bridge')."</div><div id='message' class='error'>" . __( '<p><b>Error:</b> You must complete "osTicket Data Configure" before this page will display... <a href="admin.php?page=ost-config">click here</a></p>', 'ost-menu' ) . "</div>";

    } else {

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-departments.php' );

        }

        }

function ost_licensekey() {

     $config = get_option('os_ticket_config');

    extract($config);

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );

    }

    if (($database=="") || ($username=="") || ($password=="")){

        echo "<div class='headtitleerror'>".__("osTicket Settings", 'key4ce-osticket-bridge')."</div><div id='message' class='error'>" . __( '<p><b>Error:</b> You must complete "osTicket Data Configure" before this page will display... <a href="admin.php?page=ost-config">click here</a></p>', 'ost-menu' ) . "</div>";

    } else {

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-licensekey.php' );

        }

        }

function ost_settings_page() {

    $config = get_option('os_ticket_config');

    extract($config);

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );

        }

    if (($database=="") || ($username=="") || ($password=="")){

        echo "<div class='headtitleerror'>".__("osTicket Settings", 'key4ce-osticket-bridge')."</div><div id='message' class='error'>" . __( '<p><b>Error:</b> You must complete "osTicket Data Configure" before this page will display... <a href="admin.php?page=ost-config">click here</a></p>', 'ost-menu' ) . "</div>";

    } else {

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-settings.php' );

        }

    }

function ost_emailtemp_page() {

    $config = get_option('os_ticket_config');

    extract($config);

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );

    }

    if (($database=="") || ($username=="") || ($password=="")){

        echo "<div class='headtitleerror'>".__("osTicket Settings", 'key4ce-osticket-bridge')."</div><div id='message' class='error'>" . __( '<p><b>Error:</b> You must complete "osTicket Data Configure" before this page will display... <a href="admin.php?page=ost-config">click here</a></p>', 'ost-menu' ) . "</div>";

    } else {

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-emailtemp.php' );

        }

}

add_action('wp_ajax_get_helptopic_dropdown', 'get_helptopic_dropdown_callback');

add_action('wp_ajax_nopriv_get_helptopic_dropdown', 'get_helptopic_dropdown_callback');



function get_helptopic_dropdown_callback(){

	if($_POST['dept_id'] > 0 && $_POST['dept_id']!=""){

		$config = get_option('os_ticket_config');

		extract($config);

		$ost_wpdb = new wpdb($username, $password, $database, $host);

		$topic_table=$keyost_prefix."help_topic";	

		$help_topic_opt = $ost_wpdb->get_results("SELECT topic_id,topic FROM $topic_table  where ispublic=1 and isactive=1 and dept_id='".$_POST['dept_id']."' ORDER BY `sort` ASC ");

		$rowCount = $ost_wpdb->num_rows;

		if($rowCount > 0 ){

			$helptopic_dropdown="<select id=\"topicId\" name=\"topicId\">";

			foreach ($help_topic_opt as $help_topic){

			   $helptopic_dropdown.="<option value=". $help_topic->topic_id.">".$help_topic->topic."</option>";

			}

			$helptopic_dropdown.="</select>";				

			echo $helptopic_dropdown;

			die();

		} else {

                    echo "";

                    die();

                    }

                } else {

                    die();

                }

}

add_action('wp_ajax_get_cannedresponce_dropdown', 'get_cannedresponce_dropdown_callback');

function get_cannedresponce_dropdown_callback(){

	 require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/functions.php');

	if($_POST['cannedResp']!=""){

		$config = get_option('os_ticket_config');

		extract($config);

		$ost_wpdb = new wpdb($username, $password, $database, $host);

		$ost_canned_response=$keyost_prefix."canned_response";	

		$canned_response="";

		if($_POST['cannedResp']=="original" && $_POST['ticketid']!="")

		{

			$ticket=getNumberToID($_POST['ticketid']);

			$canned_response = getOriginalMessage($ticket);

		}

		else if($_POST['cannedResp']=="lastmessage" && $_POST['ticketid']!="")

		{

			$ticket=getNumberToID($_POST['ticketid']);

			$canned_response = getLastMessage($ticket);

		}

		else

		{

			$canned_response_data = $ost_wpdb->get_var("SELECT response FROM $ost_canned_response WHERE isenabled =1 AND canned_id='".$_POST['cannedResp']."'");

			$canned_response=key4ce_ReplaceVar($canned_response_data,$_POST['ticketid']);

		}

		echo $canned_response;

			die();

                } else {

                    die();

                }

}

add_action( 'wp_ajax_faq_autocomplete', 'faq_autocomplete' );

add_action( 'wp_ajax_nopriv_faq_autocomplete', 'faq_autocomplete' );

function faq_autocomplete() {

	if($_GET['term'])

	{

		if(isset($_GET['category_ids']) && $_GET['category_ids']!="")

		{

			$category_cnt_ary=explode(",",$_GET['category_ids']);

			$category_cnt=count($category_cnt_ary);

			if($category_cnt_ary[0]=="0")

			{

				$args = array('s'=>$_GET['term'],'post_type' => 'key4ce_kb','post_status' => 'publish');

			}

			else

			{

				if($category_cnt >= 1)

				{

					$args = array('s'=>$_GET['term'],'post_type' => 'key4ce_kb','post_status' => 'publish','tax_query' => array(array('taxonomy' => 'key4ce_kb_tax','field' => 'id','terms' => array($_GET['category_ids']),'operator' => 'IN')));

				}

				else

				{

					$args = array('s'=>$_GET['term'],'post_type' => 'key4ce_kb','post_status' => 'publish','tax_query' => array(array('taxonomy' => 'key4ce_kb_tax','field' => 'id','terms' => $_GET['category_ids'])));

				}

			}

		}

		else

		{

			$args = array('s'=>$_GET['term'],'post_type' => 'key4ce_kb','post_status' => 'publish');	

		}

		$posts = get_posts($args);

		$suggestions=array();

		foreach ($posts as $post)

		{

			setup_postdata($post);

			$suggestion = array();

			$suggestion['label'] = esc_html($post->post_title);

			$suggestion['link'] = get_permalink($post->ID);

			$suggestions[]= $suggestion;

		}

		echo $searchFAQdata=json_encode($suggestions);

		wp_reset_postdata();

		die();

	}

}

 function override_tax_template($template){

        // is a specific custom taxonomy being shown?

        $taxonomy_array = array('key4ce_kb');

            if ( is_tax($taxonomy_single) ) 

			{

                $template = WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/taxonomy-key4ce_kb_tax.php';

            }

        return $template;

    }

    add_filter('taxonomy_template','override_tax_template');

function ost_tickets_page() {

$config = get_option('os_ticket_config');

extract($config);

    if (!current_user_can('manage_options')){

        wp_die( __('You do not have sufficient permissions to access this page.') );

        }

    if (($database=="") || ($username=="") || ($password=="")){

        echo "<div class='headtitleerror'>osTicket - Support/Request List</div><div id='message' class='error'>" . __( '<p><b>Error:</b> You must complete "osTicket Data Configure" before this page will display... <a href="admin.php?page=ost-config">click here</a></p>', 'ost-menu' ) . "</div>";

    } else {

        if(isset($_REQUEST['service']) && $_REQUEST['service']=='view') { 

    require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-ticketview.php' );

    } 

    else  if(isset($_REQUEST['service']) && $_REQUEST['service']=='admin-create-ticket') { 

        require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/admin_create_ticket.php' );

        } else {

            require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/ost-tickets.php' );

            }

         }

}

?>