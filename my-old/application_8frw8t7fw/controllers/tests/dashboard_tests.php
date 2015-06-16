<?php


	class Dashboard_tests extends CI_Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
		
			echo ('<h2>Dashboard Model Tests</h2>
			The following test have been ran on the Dashboard Model through the controller method dashboard_tests/run_tests <br /><br />');
			
			$this->load->library('unit_test');
			$this->load->model('dashboard_model');	
			
			////////////////
			// * UNIT TEST: Get Widget Data Test, This test tests whether widget data has been retrieved and returned as an array.
			///////////////
			$arr_widget_data = $this->dashboard_model->get_widget_data(3); 
			echo $this->unit->run($arr_widget_data, 'is_array', 'Unit Test get_widget_data()', 'This test tests whether widget data has been retrieved and returned as an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->dashboard_model->get_widget_data(3) == true, 'is_true', 'Component Test get_widget_data()', 'This test tests whether widget data has been retrieved and returned as an array.');

			////////////////
			// * UNIT TEST: Save Dashboard Test, This test tests whether the dashboard has saved to the widget data to the database.
			///////////////
			$test = $this->dashboard_model->save_dashboard(array()); 
			echo $this->unit->run($test, 'is_bool', 'Unit Test save_dashboard()', 'This test tests whether the dashboard has saved to the widget data to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->dashboard_model->save_dashboard(array()) == true, 'is_true', 'Component Test save_dashboard()', 'This test tests whether the dashboard has saved to the widget data to the database.');
			
			////////////////
			// * UNIT TEST: Get Widget View Test, This test tests whether a widget view has be sent back as an array.
			///////////////
			$test = $this->dashboard_model->get_widget_view(2); 
			echo $this->unit->run($test, 'is_object', 'Unit Test get_widget_view()', 'This test tests whether a widget view has be sent back as an object.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->dashboard_model->get_widget_view(2) == true, 'is_true', 'Component Test get_widget_view()', 'This test tests whether a widget view has be sent back as an object.');	
				
			////////////////
			// * UNIT TEST: Save Widget Test, This test tests whether widget data has been saved to the database.
			///////////////
			$test = $this->dashboard_model->save_widget($arr_widget_data[0]); 
			echo $this->unit->run($test, 'is_bool', 'Unit Test save_widget()', 'This test tests whether widget data has been saved to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->dashboard_model->save_widget($arr_widget_data[0]) == true, 'is_true', 'Component Test save_widget()', 'This test tests whether widget data has been saved to the database.');
			
			////////////////
			// * UNIT TEST: Remove Widget Test, This test tests whether widget has been removed from the database.
			///////////////
			$test = $this->dashboard_model->remove_widget(3); 
			echo $this->unit->run($test, 'is_bool', 'Unit Test remove_widget()', 'This test tests whether widget has been removed from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->dashboard_model->remove_widget(3) == true, 'is_true', 'Component Test remove_widget()', 'This test tests whether widget has been removed from the database.');
			
			////////////////
			// * UNIT TEST: Get All Widgets Test, This test tests whether widget data has been retrieved and returned as an array.
			///////////////
			$test = $this->dashboard_model->get_all_widgets(); 
			echo $this->unit->run($test, 'is_array', 'Unit Test get_all_widgets()', 'This test tests whether widget data has been retrieved and returned as an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->dashboard_model->get_all_widgets() == true, 'is_true', 'Component Test get_all_widgets()', 'This test tests whether widget data has been retrieved and returned as an array.');
	}
}