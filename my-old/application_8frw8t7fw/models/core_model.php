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

	class Core_model extends CI_Model {
		////////////////
		// Cached Data Members
		////////////////
		
		public function __construct() {
			parent::__construct();
		}

		/*******************************
		**	Get config data
		**
		**	Description:
		**	Get site config data from database
		**
		**	@param:		string
		**	@return:	boolean
		**
		**/
		public function get_config($str_config_key) {
			$this->db->where('key',$str_config_key);
			$maintenance_mode = $this->db->get('config')->result_array();
			return $maintenance_mode[0]['value'];
		}
	}
?>