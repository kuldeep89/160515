<?php
class SMRTMerchantDataTable extends WP_List_Table {
    var $merchant_data = array();

    function __construct() {
        if (isset($_POST['member_level'])) {
            $_SESSION['member_level'] = $_POST['member_level'];
        }

        $this->merchant_data = smrt_get_merchant_data();

        global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'cron_error', 'smrt_stats_table' ),
            'plural'    => __( 'cron_errors', 'smrt_stats_table' ),
            'ajax'      => false
        ));
        add_action( 'admin_head', array( &$this, 'admin_header' ) );            
    }

    function admin_header() {
        // Had to add this to make WP happy
    }

    function no_items() {
        _e( '<em>No merchants found.</em>' );
    }
    
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'users':
                $user_list = array();
                foreach($item['users'] as $cur_user_id) {
                    $get_user = get_user_by( 'id', $cur_user_id );
                    $user_list[] = ($get_user) ? '<a href="'.site_url('/wp-admin/user-edit.php?user_id='.$get_user->ID).'">'.$get_user->user_login.'</a>' : $cur_user_id;
                }
                return implode(', ', $user_list);
            default:
                return (isset($item[ $column_name ]) && trim($item[ $column_name ]) !== '') ? $item[ $column_name ] : '--';
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'merchant_id'  => array('merchant_id',false)
        );
        return $sortable_columns;
    }
    
    function get_columns(){
        $columns = array(
            'merchant_id' => __( 'Merchant ID', 'smrt_stats_table' ),
            'users' => __( 'Users', 'smrt_stats_table' )
        );
        return $columns;
    }
    
    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'merchant_id';
    
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
        usort( $this->merchant_data, array( &$this, 'usort_reorder' ) );

        // If per page is set, set it new
        if (isset($_POST['per_page'])) {
            $_SESSION['smrt_per_page'] = $_POST['per_page'];
        }

        // Check items per page
        if (!isset($_SESSION['smrt_per_page']) || trim($_SESSION['smrt_per_page']) === '') {
            $_SESSION['smrt_per_page'] = 10;
        }

        // Show all
        if ($_SESSION['smrt_per_page'] == 'all') {
            $_SESSION['smrt_per_page'] = 10000;
        }

        $current_page = $this->get_pagenum();
        $total_items = count( $this->merchant_data );

        // only ncessary because we have sample data
        $this->found_data = array_slice( $this->merchant_data,( ( $current_page-1 )* $_SESSION['smrt_per_page'] ), $_SESSION['smrt_per_page'] );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $_SESSION['smrt_per_page']
        ) );
        $this->items = $this->found_data;
    }
}
?>