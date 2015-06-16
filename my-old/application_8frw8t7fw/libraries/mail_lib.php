<?php

	/**
	* Mail Library
	* Author: Bobbie Stump
	* Date: 26 December 2013
	* Notes:
	* This library will be used to send email(s)
	*
	*/
	class Mail_lib {
		
		protected $mail_template_path;
		
		////////////////
		// Class Constructor
		////////////////
		public function __construct() {
			
			//Define mail template path
			$this->mail_template_path 	= 'backend/mail-templates/';
			
		}
		
		/*******************************
		**	Send email
		**
		**	Description:
		**	This method will send emails to users
		**
		**	@param:		$arr_template			Template data
		**										array(
		**											['path']	=> 'path to template' (relative to mail_template_path defined in construct)
		**											[...additional data...]	=> ...
		**										);
		**
		**  @param:		$arr_users_and_data		Array of user objects combined with template data to replace in email (see below)
		**
		**										array(
		**  										[uid] =>
		**  											array(
		**  												[arr_template_values] = array(
		**														'[username]'	=> bstump
		**														..
		**													)
		**  												[user] => (user object)
		**												)
		**  									)
		**
		**	@return:	void
		**
		**  Author: Bobbie Stump
		**
		**/
		public function send( $arr_template, $arr_users_and_data ) {
			
			$CI	=& get_instance();
			
			// Load important stuff
			// protected $CI =& get_instance();
			$CI->load->library('email');

			// Read in template
			$email_template = $CI->load->view($this->mail_template_path.trim($arr_template['path']), '', TRUE);

			foreach ($arr_users_and_data as $cur_user_data) {

				////////////////
				// Parse template, and replace short-codes with short-code-values from arr_users_and_data.
				////////////////

				$message	= str_replace(array_keys($cur_user_data['arr_template_values']), array_values($cur_user_data['arr_template_values']), $email_template);
				
				// Define user object
				$obj_user = $cur_user_data['obj_user'];

				// Set email information
				$CI->email->from('no-reply@dev.saltsha.com', 'Saltsha');
				$CI->email->to($obj_user->get('email'));
				$CI->email->subject($arr_template['subject']);
				$CI->email->message($message);

				// Send email
				$CI->email->send();
				
			}
			
		}
		
	}