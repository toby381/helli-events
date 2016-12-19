<?php
//setting up the form
function helli_event_add_new_booking() {

  global $wpdb;
  $table_name = $wpdb->prefix . "helli_event_booking"; //try not using Uppercase letters or blank spaces when naming db tables

  $wpdb->insert($table_name, array(
                            'firstName' => $_POST['firstName'], 
                            'lastName' => $_POST['lastName'], 
                            'epost' => $_POST['epost'],
                            'user_id' => $_POST['user_id'],
                            'event_id' => $_POST['event_id'],
                            
                            ),array(
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                            '%d') 
    );
  }

//And now to connect to form:
if( isset($_POST['booking_submit']) ) helli_event_add_new_booking();
?>