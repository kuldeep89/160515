<?php 

	/**
	* Notification Library
	* Author: Thomas Melvin
	* Date: 12/10/2012
	*
	*/

	class Notification_lib {
		
		private $CI;
		
		public function __construct() {
		
			//Get instance of CodeIgniter
			$this->CI	=& get_instance();
			
		}
		
		/*******************************
		**	Add Error
		**
		**	Description:
		**	Adds an error to the error
		**	collection.
		**
		**	@param:		string
		**	@return:	void
		**
		**/
		public function add_error( $error ) {
		
			//
			//	Get the position of the next error message.
			//
			$totalErrors	= $this->CI->session->userdata('totalErrors');
			$totalErrors	= ( $totalErrors === FALSE )? 0:$totalErrors;
			$key			= 'error'.$totalErrors;
			
			$this->CI->session->set_userdata($key, $error);

			//
			//	Update the total errors.
			//
			$this->CI->session->set_userdata('totalErrors', $totalErrors+1);
			
		}
		
		/*******************************
		**	Add Success
		**
		**	Description:
		**	Adds an success message to the 
		**	success collection.
		**
		**	@param:		String
		**	@return:	void
		**
		**/
		public function add_success( $success ) {
			
			//
			//	Get the position of the next success message.
			//
			$totalSuccess	= $this->CI->session->userdata('totalSuccess');;
			$totalSuccess	= ( $totalSuccess === FALSE )? 0:$totalSuccess;
			$key			= 'success'.$totalSuccess;
			
			$this->CI->session->set_userdata($key, $success);
			
			//
			//	Update the total successes.
			//
			$this->CI->session->set_userdata('totalSuccess', $totalSuccess+1);
			
		}
		
		/*******************************
		**	Get Errors
		**
		**	Description:
		**	Returns an array listing of all error
		**	messages that have been collected.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_errors() {
			
			$totalErrors	= $this->CI->session->userdata('totalErrors');
			$arr_errors		= array();
			
			//
			//	Check to see if there are errors. If so, get them and store them in the array.
			//
			if( $totalErrors > 0 ) {
			
				for( $i = 0; $i < $totalErrors; $i++ ) {
					
					//Retrieve the error and store it for return.
					$arr_errors[$i]	= $this->CI->session->userdata('error'.$i);
					
					//Now remove the error from the queue.
					$this->CI->session->unset_userdata('error'.$i);
					
				}
				
			}
			
			//
			//	Now reset the error count.
			//
			$this->CI->session->set_userdata('totalErrors', 0);
			
			return $arr_errors;
			
		}
		
		/*******************************
		**	Get Successes
		**
		**	Description:
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_successes() {
		
			$totalSuccess	= $this->CI->session->userdata('totalSuccess');
			$arr_successes	= array();
			
			//
			//	Check to see if there are any success messages, then get them and store them in the array for returning.
			//
			if( $totalSuccess > 0 ) {

				for( $i = 0; $i < $totalSuccess; $i++ ) {
				
					//Retrieve the message and store it for return.
					$arr_successes[$i]	= $this->CI->session->userdata('success'.$i);
					
					//Now remove the message from the queue.
					$this->CI->session->unset_userdata('success'.$i);
					
				}
				
			}
			
			//
			//	Now reset the success count.
			//
			$this->CI->session->set_userdata('totalSuccess', 0);
			
			return $arr_successes;
			
		}
		
		/**
		* Print Notifications
		*
		* Prints the errors, successes, and notifications.
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function print_notifications() {
			
			$this->print_errors();
			$this->print_successes();
			
		}
		
		/**
		* Print Errors
		*
		* Prints out the errors.
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function print_errors() {
			
			$arr_errors	= $this->get_errors();
			
			foreach( $arr_errors as $error ) {
				
				echo '<div class="alert alert-error">';
					echo '<a data-dismiss="alert" class="close"></a>';
					echo $error;
				echo '</div>';

			}
			
		}
		
		/**
		* Print Successes
		*
		* Prints out the successes.
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function print_successes() {
			
			$arr_successes	= $this->get_successes();
			
			foreach( $arr_successes as $successes ) {
				
				echo '<div class="alert alert-success">';
					echo '<a data-dismiss="alert" class="close"></a>';
					echo $successes;
				echo '</div>';
				
			}
			
		}
		
	}

?>