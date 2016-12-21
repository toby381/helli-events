<?php
// Legger til customfelter for event-post
function helli_event_add_form_box() {
    add_meta_box( 'helli_event_form_box',
        'Event form',
        'helli_event_display_form_box',
        'helli-events', 'normal', 'high'
    );
}
add_action('admin_init', 'helli_event_add_form_box' );



function helli_event_form_add_table_row($row_id,$form_label,$form_types) {
        $table ='<tr id="helli_event_row_id_'.$row_id.'"><td>Label</td>
                <td><input type="text" size="20" name="event_form_labels" value="'.$form_label.'" /></td>
                <td>Felttype</td>
                <td><select name="my_meta_box_form_type" id="my_meta_box_form_type">';
        foreach ($form_types as $form_type): 
           $table .= '<option value="' . $form_type['ID'] .'">'.esc_html($form_type['type']).'</option>';
        endforeach; 
        $table .= '</select></td></tr>';
        $table = str_replace(array("\r", "\n"), '', $table);
        return $table;
}
// Viser inputfelter for event-post
function helli_event_display_form_box( $helli_event ) {
    $helli_event_form_row_id=0;
    $helli_form_types = array(
            array(
                'ID'        => 1,
                'type'     => 'text',
            ),
            array(
                'ID'        => 1,
                'type'     => 'email',
            ),
            array(
                'ID'        => 3,
                'type'     => 'checkbox',
            ),
            array(
                'ID'        => 4,
                'type'     => 'radiobuttons',
            )
        );
    $event_form_labels= esc_html( get_post_meta( $helli_event->ID, 'event_form_labels', true ) );
    
?> <table id="helli_event_display_form_box"> <?php
        
        echo helli_event_form_add_table_row($helli_event_form_row_id,$event_form_labels,$helli_form_types);
        
?>
    </table>
    <input type="button" value="Legg til felt" id="helli_event_btnAdd"/>
    <script type="text/javascript">
        jQuery(function($) {
            var tableID = '#helli_event_display_form_box';
            $('#helli_event_btnAdd').click(function() {
                var row_id = $(tableID+" tr").length;
                $(tableID).append('<?php echo helli_event_form_add_table_row( $helli_event_form_row_id, $event_form_labels, $helli_form_types); ?>');
                $(tableID + " tr").last().attr("id","helli_event_row_id_"+row_id);
            });
        });
        </script> 
    <?php
}

?>