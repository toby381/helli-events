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


// skriver ut tabellrader med skjemafelter
function helli_event_form_add_table_row($row_id,$form_label,$form_types,$selected_type) {
        $table ='<tr id="helli_event_row_id_'.$row_id.'"><td>Label</td>
                <td><input type="text" size="20" name="helli_event_meta_box_form_labels_'.$row_id.'" value="'.$form_label.'" /></td>
                <td>Felttype</td>
                <td><select name="helli_event_meta_box_form_type_'.$row_id.'" id="helli_event_meta_box_form_type_'.$row_id.'">';
        foreach ($form_types as $form_type){ 
            if(intval($selected_type) == $form_type['ID']) {
                $table .= '<option SELECTED value="' . $form_type['ID'] .'">'.esc_html($form_type['type']).'</option>';
            } else {
                $table .= '<option value="' . $form_type['ID'] .'">'.esc_html($form_type['type']).'</option>';
            }
        }
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
                'ID'        => 2,
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
    
    ?> 
    <table id="helli_event_display_form_box"> <?php
    
    //total count of form-fields
    $event_form_total= intval(get_post_meta( $helli_event->ID, 'helli_event_meta_box_form_count', true )); 
    //echo $event_form_total;
    if($event_form_total==0){
        echo helli_event_form_add_table_row($helli_event_form_row_id,'',$helli_form_types,0);
        $event_form_total = 1;
    }else {
        for($i=0;$i<$event_form_total;$i++) {
            $helli_event_meta_box_form_labels= esc_html( get_post_meta( $helli_event->ID, 'helli_event_meta_box_form_labels_'.$i, true ) ); 
            $helli_event_meta_box_form_type= esc_html( get_post_meta( $helli_event->ID, 'helli_event_meta_box_form_type_'.$i, true ) ); 
            echo helli_event_form_add_table_row($i,$helli_event_meta_box_form_labels,$helli_form_types,$helli_event_meta_box_form_type);
        }
    }
    ?>
    </table>
    <input type="hidden" value="<?php echo $event_form_total; ?>" name="helli_event_meta_box_form_count" id="helli_event_meta_box_form_count"/>
    <input type="button" value="Legg til felt" id="helli_event_btnAdd"/>
    <script type="text/javascript">
        jQuery(function($) {
            var tableID = '#helli_event_display_form_box';
            $('#helli_event_btnAdd').click(function() {
                var row_id = $(tableID+" tr").length;
                $(tableID).append('<?php echo helli_event_form_add_table_row( $helli_event_form_row_id, '', $helli_form_types,0); ?>');
                $(tableID + " tr").last().attr("id","helli_event_row_id_"+row_id);
                $(tableID + " tr input").last().attr("name","helli_event_meta_box_form_labels_"+row_id);
                $('input#helli_event_meta_box_form_count').val(row_id+1);
            });
        });
        </script> 
    <?php
}

// lagrer customfelter for event-post i databasen
function helli_event_save_form_box( $helli_event_id, $helli_event ) {
    if ( $helli_event->post_type == 'helli-events' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['helli_event_meta_box_form_count'] )) {
            $total = intval($_POST['helli_event_meta_box_form_count']);
            update_post_meta( $helli_event_id, 'helli_event_meta_box_form_count', $total );
            for($i=0;$i<$total;$i++) {
                //echo $i . " : " . $total . " : " . isset( $_POST['helli_event_meta_box_form_labels_'.$i] ) . "<br>";
                if ( isset( $_POST['helli_event_meta_box_form_labels_'.$i] ) && $_POST['helli_event_meta_box_form_labels_'.$i] != '' ) {
                    update_post_meta( $helli_event_id, 'helli_event_meta_box_form_labels_'.$i, $_POST['helli_event_meta_box_form_labels_'.$i] );
                    if ( isset( $_POST['helli_event_meta_box_form_labels_'.$i] ) && $_POST['helli_event_meta_box_form_labels_'.$i] != '' ) {
                        update_post_meta( $helli_event_id, 'helli_event_meta_box_form_type_'.$i, $_POST['helli_event_meta_box_form_type_'.$i] );
                    }
                } else {
                    $total--;
                    delete_metadata($helli_event_id, 'helli_event_meta_box_form_labels_'.$i,'');
                    delete_metadata($helli_event_id, 'helli_event_meta_box_form_type_'.$i,'');
                }
                 
            }
        }
    }
}
add_action( 'save_post', 'helli_event_save_form_box', 10, 2 );

?>