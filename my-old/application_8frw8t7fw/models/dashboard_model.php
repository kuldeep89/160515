<?php
	
	/**
	* Dashboard Model
	* Author: Bobbie Stump
	* Date: 22 August 2013
	* Notes:
	* This model will work with the database to
	* add/update/remove data associated with the dashboard
	*
	*/

	class Dashboard_model extends CI_Model {
		////////////////
		// Cached Data Members
		////////////////
		
		public function __construct() {
			parent::__construct();
		}

		/*******************************
		**	Save Dashboard Widget Locations
		**
		**	Description:
		**	This method saves widget locations on the dashboard
		**
		**	@param:		$arr_widget_data
		**	@return:	boolean
		**
		**/
		public function save_dashboard($arr_widget_data) {
			// Loop through widgets, get data and save new widget location(s)
			foreach($arr_widget_data as $new_widget_data) :
				// Get widget data
				$this->db->where('db_id', $new_widget_data['db_id']);
				$get_widget_data = $this->db->get('widget_data');
				$arr_widget_data = $get_widget_data->result_array();

				// Parse JSON data from database
				$old_widget_data = json_decode($arr_widget_data[0]['widget_data'], true);

				// Set new column and row
				$old_widget_data['widget_location']['column'] = $new_widget_data['widget_data']['widget_location']['column'];
				$old_widget_data['widget_location']['row'] = $new_widget_data['widget_data']['widget_location']['row'];
				
				// Update database with new location
				$this->db->where('db_id', $new_widget_data['db_id']);
				if (!$this->db->update('widget_data', array('widget_data'=>json_encode($old_widget_data)))) {
					return false;
				}
			endforeach;

			// Return true because no db errors returned
			return true;
		}

		/*******************************
		**	Get current widget's fields
		**
		**	Description:
		**	This method gets stock widget data
		**
		**	@param:		none
		**	@return:	array
		**
		**/
		public function get_widget_view($int_widget_type) {
			$this->db->where('widget_type', $int_widget_type);
			return $this->db->get('widgets');
		}

		/*******************************
		**	Get widget's config data
		**
		**	Description:
		**	This method gets the widget data for the widget ID passed
		**
		**	@param:		none
		**	@return:	array
		**
		**/
		public function get_widget_data($int_widget_db_id) {
			$this->db->where('db_id', $int_widget_db_id);
			return $this->db->get('widget_data')->result_array();
		}

		/*******************************
		**	Save widget's field(s)
		**
		**	Description:
		**	This method saves edited widget data
		**
		**	@param:		array
		**	@return:	boolean
		**
		**/
		public function save_widget($arr_widget_data) {
			// Save widget data to database
			if (!isset($arr_widget_data['db_id'])) :
				if ($this->db->insert('widget_data', $arr_widget_data)) :
					return true;
				endif;
			else :
				if ($this->db->where('db_id', $arr_widget_data['db_id'])) :
					if ($this->db->update('widget_data', $arr_widget_data)) :
						return true;
					else :
						return false;
					endif;
				else:
					return false;
				endif;
			endif;
			return false;
		}

		/*******************************
		**	Save widget's field(s)
		**
		**	Description:
		**	This method saves edited widget data
		**
		**	@param:		array
		**	@return:	boolean
		**
		**/
		public function remove_widget($int_widget_db_id) {
			// Save widget data to database
			$this->db->where('db_id', $int_widget_db_id);
			if ($this->db->delete('widget_data')) {
				return true;
			} else {
				return false;
			}
		}

		/*******************************
		**	Get all widget data
		**
		**	Description:
		**	This method gets stock widget data
		**
		**	@param:		none
		**	@return:	array
		**
		**/
		public function get_all_widgets() {
			return $this->db->get('widgets')->result_array();
		}
	}