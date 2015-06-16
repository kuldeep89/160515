<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function index() {
	
		
	
		// Load academy and dashboard models
		$this->load->model(array('academy_model','dashboard_model','widgets_model'));
		
		
		
		// Load helpers
		$this->load->helper(array('js', 'calculations', 'widgets_helper'));

		// Set maximum rows for each column
		$arr_page['max_rows'] = 4;

		// Get user's widget information
		$this->db->where('user_id', $this->current_user->get('id'));
		$obj_query = $this->db->get('widget_data');

		// Get widget data and sort the rows in ascending order
		foreach ($obj_query->result_array() as $cur_widget) :
			$widget_info = json_decode($cur_widget['widget_data'], true);
			$widget_info['db_id'] = $cur_widget['db_id'];
			$arr_page['widgets'][$widget_info['widget_location']['column']][] = $widget_info;
			sort($arr_page['widgets'][$widget_info['widget_location']['column']], SORT_ASC);
		endforeach;

		// Load dashboard
		$this->load->view('backend/pages/dashboard/index', $arr_page);
	}

	// This function saves dashboard changes that have been submitted
	public function save_dashboard() {
		// Load dashboard model
		$this->load->model('dashboard_model');

		// Setup multidimensional array to store widget data
		$arr_widget_data = array();

		// Set all db information
		foreach (json_decode($this->input->post('json'), true) as $cur_widget) :
			// Set/clear data for storing current widget data
			$arr_cur_widget_data = array();

			// Academy category widget, set the data
			$arr_cur_widget_data['db_id'] = $cur_widget['db_id'];
			$arr_cur_widget_data['user_id'] = $this->current_user->get('id');
			$arr_cur_widget_data['widget_data'] = $cur_widget['widget_data'];

			// Push widget data to widget data array
			array_push($arr_widget_data, $arr_cur_widget_data);
		endforeach;

		// Send data to the model to be updated in the database
		if ($this->dashboard_model->save_dashboard($arr_widget_data)) :
			echo '{"status":"success","statusmsg":"Dashboard changes saved successfully!"}';
		else:
			echo '{"status":"error","statusmsg":"Dashboard changes were not saved successfully, please try again."}';
		endif;
	}

	// Show initial widget view
	public function show_widgets() {
		$this->load->model('dashboard_model');
		$this->load->view('backend/widgets/main');
	}

	// Get the current widget's field
	public function get_widget_view() {
		// Load dashboard model
		$this->load->model('dashboard_model');
		$this->load->model('academy_model');

		// Decode JSON data into array
		$widget_data = json_decode($this->input->post('json'), true);

		// Get widget config URL so we can load the config view
		$widget_info = $this->dashboard_model->get_widget_view($widget_data['widget_type'])->result_array();

		// Load widget view
		if ($widget_data['view_type'] == 'display') :
			$this->load->view($widget_info[0]['widget_display_url'], $widget_data);
		else:
			$this->load->view($widget_info[0]['widget_config_url'], $widget_data);
		endif;
	}

	// Pass details to widget to add to database
	public function save_widget() {
		// Load dashboard model
		$this->load->model(array('dashboard_model','widgets_model', 'academy_model'));

		// Load helper(s)
		$this->load->helper(array('widgets','calculations'));
		
		// Decode JSON and prep array for db insertion
		$widget_data = json_decode($this->input->post('json'), true);

		// Set all data
		foreach ($widget_data as $key=>$value) :
			$arr_page_data[$key] = $value;
		endforeach;


		// Set user id for db data
		$db_data['user_id'] = $this->current_user->get('id');

		// Loop through array of JSON data
		foreach ($widget_data as $key=>$value) :
			if ($key == "db_id") :
				$db_data['db_id'] = $value;
			else :
				$db_data['widget_data'][$key] = $value;
			endif;
		endforeach;
		$db_data['widget_data'] = json_encode($db_data['widget_data']);

		// Save widget data in JSON format, then load view
		if ($this->dashboard_model->save_widget($db_data)) :
			// Get widget data
			$widget_info = $this->widgets_model->get_widget_from_id($widget_data["widget_type"]);

			// Load widget view with widget data
			$this->load->view($widget_info['widget_display_url'], $widget_data);
		else:
			echo 'ERROR';
		endif;
	}

	// Remove widget
	public function remove_widget() {
		// Load dashboard model
		$this->load->model('dashboard_model');

		// Decode JSON and prep array for db insertion
		$widget_data = json_decode($this->input->post('json'), true);

		// Remove widget
		if ($this->dashboard_model->remove_widget($widget_data["db_id"])) :
			echo '{"status":"success","statusmsg":"Widget removed successfully!"}';
		else:
			echo '{"status":"error","statusmsg":"Widget was not removed successfully, please try again."}';
		endif;
	}
}