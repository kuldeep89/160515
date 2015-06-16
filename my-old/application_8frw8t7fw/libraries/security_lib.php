<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	* Security Library
	* Author: Thomas Melvin
	* Date: 12/10/2012
	*
	*/

	require_once dirname(dirname(__FILE__)).'/models/classes/objects/User_class.php';

	/*************************************
	**	Security Library
	**
	**	Description:
	**	This library handles user permissions
	**	and grants access.
	** 
	**	Author: Thomas Melvin
	**	Date:	17 May 2012
	**  E-mail: tdog@tmelvin.com
	**	Website: tmelvin.com
	**
	**************************************/
	
	class Security_lib {
		
		//
		// Data Members
		//
		private $itoa64;
		private $iteration_count_log2;
		private $portable_hashes;
		private $random_state;
		
		//
		// CodeIgniter
		//
		private $CI;
		
		/**
		* Constructor
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function __construct( $iteration_count_log2 = 8, $portable_hashes = FALSE ) {
			
			$this->CI	=& get_instance();
			
			//
			// Password hasher construct
			//
			$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

			if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31 ) {
				$iteration_count_log2 = 8;
			}
			
			$this->iteration_count_log2	= $iteration_count_log2;
			$this->portable_hashes		= $portable_hashes;
			$this->random_state			= microtime();
			
			if( function_exists('getmypid') ) {
				$this->random_state .= getmypid();
			}
			
		}
		
		/**
		* Is authenticated
		*
		* Checks to see if the current user is authenticated.
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function is_authenticated() {
		
			if( $this->CI->session->userdata('authenticated') !== FALSE ) {
				return TRUE;
			}
			else {
				return FALSE;
			}
			
		}
		
		/*******************************
		**	Is Group Member
		**
		**	Description:
		**	Checks to see if the current user is a member of the passed group.
		**
		**	@param:		$group_id
		**	@return:	boolean
		**
		**  Author: Thomas Melvin
		**
		**/
		public function is_group_member( $group_id ) {
		
			////////////////
			// Check if group is in the user's groups.
			////////////////
			
		
		}
		
		/**
		* Login From Session
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function login_from_session() {
			
			$arr_fields	= array(
				'id',
				'ip_address',
				'username', 
				'password',
				'email',
				'created_on', 
				'last_login', 
				'active',
				'first_name',
				'last_name',
				'company',
				'phone',
				'google_id',
				'profile_image',
				'name',
				'size',
				'height',
				'width',
				'type',
				'namespace',
				'date_created'
			);
			
			//
			// Now set the user that is logged in.
			//
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				$arr_data[$field]	= $this->CI->session->userdata($field);
			}
			
			$this->CI->current_user	= new User_class($arr_data);
			$this->CI->current_user->set('arr_permissions', $this->CI->session->userdata('permissions'));

		}
		
		/*******************************
		**	Permissions Required
		**
		**	Description:
		**	This method will check to see if the
		**  current user has the provided permissions
		**  to access the page it is trying to view.
		**
		**	@param:		$arr_permissions
		**	@return:	boolean:redirect
		**
		**  Author: Thomas Melvin
		**
		**/
		public function permissions_required( $arr_required_permissions ) {
			
			////////////////
			// Ensure passed parameter is an array.
			////////////////
			if( !is_array($arr_required_permissions) ) {
				$arr_required_permissions	= array($arr_required_permissions);
			}
			
			////////////////
			// Loop through permissions and check.
			////////////////
			$arr_user_permissions	= array_keys(json_decode($this->CI->current_user->get('arr_permissions'), TRUE));
			
			foreach( $arr_required_permissions as $permission_id ) {
				
				if( !in_array($permission_id, $arr_user_permissions) ) {
					
					//They do not have the permission needed to view this page.
					$this->CI->notification_lib->add_error('You do not have sufficient permissions to access this page.');
					redirect($this->CI->session->flashdata('referrer'));
					
				}
				
			}
			
			return TRUE;
			
		}
		
		/*******************************
		**	Has Permission
		**
		**	Description:
		**	This method will check to see if the user has the passed permission.
		**
		**	@param:		permission_id
		**	@return:	boolean
		**
		**  Author: Thomas Melvin
		**
		**/
		public function accessible( $permission_id ) {
		
			////////////////
			// Check to see if the user has the passed permission.
			////////////////
			$arr_user_permissions	= array_keys(json_decode($this->CI->current_user->get('arr_permissions'), TRUE));
			return in_array($permission_id, $arr_user_permissions);
		
		}
		
		/**
		* User Login
		*
		* Logs a member in.
		*
		* @access	public
		* @param	obj_member
		* @return	void
		*
		*/
		public function user_login( $obj_member ) {
			
			//
			// Set the session fields to be stored.
			//
			$arr_fields	= array(
				'id',
				'ip_address',
				'username', 
				'password',
				'email',
				'created_on', 
				'last_login', 
				'active',
				'first_name',
				'last_name',
				'company',
				'phone',
				'google_id',
				'profile_image',
				'name',
				'size',
				'height',
				'width',
				'type',
				'namespace',
				'date_created'
			);
			
			//
			// Store fields in session.
			//
			foreach( $arr_fields as $field ) {
				$this->CI->session->set_userdata($field, $obj_member->get($field));
			}
			
			//
			// Set the authentiated field.
			//
			$this->CI->session->set_userdata('authenticated', TRUE);
			
			////////////////
			// Establish the current user.
			////////////////
			$this->CI->current_user	= $obj_member;
			
			////////////////
			// Retrieve User Permissions
			////////////////
			$this->CI->load->model('permissions_model');
			
			$arr_user_permissions	= $this->CI->permissions_model->get_user_permissions($this->CI->current_user->get('id'));
			
			$arr_permissions	= array();
			
			foreach( $arr_user_permissions as $arr_permission ) {
				$arr_permissions[$arr_permission['permission_id']]	= $arr_permission['value'];
			}
			
			$json_permissions		= json_encode($arr_permissions);
			
			$this->CI->session->set_userdata('permissions', $json_permissions);
			
			////////////////
			// Store the user's group.
			////////////////
			$arr_groups	= $this->CI->current_user->get_groups();
			
			if( count($arr_groups) > 0 ) {
				foreach( $arr_groups as $arr_group ) {
					$this->CI->session->set_userdata('group_'.$arr_group['group_id'], TRUE);
				}
			}
			
		}
		
		//
		// Password Hashing Methods
		//
		
		function get_random_bytes( $count ) {
		
			$output = '';
		
			if ( FALSE &&
			    ($fh = @fopen('/dev/urandom', 'rb'))) {
				$output = fread($fh, $count);
				fclose($fh);
			}
	
			if (strlen($output) < $count) {
				$output = '';
				for ($i = 0; $i < $count; $i += 16) {
					$this->random_state =
					    md5(microtime() . $this->random_state);
					$output .=
					    pack('H*', md5($this->random_state));
				}
				$output = substr($output, 0, $count);
			}
	
			return $output;
			
		}
	
		function encode64($input, $count)
		{
			$output = '';
			$i = 0;
			do {
				$value = ord($input[$i++]);
				$output .= $this->itoa64[$value & 0x3f];
				if ($i < $count)
					$value |= ord($input[$i]) << 8;
				$output .= $this->itoa64[($value >> 6) & 0x3f];
				if ($i++ >= $count)
					break;
				if ($i < $count)
					$value |= ord($input[$i]) << 16;
				$output .= $this->itoa64[($value >> 12) & 0x3f];
				if ($i++ >= $count)
					break;
				$output .= $this->itoa64[($value >> 18) & 0x3f];
			} while ($i < $count);
	
			return $output;
		}
	
		function gensalt_private($input) {
		
			$output = '$P$';
			$output .= $this->itoa64[min($this->iteration_count_log2 +
				((PHP_VERSION >= '5') ? 5 : 3), 30)];
			$output .= $this->encode64($input, 6);
	
			return $output;
			
		}
	
		function crypt_private($password, $setting) {
		
			$output = '*0';
			if (substr($setting, 0, 2) == $output)
				$output = '*1';
	
			$id = substr($setting, 0, 3);
			# We use "$P$", phpBB3 uses "$H$" for the same thing
			if ($id != '$P$' && $id != '$H$')
				return $output;
	
			$count_log2 = strpos($this->itoa64, $setting[3]);
			if ($count_log2 < 7 || $count_log2 > 30)
				return $output;
	
			$count = 1 << $count_log2;
	
			$salt = substr($setting, 4, 8);
			if (strlen($salt) != 8)
				return $output;
	
			# We're kind of forced to use MD5 here since it's the only
			# cryptographic primitive available in all versions of PHP
			# currently in use.  To implement our own low-level crypto
			# in PHP would result in much worse performance and
			# consequently in lower iteration counts and hashes that are
			# quicker to crack (by non-PHP code).
			if (PHP_VERSION >= '5') {
				$hash = md5($salt . $password, TRUE);
				do {
					$hash = md5($hash . $password, TRUE);
				} while (--$count);
			} else {
				$hash = pack('H*', md5($salt . $password));
				do {
					$hash = pack('H*', md5($hash . $password));
				} while (--$count);
			}
	
			$output = substr($setting, 0, 12);
			$output .= $this->encode64($hash, 16);
	
			return $output;
			
		}
	
		function gensalt_extended($input) {
		
			$count_log2 = min($this->iteration_count_log2 + 8, 24);
			# This should be odd to not reveal weak DES keys, and the
			# maximum valid value is (2**24 - 1) which is odd anyway.
			$count = (1 << $count_log2) - 1;
	
			$output = '_';
			$output .= $this->itoa64[$count & 0x3f];
			$output .= $this->itoa64[($count >> 6) & 0x3f];
			$output .= $this->itoa64[($count >> 12) & 0x3f];
			$output .= $this->itoa64[($count >> 18) & 0x3f];
	
			$output .= $this->encode64($input, 3);
	
			return $output;
		
		}
	
		function gensalt_blowfish($input) {
		
			# This one needs to use a different order of characters and a
			# different encoding scheme from the one in encode64() above.
			# We care because the last character in our encoded string will
			# only represent 2 bits.  While two known implementations of
			# bcrypt will happily accept and correct a salt string which
			# has the 4 unused bits set to non-zero, we do not want to take
			# chances and we also do not want to waste an additional byte
			# of entropy.
			$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
			$output = '$2a$';
			$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
			$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
			$output .= '$';
	
			$i = 0;
			do {
				$c1 = ord($input[$i++]);
				$output .= $itoa64[$c1 >> 2];
				$c1 = ($c1 & 0x03) << 4;
				if ($i >= 16) {
					$output .= $itoa64[$c1];
					break;
				}
	
				$c2 = ord($input[$i++]);
				$c1 |= $c2 >> 4;
				$output .= $itoa64[$c1];
				$c1 = ($c2 & 0x0f) << 2;
	
				$c2 = ord($input[$i++]);
				$c1 |= $c2 >> 6;
				$output .= $itoa64[$c1];
				$output .= $itoa64[$c2 & 0x3f];
			} while (1);
	
			return $output;
			
		}
	
		function hash_password($password) {
		
			$random = '';
	
			if (CRYPT_BLOWFISH == 1 && !$this->portable_hashes) {
				$random = $this->get_random_bytes(16);
				$hash =
				    crypt($password, $this->gensalt_blowfish($random));
				if (strlen($hash) == 60)
					return $hash;
			}
	
			if (CRYPT_EXT_DES == 1 && !$this->portable_hashes) {
				if (strlen($random) < 3)
					$random = $this->get_random_bytes(3);
				$hash =
				    crypt($password, $this->gensalt_extended($random));
				if (strlen($hash) == 20)
					return $hash;
			}
	
			if (strlen($random) < 6)
				$random = $this->get_random_bytes(6);
			$hash =
			    $this->crypt_private($password,
			    $this->gensalt_private($random));
			if (strlen($hash) == 34)
				return $hash;
	
			# Returning '*' on error is safe here, but would _not_ be safe
			# in a crypt(3)-like function used _both_ for generating new
			# hashes and for validating passwords against existing hashes.
			return '*';
			
		}
	
		function check_password($password, $stored_hash) {
		
			$hash = $this->crypt_private($password, $stored_hash);
			if ($hash[0] == '*')
				$hash = crypt($password, $stored_hash);
	
			return $hash == $stored_hash;
			
		}
		
		/*******************************
		**	Password Generator
		**
		**	Description:
		**	Generates a random password.
		**	@param:		length
		**	@param:		strength
		**	@return:	void
		**
		**/
		function generate_password( $length = 9, $strength = 0 ) {
		
			$vowels		= 'aeuy';
			$consonants	= 'bdghjmnpqrstvz';
			
			if ($strength & 1) {
				$consonants .= 'BDGHJLMNPQRSTVWXZ';
			}
			
			if ($strength & 2) {
				$vowels .= "AEUY";
			}
			
			if ($strength & 4) {
				$consonants .= '23456789';
			}
			
			if ($strength & 8) {
				$consonants .= '@#$%';
			}
			
			$password	= '';
			$alt		= time() % 2;
			
			for( $i = 0; $i < $length; $i++ ) {
		
				if( $alt == 1 ) {
		
					$password	.= $consonants[(rand() % strlen($consonants))];
					$alt		= 0;
					
				} 
				else {
				
					$password	.= $vowels[(rand() % strlen($vowels))];
					$alt		= 1;
					
				}
				
			}
			
			return $password;
		
		}
		
	}

/* End of file */