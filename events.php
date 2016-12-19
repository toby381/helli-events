<?php
/*
Plugin Name: Helli-Event
Plugin URI: https://github.com/toby381/helli-events
Description: Enkel påmeldingsystem for Eventer osv. 
Version: 0.2
Author: Torbjørn Høvde
Author URI: 
License: GPLv2
*/
define("HELLIEVENTDBTABLE", 'helli_event_booking');


function helli_event_flush_rewrite_rules() {
    //oppretter custom post type before updating permalinks
    create_helli_event();

    // update permalinks
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
// oppretter db når plugin skriveres
// mangler: trenger varable for å holde på gjeldene databaseversjon!!
function helli_event_create_db() {
    
	global $wpdb;
  	$version = get_option( 'my_plugin_version', '1.0' );
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . HELLIEVENTDBTABLE;

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        event_id bigint(20),
        user_id bigint(20),
		lastName varchar(255),
        firstName varchar(255),
        address varchar(255),
        epost varchar(255),
        UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	if ( version_compare( $version, '2.0' ) < 0 ) {
		
	  	//update_option( 'my_plugin_version', '2.0' );
		
	}
    
    helli_event_flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'helli_event_create_db' );



add_action( 'init', 'create_helli_event' );
add_action( 'admin_init', 'helli_event_add_meta_box' );
add_action( 'save_post', 'add_helli_event_custom_fields', 10, 2 );
add_filter( 'template_include', 'helli_event_include_template_function', 1 );


define("HELLIEVENTPOSTTYPE", "helli-events");

include('bookings.php');
include('bookings-table.php');

include('savebooking.php');


// Lager custom post type: event
function create_helli_event() {
    register_post_type( HELLIEVENTPOSTTYPE,
        array(
            'labels' => array(
                'name' => 'Helli Events',
                'singular_name' => 'Helli Event',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Helli Event',
                'edit' => 'Edit',
                'edit_item' => 'Edit Helli Event',
                'new_item' => 'New Helli Event',
                'view' => 'View',
                'view_item' => 'View Helli Event',
                'search_items' => 'Search Helli Events',
                'not_found' => 'No Helli Event found',
                'not_found_in_trash' => 'No Helli Events found in Trash',
                'parent' => 'Parent Helli Event'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor',  'thumbnail'),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/icon.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

// Legger til customfelter for event-post
function helli_event_add_meta_box() {
    add_meta_box( 'helli_event_info_meta_box',
        'Event info',
        'display_helli_event_meta_box',
        'helli-events', 'normal', 'high'
    );
}

// Viser inputfelter for event-post
function display_helli_event_meta_box( $helli_event ) {
    
    $event_sted = esc_html( get_post_meta( $helli_event->ID, 'event_sted', true ) );
    $event_booking_status =get_post_meta( $helli_event->ID, 'event_booking_status', true );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Sted</td>
            <td><input type="text" size="80" name="helli-event_sted" value="<?php echo $event_sted; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 100%">Booking status</td>
            <td><input type="checkbox"  name="helli-event_booking_status" <?php if( $event_booking_status == 'on') { ?>checked="checked"<?php } ?> /></td>
        </tr>
    </table>
    <?php
}


// lagrer customfelter for event-post i databasen
function add_helli_event_custom_fields( $helli_event_id, $helli_event ) {
    // Check post type for movie reviews
    if ( $helli_event->post_type == HELLIEVENTPOSTTYPE ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['helli-event_sted'] ) && $_POST['helli-event_sted'] != '' ) {
            update_post_meta( $helli_event_id, 'event_sted', $_POST['helli-event_sted'] );
        }
        $book='on';
        if(!isset($_POST['helli-event_booking_status'])) $book ='off';
       // if ( $book == 'yes') {
            update_post_meta( $helli_event_id, 'event_booking_status', $book );
       // }
        
    }
}

// lager template for visning
function helli_event_include_template_function( $template_path ) {
    if ( get_post_type() == HELLIEVENTPOSTTYPE ) {
       
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-helli-events.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/templates/single-helli-events.php';
            }
        }
    }
    return $template_path;
}


?>
