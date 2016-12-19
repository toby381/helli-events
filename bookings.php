<?php


/*CREATE PAGE*/

add_action('admin_menu', 'helli_event_add_booking_page');
function helli_event_add_booking_page() {
    add_submenu_page(
        'edit.php?post_type=helli-events',
        'Bookings', /*page title*/
        'Bookings', /*menu title*/
        'manage_options', /*roles and capabiliyt needed*/
        'helli-bookings',
        'tt_render_list_page' /*replace with your own function*/
    );
}
?>