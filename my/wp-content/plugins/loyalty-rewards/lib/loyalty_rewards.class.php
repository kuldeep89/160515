<?php
class RewardsStore {
    private $environment;
    private $subdomain;
    private $token;
    private $key;
    private $return_json;
    private $catalogs;
    private $socket_id;
    private $categories;

    /**
     * Construct
     */
    public function __construct($rsp_options = null, $return_json = true) {
        // No token, no play
        if (is_null($rsp_options) || !isset($rsp_options->token)) {
            // Create fault object and return
            $return_obj = new StdClass();
            $return_obj->Fault->faultcode = 'RewardsStoreConstruct.NullArgumentsError';
            $return_obj->Fault->faultstring = 'One or more arguments for method __construct were null or blank.';
            $return_obj->Fault->detail = '';
            return $return_obj;
        }

        // Set attributes
        // $rsp_options->subdomain
        $this->environment = (is_null($rsp_options->environment)) ? 'dev' : $rsp_options->environment;
        $this->subdomain = (is_null($rsp_options->subdomain) || trim($rsp_options->subdomain) === '') ? 'sandbox' : $rsp_options->subdomain;
        $this->token = $rsp_options->token;
        $this->key = (isset($rsp_options->key)) ? $rsp_options->key : null;
        $this->return_json = $return_json;
        $this->catalogs = null;
        $this->socket_id = (isset($rsp_options->default_catalog)) ? $rsp_options->default_catalog : $this->get_socket_id();
        $this->categories = null;
    }


    /**
     * Getter
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }


    /**
     * Setter
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }


    /**
     * Generate checksum arguments
     */
    private function generate_checksum($method = null) {
        // Return blank string if null
        if (is_null($method)) {
            return '';
        }

        // Get message ID
        $message_id = $this->generate_guid();

        // Get UTC time
        $now_string = gmdate('Y-m-d\TH:i:s');

        // $now_string = date('Y-m-d\TH:i:s', strtotime('+5 hours'));
        
        // $now_datetime = new DateTime('NOW', new \DateTimeZone('UTC'));
        // $now_string = $now_datetime->format('Y-m-d H:i:s');

        // Encode checksum
        $checksum = base64_encode(hash_hmac("sha1", "$method$message_id$now_string", $this->key, true));

        // Return checksum
        return "creds_datetime=".rawurlencode($now_string)."&creds_uuid=".rawurlencode($message_id)."&creds_checksum=".rawurlencode($checksum);
    }

    
    /**
     * Generage GUID
     */
    function generate_guid() {
        if (function_exists('com_create_guid')) {
            return substr(com_create_guid(), 1, 36);
        } else {
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid,12, 4).$hyphen.substr($charid,16, 4).$hyphen.substr($charid,20,12);
            return $uuid;
        }
    }


    /**
     * Send API request
     */
    public function send_request($api_call = 'list_available_catalogs', $args = array()) {
        // Build list of arguments
        if (count($args) > 0) {
            $built_args = array();
            foreach ($args as $key => $value) {
                $built_args[] = $key.'='.rawurlencode($value);
            }
            $built_args =  '&'.$this->generate_checksum($api_call).'&'.implode('&', $built_args);
        } else {
            $built_args = '';
        }

        // Add socket ID
        if (!is_null($this->socket_id)) {
            $built_args .= '&socket_id='.$this->socket_id;
        }

        // Send request
        $the_url = 'https://'.$this->subdomain.'.'.$this->environment.'.catalogapi.com/v1/rest/'.$api_call.'/?token='.$this->token.$built_args;
        if ($this->environment === 'sandbox.dev') {
            $the_url = 'https://sandbox.dev.catalogapi.com/v1/rest/'.$api_call.'/?token='.$this->token.$built_args;
        }

        // Prepare and send curl request
        $ch = curl_init($the_url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $the_request = curl_exec($ch);
        curl_close($ch);

        // Return response in JSON or plain text
        return ($this->return_json == true) ? json_decode($the_request) : $the_request;
    }


    /**
     * Get socket ID
     */
     public function get_socket_id() {
         // Send request
         $the_request = $this->send_request('list_available_catalogs');

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return null;
        }

         // Return socket ID
         return $the_request->list_available_catalogs_response->list_available_catalogs_result->domain->sockets->Socket[0]->socket_id;
     }


    /**
     * Load view with data
     */
    public function load_view($view = 'index', $data = array()) {
        include RSP_PATH.'/views/'.$view.'.php';
    }


    /**
     * Validate method arguments for request
     */
    private function validate_method_args($method = null, $args = null) {
        // Define required arguments for each method
        $method_required_args = array(
            'view_item' => array('catalog_item_id'),
            'cart_set_address' => array('external_user_id','first_name','last_name','address_1','city','state_province','postal_code','country'),
            'cart_set_item_quantity' => array('external_user_id','catalog_item_id','quantity'),
            'cart_add_item' => array('external_user_id','catalog_item_id','quantity'),
            'cart_remove_item' => array('external_user_id','catalog_item_id'),
            'cart_empty' => array('external_user_id'),
            'cart_view' => array('external_user_id'),
            'cart_validate' => array('external_user_id'),
            'cart_unlock' => array('external_user_id'),
            'cart_order_place' => array('external_user_id'),
            'order_place' => array('first_name','last_name','address_1','city','state_province','postal_code','country'),
            'order_track' => array('order_number'),
            'order_list' => array('external_user_id')
        );

        // If no required arguments found, return true
        if (!array_key_exists($method, $method_required_args)) {
            return true;
        }

        // Validate required arguments
        $missing_args = array();
        if (is_null($args)) {
            // Null args, set all arguments as missing
            $missing_args[] = $method_required_args[$method];
        } else {
            // Validate required args
            foreach ($method_required_args[$method] as $cur_method_arg) {
                if (!array_key_exists($cur_method_arg, $args) || trim($cur_method_arg) === '' || is_null($args[$cur_method_arg])) {
                    $missing_args[] = $cur_method_arg;
                }
            }
        }

        // If missing method arguments, return Fault
        if (count($missing_args) > 0) {
            $return_obj = new StdClass();
            $return_obj->Fault = new StdClass();
            $return_obj->Fault->faultcode = 'RewardsStore'.str_replace(' ', '', ucwords(str_replace('_', ' ', $method))).'.MissingArgumentsError';
            $return_obj->Fault->faultstring = 'The following arguments for method "'.$method.'" are missing: '.implode(',', $missing_args).'.';
            $return_obj->Fault->detail = '';
            return $return_obj;
        }

        // Return true
        return true;
    }


    /**
     * List available catalogs
     */
    public function list_available_catalogs() {
        if (is_null($this->catalogs)) {
            // Send request
            $the_request = $this->send_request('list_available_catalogs');

            // If request fails, return fault
            if (isset($the_request->Fault)) {
                return $the_request;
            }

            // Return catalogs
            $return_obj = new StdClass();
            $return_obj->catalogs = $the_request->list_available_catalogs_response->list_available_catalogs_result->domain->sockets->Socket;

            // Assign categories to instance
            $this->catalogs = $return_obj;
        } else {
            $return_obj = $this->catalogs;
        }

        return $return_obj;
    }


    /**
     * Get categories
     */
    public function catalog_breakdown() {
        // If categories list is null, send request, otherwise send cached data
        if (is_null($this->categories)) {
            // Send request
            $the_request = $this->send_request('catalog_breakdown');

            // If request fails, return fault
            if (isset($the_request->Fault)) {
                return $the_request;
            }

            // Return categories
            $return_obj = new StdClass();
            $return_obj->categories = $the_request->catalog_breakdown_response->catalog_breakdown_result->categories->Category;

            // Assign categories to instance
            $this->categories = $return_obj;
        } else {
            $return_obj = $this->categories;
        }
        return $return_obj;
    }


    /**
     * Search catalog
     */
    public function search_catalog($args = array()) {
        // Send request
        $the_request = $this->send_request('search_catalog', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = new StdClass();
        $return_obj->products = $the_request->search_catalog_response->search_catalog_result->items->CatalogItem;
        $return_obj->pagination = $the_request->search_catalog_response->search_catalog_result->pager;
        return $return_obj;
    }


    /**
     * Retrieve catalog item
     */
    public function view_item($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('view_item', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('view_item', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = $the_request->view_item_response->view_item_result->item;
        return $return_obj;
    }


    /**
     * Add address to cart
     */
    public function cart_set_address($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_set_address', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_set_address', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = $the_request->cart_set_address_response->cart_set_address_result;
        return $return_obj;
    }


    /**
     * Set quantity for item
     */
    public function cart_set_item_quantity($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_set_item_quantity', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_set_item_quantity', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Return
        return true;
    }


    /**
     * Add item to cart
     */
    public function cart_add_item($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_add_item', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_add_item', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Return
        return true;
    }


    /**
     * Remove item from cart
     */
    public function cart_remove_item($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_remove_item', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_remove_item', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Return
        return true;
    }


    /**
     * Empty cart
     */
    public function cart_empty($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_empty', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_empty', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Return
        return true;
    }


    /**
     * Get cart items
     */
    public function cart_view($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_view', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_view', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        if (isset($the_request->cart_view_response->cart_view_result->items->CartItem)) {
            $return_obj = $the_request->cart_view_response->cart_view_result->items->CartItem;
        } else {
            $return_obj = array();
        }
        return $return_obj;
    }


    /**
     * Validate cart contents
     */
    public function cart_validate($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_validate', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_validate', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = $the_request->cart_validate_response->cart_validate_result;
        return $return_obj;
    }


    /**
     * Unlock cart
     */
    public function cart_unlock($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_unlock', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_unlock', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Return
        return true;
    }


    /**
     * Place order
     */
    public function cart_order_place($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('cart_order_place', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('cart_order_place', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = $the_request->cart_order_place_response->cart_order_place_result->order_number;
        return $return_obj;
    }


    /**
     * Place order without cart
     */
    public function order_place($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('order_place', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('order_place', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = $the_request->order_place_response->order_place_result->order_number;
        return $return_obj;
    }


    /**
     * Track order
     */
    public function order_track($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('order_track', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('order_track', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = $the_request->order_track_response->order_track_result->order;
        return $return_obj;
    }


    /**
     * List customer's orders
     */
    public function order_list($args = null) {
        // Return fault if method doesn't validate
        $is_valid = $this->validate_method_args('order_list', $args);
        if (isset($is_valid->Fault)) {
            return $is_valid;
        }

        // Send request
        $the_request = $this->send_request('order_list', $args);

        // If request fails, return fault
        if (isset($the_request->Fault)) {
            return $the_request;
        }

        // Create object and return
        $return_obj = new StdClass();
        $return_obj->orders = $the_request->order_list_response->order_list_result->orders;
        $return_obj->pagination = $the_request->order_list_response->order_list_result->pager;
        return $return_obj;
    }
}  
?>