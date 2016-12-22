<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TT_Example_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'booking',     //singular name of the listed records
            'plural'    => 'bookings',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }


    function column_default($item, $column_name){
        switch($column_name){
            case 'user_id':
            case 'event_id':
            case 'field_cols':
            case 'field_vals':
                return $item->$column_name;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->id                //The value of the checkbox should be the record's id
        );
    }
    function column_user_id($item) {
        $id = $item->user_id;
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&booking=%s">Edit</a>',$_REQUEST['page'],'edit',$item->id),
            'delete'    => sprintf('<a href="?page=%s&action=%s&booking=%s">Delete</a>',$_REQUEST['page'],'delete',$item->id),
        );
        
        if($id > 0) {
              //Return the title content
            
            $user = get_user_by( 'ID', $id );
            return '<a href="'.get_author_posts_url( $id, $user->nicename ).'">'. $user->first_name .'</a>'.$this->row_actions($actions);
        } else {
            return '<span style="color:red">uregistrert bruker</span>'.$this->row_actions($actions);
        }
    }
    function column_event_id($item) {
        $id = $item->event_id;
        if($id) {
            $event = get_the_title( $id );
            return $event;
        } else {
            return '<span style="color:red">Event eksisterer ikke</span>';
        }
        
        
    }
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'user_id'    => 'User',
            'event_id'    => 'Event',
            'field_cols'    => 'field_cols',
            'field_vals'    => 'field_vals'
        );
        return $columns;
    }
    /*function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'rating'    => array('rating',false),
            'director'  => array('director',false)
        );
        return $sortable_columns;
    }
    */
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }

    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 25;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        // this adds the prefix which is set by the user upon instillation of wordpress
        $table_name = $wpdb->prefix . 'helli_event_booking';
        $table_name_meta = $wpdb->prefix . 'helli_event_booking_meta';
        $table_post_meta = $wpdb->prefix . 'postmeta';
        // this will get the data from your table
        $data = $wpdb->get_results( "SELECT 
                            $table_name.id, 
                            $table_name.user_id, 
                            $table_name.event_id, 
                            GROUP_CONCAT($table_post_meta.meta_value 
                                ORDER BY $table_name_meta.field_row ASC) AS field_cols, 
                            GROUP_CONCAT($table_name_meta.field_value
                                ORDER BY $table_name_meta.field_row ASC) AS field_vals 
                                    
                            FROM $table_name 
                                LEFT JOIN $table_name_meta 
                                ON $table_name.id = $table_name_meta.booking_id 
                                LEFT JOIN $table_post_meta
                                ON $table_post_meta.post_id = $table_name.event_id
                            WHERE $table_post_meta.meta_key = CONCAT('helli_event_meta_box_form_labels_',$table_name_meta.field_row)
                            GROUP BY $table_name_meta.booking_id");
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


}

function tt_render_list_page(){
    
    /*    
    $table_book = $wpdb->prefix . 'helli_event_booking';
        $table_postmeta = $wpdb->prefix . 'postmeta';
        // this will get the data from your table
        $data = $wpdb->get_results( "SELECT * FROM $table_name LEFT JOIN $table_postmeta ON $table_postmeta.post_id = $table_name.booking_id AND $table_name.booking_id );
    */
    
    
    
    //Create an instance of our package class...
    $testListTable = new TT_Example_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    
    ?>
    <div class="wrap">
        
        <div id="icon-users" class="icon32"><br/></div>
        <h2>Bookings</h2>
        
        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
           
        </div>
        
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="bookings-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display() ?>
        </form>
        
    </div>
    <?php
}