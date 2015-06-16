<?php
/**
 * Stats table class for mailgun logs
 */
class SMRTMailgunStatsTable extends WP_List_Table {
    var $log_data = array();

    function __construct() {
        if (isset($_POST['log_type'])) {
            $_SESSION['log_type'] = $_POST['log_type'];
        }

        $this->log_data = mr_get_mail_logs();

        global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'cron_error', 'mes_stats_table' ),
            'plural'    => __( 'cron_errors', 'mes_stats_table' ),
            'ajax'      => false
        ));
        add_action( 'admin_head', array( &$this, 'admin_header' ) );            
    }

    function admin_header() {
        // Had to add this to make WP happy
    }

    function no_items() {
        _e( '<em>No logs found.</em>' );
    }
    
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'recipient':
                $get_user = get_user_by('email', trim($item[$column_name]));
                return ($get_user) ? '<a href="'.site_url('/wp-admin/user-edit.php?user_id='.$get_user->ID).'">'.$item[ $column_name ].'</a>' : $item[ $column_name ];
            default:
                return (isset($item[ $column_name ]) && trim($item[ $column_name ]) !== '') ? $item[ $column_name ] : '--';
        }
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'timestamp'  => array('timestamp',false),
            'event'  => array('event',false)
        );
        return $sortable_columns;
    }
    
    function get_columns(){
        $columns = array(
            'timestamp' => __( 'Time', 'mes_stats_table' ),
            'event' => __( 'Event', 'mes_stats_table' ),
            'recipient'    => __( 'Recipient', 'mes_stats_table' ),
            'device_type'    => __( 'Device Type', 'mes_stats_table' ),
            'client_os'    => __( 'Device OS', 'mes_stats_table' )
        );
        return $columns;
    }
    
    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'timestamp';
    
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
    
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
    
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        usort( $this->log_data, array( &$this, 'usort_reorder' ) );

        // If per page is set, set it new
        if (isset($_POST['per_page'])) {
            $_SESSION['log_per_page'] = $_POST['per_page'];
        }

        // Check items per page
        if (!isset($_SESSION['log_per_page']) || trim($_SESSION['log_per_page']) === '') {
            $_SESSION['log_per_page'] = 10;
        }

        $current_page = $this->get_pagenum();
        $total_items = count( $this->log_data );

        // only ncessary because we have sample data
        $this->found_data = array_slice( $this->log_data,( ( $current_page-1 )* $_SESSION['log_per_page'] ), $_SESSION['log_per_page'] );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $_SESSION['log_per_page']
        ) );
        $this->items = $this->found_data;
    }
}
?>