<?php
//setting up the form
function helli_event_add_new_booking() {

    global $wpdb;
    $table_name = $wpdb->prefix . "helli_event_booking"; //try not using Uppercase letters or blank spaces when naming db tables
    
    $wpdb->insert($table_name, array(
                            'user_id' => $_POST['user_id'],
                            'event_id' => $_POST['event_id']
                            ),array(
                            '%d',
                            '%d') 
    );
    $booking_id = $wpdb->insert_id;
    $field_count = intval($_POST['field_count']);
    //echo  $field_count;
    for($i=0;$i<$field_count;$i++) {
        $wpdb->insert($table_name . "_meta", array(
                            'booking_id' => $booking_id,
                            'field_row' => $i,
                            'field_value' => $_POST['helli_event_field_'.$i]
                            ),array(
                            '%d',
                            '%d',
                            '%s') 
        );
    }
    
  }

//And now to connect to form:
if( isset($_POST['booking_submit']) ) helli_event_add_new_booking();
?>